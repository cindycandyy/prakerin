-- Sisi pelamar (SPESIFIKASI_PELAMAR.md bagian B6): prk_notifikasi diberi penerima
-- (sekarang global, cuma dilihat HR); pkl_data diberi alasan ringkas untuk pelamar
-- (D2) dan status 'Cancelled' untuk pembatalan oleh pelamar sendiri (D3, jejak audit
-- tetap ada lewat prk_audit, bukan hapus baris).
ALTER TABLE prk_notifikasi
    ADD COLUMN untuk ENUM('hr', 'pelamar') NOT NULL DEFAULT 'hr' AFTER jenis,
    ADD COLUMN id_pelamar VARCHAR(50) NULL AFTER untuk,
    MODIFY jenis ENUM('h7', 'h3', 'hari_h', 'analisis_selesai', 'perlu_tinjau', 'mepet', 'lewat',
        'status_otomatis', 'gagal', 'diterima', 'ditolak', 'surat_terkirim', 'diterima_jadwal',
        'pengajuan_terkirim') NOT NULL;

ALTER TABLE pkl_data ADD COLUMN alasan_pelamar VARCHAR(300) NULL;
