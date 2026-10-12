-- Seeder master data modul Analisis Penempatan — Tahap 1
-- Idempoten: aman dijalankan ulang (INSERT IGNORE / ON DUPLICATE KEY).
-- Sumber: "Analisis & Desain Sistem – Aplikasi HR Manajemen Anak Magang.pdf",
-- bagian "Struktur departemen dan posisi magang". Dua bagian masih usulan awal
-- yang perlu dikonfirmasi client (lihat catatan 12.3 #25 di spesifikasi teknis):
-- Rantai Pasok (6 divisi) dan SDM & Tata Kelola (6 divisi).

INSERT INTO prk_departemen (kode, nama, urutan) VALUES
  ('RND', 'Riset & Pengembangan', 1),
  ('PRD', 'Produksi', 2),
  ('KOM', 'Komersil', 3),
  ('SCM', 'Rantai Pasok', 4),
  ('SDM', 'SDM & Tata Kelola', 5),
  ('ADK', 'Administrasi & Keuangan', 6)
ON DUPLICATE KEY UPDATE nama = VALUES(nama);

-- Riset & Pengembangan
INSERT INTO prk_divisi (id_departemen, nama, urutan)
SELECT id, v.nama, v.urutan FROM prk_departemen d
JOIN (
  SELECT 'Development produk baru' AS nama, 1 AS urutan
  UNION ALL SELECT 'Perizinan merk dan izin edar', 2
  UNION ALL SELECT 'Desain kemasan dan label', 3
  UNION ALL SELECT 'Analisa produk 1/2 jadi', 4
  UNION ALL SELECT 'QC produk jadi', 5
  UNION ALL SELECT 'QC bahan material dan aksesoris masuk gudang', 6
  UNION ALL SELECT 'Patrol produk jadi', 7
) v ON 1=1
WHERE d.kode = 'RND'
AND NOT EXISTS (SELECT 1 FROM prk_divisi sd WHERE sd.id_departemen = d.id AND sd.nama = v.nama);

-- Produksi
INSERT INTO prk_divisi (id_departemen, nama, urutan)
SELECT id, v.nama, v.urutan FROM prk_departemen d
JOIN (
  SELECT 'Rendal dan perencanaan' AS nama, 1 AS urutan
  UNION ALL SELECT 'K3LH', 2
  UNION ALL SELECT 'Pemeliharaan', 3
  UNION ALL SELECT 'Produksi', 4
) v ON 1=1
WHERE d.kode = 'PRD'
AND NOT EXISTS (SELECT 1 FROM prk_divisi sd WHERE sd.id_departemen = d.id AND sd.nama = v.nama);

-- Komersil
INSERT INTO prk_divisi (id_departemen, nama, urutan)
SELECT id, v.nama, v.urutan FROM prk_departemen d
JOIN (
  SELECT 'Penjualan produk' AS nama, 1 AS urutan
  UNION ALL SELECT 'Penjualan produk pupuk dan chemical', 2
  UNION ALL SELECT 'Digital marketing', 3
  UNION ALL SELECT 'Pengembangan dan promosi', 4
  UNION ALL SELECT 'Penagihan', 5
) v ON 1=1
WHERE d.kode = 'KOM'
AND NOT EXISTS (SELECT 1 FROM prk_divisi sd WHERE sd.id_departemen = d.id AND sd.nama = v.nama);

-- Rantai Pasok (usulan awal — konfirmasi client, lihat pertanyaan 12.3 #25)
INSERT INTO prk_divisi (id_departemen, nama, urutan)
SELECT id, v.nama, v.urutan FROM prk_departemen d
JOIN (
  SELECT 'Pengadaan barang dalam dan luar negeri' AS nama, 1 AS urutan
  UNION ALL SELECT 'Pengadaan jasa', 2
  UNION ALL SELECT 'Penyimpanan bahan baku', 3
  UNION ALL SELECT 'Penyimpanan produk jadi', 4
  UNION ALL SELECT 'Distribusi produk ke customer', 5
  UNION ALL SELECT 'Perencanaan dan pengendalian rantai pasok', 6
) v ON 1=1
WHERE d.kode = 'SCM'
AND NOT EXISTS (SELECT 1 FROM prk_divisi sd WHERE sd.id_departemen = d.id AND sd.nama = v.nama);

-- SDM & Tata Kelola (usulan awal — konfirmasi client, lihat pertanyaan 12.3 #25)
INSERT INTO prk_divisi (id_departemen, nama, urutan)
SELECT id, v.nama, v.urutan FROM prk_departemen d
JOIN (
  SELECT 'SDM dan pengembangan kompetensi' AS nama, 1 AS urutan
  UNION ALL SELECT 'Hubungan masyarakat', 2
  UNION ALL SELECT 'Tata kelola perusahaan dan kesekretariatan', 3
  UNION ALL SELECT 'Hukum dan perizinan perusahaan', 4
  UNION ALL SELECT 'Pelayanan umum', 5
  UNION ALL SELECT 'Pengamanan aset', 6
) v ON 1=1
WHERE d.kode = 'SDM'
AND NOT EXISTS (SELECT 1 FROM prk_divisi sd WHERE sd.id_departemen = d.id AND sd.nama = v.nama);

-- Administrasi & Keuangan
INSERT INTO prk_divisi (id_departemen, nama, urutan)
SELECT id, v.nama, v.urutan FROM prk_departemen d
JOIN (
  SELECT 'Pengelolaan akuntansi dan anggaran' AS nama, 1 AS urutan
  UNION ALL SELECT 'Pengelolaan keuangan perusahaan', 2
) v ON 1=1
WHERE d.kode = 'ADK'
AND NOT EXISTS (SELECT 1 FROM prk_divisi sd WHERE sd.id_departemen = d.id AND sd.nama = v.nama);

-- Kuota awal: angka acak/sementara (sesuai jawaban client), HR mengisi ulang lewat Halaman D.
INSERT INTO prk_kuota (id_divisi, kuota_maks, diubah_oleh, diubah_pada)
SELECT id, 3, NULL, NOW() FROM prk_divisi
WHERE id NOT IN (SELECT id_divisi FROM prk_kuota);

-- Pengaturan awal (nilai default dari tab Spesifikasi teknis bagian 11.1)
INSERT INTO prk_pengaturan (kunci, nilai, diubah_oleh, diubah_pada) VALUES
  ('bobot_f1', '45', NULL, NOW()),
  ('bobot_f2', '25', NULL, NOW()),
  ('bobot_f3', '20', NULL, NOW()),
  ('bobot_f4', '10', NULL, NOW()),
  ('ambang_conf_tinggi', '75', NULL, NOW()),
  ('ambang_conf_sedang', '55', NULL, NOW()),
  ('ambang_mepet_hari', '3', NULL, NOW()),
  ('h7_hari', '7', NULL, NOW()),
  ('h3_hari', '3', NULL, NOW()),
  ('retensi_notifikasi_bulan', '12', NULL, NOW()),
  ('surat_pola_nomor', 'B2/NK.03.02/SU/DR', NULL, NOW()),
  ('surat_penandatangan_nama', 'Ahmad Agung Suhastyo', NULL, NOW()),
  ('surat_penandatangan_jabatan', 'SVP Sumber Daya Manusia & Tata Kelola', NULL, NOW()),
  ('surat_pembekalan_pukul', '08.00 WIB - Selesai', NULL, NOW())
ON DUPLICATE KEY UPDATE nilai = nilai;
