<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Partial sidebar bersama untuk semua halaman Analisis Penempatan (standalone).
// Dipakai lewat include dengan variabel $halaman_aktif berisi salah satu:
// 'dashboard', 'pelamar', 'kalender', 'kuota', 'riwayat', 'pengaturan'.
$halaman_aktif = isset($halaman_aktif) ? $halaman_aktif : '';
?>
<div class="sidebar-overlay" onclick="document.querySelector('.layout').classList.remove('sidebar-terbuka')"></div>
<aside class="sidebar">
    <button type="button" class="sidebar-tutup" aria-label="Tutup menu" onclick="document.querySelector('.layout').classList.remove('sidebar-terbuka')">
        <?php icon_analisis('x', 20, '#fff'); ?>
    </button>
    <div>
        <div class="brand">
            <div class="brand-logo"><?php icon_analisis('sprout', 22, '#fff'); ?></div>
            <span class="brand-name">E-Prakerin</span>
        </div>
        <div class="brand-sub">E-Prakerin · PT Petrosida Gresik</div>
    </div>

    <div>
        <div class="menu-label">RUANG KERJA HR</div>
        <a class="nav-item <?php echo $halaman_aktif === 'dashboard' ? 'active' : ''; ?>" href="<?php echo base_url(); ?>analisis/dashboard">
            <?php icon_analisis('layout-dashboard', 18); ?> Dashboard
        </a>
        <a class="nav-item <?php echo $halaman_aktif === 'pelamar' ? 'active' : ''; ?>" href="<?php echo base_url(); ?>analisis/pelamar">
            <?php icon_analisis('users-round', 18); ?> Pelamar
        </a>
        <a class="nav-item <?php echo $halaman_aktif === 'kalender' ? 'active' : ''; ?>" href="<?php echo base_url(); ?>analisis/kalender">
            <?php icon_analisis('calendar-days', 18); ?> Kalender magang
        </a>
        <a class="nav-item <?php echo $halaman_aktif === 'kuota' ? 'active' : ''; ?>" href="<?php echo base_url(); ?>analisis/kuota">
            <?php icon_analisis('chart-no-axes-combined', 18); ?> Kuota penempatan
        </a>
        <a class="nav-item <?php echo $halaman_aktif === 'riwayat' ? 'active' : ''; ?>" href="<?php echo base_url(); ?>analisis/riwayat">
            <?php icon_analisis('clipboard-list', 18); ?> Riwayat keputusan
        </a>
    </div>

    <div class="sidebar-note">
        <?php icon_analisis('shield-check', 18); ?>
        <strong>Satu data, dari E-Prakerin</strong>
        <p>Data pelamar tersinkron. HR tidak perlu mengisi ulang.</p>
    </div>

    <div>
        <a class="nav-item <?php echo $halaman_aktif === 'pengaturan' ? 'active' : ''; ?>" href="<?php echo base_url(); ?>analisis/pengaturan">
            <?php icon_analisis('settings', 18); ?> Pengaturan
        </a>
        <a class="nav-item" href="#">
            <?php icon_analisis('info', 18); ?> Panduan Analisis Penempatan
        </a>
    </div>

    <div class="sidebar-footer">
        <strong>MODE CONTOH</strong>
        <p>Nama, foto, dan angka adalah ilustrasi. Bukan data langsung.</p>
    </div>
</aside>
