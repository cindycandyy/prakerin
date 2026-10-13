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
    r"^(.+?)\s+(\d{2,3}(?:[.,]\d+)?|A|AB|A-|B\+|B|BC|B-|C\+|C|CD|D|E)"
    r"(?:\s+(\d{2,3}(?:[.,]\d+)?|A|AB|A-|B\+|B|BC|B-|C\+|C|CD|D|E))?\s*$"
)
_POLA_IPK = re.compile(r"(?:IPK|Indeks Prestasi Kumulatif)\s*[:=]?\s*(\d[.,]\d+)", re.I)
# Dua skema huruf yang umum dipakai kampus Indonesia: A/A-/B+/... (skala umum) dan
# A/AB/B/BC/... (skala ITS dkk, "AB" = Baik Sekali di antara A dan B). Keduanya
# dipetakan ke 0-100 yang sama supaya bisa dibandingkan lintas skema.
_NILAI_HURUF = {
    "A": 90, "A-": 85, "AB": 85, "B+": 80, "B": 75, "BC": 70, "B-": 70,
    "C+": 65, "C": 60, "CD": 55, "D": 50, "E": 0,
}
_POLA_NILAI_HURUF = re.compile(r"^(A|AB|A-|B\+|B|BC|B-|C\+|C|CD|D|E)$")


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


def _nilai_dari_sel(teks: str) -> float | None:
    """Angka 0-100 langsung, atau huruf (A/AB/B+/dst, lihat _NILAI_HURUF)."""
    angka = _angka(teks)
    if angka is not None:
        return angka
    huruf = teks.strip().upper()
    if _POLA_NILAI_HURUF.match(huruf):
        return float(_NILAI_HURUF[huruf])
    return None


_POLA_HEADER_MAPEL = re.compile(r"mata\s*kuliah|mata\s*pelajaran|^subject$|nama\s*mk", re.I)
_POLA_HEADER_NILAI = re.compile(r"^nilai", re.I)
_POLA_HEADER_GRADE = re.compile(r"^grade$", re.I)


def _kolom_dari_header(header: list) -> tuple[int, int] | None:
    """(index_kolom_mapel, index_kolom_nilai) dari baris header tabel, atau None
    kalau tabelnya tidak punya header yang dikenali (dipakai _dari_tabel sebagai
    jalur utama; heuristik lama jadi fallback kalau header tak ketemu)."""
    idx_mapel = idx_nilai = None
    for i, cell in enumerate(header):
        if not cell:
            continue
        c = cell.strip()
        if _POLA_HEADER_MAPEL.search(c):
            idx_mapel = i
        elif _POLA_HEADER_NILAI.search(c) or _POLA_HEADER_GRADE.match(c):
            idx_nilai = i
    if idx_mapel is None or idx_nilai is None:
        return None
    return idx_mapel, idx_nilai


def _dari_tabel(path: str) -> list[dict]:
    hasil = []
    with pdfplumber.open(path) as pdf:
        for page in pdf.pages:
            for table in page.extract_tables() or []:
                if not table:
                    continue
                kolom = _kolom_dari_header(table[0])
                for row in table[1:] if kolom else table:
                    if kolom:
                        idx_mapel, idx_nilai = kolom
                        if idx_mapel >= len(row) or idx_nilai >= len(row):
                            continue
                        sel_mapel, sel_nilai = row[idx_mapel], row[idx_nilai]
                        if not sel_mapel or not sel_nilai:
                            continue
                        mapel = _normalisasi_mapel(sel_mapel)
                        nilai = _nilai_dari_sel(sel_nilai)
                        if not mapel or nilai is None or _BUANG_MAPEL.search(mapel):
                            continue
                        hasil.append({"mapel": mapel, "nilai": round(nilai, 2)})
                        continue

                    # Fallback tanpa header dikenali: heuristik lama (kolom teks
                    # pertama + rata-rata kolom angka berbasis angka 0-100 saja).
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
            nilai_list = [v for g in (m.group(2), m.group(3)) if g for v in [_nilai_dari_sel(g)] if v is not None]
            if not nilai_list:
                continue
            hasil.append({"mapel": mapel, "nilai": round(sum(nilai_list) / len(nilai_list), 2)})
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
