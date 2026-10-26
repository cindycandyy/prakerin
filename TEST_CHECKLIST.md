# Checklist uji manual T01–T46

Dibuat berdasarkan seluruh fitur yang sudah dibangun di modul SIMAGANG/Analisis Penempatan
(portal pelamar + dashboard HR). Status diisi setelah dijalankan: **OK** / **GAGAL** / **PERLU TES BROWSER**.

## A. Autentikasi & kredensial (C3/C4)

- [x] T01 — **OK**. Login HR (`hr@eprakerin.local`/`hr123`) berhasil, redirect ke `/analisis/dashboard`
- [x] T02 — **OK**. Login pelamar baru berhasil, redirect ke `/pelamar/beranda`
- [x] T03 — **OK**. Password salah ditolak (redirect balik ke `login_sistem`), tidak ada SQL error
- [x] T04 — **OK**. Payload `userName=test' OR '1'='1` tidak menyebabkan SQL error, hanya dianggap kredensial tidak valid
- [x] T05 — **OK**. Seed data plaintext (`hr123`) otomatis jadi `$2y$10$...` setelah login sukses pertama
- [x] T06 — **OK**. Registrasi baru langsung tersimpan sebagai hash bcrypt
- [ ] T07 — **PERLU TES MANUAL** (butuh SMTP benar-benar terkirim, tidak dites di sesi ini — tapi kode sudah diverifikasi lewat review: token dikirim plaintext di email, DB simpan hash)
- [x] T08 — **OK**. Password lama salah ditolak ("Kata sandi lama tidak sesuai"); password benar diterima, hash berubah, password baru langsung bisa login
- [ ] T09 — **PERLU TES MANUAL** (perlu form admin user yang detailnya tidak dieksplorasi di sesi ini; kode sudah di-review dan diperbaiki)
- [ ] T10 — **PERLU TES MANUAL** (sama seperti T09)

## B. Model_transaksi & Model_user refactor (Query Builder)

- [ ] T11 — Daftar transaksi PKL (viewalldata, viewalldata_st) tetap tampil sama seperti sebelum refactor
- [ ] T12 — Filter status pending/accept di dashboard Home masih menghitung dengan benar (getCountfield, getJmlfield)
- [ ] T13 — Detail satu transaksi (getSingleRowData/getSingleData/getSingleDatax) tetap menampilkan data yang benar
- [ ] T14 — Hapus data transaksi (delete) tetap berfungsi dan tidak error
- [ ] T15 — Dashboard Home.php (grafik/jumlah per bulan) menampilkan angka yang sama persis dengan sebelum refactor (regresi logic getSubJmlfield/getJmlfield)

## C. Dokumen pelamar (B8, kartu pelajar fix)

- [ ] T16 — Pelamar upload kartu pelajar → langsung terlihat benar oleh HR di detail pelamar (bukan lagi doc_4/doc_6 yang salah mapping)
- [ ] T17 — Pelamar upload surat pernyataan (doc_7) → terlihat oleh HR, bisa diunduh di ZIP (berkas_unduh)
- [ ] T18 — Klik "Lihat" pada salah satu berkas dari halaman detail HR → masuk ke pratinjau dengan tombol "Kembali ke E-Prakerin" **(PERLU TES BROWSER)**
- [ ] T19 — Tombol kembali di pratinjau berkas benar-benar membawa balik ke halaman sebelumnya, bukan ke beranda **(PERLU TES BROWSER)**

## D. Keputusan HR (terima/tolak)

- [x] T20 — **OK** (Playwright). Klik `#btn-buka-terima` memunculkan `#modal-terima.tampil`
- [x] T21 — **OK** (Playwright). Klik `#btn-buka-tolak` tanpa isi alasan TIDAK memunculkan `#modal-tolak.tampil`
- [ ] T22 — Setelah konfirmasi Terima, redirect ke /analisis/pelamar (daftar), bukan tetap di halaman detail
- [ ] T23 — Flashdata sukses/error di halaman /analisis/pelamar tampil sekali saja, lalu hilang saat refresh (tidak nyangkut)
- [ ] T24 — Setelah HR menerima pelamar, mekanisme lanjutan (status berubah ke Accept/kartu magang/dsb) berjalan sesuai alur lama

