-- Bootstrap lokal SAJA (bukan untuk production): tabel legacy minimal supaya
-- app bisa login dan migrasi 001/002 (FK ke pkl_data) bisa jalan di lingkungan
-- dev tanpa dump database produksi. Kolom pkl_data diturunkan dari array $data
-- yang benar-benar dipakai di application/controllers/Transaksi.php (insert
-- pendaftaran baru, sekitar baris 119-149) dan kolom doc_4/no_pkl (baris
-- 282-511) -- BUKAN skema lengkap, hanya kolom yang kodenya benar-benar baca/tulis.
CREATE TABLE IF NOT EXISTS user (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id VARCHAR(50) DEFAULT NULL,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  password VARCHAR(255) NOT NULL,
  type INT NOT NULL,
  foto VARCHAR(255) DEFAULT NULL,
  last_login DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS pkl_data (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id VARCHAR(50) DEFAULT NULL,
  idkey VARCHAR(255) DEFAULT NULL,
  no_pkl VARCHAR(50) DEFAULT NULL,
  kegiatan VARCHAR(255) DEFAULT NULL,
  nama VARCHAR(150) DEFAULT NULL,
  alamat VARCHAR(255) DEFAULT NULL,
  jk VARCHAR(20) DEFAULT NULL,
  tgl_lahir DATE DEFAULT NULL,
  tmpt_lahir VARCHAR(100) DEFAULT NULL,
  nm_ortu VARCHAR(150) DEFAULT NULL,
  hp_ortu VARCHAR(30) DEFAULT NULL,
  telp VARCHAR(30) DEFAULT NULL,
  email VARCHAR(150) DEFAULT NULL,
  pendidikan VARCHAR(50) DEFAULT NULL,
  jurusan VARCHAR(150) DEFAULT NULL,
  institusi VARCHAR(150) DEFAULT NULL,
  almt_institusi VARCHAR(255) DEFAULT NULL,
  id_depag INT DEFAULT NULL,
  pembimbing VARCHAR(150) DEFAULT NULL,
  hp_pembimbing VARCHAR(30) DEFAULT NULL,
  tgl_mulai DATE DEFAULT NULL,
  tgl_selesai DATE DEFAULT NULL,
  status VARCHAR(30) DEFAULT 'Pending',
  doc_1 VARCHAR(255) DEFAULT NULL,
  doc_2 VARCHAR(255) DEFAULT NULL,
  doc_3 VARCHAR(255) DEFAULT NULL,
  doc_4 VARCHAR(255) DEFAULT NULL,
  doc_5 VARCHAR(255) DEFAULT NULL,
  doc_6 VARCHAR(255) DEFAULT NULL,
  doc_7 VARCHAR(255) DEFAULT NULL,
  foto VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS depag (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama_depag VARCHAR(150) DEFAULT NULL,
  deskripsi VARCHAR(255) DEFAULT NULL,
  kuota INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- HR login contoh: email hr@eprakerin.local / password hr123 (plaintext,
-- mengikuti cara lama Model_login.php membandingkan password apa adanya).
-- SES_PRIV = 2 artinya HR (lihat Analisis.php).
INSERT INTO user (user_id, name, email, password, type)
SELECT 'HR01', 'Dewi Anggraini', 'hr@eprakerin.local', 'hr123', 2
WHERE NOT EXISTS (SELECT 1 FROM user WHERE email = 'hr@eprakerin.local');

-- Admin contoh: email admin@eprakerin.local / password admin123. SES_PRIV = 1
-- -- satu-satunya peran yang boleh menyimpan di /analisis/pengaturan.
INSERT INTO user (user_id, name, email, password, type)
SELECT 'ADM01', 'Admin Sistem', 'admin@eprakerin.local', 'admin123', 1
WHERE NOT EXISTS (SELECT 1 FROM user WHERE email = 'admin@eprakerin.local');

-- Departemen legacy (dipakai id_depag di pkl_data) -- namanya dibuat sejajar
-- dengan 6 prk_departemen dari 002_analisis_seed_master.sql supaya
-- prk_depag_map (ditambahkan di bawah via 003) bisa menghubungkan keduanya.
INSERT INTO depag (id, nama_depag, deskripsi, kuota) VALUES
  (1, 'Riset & Pengembangan', 'Departemen Riset & Pengembangan', 12),
  (2, 'Produksi', 'Departemen Produksi', 12),
  (3, 'Komersil', 'Departemen Komersil', 8),
  (4, 'Rantai Pasok', 'Departemen Rantai Pasok', 12),
  (5, 'SDM & Tata Kelola', 'Departemen SDM & Tata Kelola', 8),
  (6, 'Administrasi & Keuangan', 'Departemen Administrasi & Keuangan', 8)
ON DUPLICATE KEY UPDATE nama_depag = VALUES(nama_depag);

-- Contoh pendaftar (dulunya data ilustrasi hardcode di dashboard.php/pelamar.php,
-- Tahap 2: sekarang data asli di pkl_data). Status dipakai apa adanya seperti
-- kode lama: 'Pending', 'Accept', 'Reject'. Lihat application/controllers/Transaksi.php
-- untuk konvensi kapitalisasi status yang memang tidak konsisten di app lama.
INSERT INTO pkl_data
  (user_id, idkey, no_pkl, nama, institusi, jurusan, pendidikan, email, telp,
   id_depag, tgl_mulai, tgl_selesai, status, doc_1, doc_2, doc_3, doc_4, doc_5, doc_6, doc_7, foto)
SELECT * FROM (SELECT
  'USR01' AS user_id, 'USR01AyuPutriLestari0812' AS idkey, 'SIM-2026-018' AS no_pkl,
  'Ayu Putri Lestari' AS nama, 'Universitas Airlangga' AS institusi, 'Kimia analitik' AS jurusan,
  'S1' AS pendidikan, 'ayu.lestari@example.com' AS email, '081234567890' AS telp,
  1 AS id_depag, '2026-11-02' AS tgl_mulai, '2027-01-29' AS tgl_selesai, 'Pending' AS status,
  'surat_pengantar_ayu.pdf' AS doc_1, 'proposal_ayu.pdf' AS doc_2, 'transkrip_ayu.pdf' AS doc_3,
  'kartu_pelajar_ayu.pdf' AS doc_4, 'asuransi_ayu.pdf' AS doc_5, 'pernyataan_ayu.pdf' AS doc_6,
  '' AS doc_7, 'foto_ayu.jpg' AS foto
) t WHERE NOT EXISTS (SELECT 1 FROM pkl_data WHERE idkey = 'USR01AyuPutriLestari0812');

INSERT INTO pkl_data
  (user_id, idkey, no_pkl, nama, institusi, jurusan, pendidikan, email, telp,
   id_depag, tgl_mulai, tgl_selesai, status, doc_1, doc_2, doc_3, doc_4, doc_5, doc_6, doc_7, foto)
SELECT * FROM (SELECT
  'USR02', 'USR02CitraMaharani0813', 'SIM-2026-019',
  'Citra Maharani', 'Universitas Negeri Surabaya', 'Ilmu Komunikasi',
  'S1', 'citra.maharani@example.com', '081322334455',
  3, '2026-10-19', '2026-12-18', 'Pending',
  'surat_pengantar_citra.pdf', 'proposal_citra.pdf', 'transkrip_citra.pdf',
  'kartu_pelajar_citra.pdf', 'asuransi_citra.pdf', 'pernyataan_citra.pdf', '', 'foto_citra.jpg'
) t WHERE NOT EXISTS (SELECT 1 FROM pkl_data WHERE idkey = 'USR02CitraMaharani0813');

INSERT INTO pkl_data
  (user_id, idkey, no_pkl, nama, institusi, jurusan, pendidikan, email, telp,
   id_depag, tgl_mulai, tgl_selesai, status, doc_1, doc_2, doc_3, doc_4, doc_5, doc_6, doc_7, foto)
SELECT * FROM (SELECT
  'USR03', 'USR03BagasPratama0814', 'SIM-2026-020',
  'Bagas Pratama', 'Politeknik Negeri Malang', 'Teknik Mesin',
  'D3', 'bagas.pratama@example.com', '081433445566',
  2, '2026-11-02', '2026-12-31', 'Pending',
  'surat_pengantar_bagas.pdf', 'proposal_bagas.pdf', 'transkrip_bagas.pdf',
  'kartu_pelajar_bagas.pdf', 'asuransi_bagas.pdf', 'pernyataan_bagas.pdf', '', 'foto_bagas.jpg'
) t WHERE NOT EXISTS (SELECT 1 FROM pkl_data WHERE idkey = 'USR03BagasPratama0814');

INSERT INTO pkl_data
  (user_id, idkey, no_pkl, nama, institusi, jurusan, pendidikan, email, telp,
   id_depag, tgl_mulai, tgl_selesai, status, doc_1, doc_2, doc_3, doc_4, doc_5, doc_6, doc_7, foto)
SELECT * FROM (SELECT
  'USR04', 'USR04RizkyRamadhan0815', 'SIM-2026-021',
  'Rizky Ramadhan', 'SMK Negeri 1 Gresik', 'Teknik Kimia Industri',
  'SMK', 'rizky.ramadhan@example.com', '081544556677',
  2, '2026-10-12', '2026-12-11', 'Accept',
  'surat_pengantar_rizky.pdf', 'proposal_rizky.pdf', 'transkrip_rizky.pdf',
  'kartu_pelajar_rizky.pdf', 'asuransi_rizky.pdf', 'pernyataan_rizky.pdf', '', 'foto_rizky.jpg'
) t WHERE NOT EXISTS (SELECT 1 FROM pkl_data WHERE idkey = 'USR04RizkyRamadhan0815');

INSERT INTO pkl_data
  (user_id, idkey, no_pkl, nama, institusi, jurusan, pendidikan, email, telp,
   id_depag, tgl_mulai, tgl_selesai, status, doc_1, doc_2, doc_3, doc_4, doc_5, doc_6, doc_7, foto)
SELECT * FROM (SELECT
  'USR05', 'USR05DimasSaputra0816', 'SIM-2026-022',
  'Dimas Saputra', 'Universitas Muhammadiyah Gresik', 'Akuntansi',
  'S1', 'dimas.saputra@example.com', '081655667788',
  6, '2026-10-01', '2026-12-10', 'Reject',
  'surat_pengantar_dimas.pdf', 'proposal_dimas.pdf', 'transkrip_dimas.pdf',
  'kartu_pelajar_dimas.pdf', 'asuransi_dimas.pdf', 'pernyataan_dimas.pdf', '', 'foto_dimas.jpg'
) t WHERE NOT EXISTS (SELECT 1 FROM pkl_data WHERE idkey = 'USR05DimasSaputra0816');

INSERT INTO pkl_data
  (user_id, idkey, no_pkl, nama, institusi, jurusan, pendidikan, email, telp,
   id_depag, tgl_mulai, tgl_selesai, status, doc_1, doc_2, doc_3, doc_4, doc_5, doc_6, doc_7, foto)
SELECT * FROM (SELECT
  'USR06', 'USR06NadiaSafitri0817', 'SIM-2026-023',
  'Nadia Safitri', 'Universitas Brawijaya', 'Teknik Industri',
  'S1', 'nadia.safitri@example.com', '081766778899',
  4, '2026-07-15', '2026-10-11', 'Inactive',
  'surat_pengantar_nadia.pdf', 'proposal_nadia.pdf', 'transkrip_nadia.pdf',
  'kartu_pelajar_nadia.pdf', 'asuransi_nadia.pdf', 'pernyataan_nadia.pdf', '', 'foto_nadia.jpg'
) t WHERE NOT EXISTS (SELECT 1 FROM pkl_data WHERE idkey = 'USR06NadiaSafitri0817');
