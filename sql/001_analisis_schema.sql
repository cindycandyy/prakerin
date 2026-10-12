-- Migrasi modul Analisis Penempatan — Tahap 1
-- Menambahkan tabel baru berawalan prk_ ke database `prakerin` yang sudah ada.
-- Tidak mengubah tabel lama (pkl_data, depag, user, dst).
--
-- CATATAN PENTING sebelum menjalankan di server nyata:
--   - id_transaksi di bawah ini menunjuk ke pkl_data.id (INT, lihat Model_transaksi.php).
--     Jalankan `SHOW CREATE TABLE pkl_data;` dan `SHOW CREATE TABLE depag;` dulu untuk
--     memastikan tipe kolom `id` persis (INT UNSIGNED vs INT biasa) sebelum menambahkan FK.
--   - Tabel pkl_data dan depag harus bermesin InnoDB agar FK di bawah bisa dibuat;
--     bila MyISAM, hapus baris FOREIGN KEY yang menunjuk ke sana dan jaga konsistensi di kode.
--   - kolom pkl_data.status dipakai dengan kapitalisasi tidak konsisten di kode lama
--     ('pending', 'Pending', 'accept', 'Accept', 'Inactive', dst). Migrasi ini tidak
--     mengubahnya; kode modul Analisis Penempatan baru membandingkan dengan LOWER(status).

SET time_zone = '+07:00';

-- ============================================================
-- Master data departemen & divisi
-- ============================================================

