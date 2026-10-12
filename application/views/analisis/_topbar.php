<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Partial topbar bersama. Variabel: $breadcrumb_akhir (teks tebal terakhir),
// $nama_user (opsional, default "Admin HR").
$breadcrumb_akhir = isset($breadcrumb_akhir) ? $breadcrumb_akhir : 'Dashboard';
?>
<header class="topbar">
    <div class="breadcrumb">
        <span class="muted">E-Prakerin</span>
        <span class="sep">/</span>
        <span class="current"><?php echo $breadcrumb_akhir; ?></span>
    </div>
    <div class="topbar-right">
        <span class="badge">Data ilustrasi</span>
        <div class="notif">
            <?php icon_analisis('bell', 20); ?>
            <span class="badge badge-warn">2</span>
        </div>
        <div class="divider-v"></div>
        <img class="avatar" src="<?php echo base_url(); ?>assets/images/avatar-default.png" alt="">
        <div>
            <div class="profil-nama"><?php echo htmlspecialchars(!empty($nama_user) ? $nama_user : 'Admin HR'); ?></div>
            <div class="profil-peran">Admin HR</div>
        </div>
        <?php icon_analisis('chevron-down', 14); ?>
    </div>
</header>
