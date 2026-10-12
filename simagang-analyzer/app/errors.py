"""Kode galat tetap (spesifikasi teknis bagian 4 & 9.1)."""


class AnalisisError(Exception):
    """Galat terduga: kode + pesan jadi respons HTTP 422/500/503."""

    def __init__(self, kode: str, pesan: str, http_status: int = 422):
        self.kode = kode
        self.pesan = pesan
        self.http_status = http_status
        super().__init__(pesan)


def proposal_kosong() -> AnalisisError:
    return AnalisisError("PROPOSAL_KOSONG", "PDF proposal tidak punya teks (kemungkinan hasil scan).")


def pdf_rusak(detail: str = "") -> AnalisisError:
    return AnalisisError("PDF_RUSAK", f"Berkas tidak bisa dibuka.{(' ' + detail) if detail else ''}")


def master_tidak_valid(pesan: str) -> AnalisisError:
    return AnalisisError("MASTER_TIDAK_VALID", pesan)
