"""Normalisasi jurusan & jenis institusi (spesifikasi teknis bagian 4)."""
import re

_SINONIM = {
    "teknik kimia industri": "teknik kimia industri",
    "tki": "teknik kimia industri",
}


def normalisasi_jurusan(jurusan_mentah: str) -> str:
    t = jurusan_mentah.lower()
    t = re.sub(r"\b(program studi|prodi)\b", "", t)
    t = re.sub(r"^\s*(s1|s2|s3|d3|d4|smk|sma)\s*/\s*", "", t)
    t = re.sub(r"\s{2,}", " ", t).strip()
    return _SINONIM.get(t, t)


def jenis_institusi(pendidikan: str) -> str:
    p = pendidikan.strip().upper()
    if p.startswith("S1") or p.startswith("D3") or p.startswith("D4") or p.startswith("S2"):
        return "universitas"
    if p.startswith("SMK") or p.startswith("SMA"):
        return "smk"
    return "lainnya"
