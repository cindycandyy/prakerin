-- Kolom opsional supaya HR bisa menimpa teks "Kepada" bawaan (bagian 8.1) saat
-- auto-generate dari instansi peserta pertama tidak sesuai (misal sebutan jabatan
-- berbeda). Kosong/NULL -> dipakai teks bawaan seperti sebelumnya.
ALTER TABLE prk_surat_balasan ADD COLUMN kepada_override TEXT NULL AFTER rujukan_tanggal;
