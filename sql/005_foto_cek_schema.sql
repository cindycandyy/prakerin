-- Hasil pemeriksaan foto otomatis lokal (FR-29, spesifikasi teknis bagian 4).
-- "Pemeriksaan otomatis lokal sebagai peringatan" -- bukan validasi blocking, cuma
-- flag + catatan yang HR lihat di halaman detail pelamar; HR yang memutuskan terima
-- atau minta foto ulang. is_current dipakai karena foto bisa diganti berkali-kali
-- (updateFoto di Transaksi.php) dan HR hanya perlu lihat hasil pemeriksaan terbaru.
CREATE TABLE prk_foto_cek (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  id_transaksi INT UNSIGNED NOT NULL,
  is_current TINYINT(1) NOT NULL DEFAULT 1,
  ok TINYINT(1) NOT NULL,
  catatan VARCHAR(1000) NOT NULL,
  diperiksa_pada DATETIME NOT NULL,
  FOREIGN KEY (id_transaksi) REFERENCES pkl_data(id),
  INDEX idx_foto_cek_transaksi (id_transaksi, is_current)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
