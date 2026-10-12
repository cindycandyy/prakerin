-- Menghubungkan depag (lama) ke prk_departemen (baru), yang belum diisi di
-- 002_analisis_seed_master.sql. Pemetaan id disamakan urutan dengan seed
-- depag di 000_legacy_minimal_local.sql (keduanya mengikuti 6 departemen yang
-- sama). Jalankan setelah 000 dan 002.
INSERT INTO prk_depag_map (id_depag, id_departemen)
SELECT d.id, pd.id
FROM depag d
JOIN prk_departemen pd ON pd.nama = d.nama_depag
WHERE NOT EXISTS (SELECT 1 FROM prk_depag_map m WHERE m.id_depag = d.id);
