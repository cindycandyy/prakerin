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
- [x] **Tahap 2 — Hook integrasi & antrean** (selesai, lihat rincian di bawah)
- [x] **Tahap 3 — Layanan analisis Python** (selesai — `simagang-analyzer/`, baca PDF,
      redaksi, skor; lihat README di folder itu)
- [x] **Tahap 4 — Keputusan HR, kontrak, surat balasan, notifikasi** (selesai, lihat
      rincian di bawah)
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

## Tahap 2 — Hook integrasi & antrean (selesai)

Setiap kali `pkl_data` dapat baris baru atau proposal/transkrip diganti, satu pekerjaan
masuk ke `prk_analisis_job`, lalu `cli_analisis antrean` mengambilnya dan memanggil
layanan Python (`simagang-analyzer`, Tahap 3) langsung — tidak dipecah "antre tanpa AI
dulu" seperti rencana awal, karena layanan Python sudah ada saat Tahap 2 dikerjakan.

Yang dibuat:
- Hook `antrekanAnalisis($id)` di `application/controllers/Transaksi.php`: dipanggil
  setelah `insert` (pendaftaran baru, pakai `$this->db->insert_id()`), `updateDoc2`, dan
  `updateDoc3` (proposal/transkrip diganti). Dibungkus try/catch + cek
  `analisis_aktif` — kegagalan di sini ditulis ke log tapi **tidak pernah** menggagalkan
  alur pendaftaran/unggah lama.
- `application/libraries/Analisis_queue.php`: `enqueue($id_transaksi)` — hash sha256
  `doc_2`+`doc_3`, lewati bila salah satu belum ada, lewati bila hash sama dengan job
  `selesai` terakhir (bagian 3 spesifikasi, "proposal/transkrip tidak berubah").
- `application/models/Model_prk_job.php`: CRUD `prk_analisis_job` (enqueue, klaim atomik,
  tandai selesai/gagal/gagal-sementara-dengan-retry, pulihkan job macet >10 menit) +
  `simpanHasil()` (tulis `prk_profil` versi baru + `prk_rekomendasi`, satu transaksi DB).
- `application/models/Model_prk_master.php` ditambah: `sisaKuotaSemuaDivisi()` (rumus
  irisan periode bagian 2 spesifikasi), `listSinyalDivisi()`/`listPemetaanJurusan()`/
  `listMapelRelevan()`/`bobotDanAmbang()` — semua dipakai menyusun payload `master` untuk
  `/v1/analisis`.
- `application/controllers/Cli_analisis.php`: `php index.php cli_analisis antrean` —
  ambil sampai 5 job `antre`, panggil `POST /v1/analisis` lewat `file_get_contents` +
  stream context (tanpa ekstensi curl), tangani sukses/422/5xx-timeout sesuai tabel
  bagian 3 & 9.1 spesifikasi, buat notifikasi (`analisis_selesai`/`perlu_tinjau`/`gagal`).

Nilai `status` yang dipakai dikonfirmasi dari dropdown manual di `v_transaksi.php`:
**`Pending`, `Accept`, `Rejected`, `Inactive`** (bukan `Reject` — dua bug ditemukan dan
diperbaiki memakai nilai salah ini sebelum `Analisis_status` dibuat).

## Tahap 4 (sebagian) — status machine, kontrak otomatis, cli_analisis harian

- `application/libraries/Analisis_status.php`: satu titik masuk untuk mengubah
  `pkl_data.status` (bagian 6.1 spesifikasi) -- validasi transisi, catat `prk_audit`
  (`entitas='status'`). Dipakai `Analisis::terima()`/`tolak()` dan
  `Cli_analisis::harian()`.
- `application/models/Model_prk_kontrak.php`: `buatPertama()` (kontrak urutan 1, dipanggil
  otomatis dari `Analisis::terima()` dengan `tgl_mulai`/`tgl_selesai` pengajuan),
  `hitungStatus()`/`perbaruiSemuaStatus()` (status turunan dari tanggal, bagian 6.2).
  Perpanjangan (kontrak urutan 2 dst) belum dikerjakan.
- `Analisis::tolak()` sekarang mewajibkan alasan minimal 5 karakter (bagian 6.1) --
  ditambah field `name="alasan"` di form Tolak pada `detail_pelamar.php` (sebelumnya
  tidak ada field sama sekali, jadi validasi lama tidak pernah tercapai).
