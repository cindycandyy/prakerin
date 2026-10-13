"""Baca proposal PDF -> teks per bagian terpilih (spesifikasi teknis bagian 4).

PyMuPDF untuk teks per halaman, pdfplumber untuk tabel (baris "Objek PKL").
Tidak ada OCR: PDF hasil scan tanpa teks -> PROPOSAL_KOSONG.
"""
import re

import pymupdf as fitz
import pdfplumber

from .errors import pdf_rusak, proposal_kosong

MAKS_HALAMAN = 60
MAKS_UKURAN_MB = 20

# Halaman yang dibuang sebelum pemecahan bagian.
_POLA_SURAT_PERMOHONAN = [re.compile(r"\bperihal\b", re.I), re.compile(r"hormat kami", re.I)]
_POLA_NIM_NISN = re.compile(r"\b(nim|nisn)\b", re.I)
_POLA_PENGESAHAN = re.compile(r"\blembar pengesahan\b|\bhalaman pengesahan\b", re.I)
_POLA_DAFTAR_ISI = re.compile(r"\bdaftar isi\b", re.I)
_POLA_PROFIL_SEKOLAH = re.compile(r"\bprofil (sekolah|kampus|perusahaan)\b", re.I)
_POLA_BIODATA = re.compile(r"\bbiodata\b|\bdaftar riwayat hidup\b", re.I)

# Judul bagian: di awal baris, boleh diawali nomor bertingkat ("1.2") atau "BAB I",
# dan kata kuncinya harus berhuruf awal Kapital (judul proposal akademik ditulis
# Title Case: "Tujuan Praktik Kerja Lapangan"). Peka huruf besar pada kata kunci
# sendiri -- bukan sekadar cek baris pendek -- supaya kalimat badan teks yang
# kebetulan diawali kata sama dalam huruf kecil (mis. baris wrap "tujuan
# perusahaan.") tidak ikut dianggap heading bagian.
_AWALAN_NOMOR = r"^\s*(?:[Bb][Aa][Bb]\s+[IVXivx]+\.?\s*)?(?:\d+(?:\.\d+)*[.\)]?\s*)?"
_JUDUL_BAGIAN = {
    "latar_belakang": re.compile(_AWALAN_NOMOR + r"Latar\s+[Bb]elakang\s*$", re.M),
    # (?!...) menolak heading seperti "Visi, Misi, dan Tujuan Pendidikan" atau
    # "Tujuan Sekolah" -- itu section tujuan PENDIDIKAN/SEKOLAH (proposal kerja
    # sama institusi), bukan tujuan PKL peserta; isinya salah tangkap bila ikut match.
    "tujuan": re.compile(_AWALAN_NOMOR + r"Tujuan\b(?!\s*(?:[Ss]ekolah|[Pp]endidikan)\b|.{0,40}[Pp]endidikan).{0,60}$", re.M),
    "ruang_lingkup": re.compile(_AWALAN_NOMOR + r"Ruang\s+[Ll]ingkup\s*$", re.M),
    "bidang": re.compile(_AWALAN_NOMOR + r"Bidang\s+(?:[Pp][Kk][Ll]|[Kk]erja)\b.{0,60}$", re.M),
    "mata_kuliah": re.compile(_AWALAN_NOMOR + r"Mata\s+[Kk]uliah\b.{0,60}$", re.M),
    "objek_pkl": re.compile(_AWALAN_NOMOR + r"Objek\s+[Pp][Kk][Ll]\s*$", re.M),
    "manfaat": re.compile(_AWALAN_NOMOR + r"Manfaat\b.{0,60}$", re.M),
    "waktu": re.compile(_AWALAN_NOMOR + r"Waktu\s*$", re.M),
}

BAGIAN_DIPAKAI = {"ruang_lingkup", "bidang", "tujuan", "mata_kuliah", "objek_pkl"}

_POLA_OBJEK_PKL_BARIS = re.compile(r"objek\s*pkl\s*[:\-]?\s*(.+)", re.I)
_POLA_JUDUL_SAMPUL = re.compile(r"\bproposal\b[^\n]*\n(.+)", re.I)


