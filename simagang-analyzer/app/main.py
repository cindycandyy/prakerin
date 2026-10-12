"""Layanan analisis SIMAGANG (FastAPI). Tanpa database; PHP yang menulis hasil ke MySQL.

Hanya mendengarkan 127.0.0.1, wajib header X-Api-Key, tidak log isi dokumen/nama peserta
(spesifikasi teknis bagian 3, "Keamanan layanan").
"""
import logging
import os
import time

from fastapi import FastAPI, Header, HTTPException, Request
from fastapi.responses import JSONResponse

from . import config
from .baca_proposal import baca_proposal
from .baca_transkrip import baca_transkrip
from .errors import AnalisisError, master_tidak_valid
from .jurusan import jenis_institusi, normalisasi_jurusan
from .llm import coba_llm, perlu_llm
from .skor import hitung_rekomendasi

logging.basicConfig(level=logging.INFO, format="%(asctime)s %(levelname)s %(message)s")
log = logging.getLogger("simagang-analyzer")

app = FastAPI(title="simagang-analyzer", version=config.VERSI)


def _cek_api_key(x_api_key: str | None):
    if not config.API_KEY:
        return  # dev lokal tanpa kunci diset
    if x_api_key != config.API_KEY:
        raise HTTPException(status_code=401, detail="X-Api-Key tidak valid")


def _cek_path_dalam_upload_dir(path: str):
    if not config.UPLOAD_DIR:
        return
    base = os.path.realpath(config.UPLOAD_DIR)
    target = os.path.realpath(path)
    if not target.startswith(base + os.sep) and target != base:
        raise AnalisisError("PATH_TIDAK_DIIZINKAN", "Berkas di luar direktori unggahan.", 422)


def _validasi_master(master: dict):
    bobot = master.get("bobot", {})
    total = sum(bobot.get(k, 0) for k in ("jurusan", "skill", "proposal", "kebutuhan"))
    if not master.get("divisi") or total != 100:
        raise master_tidak_valid("Master kosong atau bobot tidak berjumlah 100.")


@app.exception_handler(AnalisisError)
async def _handler_analisis_error(request: Request, exc: AnalisisError):
    log.info("galat kode=%s status=%s", exc.kode, exc.http_status)
    return JSONResponse(status_code=exc.http_status, content={"kode": exc.kode, "pesan": exc.pesan})


@app.get("/v1/health")
def health():
    if not config.LLM_SIAP:
        model = "mati"
    elif config.GROQ_API_KEY:
        model = "eksternal"
    else:
        model = "lokal"
    return {"status": "ok", "model": model, "versi": config.VERSI}


@app.post("/v1/analisis")
async def analisis(payload: dict, x_api_key: str | None = Header(default=None, alias="X-Api-Key")):
    _cek_api_key(x_api_key)
    mulai = time.time()
    job_id = payload.get("job_id")

    berkas = payload["berkas"]
    peserta = payload["peserta"]
    master = payload["master"]
    opsi = payload.get("opsi", {})
    _validasi_master(master)

    for p in (berkas["proposal"], berkas["transkrip"]):
        _cek_path_dalam_upload_dir(p)

    jurusan_norm = normalisasi_jurusan(peserta["jurusan"])
    jenis_inst = peserta.get("jenis_institusi") or jenis_institusi(peserta.get("pendidikan", ""))

    kata_kunci_master = master.get("sinyal", [])
    hasil_proposal = baca_proposal(berkas["proposal"], kata_kunci_master, jurusan_norm)

    catatan = list(hasil_proposal.get("catatan", []))
    try:
        hasil_transkrip = baca_transkrip(berkas["transkrip"])
        catatan.extend(hasil_transkrip.get("catatan", []))
        transkrip_gagal = False
    except AnalisisError as e:
        if e.kode != "TRANSKRIP_TIDAK_TERBACA":
            raise
        hasil_transkrip = {"nilai": [], "rata_nilai": None, "catatan": []}
        catatan.append("transkrip: tidak terbaca")
        transkrip_gagal = True

    profil = {
        "jenis_institusi": jenis_inst,
        "jurusan_norm": jurusan_norm,
        "peminatan": hasil_proposal["peminatan"],
        "judul": hasil_proposal["judul"],
        "bidang": hasil_proposal["bidang"],
        "alat": hasil_proposal["alat"],
        "tujuan": hasil_proposal["tujuan"],
        "mata_kuliah": hasil_proposal["mata_kuliah"],
        "nilai": hasil_transkrip["nilai"],
        "rata_nilai": hasil_transkrip["rata_nilai"],
        "sumber": "aturan",
    }

    hasil_skor = hitung_rekomendasi(profil, master)
    rekomendasi = hasil_skor["rekomendasi"]
    catatan.extend(hasil_skor["catatan"])

    audit_llm = {"dipakai": False, "teks_dikirim": None}
    if config.LLM_SIAP and opsi.get("izinkan_llm") and rekomendasi:
        jumlah_sinyal = len(profil["bidang"]) + len(profil["alat"])
        skor1 = rekomendasi[0]["skor"]
        skor2 = rekomendasi[1]["skor"] if len(rekomendasi) > 1 else 0
        if perlu_llm(jumlah_sinyal, skor1, skor2):
            hasil_llm, teks_dikirim = coba_llm(
                hasil_proposal["bagian_terpilih_teks"], peserta["nama"], peserta["instansi"]
            )
            audit_llm["teks_dikirim"] = teks_dikirim
            if hasil_llm:
                audit_llm["dipakai"] = True
                profil["bidang"] = list(dict.fromkeys(profil["bidang"] + hasil_llm["bidang"]))[:6]
                profil["alat"] = list(dict.fromkeys(profil["alat"] + hasil_llm["alat"]))[:8]
                profil["tujuan"] = profil["tujuan"] or hasil_llm["tujuan"]
                profil["sumber"] = "llm"
                hasil_skor = hitung_rekomendasi(profil, master)
                rekomendasi = hasil_skor["rekomendasi"]

    peringkat_1_rendah = bool(rekomendasi) and rekomendasi[0]["keyakinan"] == "rendah"
    ada_berkas_tak_terbaca = transkrip_gagal or any("tidak terbaca" in c for c in catatan)
    status = "perlu_tinjau" if (peringkat_1_rendah or ada_berkas_tak_terbaca) else "selesai"

    log.info("job_id=%s status=%s durasi_ms=%d", job_id, status, int((time.time() - mulai) * 1000))

    return {
        "job_id": job_id,
        "status": status,
        "profil": profil,
        "rekomendasi": rekomendasi,
        "divisi_penuh_disembunyikan": hasil_skor["divisi_penuh_disembunyikan"],
        "catatan": list(dict.fromkeys(catatan)),
        "audit_llm": audit_llm,
        "meta": {"durasi_ms": int((time.time() - mulai) * 1000), "versi_aturan": config.VERSI},
    }


@app.post("/v1/skor")
async def skor(payload: dict, x_api_key: str | None = Header(default=None, alias="X-Api-Key")):
    _cek_api_key(x_api_key)
    profil = payload["profil"]
    master = payload["master"]
    _validasi_master(master)
    hasil = hitung_rekomendasi(profil, master)
    return {"rekomendasi": hasil["rekomendasi"], "divisi_penuh_disembunyikan": hasil["divisi_penuh_disembunyikan"]}