- `Cli_analisis::harian()` (`php index.php cli_analisis harian`, cron 00:10 WIB):
  auto-tolak Pending mepet/lewat tanggal mulai -> perbarui status kontrak turunan ->
  auto-Inactive peserta Accept yang kontraknya selesai -> notifikasi H-7/H-3 (lewat
  `Model_prk_notifikasi::generatePengingatKontrak()` yang sudah ada) -> notifikasi
  hari-H. Idempoten lewat `dedupe_key` per tanggal.

Cron yang disarankan (server aplikasi):
```
* * * * * php /path/index.php cli_analisis antrean
10 0 * * * php /path/index.php cli_analisis harian
```

## Validasi kuota, perpanjang & akhiri kontrak

- `Analisis::terima()` sekarang mengunci baris `prk_kuota` divisi yang dipilih
  (`Model_prk_master::sisaKuotaDivisiUntukUpdate()`, `SELECT ... FOR UPDATE` dalam
  `trans_start()`/`trans_complete()`) sebelum menyimpan penempatan -- mencegah dua HR
  menerima bersamaan ke divisi yang kuotanya tinggal 1 (bagian 9.2, `KUOTA_HABIS`).
  Re-konfirmasi divisi yang sama tidak ikut diblokir.
- `POST /analisis/kontrak/{id}/perpanjang` (`Analisis::kontrak_perpanjang()`): kontrak
  lama ditandai `tindak_lanjut=diperpanjang` (berhenti muncul di H-7/H-3), kontrak baru
  urutan berikutnya dibuat dari `tanggal_selesai` lama + 1 hari sampai tanggal yang
  diisi HR. Status pelamar tetap `Accept`.
- `POST /analisis/kontrak/{id}/akhiri` (`Analisis::kontrak_akhiri()`): alasan wajib
  min. 5 karakter, kontrak ditandai `diakhiri_awal`, status pelamar diubah ke
  `Inactive` lewat `Analisis_status`.
- `detail_pelamar.php` dan `Analisis::peserta()` ditambah bagian "Kontrak magang"
  (hanya tampil kalau penempatan+kontrak sudah ada) dengan form Perpanjang/Akhiri —
  sebelumnya tidak ada tampilan kontrak sama sekali di halaman ini.
- Rute baru di `routes.php`: `analisis/kontrak/(:num)/perpanjang` dan `.../akhiri`
  (pola sama dengan `analisis/peserta/(:num)/terima|tolak`).

**Belum dikerjakan:** surat balasan, isolasi akses berkas + syarat foto (Tahap 5).

### Pemetaan slot `doc_1`..`doc_7` (dikonfirmasi 2026-10-12)

Sumber: label tombol upload/download di `application/views/transaksi/v_transaksi.php`
baris 93, 117, 137, 180, 200, 253, 280 (dicocokkan dengan `insert` di
`application/controllers/Transaksi.php` baris 111-149, yang hanya mengunggah `doc_1/2/3/5/6/7`
+ `foto` saat pendaftaran; `doc_4` diisi belakangan oleh HR).

| Slot | Dokumen | Diisi saat |
|---|---|---|
| `doc_1` | Dokumen/Surat Pengantar | pendaftaran |
| `doc_2` | **Proposal** → `berkas.proposal` di `/v1/analisis` | pendaftaran |
| `doc_3` | **Transkrip Nilai** → `berkas.transkrip` di `/v1/analisis` | pendaftaran |
| `doc_4` | Surat Balasan (dari HR, bukan pendaftar — beda alur dari modul ini) | setelah accept |
| `doc_5` | Kartu Asuransi | pendaftaran |
| `doc_6` | Kartu Pelajar | pendaftaran |
| `doc_7` | Surat Pernyataan | pendaftaran |
| `foto` | Foto (kolom terpisah, bukan `doc_N`) | pendaftaran |

Jadi hook antre hanya perlu dipasang di `updateDoc2`, `updateDoc3`, dan `insert` (ketiganya
mengisi salah satu dari proposal/transkrip) — `updateDoc1/4/5/6/7` dan `updateFoto` tidak
memicu analisis ulang.

**Masih perlu dikonfirmasi sebelum Tahap 2 dimulai:**
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

## Surat balasan (bagian 8 spesifikasi teknis)

LibreOffice tidak tersedia di server pengembangan ini, jadi dipakai **Dompdf + templat
HTML** (alternatif resmi yang disebut bagian 8.4), bukan PHPWord+LibreOffice. Dompdf
butuh banyak dependency jadi ditambahkan lewat **Composer** (`composer.json` baru di
root project) — satu-satunya bagian modul Analisis yang pakai Composer; sisa project
tetap vendor manual seperti sebelumnya. Jalankan `composer install` sekali di server
sebelum fitur surat dipakai; folder `vendor/` tidak masuk git (lihat `.gitignore`).

