<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Partial topbar bersama. Variabel: $breadcrumb_akhir (teks tebal terakhir),
// $nama_user (opsional, default "Admin HR").
$breadcrumb_akhir = isset($breadcrumb_akhir) ? $breadcrumb_akhir : 'Dashboard';

$CI = &get_instance();
$CI->load->model('model_prk_notifikasi');
$id_user_notif = $CI->session->userdata('SES_ID');
$jumlah_belum_dibaca = $CI->model_prk_notifikasi->hitungBelumDibaca($id_user_notif);
$daftar_notif = $CI->model_prk_notifikasi->listTerbaru($id_user_notif);
?>
<header class="topbar">
    <div class="breadcrumb">
        <span class="muted">E-Prakerin</span>
        <span class="sep">/</span>
        <span class="current"><?php echo $breadcrumb_akhir; ?></span>
    </div>
    <div class="topbar-right">
        <span class="badge">Data ilustrasi</span>
        <div class="notif" style="position:relative; cursor:pointer;" onclick="document.getElementById('dropdown-notif').classList.toggle('tampil')">
            <?php icon_analisis('bell', 20); ?>
            <?php if ($jumlah_belum_dibaca > 0) { ?>
            <span class="badge badge-warn"><?php echo $jumlah_belum_dibaca; ?></span>
            <?php } ?>
            <div id="dropdown-notif" class="dropdown-notif">
                <div class="dropdown-notif-head">Notifikasi</div>
                <?php if (empty($daftar_notif)) { ?>
                <div class="dropdown-notif-kosong">Belum ada notifikasi.</div>
                <?php } ?>
                <?php foreach ($daftar_notif as $n) { ?>
                <a class="dropdown-notif-item <?php echo $n->sudah_dibaca ? '' : 'belum-dibaca'; ?>" href="<?php echo htmlspecialchars($n->tautan); ?>"
                   onclick="fetch('<?php echo base_url(); ?>analisis/notifikasi_baca', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'id_notifikasi=<?php echo (int) $n->id; ?>'})">
                    <div class="dropdown-notif-judul"><?php echo htmlspecialchars($n->judul); ?></div>
                    <div class="dropdown-notif-isi"><?php echo htmlspecialchars($n->isi); ?></div>
                </a>
                <?php } ?>
            </div>
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
