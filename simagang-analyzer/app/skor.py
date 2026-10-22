"""Rumus skor (spesifikasi teknis bagian 5). Fungsi murni: masukan sama, keluaran sama."""
import math
import re

TARGET_ALAT_TOPIK = 6  # dua sinyal bobot 3 = penuh


def _bobot_sinyal(sinyal: list[dict], id_divisi: int, jenis: str, ditemukan: set[str]) -> int:
    return sum(
        s["bobot"] for s in sinyal
        if s["id_divisi"] == id_divisi and s["jenis"] == jenis and s["kata_kunci"].lower() in ditemukan
    )


def _s_alat(sinyal: list[dict], id_divisi: int, alat_profil: list[str]) -> float:
    ditemukan = {a.lower() for a in alat_profil}
    total = _bobot_sinyal(sinyal, id_divisi, "alat", ditemukan)
    return min(100.0, 100.0 * total / TARGET_ALAT_TOPIK)


def _s_topik(sinyal: list[dict], id_divisi: int, teks_topik: list[str]) -> float:
    gabung = " ".join(t.lower() for t in teks_topik if t)
    ditemukan = {
        s["kata_kunci"].lower() for s in sinyal
        if s["id_divisi"] == id_divisi and s["jenis"] == "topik"
        and re.search(r"(?<!\w)" + re.escape(s["kata_kunci"].lower()) + r"(?!\w)", gabung)
    }
    total = _bobot_sinyal(sinyal, id_divisi, "topik", ditemukan)
    return min(100.0, 100.0 * total / TARGET_ALAT_TOPIK)


def _s_nilai(mapel_relevan: list[dict], id_divisi: int, nilai_profil: list[dict], mata_kuliah: list[str]) -> float:
    pola_divisi = [m for m in mapel_relevan if m["id_divisi"] == id_divisi]
    if not pola_divisi:
        return 50.0
    cocok = []
    for n in nilai_profil:
        mapel = n["mapel"].lower()
        for p in pola_divisi:
            if p["pola_mapel"].lower() in mapel:
                cocok.append((n["nilai"], p["bobot"]))
                break
    if cocok:
        total_bobot = sum(b for _, b in cocok)
        return round(sum(v * b for v, b in cocok) / total_bobot, 2) if total_bobot else 50.0
    mk_gabung = " ".join(m.lower() for m in mata_kuliah)
    if any(p["pola_mapel"].lower() in mk_gabung for p in pola_divisi):
        return 60.0
    return 50.0


_JURUSAN_SEMUA = "semua jurusan"


def _f1_jurusan(pemetaan: list[dict], id_divisi: int, jurusan_norm: str, peminatan: str) -> tuple[float, bool]:
    """Return (skor, dipetakan). Baris dipetakan HARUS untuk id_divisi yang sama --
    tanpa filter ini, skor jurusan milik divisi lain bisa "bocor" terpakai (bug
    yang pernah terjadi: fungsi lama tidak menerima id_divisi sama sekali, jadi
    baris pertama yang match jurusan menang terlepas divisinya siapa).

    Jurusan yang tidak match spesifik ke divisi ini jatuh ke baris "semua jurusan"
    milik divisi itu, kalau ada -- bukan skor flat 30 untuk semua divisi.
    """
    baris_divisi = [p for p in pemetaan if p["id_divisi"] == id_divisi]
    for p in baris_divisi:
        if p["jurusan_norm"] == jurusan_norm and p["peminatan"] == peminatan:
            return float(p["skor"]), True
    for p in baris_divisi:
        if p["jurusan_norm"] == jurusan_norm and p["peminatan"] == "":
            return float(p["skor"]), True
    for p in baris_divisi:
        if p["jurusan_norm"] == _JURUSAN_SEMUA:
            return float(p["skor"]), True
    return 30.0, False


def _f4_kebutuhan(sisa_kuota: int) -> float:
    return {0: 0.0, 1: 50.0, 2: 75.0}.get(sisa_kuota, 100.0)


