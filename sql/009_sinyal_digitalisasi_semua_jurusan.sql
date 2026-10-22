-- Divisi yang menerima "semua jurusan" (prk_pemetaan_jurusan) sebelumnya tidak punya
-- satu pun sinyal topik yang menyinggung IT/sistem/digitalisasi -- jadi proposal
-- pengembangan sistem/web yang mendukung kerja divisi itu (relevan secara nyata,
-- apapun jurusan pelamarnya) tidak pernah terdeteksi cocok oleh F3 (topik proposal).
-- Bobot 2 (sekunder, sama seperti kata kunci pendukung lain) supaya tidak
-- mengalahkan kecocokan domain inti tiap divisi, hanya menambah sinyal yang
-- sebelumnya hilang total.
INSERT INTO prk_sinyal_divisi (id_divisi, kata_kunci, jenis, bobot) VALUES
    -- Komersil
    (18, 'sistem informasi', 'topik', 2), (18, 'digitalisasi', 'topik', 2), (18, 'aplikasi', 'topik', 2), (18, 'otomasi proses', 'topik', 2),
    (15, 'sistem informasi', 'topik', 2), (15, 'digitalisasi', 'topik', 2), (15, 'aplikasi', 'topik', 2), (15, 'otomasi proses', 'topik', 2),
    (16, 'sistem informasi', 'topik', 2), (16, 'digitalisasi', 'topik', 2), (16, 'aplikasi', 'topik', 2), (16, 'otomasi proses', 'topik', 2),
    -- Produksi
    (9, 'sistem informasi', 'topik', 2), (9, 'digitalisasi', 'topik', 2), (9, 'aplikasi', 'topik', 2), (9, 'otomasi proses', 'topik', 2),
    (10, 'sistem informasi', 'topik', 2), (10, 'digitalisasi', 'topik', 2), (10, 'aplikasi', 'topik', 2), (10, 'otomasi proses', 'topik', 2),
    (11, 'sistem informasi', 'topik', 2), (11, 'digitalisasi', 'topik', 2), (11, 'aplikasi', 'topik', 2), (11, 'otomasi proses', 'topik', 2),
    (8, 'sistem informasi', 'topik', 2), (8, 'digitalisasi', 'topik', 2), (8, 'aplikasi', 'topik', 2), (8, 'otomasi proses', 'topik', 2),
    -- SDM & Tata Kelola
    (30, 'sistem informasi', 'topik', 2), (30, 'digitalisasi', 'topik', 2), (30, 'aplikasi', 'topik', 2), (30, 'otomasi proses', 'topik', 2),
    (32, 'sistem informasi', 'topik', 2), (32, 'digitalisasi', 'topik', 2), (32, 'aplikasi', 'topik', 2), (32, 'otomasi proses', 'topik', 2),
    (33, 'sistem informasi', 'topik', 2), (33, 'digitalisasi', 'topik', 2), (33, 'aplikasi', 'topik', 2), (33, 'otomasi proses', 'topik', 2),
    (34, 'sistem informasi', 'topik', 2), (34, 'digitalisasi', 'topik', 2), (34, 'aplikasi', 'topik', 2), (34, 'otomasi proses', 'topik', 2),
    (29, 'sistem informasi', 'topik', 2), (29, 'digitalisasi', 'topik', 2), (29, 'aplikasi', 'topik', 2), (29, 'otomasi proses', 'topik', 2),
    (31, 'sistem informasi', 'topik', 2), (31, 'digitalisasi', 'topik', 2), (31, 'aplikasi', 'topik', 2), (31, 'otomasi proses', 'topik', 2);
