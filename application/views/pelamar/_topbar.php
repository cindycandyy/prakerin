<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Partial topbar sisi pelamar. Variabel: $breadcrumb_akhir, $nama_user.
// Notifikasi difilter untuk='pelamar' AND id_pelamar=SES_LOGIN (B7/C8) -- tidak
// pernah menampilkan notifikasi HR (nama pelamar lain, skor, dst).
$breadcrumb_akhir = isset($breadcrumb_akhir) ? $breadcrumb_akhir : 'Beranda';

$CI = &get_instance();
$CI->load->model('model_prk_notifikasi');
$id_user_notif = $CI->session->userdata('SES_ID');
$id_pelamar_notif = $CI->session->userdata('SES_LOGIN');
$jumlah_belum_dibaca = $CI->model_prk_notifikasi->hitungBelumDibacaPelamar($id_user_notif, $id_pelamar_notif);
$daftar_notif = $CI->model_prk_notifikasi->listTerbaruPelamar($id_user_notif, $id_pelamar_notif);
?>
<header class="topbar">
    <button type="button" class="hamburger" aria-label="Buka menu" onclick="document.querySelector('.layout').classList.toggle('sidebar-terbuka')">
        <?php icon_analisis('menu', 20); ?>
    </button>
    <div class="breadcrumb">
        <span class="muted">E-Prakerin</span>
        <span class="sep">/</span>
        <span class="current"><?php echo $breadcrumb_akhir; ?></span>
    </div>
    <div class="topbar-right">
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
                   onclick="fetch('<?php echo base_url(); ?>pelamar/notifikasi_baca', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'id_notifikasi=<?php echo (int) $n->id; ?>'})">
                    <div class="dropdown-notif-judul"><?php echo htmlspecialchars($n->judul); ?></div>
                    <div class="dropdown-notif-isi"><?php echo htmlspecialchars($n->isi); ?></div>
                </a>
                <?php } ?>
            </div>
        </div>
        <div class="divider-v"></div>
        <img class="avatar" src="<?php echo base_url(); ?>assets/images/avatar-default.png" alt="">
        <div>
            <div class="profil-nama"><?php echo htmlspecialchars(!empty($nama_user) ? $nama_user : 'Pelamar'); ?></div>
            <div class="profil-peran">Pelamar</div>
        </div>
    </div>
</header>