def round_half_up(nilai: float, desimal: int = 1) -> float:
    faktor = 10 ** desimal
    return math.floor(nilai * faktor + 0.5) / faktor


def skor_divisi(profil: dict, divisi: dict, master: dict) -> tuple[float, dict, list[str]]:
    """Hitung skor + rincian faktor untuk satu divisi. Return (skor, faktor_dict, catatan)."""
    catatan = []
    pemetaan = master.get("pemetaan_jurusan", [])
    f1, dipetakan = _f1_jurusan(pemetaan, divisi["id"], profil["jurusan_norm"], profil.get("peminatan", ""))
    if not dipetakan:
        catatan.append("jurusan belum dipetakan")

    sinyal = master.get("sinyal", [])
    s_alat = _s_alat(sinyal, divisi["id"], profil.get("alat", []))
    s_nilai = _s_nilai(master.get("mapel_relevan", []), divisi["id"], profil.get("nilai", []), profil.get("mata_kuliah", []))
    f2 = 0.6 * s_alat + 0.4 * s_nilai

    teks_topik = list(profil.get("bidang", [])) + [profil.get("tujuan") or "", profil.get("judul") or ""]
    f3 = _s_topik(sinyal, divisi["id"], teks_topik)

    f4 = _f4_kebutuhan(divisi.get("sisa_kuota", 0))

    w = master["bobot"]
    total = (w["jurusan"] * f1 + w["skill"] * f2 + w["proposal"] * f3 + w["kebutuhan"] * f4) / 100
    faktor = {"jurusan": round(f1, 2), "skill": round(f2, 2), "proposal": round(f3, 2), "kebutuhan": round(f4, 2)}
    return round_half_up(total, 1), faktor, catatan


def _keyakinan(skor: float, ambang: dict) -> str:
    if skor >= ambang["tinggi"]:
        return "tinggi"
    if skor >= ambang["sedang"]:
        return "sedang"
    return "rendah"


def _teks_alasan(profil: dict, divisi: dict, faktor: dict, master: dict) -> str:
    bagian = []
    if faktor["jurusan"] >= 70:
        pem = f" {profil['peminatan']}" if profil.get("peminatan") else ""
        bagian.append(f"Jurusan {profil['jurusan_norm']}{pem}")
    if faktor["proposal"] >= 50:
        topik_cocok = [
            s["kata_kunci"] for s in master.get("sinyal", [])
            if s["id_divisi"] == divisi["id"] and s["jenis"] == "topik"
            and s["kata_kunci"].lower() in " ".join(profil.get("bidang", [])).lower()
        ][:2]
        if topik_cocok:
            bagian.append(f"dan proposal tentang {' dan '.join(topik_cocok)}")
    if bagian:
        return (" ".join(bagian) + f" cocok dengan {divisi['nama']}.")[:500]
    return "Kecocokan terbatas; ini sinyal tertinggi di antara divisi yang tersedia."[:500]


def _teks_kekuatan(profil: dict, divisi: dict, master: dict) -> str:
    bagian = []
    nilai = profil.get("nilai", [])
    mapel_relevan = [m for m in master.get("mapel_relevan", []) if m["id_divisi"] == divisi["id"]]
    if nilai and mapel_relevan:
        cocok = [n for n in nilai if any(p["pola_mapel"].lower() in n["mapel"].lower() for p in mapel_relevan)]
        if cocok:
            terbaik = max(cocok, key=lambda n: n["nilai"])
            bagian.append(f"Nilai {terbaik['mapel']} {terbaik['nilai']}")
    alat_cocok = [
        s["kata_kunci"] for s in master.get("sinyal", [])
        if s["id_divisi"] == divisi["id"] and s["jenis"] == "alat"
        and s["kata_kunci"].lower() in [a.lower() for a in profil.get("alat", [])]
    ][:2]
    if alat_cocok:
        bagian.append(f"menyebut {' dan '.join(alat_cocok)}")
    return (", ".join(bagian) + ".")[:500] if bagian else "Tidak ada kekuatan menonjol."


