<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Beranda — E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,500,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <?php include 'pelamar/_style.php'; ?>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'beranda'; include APPPATH . 'views/pelamar/_sidebar.php'; ?>
    <div class="main">
        <?php $breadcrumb_akhir = 'Beranda'; include APPPATH . 'views/pelamar/_topbar.php'; ?>
        <main class="content">
            <div class="page-head">
                <div>
                    <h1>Halo, <?php echo htmlspecialchars($nama_user); ?></h1>
                    <p>Pantau status pengajuan magang/penelitianmu di sini.</p>
                </div>
                <?php if (!$terbaru || in_array(strtolower($terbaru->status), array('rejected', 'inactive', 'cancelled'))) { ?>
                <a class="btn-primary" href="<?php echo base_url(); ?>pelamar/daftar">Ajukan magang baru</a>
                <?php } ?>
            </div>

            <?php $CI = &get_instance(); if ($CI->session->flashdata('sukses')) { ?>
            <div class="profil-note" style="margin:0; background:var(--hijau-100); color:var(--hijau-800); border-radius:10px; padding:12px 14px; font-size:12px;"><?php echo htmlspecialchars($CI->session->flashdata('sukses')); ?></div>
            <?php } ?>

            <?php if (!$terbaru) { ?>
            <div class="panel">
                <div class="empty-state">
                    <?php icon_analisis('inbox', 28); ?>
                    <p>Kamu belum punya pengajuan magang/penelitian.</p>
                    <a class="btn-primary" href="<?php echo base_url(); ?>pelamar/daftar">Ajukan sekarang</a>
                </div>
            </div>
            <?php } else {
                $peta_status = array('pending' => array('Menunggu keputusan', 'status-pending'),
                    'accept' => array('Diterima', 'status-diterima'), 'rejected' => array('Tidak dapat diproses', 'status-ditolak'),
                    'inactive' => array('Selesai', 'status-tidakaktif'), 'cancelled' => array('Dibatalkan', 'status-tidakaktif'));
                $s = $peta_status[strtolower($terbaru->status)];
                $jumlah_berkas = 0;
                foreach (array('doc_1', 'doc_2', 'doc_3', 'doc_5', 'doc_6', 'doc_7', 'foto') as $slot) { if (!empty($terbaru->{$slot})) { $jumlah_berkas++; } }
            ?>
            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-top"><span class="kpi-label">Status pengajuan</span><span class="kpi-icon"><?php icon_analisis('clipboard-list', 18); ?></span></div>
                    <div class="kpi-value" style="font-size:18px;"><span class="status-pill <?php echo $s[1]; ?>"><?php echo $s[0]; ?></span></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-top"><span class="kpi-label">Berkas lengkap</span><span class="kpi-icon"><?php icon_analisis('file-text', 18); ?></span></div>
                    <div class="kpi-value"><?php echo $jumlah_berkas; ?>/7</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-top"><span class="kpi-label">Jadwal mulai</span><span class="kpi-icon"><?php icon_analisis('calendar-days', 18); ?></span></div>
                    <div class="kpi-value" style="font-size:16px;"><?php echo tanggal_indo($terbaru->tgl_mulai); ?></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-top"><span class="kpi-label">Surat balasan</span><span class="kpi-icon"><?php icon_analisis('file-text', 18); ?></span></div>
                    <div class="kpi-value" style="font-size:14px;">
                        <?php if ($surat) { ?>
                        <a class="btn-outline" style="padding:6px 12px; font-size:11px;" href="<?php echo base_url(); ?>pelamar/surat/<?php echo (int) $terbaru->id; ?>/unduh">Unduh</a>
                        <?php } else { ?>
                        <span style="color:var(--abu-600); font-size:12px;">Belum tersedia</span>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head"><h2>Langkah berikutnya</h2></div>
                <?php
                $status_l = strtolower($terbaru->status);
                if ($status_l === 'pending') { echo '<p class="field-note">Berkas kamu sedang ditinjau HR. Pastikan semua dokumen sudah lengkap dan benar — kamu masih bisa mengubah data atau mengganti berkas selama status masih menunggu.</p>';
                } else if ($status_l === 'accept') { echo '<p class="field-note">Selamat! Siapkan surat pernyataan kalau belum diunggah, dan pantau jadwal magangmu di halaman pengajuan.</p>';
                } else if ($status_l === 'rejected') { echo '<p class="field-note">Pengajuan ini belum dapat diproses. Kamu bisa mengajukan kembali dengan data terbaru.</p>';
                } else { echo '<p class="field-note">Riwayat pengajuanmu bisa dilihat kapan saja di halaman Pengajuan saya.</p>'; }
                ?>
                <a class="btn-outline" href="<?php echo base_url(); ?>pelamar/pengajuan/<?php echo (int) $terbaru->id; ?>">Lihat detail pengajuan</a>
            </div>
            <?php } ?>
        </main>
    </div>
</div>
</body>
</html>
