<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Salin berkas ini menjadi analisis.php lalu sesuaikan dengan server tujuan.
// analisis.php tidak ikut masuk ke repositori (lihat .gitignore).

// Saklar modul: false menyembunyikan menu dan mematikan semua hook modul analisis,
// sehingga E-Prakerin lama berjalan persis seperti sebelum modul ini ada.
$config['analisis_aktif'] = false;

// Alamat layanan analisis Python (ditambahkan Tahap 3).
$config['analisis_layanan_url'] = 'http://127.0.0.1:8001';
$config['analisis_layanan_api_key'] = '';
$config['analisis_layanan_timeout_detik'] = 60;

$config['analisis_antrean_percobaan_maks'] = 3;

$config['analisis_llm_aktif'] = false;
$config['analisis_llm_penyedia'] = 'lokal';

$config['analisis_storage_surat'] = FCPATH . '../storage/analisis/surat';
$config['analisis_surat_mesin'] = 'libreoffice';