def _halaman_informatif(teks: str) -> bool:
    """False bila halaman termasuk kategori yang dibuang (langkah 2)."""
    if any(p.search(teks) for p in _POLA_SURAT_PERMOHONAN) and _POLA_NIM_NISN.search(teks):
        return False
    if _POLA_PENGESAHAN.search(teks):
        return False
    if _POLA_DAFTAR_ISI.search(teks):
        return False
    if _POLA_PROFIL_SEKOLAH.search(teks):
        return False
    if _POLA_BIODATA.search(teks):
        return False
    return True


_POLA_AWAL_BAB = re.compile(r"^\s*BAB\s+[IVX]+\b", re.M)


def _pecah_bagian(teks: str) -> dict:
    """Pecah teks gabungan jadi {nama_bagian: isi}. Teks di luar bagian diabaikan.

    Awal "BAB <angka romawi>" berikutnya (mis. "BAB II") selalu memotong bagian
    sebelumnya meski bab itu sendiri bukan salah satu nama bagian yang dikenal
    -- tanpa ini, bagian terakhir pada sebuah bab (mis. "1.5 Mata Kuliah yang
    Relevan" sebelum "BAB II") akan ikut menyedot seluruh bab berikutnya karena
    tidak ada penanda bagian lain yang memotongnya.
    """
    tanda = []
    for nama, pola in _JUDUL_BAGIAN.items():
        for m in pola.finditer(teks):
            tanda.append((m.start(), nama))
    pemutus_bab = [m.start() for m in _POLA_AWAL_BAB.finditer(teks)]
    tanda.sort()
    if not tanda:
        return {}
    hasil = {}
    for i, (mulai, nama) in enumerate(tanda):
        akhir = tanda[i + 1][0] if i + 1 < len(tanda) else len(teks)
        batas_bab = next((p for p in pemutus_bab if mulai < p < akhir), None)
        if batas_bab is not None:
            akhir = batas_bab
        isi = teks[mulai:akhir]
        hasil[nama] = (hasil.get(nama, "") + "\n" + isi).strip()
    return hasil


def _ambil_objek_pkl_tabel(path: str) -> str | None:
    """Baris 'Objek PKL' di tabel surat (pdfplumber), bila ada."""
    try:
        with pdfplumber.open(path) as pdf:
            for page in pdf.pages[:5]:  # tabel surat biasanya di halaman awal
                for table in page.extract_tables() or []:
                    for row in table:
                        for cell in row:
                            if cell and re.search(r"objek\s*pkl", cell, re.I):
                                m = _POLA_OBJEK_PKL_BARIS.search(cell)
                                if m:
                                    return m.group(1).strip()
                                idx = row.index(cell)
                                if idx + 1 < len(row) and row[idx + 1]:
                                    return row[idx + 1].strip()
    except Exception:
        return None
    return None


def _cocokkan_kata_kunci(teks_list: list[str], kata_kunci_divisi: list[dict], jenis: str) -> list[str]:
    """Kata kunci (dari master, jenis tertentu) yang muncul utuh di gabungan teks, tanpa duplikat."""
    gabungan = " \n ".join(t.lower() for t in teks_list if t)
    ditemukan, dilihat = [], set()
    for kk in kata_kunci_divisi:
        if kk["jenis"] != jenis:
            continue
        kata = kk["kata_kunci"].lower()
        if kata in dilihat:
            continue
        if re.search(r"(?<!\w)" + re.escape(kata) + r"(?!\w)", gabungan):
            ditemukan.append(kata)
            dilihat.add(kata)
    return ditemukan


def _ambil_mata_kuliah(bagian: dict) -> list[str]:
    teks = bagian.get("mata_kuliah", "")
    if not teks:
        return []
    # Buang baris judul itu sendiri (baris pertama)
    baris_baris = teks.split("\n", 1)
    sisa = baris_baris[1] if len(baris_baris) > 1 else ""
    potongan = re.split(r"[;\n]|(?:^|\n)\s*\d+[.\)]\s*", sisa)
    hasil = []
    for p in potongan:
        p = re.sub(r"^\s*\d+[.\)]\s*", "", p).strip().lower()
        p = re.sub(r"\s{2,}", " ", p)
        if p and len(p) > 2 and not _JUDUL_BAGIAN["mata_kuliah"].match(p):
            hasil.append(p)
    return hasil[:20]