CREATE TABLE IF NOT EXISTS prk_departemen (
  id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  kode VARCHAR(10) NOT NULL UNIQUE,
  nama VARCHAR(100) NOT NULL,
  urutan TINYINT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_divisi (
  id SMALLINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  id_departemen TINYINT UNSIGNED NOT NULL,
  nama VARCHAR(120) NOT NULL,
  aktif TINYINT(1) NOT NULL DEFAULT 1,
  urutan SMALLINT NOT NULL DEFAULT 0,
  FOREIGN KEY (id_departemen) REFERENCES prk_departemen(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pemetaan unit lama (tabel `depag`) ke departemen baru.
-- id_depag menunjuk ke depag.id — cek tipe kolomnya dulu (lihat catatan di atas).
CREATE TABLE prk_depag_map (
  id_depag INT UNSIGNED PRIMARY KEY,
  id_departemen TINYINT UNSIGNED NOT NULL,
  FOREIGN KEY (id_departemen) REFERENCES prk_departemen(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_kuota (
  id_divisi SMALLINT UNSIGNED PRIMARY KEY,
  kuota_maks SMALLINT UNSIGNED NOT NULL,
  diubah_oleh INT UNSIGNED NULL,
  diubah_pada DATETIME NOT NULL,
  FOREIGN KEY (id_divisi) REFERENCES prk_divisi(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_sinyal_divisi (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  id_divisi SMALLINT UNSIGNED NOT NULL,
  kata_kunci VARCHAR(80) NOT NULL,
  jenis ENUM('alat','topik') NOT NULL,
  bobot TINYINT UNSIGNED NOT NULL DEFAULT 1,
  FOREIGN KEY (id_divisi) REFERENCES prk_divisi(id),
  UNIQUE KEY uq_sinyal (id_divisi, kata_kunci, jenis)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_pemetaan_jurusan (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  jurusan_norm VARCHAR(100) NOT NULL,
  peminatan VARCHAR(60) NOT NULL DEFAULT '',
  id_divisi SMALLINT UNSIGNED NOT NULL,
  skor TINYINT UNSIGNED NOT NULL,
  FOREIGN KEY (id_divisi) REFERENCES prk_divisi(id),
  UNIQUE KEY uq_pemetaan (jurusan_norm, peminatan, id_divisi)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_mapel_relevan (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  pola_mapel VARCHAR(80) NOT NULL,
  id_divisi SMALLINT UNSIGNED NOT NULL,
  bobot TINYINT UNSIGNED NOT NULL DEFAULT 1,
  FOREIGN KEY (id_divisi) REFERENCES prk_divisi(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_pengaturan (
  kunci VARCHAR(60) PRIMARY KEY,
  nilai VARCHAR(255) NOT NULL,
  diubah_oleh INT UNSIGNED NULL,
  diubah_pada DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Hasil analisis dokumen
-- ============================================================

-- id_transaksi menunjuk ke pkl_data.id (bukan tabel `transaksi` — nama lama di
-- spesifikasi; nama tabel sebenarnya di project ini adalah pkl_data).
CREATE TABLE prk_teks_dokumen (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  id_transaksi INT UNSIGNED NOT NULL,
  jenis ENUM('proposal','transkrip') NOT NULL,
  hash_berkas CHAR(64) NOT NULL,
  teks_mentah MEDIUMTEXT NOT NULL,
  dibuat_pada DATETIME NOT NULL,
  KEY ix_teks (id_transaksi, jenis),
  FOREIGN KEY (id_transaksi) REFERENCES pkl_data(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_analisis_job (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  id_transaksi INT UNSIGNED NOT NULL,
  hash_proposal CHAR(64) NOT NULL,
  hash_transkrip CHAR(64) NOT NULL,
  status ENUM('antre','jalan','selesai','gagal','perlu_tinjau') NOT NULL DEFAULT 'antre',
  percobaan TINYINT UNSIGNED NOT NULL DEFAULT 0,
  kode_galat VARCHAR(60) NULL,
  galat VARCHAR(500) NULL,
  diminta_pada DATETIME NOT NULL,
  mulai_pada DATETIME NULL,
  selesai_pada DATETIME NULL,
  KEY ix_job (status, diminta_pada),
  KEY ix_job_trx (id_transaksi),
  FOREIGN KEY (id_transaksi) REFERENCES pkl_data(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_profil (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  id_transaksi INT UNSIGNED NOT NULL,
  versi SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  is_current TINYINT(1) NOT NULL DEFAULT 1,
  jenis_institusi ENUM('universitas','smk','lainnya') NOT NULL,
  jurusan_norm VARCHAR(100) NOT NULL,
  peminatan VARCHAR(60) NOT NULL DEFAULT '',
  judul VARCHAR(255) NULL,
  bidang JSON NOT NULL,
  alat JSON NOT NULL,
  tujuan TEXT NULL,
  mata_kuliah JSON NOT NULL,
  nilai JSON NOT NULL,
  rata_nilai DECIMAL(5,2) NULL,
  sumber ENUM('aturan','llm','koreksi_hr') NOT NULL,
  dikoreksi_oleh INT UNSIGNED NULL,
  dibuat_pada DATETIME NOT NULL,
  KEY ix_profil (id_transaksi, is_current),
  FOREIGN KEY (id_transaksi) REFERENCES pkl_data(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_rekomendasi (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  id_transaksi INT UNSIGNED NOT NULL,
  id_profil INT UNSIGNED NOT NULL,
  is_current TINYINT(1) NOT NULL DEFAULT 1,
  peringkat TINYINT UNSIGNED NOT NULL,
  id_divisi SMALLINT UNSIGNED NOT NULL,
  skor DECIMAL(5,2) NOT NULL,
  keyakinan ENUM('tinggi','sedang','rendah') NOT NULL,
  f_jurusan DECIMAL(5,2) NOT NULL,
  f_skill DECIMAL(5,2) NOT NULL,
  f_proposal DECIMAL(5,2) NOT NULL,
  f_kebutuhan DECIMAL(5,2) NOT NULL,
  alasan VARCHAR(500) NOT NULL,
  kekuatan VARCHAR(500) NOT NULL,
  kekurangan VARCHAR(500) NOT NULL,
  dibuat_pada DATETIME NOT NULL,
  KEY ix_rek (id_transaksi, is_current, peringkat),
  FOREIGN KEY (id_profil) REFERENCES prk_profil(id),
  FOREIGN KEY (id_divisi) REFERENCES prk_divisi(id),
  FOREIGN KEY (id_transaksi) REFERENCES pkl_data(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Alur: penempatan, kontrak, notifikasi, surat, audit
-- ============================================================

CREATE TABLE prk_penempatan (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  id_transaksi INT UNSIGNED NOT NULL UNIQUE,
  id_divisi SMALLINT UNSIGNED NOT NULL,
  peringkat_dipilih TINYINT UNSIGNED NULL,
  alasan_ubah VARCHAR(500) NULL,
  mentor VARCHAR(100) NULL,
  ditetapkan_oleh INT UNSIGNED NOT NULL,
  ditetapkan_pada DATETIME NOT NULL,
  FOREIGN KEY (id_divisi) REFERENCES prk_divisi(id),
  FOREIGN KEY (id_transaksi) REFERENCES pkl_data(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_kontrak (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  id_penempatan INT UNSIGNED NOT NULL,
  urutan TINYINT UNSIGNED NOT NULL DEFAULT 1,
  tanggal_mulai DATE NOT NULL,
  tanggal_selesai DATE NOT NULL,
  status ENUM('akan_mulai','aktif','akan_berakhir','selesai','diakhiri_awal') NOT NULL,
  tindak_lanjut ENUM('belum','diperpanjang','diakhiri') NOT NULL DEFAULT 'belum',
  catatan VARCHAR(500) NULL,
  dibuat_pada DATETIME NOT NULL,
  KEY ix_kontrak (status, tanggal_selesai),
  FOREIGN KEY (id_penempatan) REFERENCES prk_penempatan(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_notifikasi (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  jenis ENUM('h7','h3','hari_h','analisis_selesai','perlu_tinjau','mepet','lewat','status_otomatis','gagal') NOT NULL,
  id_transaksi INT UNSIGNED NOT NULL,
  id_kontrak INT UNSIGNED NULL,
  judul VARCHAR(150) NOT NULL,
  isi VARCHAR(500) NOT NULL,
  tautan VARCHAR(200) NOT NULL,
  dedupe_key VARCHAR(100) NOT NULL UNIQUE,
  dibuat_pada DATETIME NOT NULL,
  selesai_pada DATETIME NULL,
  FOREIGN KEY (id_transaksi) REFERENCES pkl_data(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_notifikasi_baca (
  id_notifikasi INT UNSIGNED NOT NULL,
  id_user INT UNSIGNED NOT NULL,
  dibaca_pada DATETIME NOT NULL,
  PRIMARY KEY (id_notifikasi, id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_surat_balasan (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  nomor_surat VARCHAR(60) NULL UNIQUE,
  tanggal_surat DATE NULL,
  rujukan_nomor VARCHAR(80) NULL,
  rujukan_tanggal DATE NULL,
  pembekalan_tanggal DATE NULL,
  pembekalan_pukul VARCHAR(30) NULL,
  pembekalan_tempat VARCHAR(150) NULL,
  pembekalan_keterangan TEXT NULL,
  status ENUM('draf','terkirim','diganti') NOT NULL DEFAULT 'draf',
  id_pengganti INT UNSIGNED NULL,
  file_path VARCHAR(255) NULL,
  dikirim_pada DATETIME NULL,
  dibuat_oleh INT UNSIGNED NOT NULL,
  dibuat_pada DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_surat_peserta (
  id_surat INT UNSIGNED NOT NULL,
  id_transaksi INT UNSIGNED NOT NULL,
  PRIMARY KEY (id_surat, id_transaksi),
  FOREIGN KEY (id_surat) REFERENCES prk_surat_balasan(id),
  FOREIGN KEY (id_transaksi) REFERENCES pkl_data(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE prk_audit (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  waktu DATETIME NOT NULL,
  pelaku VARCHAR(30) NOT NULL,
  entitas VARCHAR(40) NOT NULL,
  id_entitas INT UNSIGNED NOT NULL,
  aksi VARCHAR(40) NOT NULL,
  sebelum JSON NULL,
  sesudah JSON NULL,
  alasan VARCHAR(500) NULL,
  KEY ix_audit (entitas, id_entitas)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Status foto (bagian 7.7 spesifikasi teknis) — ditambahkan di Tahap 1 sekaligus
-- karena satu migrasi DDL lebih mudah dijalankan sekali oleh HR/DBA daripada bertahap.
CREATE TABLE prk_foto_status (
  id_transaksi INT UNSIGNED PRIMARY KEY,
  hasil ENUM('belum','lolos','perlu_cek','diterima_hr','diminta_ulang') NOT NULL DEFAULT 'belum',
  jumlah_wajah TINYINT UNSIGNED NULL,
  rasio_wajah DECIMAL(4,3) NULL,
  catatan VARCHAR(500) NULL,
  sha256 CHAR(64) NULL,
  dicek_oleh INT UNSIGNED NULL,
  dicek_pada DATETIME NULL,
  FOREIGN KEY (id_transaksi) REFERENCES pkl_data(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
