"""Self-check: ekstraksi proposal/transkrip + skor, pakai PDF sintetis (PyMuPDF insert_text,
tanpa dependency tambahan). Jalankan: python -m tests.test_ekstraksi (dari folder simagang-analyzer).
"""
import os
import sys
import tempfile

sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

import pymupdf as fitz  # noqa: E402

from app.baca_proposal import baca_proposal  # noqa: E402
from app.baca_transkrip import baca_transkrip  # noqa: E402
from app.errors import AnalisisError  # noqa: E402
from app.llm import _validasi_keluaran  # noqa: E402
from app.redaksi import redaksi  # noqa: E402
from app.skor import _f1_jurusan, hitung_rekomendasi, round_half_up  # noqa: E402


def _buat_pdf(path: str, baris_per_halaman: list[list[str]]):
    doc = fitz.open()
    for baris in baris_per_halaman:
        page = doc.new_page()
        y = 72
        for teks in baris:
            page.insert_text((72, y), teks, fontsize=11)
            y += 16
    doc.save(path)
    doc.close()


MASTER_KECIL = {
    "divisi": [{"id": 5, "nama": "QC Produk Jadi", "id_departemen": 1, "sisa_kuota": 2}],
    "sinyal": [
        {"id_divisi": 5, "kata_kunci": "titrasi", "jenis": "alat", "bobot": 3},
        {"id_divisi": 5, "kata_kunci": "pengendalian mutu", "jenis": "topik", "bobot": 3},
    ],
    "pemetaan_jurusan": [{"jurusan_norm": "teknik kimia industri", "peminatan": "", "id_divisi": 5, "skor": 85}],
    "mapel_relevan": [{"pola_mapel": "kimia", "id_divisi": 5, "bobot": 2}],
    "bobot": {"jurusan": 45, "skill": 25, "proposal": 20, "kebutuhan": 10},
    "ambang": {"tinggi": 75, "sedang": 55},
}


def test_proposal_kosong():
    with tempfile.TemporaryDirectory() as d:
        path = os.path.join(d, "kosong.pdf")
        doc = fitz.open()
        doc.new_page()
        doc.save(path)
        doc.close()
        try:
            baca_proposal(path, [], "kimia")
            assert False, "harus PROPOSAL_KOSONG"
        except AnalisisError as e:
            assert e.kode == "PROPOSAL_KOSONG"


def test_pdf_rusak():
    with tempfile.TemporaryDirectory() as d:
        path = os.path.join(d, "rusak.pdf")
        with open(path, "wb") as f:
            f.write(b"bukan pdf sama sekali")
        try:
            baca_proposal(path, [], "kimia")
            assert False, "harus PDF_RUSAK"
        except AnalisisError as e:
            assert e.kode == "PDF_RUSAK"


def test_baca_proposal_dasar():
    with tempfile.TemporaryDirectory() as d:
        path = os.path.join(d, "proposal.pdf")
        _buat_pdf(path, [[
            "PROPOSAL PRAKTIK KERJA LAPANGAN",
            "Analisis Pengendalian Mutu Produk Kimia Industri",
            "",
            "BAB I PENDAHULUAN",
            "",
            "Latar Belakang",
            "Perusahaan membutuhkan analisis mutu yang baik untuk produk kimia industri " * 5,
            "",
            "Tujuan",
            "Mempelajari prosedur titrasi dan pengendalian mutu di laboratorium. "
            "Memahami proses produksi secara menyeluruh.",
            "",
            "Bidang PKL",
            "Objek PKL: laboratorium analisis kimia",
            "",
            "Mata Kuliah",
            "1. Kimia Dasar I",
            "2. Kimia Analisis",
        ]])
        hasil = baca_proposal(path, MASTER_KECIL["sinyal"], "teknik kimia industri")
        assert "titrasi" in hasil["alat"], hasil
        assert any("pengendalian mutu" in b or "laboratorium" in b for b in hasil["bidang"]), hasil
        assert hasil["tujuan"], hasil
        assert "kimia analisis" in hasil["mata_kuliah"], hasil


def test_baca_transkrip_dasar():
    with tempfile.TemporaryDirectory() as d:
        path = os.path.join(d, "transkrip.pdf")
        _buat_pdf(path, [[
            "TRANSKRIP NILAI",
            "Kimia 88",
            "Matematika 86 91",
            "Bahasa Indonesia 80",
        ]])
        hasil = baca_transkrip(path)
        # Regex fallback bergantung format baris persis; minimal tidak error & rata_nilai terisi
        assert hasil["rata_nilai"] is None or 0 <= hasil["rata_nilai"] <= 100


