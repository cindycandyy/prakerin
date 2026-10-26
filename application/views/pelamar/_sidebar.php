<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Partial sidebar sisi pelamar (SPESIFIKASI_PELAMAR.md B2/B9) -- sama kelas/warna
// dengan sidebar HR (_sidebar.php di views/analisis), menu sendiri.
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
        <div class="menu-label">RUANG PELAMAR</div>
        <a class="nav-item <?php echo $halaman_aktif === 'beranda' ? 'active' : ''; ?>" href="<?php echo base_url(); ?>pelamar/beranda">
            <?php icon_analisis('layout-dashboard', 18); ?> Beranda
        </a>
        <a class="nav-item <?php echo $halaman_aktif === 'daftar' ? 'active' : ''; ?>" href="<?php echo base_url(); ?>pelamar/daftar">
            <?php icon_analisis('sparkles', 18); ?> Daftar magang
        </a>
        <a class="nav-item <?php echo $halaman_aktif === 'pengajuan' ? 'active' : ''; ?>" href="<?php echo base_url(); ?>pelamar/pengajuan">
            <?php icon_analisis('clipboard-list', 18); ?> Pengajuan saya
        </a>
        <a class="nav-item <?php echo $halaman_aktif === 'panduan' ? 'active' : ''; ?>" href="<?php echo base_url(); ?>pelamar/panduan">
            <?php icon_analisis('info', 18); ?> Syarat &amp; panduan
        </a>
    </div>

    <div class="sidebar-note">
        <?php icon_analisis('shield-check', 18); ?>
        <strong>Data Anda aman</strong>
        <p>Hanya Anda dan HR yang dapat melihat pengajuan ini.</p>
    </div>

    <div>
        <a class="nav-item <?php echo $halaman_aktif === 'akun' ? 'active' : ''; ?>" href="<?php echo base_url(); ?>pelamar/akun">
            <?php icon_analisis('user', 18); ?> Akun saya
        </a>
        <a class="nav-item" href="<?php echo base_url(); ?>login/sistem/logout">
            <?php icon_analisis('log-out', 18); ?> Keluar
        </a>
    </div>
</aside>