def _teks_kekurangan(divisi: dict, faktor: dict, profil: dict) -> str:
    """Narasi kekurangan (spesifikasi bagian 5): ambang dinaikkan dari <30/<50 ke <60
    supaya skor "pas-pasan" (lolos ambang lama tapi tetap bikin keyakinan rendah/
    sedang secara keseluruhan) ikut dapat penjelasan, bukan cuma skor yang sangat
    buruk. Proposal rendah dipecah dua: topik sama sekali tidak nyambung (<30) vs
    nyambung tapi tipis/kurang detail (30-59) -- dua akar masalah beda, saran
    perbaikannya juga beda.
    """
    bagian = []
    if divisi.get("sisa_kuota") == 1:
        bagian.append("Sisa kuota divisi tinggal 1.")
    if faktor["jurusan"] < 60:
        bagian.append("Latar belakang jurusan kurang sejalan dengan bidang kerja divisi ini.")

    if faktor["proposal"] < 30:
        bagian.append("Topik proposal tidak menyinggung bidang kerja divisi ini sama sekali -- minat yang ditulis tampak mengarah ke bidang lain.")
    elif faktor["proposal"] < 60:
        bagian.append("Topik proposal menyinggung bidang divisi ini, tetapi kurang detail -- bagian Tujuan/Ruang Lingkup/Bidang PKL perlu menjelaskan lebih spesifik dan menyebut tools/metode yang relevan.")

    if not profil.get("alat"):
        bagian.append("Proposal tidak menyebut tools, software, atau metode kerja yang dikenali sistem secara eksplisit.")
    if not profil.get("nilai"):
        bagian.append("Nilai/transkrip tidak terbaca, sehingga faktor skill tidak bisa dinilai penuh.")
    elif faktor["skill"] < 60:
        bagian.append("Nilai mata kuliah yang relevan dengan divisi ini belum cukup menonjol.")

    return (" ".join(bagian))[:500] if bagian else "Tidak ada kekurangan menonjol."


def hitung_rekomendasi(profil: dict, master: dict) -> dict:
    """Pemilihan satu divisi terbaik. Return {"rekomendasi": [...], "divisi_penuh_disembunyikan": n}.

    "rekomendasi" tetap berbentuk list (1 elemen) demi kompatibilitas pemanggil,
    bukan dict tunggal.
    """
    divisi_list = [d for d in master["divisi"] if d.get("aktif", 1)]
    penuh = sum(1 for d in divisi_list if d.get("sisa_kuota", 0) <= 0)
    tersedia = [d for d in divisi_list if d.get("sisa_kuota", 0) > 0]

    dihitung = []
    for d in tersedia:
        skor, faktor, catatan = skor_divisi(profil, d, master)
        dihitung.append((skor, faktor, d, catatan))

    dihitung.sort(key=lambda x: (-x[0], -x[1]["jurusan"], -x[1]["proposal"], x[2]["nama"]))

    ambang = master["ambang"]
    rekomendasi = []
    semua_catatan = []
    for peringkat, (skor, faktor, d, catatan) in enumerate(dihitung[:1], start=1):
        semua_catatan.extend(catatan)
        rekomendasi.append({
            "peringkat": peringkat,
            "id_divisi": d["id"],
            "skor": skor,
            "keyakinan": _keyakinan(skor, ambang),
            "faktor": faktor,
            "alasan": _teks_alasan(profil, d, faktor, master),
            "kekuatan": _teks_kekuatan(profil, d, master),
            "kekurangan": _teks_kekurangan(d, faktor, profil),
        })

    return {
        "rekomendasi": rekomendasi,
        "divisi_penuh_disembunyikan": penuh,
        "catatan": list(dict.fromkeys(semua_catatan)),
    }
