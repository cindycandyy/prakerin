"""Tahap LLM opsional (spesifikasi teknis bagian 4). Mati secara bawaan (config.llm_aktif).

Hanya dipanggil bila: (bidang+alat < 2) ATAU (selisih skor #1-#2 < 3 DAN skor #1 < 75).
Keluaran divalidasi ketat; pelanggaran -> dibuang & dicoba sekali lagi, lalu diabaikan.
"""
import json
import logging
import re

import requests

from . import config
from .redaksi import masih_ada_data_sensitif, redaksi

log = logging.getLogger("simagang-analyzer")

_GROQ_URL = "https://api.groq.com/openai/v1/chat/completions"

_SKEMA_MAKS = {"bidang": 6, "alat": 8}
_POLA_DIGIT_PANJANG = re.compile(r"\b\d{8,}\b")
_POLA_EMAIL = re.compile(r"[\w.+-]+@[\w-]+\.[\w.-]+")

PROMPT_SISTEM = (
    "Baca potongan proposal berikut dan jawab HANYA dengan JSON persis skema ini, "
    "tanpa teks lain:\n"
    '{"bidang": ["string, maks 6 elemen, huruf kecil"], '
    '"alat": ["string, maks 8 elemen, huruf kecil"], '
    '"tujuan": "string, maks 300 karakter"}'
)


def perlu_llm(jumlah_bidang_alat: int, skor1: float, skor2: float) -> bool:
    if jumlah_bidang_alat < 2:
        return True
    if skor1 < 75 and (skor1 - skor2) < 3:
        return True
    return False


def _validasi_keluaran(data: dict) -> dict | None:
    if not isinstance(data, dict):
        return None
    if set(data.keys()) - {"bidang", "alat", "tujuan"}:
        return None
    bidang = data.get("bidang", [])
    alat = data.get("alat", [])
    tujuan = data.get("tujuan", "")
    if not isinstance(bidang, list) or not isinstance(alat, list) or not isinstance(tujuan, str):
        return None
    if len(bidang) > _SKEMA_MAKS["bidang"] or len(alat) > _SKEMA_MAKS["alat"] or len(tujuan) > 300:
        return None
    gabung = " ".join(bidang) + " ".join(alat) + tujuan
    if _POLA_DIGIT_PANJANG.search(gabung) or _POLA_EMAIL.search(gabung):
        return None
    return {
        "bidang": [b.lower().strip() for b in bidang],
        "alat": [a.lower().strip() for a in alat],
        "tujuan": tujuan.strip() or None,
    }


def panggil_model_lokal(teks_redaksi: str) -> dict | None:  # pragma: no cover
    """Belum ada model di server sendiri (bagian 12.2 #17) — tidak dipanggil selama
    GROQ_API_KEY diset, lihat panggil_groq().
    """
    return None


def panggil_groq(teks_redaksi: str) -> str | None:
    """API eksternal (bukan server sendiri) — hanya untuk dev/testing, lihat config.py."""
    if not config.GROQ_API_KEY:
        return None
    try:
        r = requests.post(
            _GROQ_URL,
            headers={"Authorization": f"Bearer {config.GROQ_API_KEY}"},
            json={
                "model": config.GROQ_MODEL,
                "messages": [
                    {"role": "system", "content": PROMPT_SISTEM},
                    {"role": "user", "content": teks_redaksi},
                ],
                "response_format": {"type": "json_object"},
                "temperature": 0,
            },
            timeout=config.GROQ_TIMEOUT_DETIK,
        )
        r.raise_for_status()
        return r.json()["choices"][0]["message"]["content"]
    except (requests.RequestException, KeyError, IndexError) as e:
        log.info("galat panggil Groq: %s", type(e).__name__)
        return None


def coba_llm(teks_bagian: str, nama_peserta: str, instansi: str) -> tuple[dict | None, str | None]:
    """Return (hasil_tervalidasi_atau_None, teks_yang_dikirim_atau_None)."""
    teks_bersih = redaksi(teks_bagian, nama_peserta, instansi)
    if masih_ada_data_sensitif(teks_bersih):
        return None, None  # teks tidak dikirim; analisis berjalan tanpa model

    panggil = panggil_groq if config.GROQ_API_KEY else panggil_model_lokal

    for _ in range(2):  # jawaban gagal validasi -> dicoba sekali lagi, lalu diabaikan
        mentah = panggil(teks_bersih)
        if mentah is None:
            break
        try:
            data = mentah if isinstance(mentah, dict) else json.loads(mentah)
        except (TypeError, json.JSONDecodeError):
            continue
        tervalidasi = _validasi_keluaran(data)
        if tervalidasi:
            return tervalidasi, teks_bersih

    return None, teks_bersih