## E. Dropdown & filter (duplikasi TomSelect)

- [x] T25 — **OK** (Playwright). `.ts-wrapper` = 1 di halaman detail pelamar, tidak dobel
- [x] T26 — **OK** (Playwright). 4 `select.select-mini` → 4 `.ts-wrapper`, rasio 1:1, tidak dobel

## F. Skor penilaian ("semua jurusan" fairness fix)

- [ ] T27 — Proposal topik IT/digitalisasi dari pelamar jurusan non-match di divisi "semua jurusan" sekarang mendapat skor F3 (topik) yang wajar, bukan otomatis rendah
- [ ] T28 — Proposal topik non-IT di divisi yang sama tetap dinilai apa adanya (tidak ada regresi ke arah sebaliknya)

## G. Media sosial pelamar

- [ ] T29 — Pelamar isi LinkedIn/Instagram/TikTok/X di wizard pendaftaran → langsung muncul di panel "Media sosial" HR, bukan data dummy **(PERLU TES BROWSER)**
- [ ] T30 — Field sosmed kosong tidak menyebabkan error di halaman manapun

## H. Wizard pendaftaran & draft

- [ ] T31 — Submit wizard gagal di step upload berkas → kembali ke wizard tidak perlu isi ulang dari step 1 (draft localStorage tetap ada) **(PERLU TES BROWSER)**
- [ ] T32 — Setelah submit berhasil (retry dari draft), tidak ada flashdata sukses yang dobel muncul di halaman sukses

## I. Foto profil

- [ ] T33 — Halaman akun pelamar menampilkan avatar default, TIDAK memakai foto dari berkas yang disubmit
- [ ] T34 — Dashboard HR (detail pelamar) tetap menampilkan foto asli yang disubmit pelamar

## J. Notifikasi

- [x] T35 — **OK** (Playwright). Dropdown tampil saat diklik, 8 item muncul (lihat screenshot)

## K. Keamanan file (C6 unlink guard)

- [x] T36 — **OK**. Baris dengan slot dokumen kosong/NULL dihapus tanpa PHP warning
- [x] T37 — **OK**. File yang namanya ada di DB tapi tidak ada fisik di gambar/ tidak memunculkan warning saat delete
- [x] T38 — **OK**. doc_7 ikut masuk loop unlink (tervalidasi lewat kode; slot kosong jadi no-op aman)
- [x] T39 — **OK**. Payload `../../../../../../etc/passwd` di-`basename()` jadi `passwd`, tidak ada file terhapus di luar gambar/

## L. Keamanan dompdf & PHP

- [x] T40 — **OK**. PDF 24.8KB valid (dompdf 3.1.6) berhasil dibuat lewat `/analisis/surat_pratinjau/1`
- [x] T41 — **OK**. Login, DB, PDF semua berjalan normal di PHP 8.2.35 tanpa error

### Bug kritis ditemukan & diperbaiki selama pengujian ini
Saat menjalankan T04, ditemukan setiap request development-mode membocorkan puluhan
notice `E_DEPRECATED` ("Creation of dynamic property ... is deprecated", dari core CI3
yang belum kompatibel PHP 8+) ke body response. Pada route yang butuh `session_start()`
(seperti login), output ini terjadi sebelum header/cookie dikirim sehingga memicu
"headers already sent" dan membuat SESSION/LOGIN GAGAL TOTAL di environment manapun
yang berjalan tanpa `CI_ENV=production` (yang mana adalah default, karena `CI_ENV`
tidak pernah di-set di manapun). Diperbaiki di [index.php](index.php) baris ~69:
`E_DEPRECATED`/`E_USER_DEPRECATED` dikecualikan dari `error_reporting()` di mode
development, sama seperti sudah dilakukan branch testing/production. Setelah fix,
login kembali normal (303 redirect + cookie ci_session, bukan body error 0 byte).

## M. Responsive 480px (D8/B9)