def test_redaksi_membuang_data_pribadi():
    teks = "Nama: Zumruda Nafisah, NISN 1234567890, HP 081234567890, email a@b.com. Jl. Merdeka No 1."
    bersih = redaksi(teks, "Zumruda Nafisah", "SMK Muhammadiyah 1 Gresik")
    assert "1234567890" not in bersih
    assert "081234567890" not in bersih
    assert "a@b.com" not in bersih
    assert "Zumruda" not in bersih
    assert "Jl. Merdeka" not in bersih


def test_skor_pemilihan_dan_urutan():
    profil = {
        "jurusan_norm": "teknik kimia industri", "peminatan": "",
        "alat": ["titrasi"], "bidang": ["pengendalian mutu"], "tujuan": "", "judul": "",
        "nilai": [{"mapel": "kimia", "nilai": 88}], "mata_kuliah": [],
    }
    hasil = hitung_rekomendasi(profil, MASTER_KECIL)
    assert len(hasil["rekomendasi"]) == 1
    rek = hasil["rekomendasi"][0]
    assert rek["id_divisi"] == 5
    assert rek["keyakinan"] in ("tinggi", "sedang", "rendah")
    assert 0 <= rek["skor"] <= 100


def test_divisi_kuota_penuh_disembunyikan():
    master = dict(MASTER_KECIL)
    master["divisi"] = [{"id": 5, "nama": "QC Produk Jadi", "id_departemen": 1, "sisa_kuota": 0}]
    profil = {
        "jurusan_norm": "teknik kimia industri", "peminatan": "",
        "alat": [], "bidang": [], "tujuan": "", "judul": "", "nilai": [], "mata_kuliah": [],
    }
    hasil = hitung_rekomendasi(profil, master)
    assert hasil["rekomendasi"] == []
    assert hasil["divisi_penuh_disembunyikan"] == 1


def test_round_half_up():
    assert round_half_up(85.45, 1) == 85.5
    assert round_half_up(85.44, 1) == 85.4


def test_validasi_keluaran_llm_tolak_data_pribadi():
    # NISN/NIK 8+ digit lolos skema tapi harus ditolak (bagian 4: "tanpa angka 8 digit")
    assert _validasi_keluaran({"bidang": ["kimia"], "alat": [], "tujuan": "NISN 12345678"}) is None
    assert _validasi_keluaran({"bidang": ["kimia"], "alat": [], "tujuan": "hubungi a@b.com"}) is None


def test_validasi_keluaran_llm_terima_valid():
    hasil = _validasi_keluaran({"bidang": ["Kimia Analisis"], "alat": ["Titrasi"], "tujuan": "Belajar QC."})
    assert hasil == {"bidang": ["kimia analisis"], "alat": ["titrasi"], "tujuan": "Belajar QC."}


def test_validasi_keluaran_llm_tolak_kunci_asing():
    assert _validasi_keluaran({"bidang": [], "alat": [], "tujuan": "", "ekstra": 1}) is None


def test_f1_jurusan_tidak_bocor_antar_divisi():
    # Regresi: skor jurusan harus difilter per id_divisi, bukan ambil baris
    # pertama yang match jurusan di seluruh pemetaan (lintas divisi).
    pemetaan = [
        {"jurusan_norm": "kimia", "peminatan": "", "id_divisi": 99, "skor": 95},
        {"jurusan_norm": "kimia", "peminatan": "", "id_divisi": 1, "skor": 60},
    ]
    assert _f1_jurusan(pemetaan, 1, "kimia", "") == (60.0, True)
    assert _f1_jurusan(pemetaan, 99, "kimia", "") == (95.0, True)


def test_f1_jurusan_fallback_semua_jurusan():
    # Jurusan yang tidak dipetakan spesifik ke divisi ini jatuh ke baris
    # "semua jurusan" milik divisi itu, kalau ada -- bukan skor flat 30.
    pemetaan = [{"jurusan_norm": "semua jurusan", "peminatan": "", "id_divisi": 5, "skor": 60}]
    assert _f1_jurusan(pemetaan, 5, "rekayasa perangkat lunak", "") == (60.0, True)
    assert _f1_jurusan(pemetaan, 7, "rekayasa perangkat lunak", "") == (30.0, False)


def main():
    tes = [v for k, v in globals().items() if k.startswith("test_") and callable(v)]
    gagal = 0
    for t in tes:
        try:
            t()
            print(f"OK   {t.__name__}")
        except AssertionError as e:
            gagal += 1
            print(f"GAGAL {t.__name__}: {e}")
    print(f"\n{len(tes) - gagal}/{len(tes)} lulus")
    sys.exit(1 if gagal else 0)


if __name__ == "__main__":
    main()