Yang dibuat:
- `composer.json` — `dompdf/dompdf: ^3.1` (v2.0.x dihindari karena kena beberapa
  security advisory, lihat output `composer install`).
- `application/helpers/analisis_helper.php` — `terbilang()` (angka ke kata, "1 (Satu)")
  dan `tanggal_indo()` (nama bulan/hari Indonesia tanpa bergantung `setlocale()`, yang
  sering tidak tersedia/beda nama locale antar server — `date('d F Y')` bawaan PHP
  menghasilkan bahasa Inggris kalau locale server bukan `id_ID`).
- `application/models/Model_prk_surat.php` — CRUD `prk_surat_balasan`+`prk_surat_peserta`:
  `buatDari()` (gabung beberapa peserta instansi sama ke satu surat), validasi nomor unik,
  saran nomor urut berikutnya per tahun, `buatPengganti()` (status lama → `diganti`).
- `application/libraries/Analisis_surat.php` — render templat ke PDF (Dompdf), simpan
  PDF final ke `storage/analisis/surat/{tahun}/{id}.pdf` (di luar web root, konsisten
  dengan `analisis_storage_surat` di config).
- `application/views/analisis/templat/surat_balasan.php` — templat HTML: kop, nomor,
  rujukan, tabel peserta, jadwal pembekalan, tanda tangan, tembusan. **Tidak pernah
  menyebut departemen/divisi penempatan** (bagian 8 & 9.3) — HR bebas menempatkan
  peserta di departemen berbeda dari yang diminta pendaftar tanpa itu terlihat di surat.
  Watermark "DRAF" di pratinjau, tanpa watermark di PDF final.
- `application/views/analisis/surat.php` — Halaman C: isian (nomor, rujukan, jadwal
  pembekalan), tombol Simpan draf/Pratinjau/Kirim, dan tampilan terkunci + tombol "Buat
  surat pengganti" setelah terkirim.
- `Analisis::surat_buat()`, `surat()`, `surat_simpan()`, `surat_pratinjau()`,
  `surat_kirim()`, `surat_unduh()`, `surat_pengganti()` di `Analisis.php` — alur draf →
  pratinjau → kirim (terkunci) → pengganti (bagian 8.3). Tombol "Buat / buka surat
  balasan" muncul di `detail_pelamar.php` hanya untuk pelamar berstatus `Accept`.
- Rute eksplisit di `routes.php` untuk semua aksi (`analisis/surat/(:num)/simpan` dst),
  pola sama dengan `terima`/`tolak` yang sudah ada.
- Validasi: nomor surat `^\d{5}/[A-Z0-9./]+/\d{4}$` dan unik (bagian 8.2); kirim ditolak
  kalau nomor/jadwal pembekalan belum lengkap; isian terkunci setelah `terkirim`
  (hanya `buat_pengganti` yang bisa membuat salinan baru untuk dikoreksi).

Diverifikasi dengan merender templat lewat Dompdf langsung (tanpa CodeIgniter/DB, PHP CLI
`php -r`) dan membaca ulang teks PDF hasilnya (PyMuPDF) — isi lengkap dan benar: nomor,
rujukan, tabel peserta, jadwal pembekalan dengan nama bulan/hari Indonesia ("27 Juli
2026", "Senin, 03 Agustus 2026"), tanda tangan, tembusan.

**Belum dikerjakan:**
- Tanda tangan/stempel/QR digital — PDF hanya memuat nama+jabatan (sesuai bagian 8.4,
  "sistem tidak membuatnya"); unggah PDF yang sudah ditandatangani manual untuk
  menggantikan hasil sistem belum ada di versi ini.
- Form "Kepada" yang bisa diedit HR secara terpisah — saat ini disusun otomatis dari
  instansi peserta pertama (SMK → "Kepala Sekolah", selain itu → "Kepala
  Departemen/Program Studi") dan tidak bisa diubah lewat UI.
- Penggabungan beberapa peserta instansi sama ke satu surat lewat form (model
  `listKandidatGabung()` sudah ada, UI baru menampilkan daftarnya sebagai info, belum
  ada tombol gabung).
- Email ke pendaftar saat surat terkirim (prioritas terakhir per spesifikasi).

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
