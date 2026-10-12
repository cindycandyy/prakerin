# Modul Analisis Penempatan Magang — Rencana Pengerjaan & Status

Fitur tambahan di dalam aplikasi E-Prakerin yang sudah ada (CodeIgniter 3.0.6 + MySQL),
bukan aplikasi atau produk terpisah — tidak diberi nama produk sendiri, tetap bagian dari
E-Prakerin. Fitur ini membaca proposal/transkrip magang secara otomatis dan merekomendasikan
penempatan divisi ke HR. Sumber: dokumen "Analisis & Desain Sistem" dan "Spesifikasi teknis"
dari client (2026-10-06).

Dikerjakan bertahap. Setiap tahap punya tes penerimaan sendiri sebelum lanjut ke tahap
berikutnya. Fitur **nonaktif secara default** (`analisis_aktif = false` di
`application/config/analisis.php`) sampai Tahap 5 — E-Prakerin lama tidak terpengaruh
selama pengerjaan berlangsung.

## Koreksi penting terhadap spesifikasi asli

Spesifikasi menyebut tabel lama bernama `Transaksi`/`transaksi`. Setelah membaca kode asli,
nama tabel sebenarnya adalah **`pkl_data`** (lihat `application/models/Model_transaksi.php`).
Semua `id_transaksi` di skema baru menunjuk ke `pkl_data.id`. Dokumen ini dan semua kode
memakai nama tabel yang benar.

Tabel, controller, dan config baru tidak memakai nama produk ("SIMAGANG") — konsisten dengan
E-Prakerin yang sudah ada: prefix tabel baru `prk_`, controller `Analisis.php`, config
`analisis.php`, URL `/analisis/*`, menu sidebar "Analisis".

## Status tahapan

- [x] **Tahap 1 — Fondasi** (selesai, lihat rincian di bawah)
- [ ] Tahap 2 — Hook integrasi & antrean (tanpa AI)
- [ ] Tahap 3 — Layanan analisis Python (baca PDF, skor, rekomendasi)
- [ ] Tahap 4 — Keputusan HR, kontrak, surat balasan, notifikasi
- [ ] Tahap 5 — Isolasi akses berkas, syarat foto, uji & aktivasi

---

## Tahap 1 — Fondasi (selesai)

Tidak menyentuh alur bisnis E-Prakerin yang ada. Aman di-deploy kapan saja.

### 1. Dua bug lama diperbaiki

- **`application/controllers/Transaksi.php`**, method `updateDoc1`–`updateDoc7` dan
  `updateFoto`: key array punya trailing space (`'doc_1 '`, `'Foto '`) sehingga update ke
  kolom yang salah/tidak ada. Diperbaiki jadi `'doc_1'`, `'foto'`, dst. Juga method-method
  ini memanggil `unlink($data->gambar)` padahal kolom itu tidak ada di `pkl_data` (yang ada
  `doc_1`..`doc_7`, `foto`) — diperbaiki agar menghapus file lama dari kolom yang benar,
  dan dibungkus `@unlink` + cek kosong supaya tidak fatal error bila file sudah tidak ada.
- Bug kecil tambahan yang ditemukan saat membaca kode: `Transaksi::insert` memakai variabel
  `$name` yang tidak pernah didefinisikan (seharusnya `$nama`) saat membangun `idkey`.
  Diperbaiki.
- **`application/controllers/Email.php`**: kredensial SMTP (email + password) pindah dari
  hardcode ke `application/config/email_smtp.php` (tidak masuk git, lihat `.gitignore`).
  Salinan contoh ada di `email_smtp.example.php`. Typo host `smptp.gmail.com` juga
  diperbaiki jadi `smtp.gmail.com`.
  - `Forget.php` sudah mengambil kredensial dari tabel `setting` sejak awal — tidak diubah.

### 2. Migrasi SQL 11+1 tabel baru

File: `sql/001_analisis_schema.sql` (DDL) dan `sql/002_analisis_seed_master.sql` (seeder,
idempoten — aman dijalankan ulang).

Tabel baru, semua berawalan `prk_`, InnoDB, utf8mb4: `prk_departemen`, `prk_divisi`,
`prk_depag_map`, `prk_kuota`, `prk_sinyal_divisi`, `prk_pemetaan_jurusan`,
`prk_mapel_relevan`, `prk_pengaturan`, `prk_teks_dokumen`, `prk_analisis_job`,
`prk_profil`, `prk_rekomendasi`, `prk_penempatan`, `prk_kontrak`, `prk_notifikasi`,
`prk_notifikasi_baca`, `prk_surat_balasan`, `prk_surat_peserta`, `prk_audit`,
`prk_foto_status`.

