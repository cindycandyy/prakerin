<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Salin berkas ini menjadi email_smtp.php lalu isi kredensial asli.
// email_smtp.php tidak ikut masuk ke repositori (lihat .gitignore).

$config['smtp_from_email'] = 'ganti@gmail.com';
$config['smtp_host']       = 'smtp.gmail.com';
$config['smtp_user']       = 'ganti@gmail.com';
$config['smtp_pass']       = 'ganti-password-aplikasi';
$config['smtp_port']       = 587;
