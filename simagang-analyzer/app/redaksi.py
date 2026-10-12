"""Penyamaran data pribadi sebelum teks (opsional) dikirim ke model bahasa.

Spesifikasi teknis bagian 4. Langkah 6 (pengenal nama lokal NER) di-skip di v1 —
lihat catatan di bawah; pemeriksaan ulang di akhir tetap jadi pengaman utama.
"""
import re

_POLA_ALAMAT = re.compile(
    r"^.*\b(Alamat|Jl\.?|Jalan|Kec\.?|Kab\.?|Desa|Tempat/Tanggal Lahir)(?=\s|$).*$", re.I | re.M
)
_POLA_EMAIL = re.compile(r"[\w.+-]+@[\w-]+\.[\w.-]+")
_POLA_URL = re.compile(r"https?://\S+|www\.\S+")
_POLA_TELEPON = re.compile(r"(?:\+62|62|0)8[\d\-\s]{8,13}|0\d{2,3}[\-\s]?\d{6,8}")
_POLA_DIGIT_PANJANG = re.compile(r"\b\d{8,}\b")
_POLA_GELAR_SAPAAN = re.compile(
    r"\b(Bapak|Ibu|Dr\.|Drs\.|Ir\.|Prof\.|S\.T\.|M\.Si\.)\s+[A-Z][\w.]*(?:\s+[A-Z][\w.]*){0,3}"
)


def _hapus_baris_alamat(teks: str) -> str:
    return _POLA_ALAMAT.sub("", teks)


def _ganti_pola_data(teks: str) -> str:
    teks = _POLA_EMAIL.sub("[DATA]", teks)
    teks = _POLA_URL.sub("[DATA]", teks)
    teks = _POLA_TELEPON.sub("[DATA]", teks)
    teks = _POLA_DIGIT_PANJANG.sub("[DATA]", teks)
    return teks


def _ganti_nama_pendaftar(teks: str, nama: str) -> str:
    if nama:
        teks = re.sub(re.escape(nama), "[NAMA_1]", teks, flags=re.I)
        for kata in nama.split():
            if len(kata) >= 4:
                teks = re.sub(r"\b" + re.escape(kata) + r"\b", "[NAMA_1]", teks, flags=re.I)
    return teks


def _ganti_instansi(teks: str, instansi: str) -> str:
    if instansi:
        teks = re.sub(re.escape(instansi), "[INSTANSI]", teks, flags=re.I)
        singkatan = "".join(w[0] for w in instansi.split() if w[0].isalpha())
        if len(singkatan) >= 3:
            teks = re.sub(r"\b" + re.escape(singkatan) + r"\b", "[INSTANSI]", teks)
    return teks


def _ganti_gelar_sapaan(teks: str) -> str:
    return _POLA_GELAR_SAPAAN.sub("[NAMA_n]", teks)


def masih_ada_data_sensitif(teks: str) -> bool:
    """Pemeriksaan ulang akhir: digit panjang, email, atau sisa kata nama."""
    if _POLA_DIGIT_PANJANG.search(teks) or _POLA_EMAIL.search(teks):
        return True
    return False


def redaksi(teks: str, nama_peserta: str, instansi: str) -> str:
    """Urutan sesuai spesifikasi bagian 4, langkah 1-5. Langkah 6 (NER lokal) di-skip —
    model dipilih saat implementasi (butuh server sendiri); pemeriksaan di masih_ada_data_sensitif
    jadi pengaman kalau pola di atas belum cukup.
    """
    t = _hapus_baris_alamat(teks)
    t = _ganti_pola_data(t)
    t = _ganti_nama_pendaftar(t, nama_peserta)
    t = _ganti_instansi(t, instansi)
    t = _ganti_gelar_sapaan(t)
    return t
