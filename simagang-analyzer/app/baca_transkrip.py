"""Baca transkrip PDF -> daftar {mapel, nilai} + rata_nilai (spesifikasi teknis bagian 4).

Tidak pernah dikirim ke model bahasa. pdfplumber untuk tabel, regex sebagai fallback.
"""
import re

import pdfplumber

from .errors import pdf_rusak

_BUANG_MAPEL = re.compile(
    r"jumlah|rata-rata|peringkat|ranking|sakit|izin|alpa|absen|catatan", re.I
)
_POLA_BARIS_REGEX = re.compile(
    r"^(.+?)\s+(\d{2,3}(?:[.,]\d+)?)(?:\s+(\d{2,3}(?:[.,]\d+)?))?\s*$"
)
_POLA_IPK = re.compile(r"(?:IPK|Indeks Prestasi Kumulatif)\s*[:=]?\s*(\d[.,]\d+)", re.I)
_NILAI_HURUF = {"A": 90, "A-": 85, "B+": 80, "B": 75, "B-": 70, "C+": 65, "C": 60, "D": 50, "E": 0}


def _normalisasi_mapel(teks: str) -> str:
    t = teks.strip().lower()
    t = re.sub(r"^\s*\d+[.\)]\s*", "", t)  # buang nomor urut
    t = re.sub(r"^\s*kelompok\s*[a-z0-9]*\s*[:\-]?\s*", "", t)
    t = re.sub(r"\s{2,}", " ", t).strip()
    return t


def _angka(teks: str) -> float | None:
    t = teks.strip().replace(",", ".")
    try:
        v = float(t)
    except ValueError:
        return None
    return v if 0 <= v <= 100 else None


def _dari_tabel(path: str) -> list[dict]:
    hasil = []
    with pdfplumber.open(path) as pdf:
        for page in pdf.pages:
            for table in page.extract_tables() or []:
                for row in table:
                    sel_teks = [c for c in row if c and len(c.strip()) >= 3 and _angka(c) is None]
                    sel_angka = [a for c in row if c for a in [_angka(c)] if a is not None]
                    if not sel_teks or not sel_angka:
                        continue
                    mapel = _normalisasi_mapel(sel_teks[0])
                    if not mapel or _BUANG_MAPEL.search(mapel):
                        continue
                    nilai = sum(sel_angka) / len(sel_angka)
                    hasil.append({"mapel": mapel, "nilai": round(nilai, 2)})
    return hasil


def _dari_regex(teks_halaman: list[str]) -> list[dict]:
    hasil = []
    for teks in teks_halaman:
        for baris in teks.splitlines():
            m = _POLA_BARIS_REGEX.match(baris.strip())
            if not m:
                continue
            mapel = _normalisasi_mapel(m.group(1))
            if not mapel or _BUANG_MAPEL.search(mapel) or len(mapel) < 3:
                continue
            angka = [a for g in (m.group(2), m.group(3)) if g for a in [_angka(g)] if a is not None]
            if not angka:
                continue
            hasil.append({"mapel": mapel, "nilai": round(sum(angka) / len(angka), 2)})
    return hasil


def _gabung_duplikat(baris: list[dict]) -> list[dict]:
    """Mapel yang muncul lebih dari sekali: nilainya dirata-rata."""
    akum: dict[str, list[float]] = {}
    for b in baris:
        akum.setdefault(b["mapel"], []).append(b["nilai"])
    return [{"mapel": m, "nilai": round(sum(v) / len(v), 2)} for m, v in akum.items()]


def baca_transkrip(path: str) -> dict:
    """Return {"nilai": [...], "rata_nilai": float|None, "catatan": [...]}."""
    try:
        with pdfplumber.open(path):
            pass
    except Exception as e:
        raise pdf_rusak(str(e))

    baris = _dari_tabel(path)
    if not baris:
        try:
            import pymupdf as fitz
            doc = fitz.open(path)
            teks_halaman = [p.get_text("text") or "" for p in doc]
            doc.close()
        except Exception as e:
            raise pdf_rusak(str(e))
        baris = _dari_regex(teks_halaman)

    nilai = _gabung_duplikat(baris)
    catatan = []

    teks_lengkap = ""
    try:
        import pymupdf as fitz
        doc = fitz.open(path)
        teks_lengkap = "\n".join(p.get_text("text") or "" for p in doc)
        doc.close()
    except Exception:
        pass

    m_ipk = _POLA_IPK.search(teks_lengkap)
    rata_nilai = None
    if m_ipk:
        ipk = float(m_ipk.group(1).replace(",", "."))
        rata_nilai = round(min(100.0, ipk * 25), 2)
    elif nilai:
        rata_nilai = round(sum(n["nilai"] for n in nilai) / len(nilai), 2)

    if 0 < len(nilai) < 3:
        catatan.append(f"transkrip: hanya {len(nilai)} mata pelajaran terbaca")

    return {"nilai": nilai, "rata_nilai": rata_nilai, "catatan": catatan}