**Belum bisa dijalankan dan diverifikasi di lingkungan ini** — tidak ada server MySQL aktif
yang bisa diakses dari sesi ini. **Sebelum menjalankan migrasi di server nyata:**

1. `SHOW CREATE TABLE pkl_data;` dan `SHOW CREATE TABLE depag;` — pastikan kedua tabel
   bermesin **InnoDB** (migrasi memakai FOREIGN KEY ke keduanya) dan tipe kolom `id` cocok
   dengan `INT UNSIGNED` yang dipakai di `id_transaksi`/`id_depag` pada skema baru. Bila
   beda tipe atau MyISAM, sesuaikan dulu sebelum `CREATE TABLE`.
2. Jalankan `001_analisis_schema.sql` lalu `002_analisis_seed_master.sql`.
3. Seeder mengisi 6 departemen dan 30 divisi dari dokumen analisis. **Dua kelompok (Rantai
   Pasok, SDM & Tata Kelola) masih usulan awal** — perlu dikonfirmasi client sebelum
   dipakai untuk skor rekomendasi nyata (lihat catatan di `002_analisis_seed_master.sql`).
4. Kuota awal diisi angka sembarang (3) per divisi sesuai arahan client ("pakai angka random
   dulu nanti dimasukin sama HR") — HR mengisi ulang lewat halaman Kuota (lihat di bawah).

### 3. Kerangka modul CodeIgniter

- `application/config/analisis.php` (+ `.example.php`): saklar `analisis_aktif` (default
  **false**) dan pengaturan lain yang dipakai mulai Tahap 3 (URL layanan Python, kunci API,
  dsb).
- `application/controllers/Analisis.php`: cek saklar aktif (404 bila mati) dan hak akses
  `SES_PRIV` 1–2 (redirect bila bukan). Baru berisi:
  - `dashboard()` — placeholder, menampilkan daftar departemen.
  - `kuota()` / `kuota_simpan()` — HR mengedit kuota per divisi, tersimpan ke `prk_kuota`,
    tercatat di `prk_audit`.
  - `pengaturan()` / `pengaturan_simpan()` — Admin (SES_PRIV 1) mengatur bobot F1–F4 (HR
    hanya melihat, form disabled); validasi jumlah bobot harus 100.
- `application/models/Model_prk_master.php`: akses departemen/divisi/kuota/pengaturan.
- `application/models/Model_prk_audit.php`: satu fungsi `catat()` dipakai semua kode fitur
  Analisis untuk menulis `prk_audit` — sesuai aturan spesifikasi "setiap perubahan data
  bisnis melewati pencatatan audit terpusat".
- `application/views/analisis/`: `dashboard.php`, `kuota.php`, `pengaturan.php`.
- `application/views/header.php`: menambahkan item menu sidebar "Analisis", muncul **hanya**
  jika `analisis_aktif = true` dan `SES_PRIV` 1 atau 2. Dibungkus try/catch agar kegagalan
  memuat config analisis tidak merusak halaman manapun di E-Prakerin lama.

### Tes penerimaan Tahap 1

- [ ] `php -l` pada `Transaksi.php` dan `Email.php` tanpa error sintaks (bisa dicek sekarang,
      lihat di bawah).
- [ ] Di server dengan MySQL: migrasi 001 dan 002 berjalan tanpa error pada database
      `prakerin` yang sudah ada.
- [ ] `analisis_aktif = false` → menu "Analisis" tidak muncul di sidebar manapun, dan
      `/analisis/dashboard` mengembalikan 404 — alur E-Prakerin lama sama sekali tidak
      berubah tampilannya.
- [ ] `analisis_aktif = true` + login sebagai SES_PRIV 1 atau 2 → menu "Analisis" muncul,
      halaman Dashboard dan Kuota terbuka tanpa error.
- [ ] Login sebagai SES_PRIV 3/4 (siswa) dengan fitur aktif → mengakses `/analisis/*` manapun
      redirect ke halaman utama, bukan error.
- [ ] Edit kuota satu divisi di halaman Kuota → nilai tersimpan di `prk_kuota` dan baris baru
      muncul di `prk_audit` dengan aksi `ubah_kuota`.
- [ ] Unggah ulang salah satu dokumen peserta lewat `updateDoc1`–`updateDoc7`/`updateFoto`
      (alur lama) → kolom `pkl_data` yang benar ter-update (cek `doc_1` tanpa spasi di nama
      kolom), file lama yang sesuai terhapus dari folder `gambar/`.

---

## Tahap 2 — Hook integrasi & antrean (belum dikerjakan)

Tujuan: setiap kali `pkl_data` dapat baris baru atau dokumennya diganti, satu pekerjaan
masuk ke `prk_analisis_job` — tanpa memanggil AI apa pun dulu (disiapkan untuk Tahap 3).

Rencana kerja:
- Hook di `Transaksi::insert` (setelah sukses) dan tiap `updateDoc1`–`7` (setelah sukses):
  hitung sha256 kedua berkas (proposal + transkrip, perlu dipastikan slot doc mana — lihat
  "Perlu dikonfirmasi" di bawah), panggil `fungsi antrekan($id)`. Dibungkus try/catch,
  kegagalan dicatat ke log tapi tidak menggagalkan simpan data lama.
- `application/libraries/Analisis_queue.php`: logika antre + dedup berdasar hash.
- `application/controllers/Cli_analisis.php`: perintah `php index.php cli_analisis antrean`
  (placeholder, belum memanggil Python) dan `cli_analisis harian` (auto-tolak Pending mepet,
  auto-inactive, dasar notifikasi H-7/H-3 — logika tanpa AI).
- `Analisis_status::ubah()` sebagai satu-satunya titik ubah kolom `pkl_data.status`.

**Perlu dikonfirmasi sebelum Tahap 2 dimulai** (tidak bisa ditebak dari kode):
- Pemetaan slot `doc_1`..`doc_7` ke 7 jenis dokumen (surat pengantar, proposal, transkrip,
  foto terpisah, kartu asuransi, kartu pelajar, surat pernyataan). Form `view` pendaftaran
  lama (`application/views/transaksi/`) perlu dibaca urutan labelnya untuk memastikan slot
  mana "proposal" dan mana "transkrip" — **ini blocker** karena seluruh analisis Tahap 3
  bergantung pada pemetaan yang benar.
- Nilai persis kolom `status` yang dipakai sebagai acuan (kode lama tidak konsisten:
  campuran `'pending'`/`'Pending'`, `'accept'`/`'Accept'`, `'Inactive'`). Disarankan
  menyeragamkan di titik masuk baru (`Analisis_status::ubah()`) dengan `LOWER()` di semua
  perbandingan, tanpa migrasi data lama.

---

## Tahap 3 — Layanan analisis Python (belum dikerjakan)

Layanan FastAPI terpisah, baca PDF dengan PyMuPDF/pdfplumber, hitung skor 4 faktor (bagian 5
spesifikasi teknis), redaksi data pribadi sebelum opsi LLM. Tidak menyimpan apa pun — PHP
yang menulis ke `prk_profil`/`prk_rekomendasi`.

Baru dikerjakan setelah slot dokumen (Tahap 2) terkonfirmasi, karena fixture data uji
(`fixture/proposal_*.pdf`) perlu dokumen asli dengan slot yang benar.

## Tahap 4 — Keputusan HR, kontrak, surat balasan, notifikasi (belum dikerjakan)

Halaman A–E penuh (daftar analisis, detail peserta, kontrak, surat DOCX→PDF, pusat
notifikasi), mesin status `pending→accept/reject→inactive`, cron harian H-7/H-3.

## Tahap 5 — Isolasi akses berkas, syarat foto, uji & aktivasi (belum dikerjakan)

Rute berkas lewat PHP dengan cek kepemilikan (bagian 7.7 spesifikasi — ini temuan keamanan
di E-Prakerin lama: pendaftar lain bisa melihat berkas siapa pun lewat folder `gambar/` yang
ada di web root), pemeriksaan foto otomatis, lalu uji T01–T46 sebelum `analisis_aktif = true`
di produksi.

---

## Cara verifikasi sintaks PHP Tahap 1 secara lokal

```
php -l application/controllers/Transaksi.php
php -l application/controllers/Email.php
php -l application/controllers/Analisis.php
php -l application/models/Model_prk_master.php
php -l application/models/Model_prk_audit.php
```

Tidak bisa diverifikasi jalan end-to-end di lingkungan ini karena tidak ada server
Apache+MySQL yang dikonfigurasi untuk project ini. Jalankan `php -l` di atas sekarang sebagai
pemeriksaan minimal.