def _ambil_tujuan(bagian: dict) -> str | None:
    teks = bagian.get("tujuan", "")
    if not teks:
        return None
    baris_baris = teks.split("\n", 1)
    sisa = (baris_baris[1] if len(baris_baris) > 1 else "").strip()
    kalimat = re.split(r"(?<=[.!?])\s+", sisa)
    dua = " ".join(k for k in kalimat[:2] if k).strip()
    return dua[:300] if dua else None


def _ambil_judul(teks_halaman_pertama: str) -> str | None:
    m = _POLA_JUDUL_SAMPUL.search(teks_halaman_pertama)
    if not m:
        return None
    judul = m.group(1).strip().splitlines()[0].strip()
    return judul[:150] if judul else None


_PEMINATAN_INDUSTRI = ["proses produksi", "pabrik", "industri", "optimasi proses", "teknik kimia"]
_PEMINATAN_MURNI = ["analisis", "laboratorium", "sintesis", "spektro", "kromatografi"]


def tentukan_peminatan(jurusan_norm: str, teks_bagian_gabung: str) -> str:
    """Bagian 4: peminatan hanya untuk jurusan 'kimia'."""
    if jurusan_norm != "kimia":
        return ""
    t = teks_bagian_gabung.lower()
    n_industri = sum(1 for kw in _PEMINATAN_INDUSTRI if kw in t)
    n_murni = sum(1 for kw in _PEMINATAN_MURNI if kw in t)
    if n_industri >= 2 and n_industri > n_murni:
        return "kimia industri"
    if n_murni >= 2 and n_murni > n_industri:
        return "kimia murni"
    return ""


def baca_proposal(path: str, kata_kunci_master: list[dict], jurusan_norm: str) -> dict:
    """Pipeline lengkap: ekstrak -> buang halaman -> pecah bagian -> ambil field.

    kata_kunci_master: list of {"id_divisi", "kata_kunci", "jenis", "bobot"} (semua divisi;
    dipakai untuk mendeteksi kata kunci apa saja yang MUNCUL, penjumlahan per divisi
    dilakukan di skor.py).
    """
    try:
        doc = fitz.open(path)
    except Exception as e:
        raise pdf_rusak(str(e))

    import os
    if os.path.getsize(path) > MAKS_UKURAN_MB * 1024 * 1024:
        doc.close()
        raise pdf_rusak("Berkas melebihi 20 MB.")

    halaman_dipakai = []
    teks_halaman_pertama = ""
    try:
        for i, page in enumerate(doc):
            if i >= MAKS_HALAMAN:
                break
            teks = page.get_text("text") or ""
            if i == 0:
                teks_halaman_pertama = teks
            if _halaman_informatif(teks):
                halaman_dipakai.append(teks)
    finally:
        doc.close()

    gabungan = "\n".join(halaman_dipakai)
    if len(gabungan.strip()) < 200:
        raise proposal_kosong()

    bagian = _pecah_bagian(gabungan)
    bagian_terpilih = {k: v for k, v in bagian.items() if k in BAGIAN_DIPAKAI}
    teks_terpilih_list = list(bagian_terpilih.values())

    objek_pkl = _ambil_objek_pkl_tabel(path)
    if not objek_pkl:
        m = _POLA_OBJEK_PKL_BARIS.search(gabungan)
        if m:
            objek_pkl = m.group(1).strip().splitlines()[0][:200]

    topik_ditemukan = _cocokkan_kata_kunci(teks_terpilih_list, kata_kunci_master, "topik")
    bidang = ([objek_pkl] if objek_pkl else []) + topik_ditemukan

    alat = _cocokkan_kata_kunci(teks_terpilih_list, kata_kunci_master, "alat")
    mata_kuliah = _ambil_mata_kuliah(bagian)
    tujuan = _ambil_tujuan(bagian)
    judul = _ambil_judul(teks_halaman_pertama)

    teks_topik_gabung = " ".join(teks_terpilih_list)
    peminatan = tentukan_peminatan(jurusan_norm, teks_topik_gabung)

    return {
        "bidang": bidang[:6],
        "alat": alat[:8],
        "tujuan": tujuan,
        "judul": judul,
        "mata_kuliah": mata_kuliah,
        "peminatan": peminatan,
        "bagian_terpilih_teks": "\n\n".join(teks_terpilih_list),  # dipakai untuk redaksi/LLM
        "catatan": [],
    }