- [x] T42 — **OK** (Playwright, setelah perbaikan). `pelamar/beranda` di 375px: scrollWidth=375=clientWidth, nol overflow
- [x] T43 — **OK** (Playwright, setelah perbaikan). `pelamar/daftar` di 375px: scrollWidth=375=clientWidth, nol overflow
- [x] T44 — **OK** (Playwright, direvisi jadi hamburger drawer atas permintaan). Tertutup: `.sidebar` off-canvas (`left: -260px`). Diklik tombol hamburger: drawer 260px muncul dari kiri menutupi konten dengan overlay gelap; tombol X atau klik overlay menutup lagi.
- [ ] T45 — **PERLU TES MANUAL** (dropdown notifikasi tidak dicoba dibuka di layar 375px pada sesi ini, hanya `right: calc()` sudah di CSS; perlu verifikasi visual)
- [x] T46 — **OK** (Playwright). Di 1440px, `.sidebar` tetap 224px — breakpoint tidak bocor ke desktop

### Bug kedua ditemukan & diperbaiki selama pengujian Playwright
Setelah breakpoint 480px pertama kali ditambahkan (sesi sebelumnya), tes otomatis
menunjukkan overflow horizontal 13-17px yang TIDAK terdeteksi waktu hanya dibaca
kodenya. Dua akar masalah:
1. `.sidebar > div { display: flex }` punya spesifisitas CSS lebih tinggi daripada
   `.sidebar-note { display: none }` (karena `.sidebar-note` adalah div anak langsung
   dari `.sidebar`), jadi aturan sembunyikan sidebar-note kalah dan tetap tampil,
   mendorong lebar sidebar jauh di luar 375px. Diperbaiki dengan `!important`.
2. Sisa sub-pixel overflow (13px) dari flex children di dalam area scroll sidebar
   bocor ke `document.documentElement.scrollWidth` walau `.sidebar` sendiri sudah
   dibatasi lebarnya. Diperbaiki dengan `overflow-x: hidden` di `html, body, .layout`
   pada breakpoint 480px sebagai pengaman tambahan.

Keduanya di [assets/css/analisis-shell.css](assets/css/analisis-shell.css). Ini
membuktikan kenapa T42/T43 wajib dites beneran di browser (lewat Playwright atau
manual) -- membaca kode CSS saja tidak cukup untuk menangkap bug spesifisitas
begini.

### Revisi UX mobile: sidebar topbar horizontal -> hamburger drawer
Setelah dilihat screenshot, sidebar jadi topbar horizontal yang bisa digeser
dirasa masih berantakan secara visual (menu terpotong, harus digeser manual).
Diganti jadi pola hamburger menu standar: tombol ikon garis-tiga di pojok kiri
atas topbar membuka sidebar sebagai drawer dari kiri (lebar 260px, maksimal
80vw) menutupi konten dengan overlay gelap transparan; ditutup lewat tombol X
di pojok sidebar atau klik area overlay. Diimplementasi di:
- [application/views/pelamar/_topbar.php](application/views/pelamar/_topbar.php) dan [application/views/analisis/_topbar.php](application/views/analisis/_topbar.php) -- tombol `.hamburger`
- [application/views/pelamar/_sidebar.php](application/views/pelamar/_sidebar.php) dan [application/views/analisis/_sidebar.php](application/views/analisis/_sidebar.php) -- tombol `.sidebar-tutup` + `.sidebar-overlay`
- [application/views/analisis/_icon.php](application/views/analisis/_icon.php) -- ikon `menu` dan `x` ditambahkan
- [assets/css/analisis-shell.css](assets/css/analisis-shell.css) -- breakpoint 480px direvisi total, sidebar jadi `position: fixed` + `transform: translateX(-100%)`, class `.sidebar-terbuka` di `.layout` men-toggle tampilannya

Diverifikasi ulang dengan Playwright: drawer tertutup (`left: -260px`, off-canvas,
tidak ada overflow), drawer terbuka (lebar 260px, overlay tampil), dan dua cara
tutup (tombol X, klik overlay) sama-sama berfungsi. Screenshot: `hamburger_closed.png`,
`hamburger_open.png`.

---
*Status hasil eksekusi otomatis (curl/php-cli, bagian yang tidak butuh browser) ada di bawah setelah dijalankan.*
