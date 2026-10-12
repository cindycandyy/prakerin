# Dokumentasi Sistem Prakerin

## 1. Gambaran Umum

Aplikasi web berbasis **CodeIgniter (PHP)** untuk mengelola pendaftaran **PKL / magang (Praktik Kerja Industri)** di sebuah perusahaan (referensi "Petrosida Gresik" ditemukan di kode). Sistem menangani pendaftaran peserta magang, penempatan ke unit/bagian ("Depag"), persyaratan per unit, status penerimaan, cetak kartu tanda peserta, dan laporan rekap.

## 2. Teknologi & Konfigurasi

| Item | Detail |
|---|---|
| Framework | CodeIgniter (versi lama, struktur `application/`, `system/`) |
| Database | MySQL (mysqli), nama DB `prakerin` |
| Base URL | Dinamis mengikuti `HTTP_HOST` |
| Default controller | `Home` |
| 404 | `errors/` |
| URL rewrite | `.htaccess` menghapus `index.php` dari URL (mod_rewrite standar CI) |
| Export laporan | PHPExcel (file `.xls`) |
| Email | SMTP Gmail (kredensial di tabel `setting` untuk reset password; kredensial lain hardcoded di `Email.php` untuk testing) |

## 3. Hak Akses (Session `SES_PRIV`)

- **1–2**: Admin / staff SDM — akses penuh, bisa melihat & mengelola semua data.
- **3–4**: Siswa / peserta magang — hanya bisa melihat & mengelola data miliknya sendiri.

## 4. Modul & Controller

### Home
Halaman utama publik. Menampilkan mode maintenance jika diaktifkan, atau halaman depan dengan data unit (depag). Ada varian untuk user yang sudah login.

### Login
- `sistem()` multiplexer: form login, proses autentikasi (set session), logout.

### Register
- `sistem()` multiplexer: form registrasi, proses daftar akun baru (cek email duplikat, generate user_id otomatis, default role peserta).

### Forget (Lupa Password)
- `index()` — form lupa password.
- `send_email()` — generate password baru, update DB, kirim via email.

### Email
Form uji kirim email (terpisah dari fitur reset password).

### Depag (Master Unit/Bagian Penerima Magang)
CRUD unit kerja: list, insert, update, delete (dicegah bila masih dipakai di transaksi/persyaratan), upload gambar unit.

### Persyaratan
CRUD syarat pendaftaran per unit (`id_depag`): list, insert, update, delete.

### Pengumuman
CRUD pengumuman: list, insert, update, delete.
> **Bug diketahui**: method `update` memanggil `$this->model_umum` yang tidak pernah di-load — seharusnya `model_pengumuman`. Update kemungkinan gagal/error.

### Type_user
CRUD tipe/role user: list, insert, update, delete (dicegah bila masih dipakai user).

### User
Manajemen akun: list (siswa hanya lihat milik sendiri), insert, update, update password, delete (dicegah bila masih punya transaksi), upload foto profil.

### Transaksi (Inti Pendaftaran Magang)
Controller terbesar. Mengelola seluruh siklus pendaftaran:
- List dengan berbagai filter status: pending, inactive, accept, reject.
- Insert pendaftaran baru (biodata, institusi, jadwal, upload dokumen PDF ganda + foto).
- Cek duplikasi data pendaftaran.
- Update data, update status (approve/reject), generate nomor badge otomatis.
- Upload ulang dokumen per slot (`doc1`–`doc7`) dan foto.
- Cetak kartu tanda peserta (ID card) dalam format Excel.
- Delete data beserta file terkait.

> **Bug diketahui**: beberapa key array pada `updateDoc1`–`updateDoc7` dan `updateFoto` punya trailing space pada nama kolom (`'doc_1 '`, `'Foto '`), kemungkinan menyebabkan update tidak mengenai kolom yang benar.

### Laporan
- `data()` — list laporan, simpan filter tahun, cetak rekap seluruh data peserta magang ke Excel.
- `index()` — placeholder (fitur generate Excel lama sudah dikomentari/tidak aktif).

### Setting
Pengaturan sistem: email pengirim, password email, nama manager, dan mode maintenance on/off.

### Tutorial
Halaman panduan/tutorial penggunaan sistem, menampilkan data persyaratan & unit terkait. Bisa diedit deskripsinya oleh admin.

## 5. Helper / Model Umum

`Model_all_function.php` — kumpulan fungsi utilitas lintas modul (bukan CRUD generik):
- `getTypeUser()` — ambil nama tipe user.
- `customidCreator()` — generate ID user otomatis (format `USR00001`).
- `customNoDoc()` — generate nomor dokumen/badge (format `001/I/SDM/2026`).
- `sql2date()` / `date2sql()` — konversi format tanggal.
- `num2RomBulan()` / `num2Bulan()` — konversi angka bulan ke romawi/nama bulan Indonesia.

CRUD generik untuk tiap entitas ada di model masing-masing (`Model_depag`, `Model_transaksi`, dst).

## 6. Struktur Views

Satu sub-folder per modul: `home`, `login`, `register`, `forget`, `email`, `depag`, `persyaratan`, `pengumuman`, `type_user`, `user`, `transaksi`, `laporan`, `setting`, `tutorial`, `errors` (bawaan CI). Komponen bersama: `header.php`, `footer.php`, `notif.php`, `maintenance.php`.

## 7. Pola Routing Controller

Hampir semua controller CRUD mengikuti pola yang sama: method `index()` redirect ke `data/list`, lalu method `data()` sebagai multiplexer berdasarkan segmen URI ke-3 (`list`, `insert`, `update`, `delete`, `getField...`/`getData...` untuk AJAX JSON).

## 8. Known Issues (untuk perbaikan ke depan)

1. `Pengumuman::data()` → update memakai model yang salah/tidak di-load (`model_umum`).
2. `Transaksi` → trailing space pada beberapa key kolom di `updateDoc1`–`7` dan `updateFoto`.
3. Kredensial email di `Email.php` hardcoded di source code (sebaiknya dipindah ke config/env).
