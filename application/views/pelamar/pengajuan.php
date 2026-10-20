<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengajuan saya - E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,500,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-form.css" rel="stylesheet">
    <?php include APPPATH . 'views/pelamar/_style.php'; ?>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'pengajuan'; include APPPATH . 'views/pelamar/_sidebar.php'; ?>
    <div class="main">
        <?php $breadcrumb_akhir = 'Pengajuan saya'; include APPPATH . 'views/pelamar/_topbar.php'; ?>
        <main class="content">
            <div class="page-head">
                <div><h1>Pengajuan Saya</h1><p>Seluruh pengajuan magang atau penelitian yang telah dikirim.</p></div>
                <a class="btn-primary" href="<?php echo base_url(); ?>pelamar/daftar">Ajukan magang baru</a>
            </div>

            <div class="panel">
                <?php if (empty($daftar)) { ?>
                <div class="empty-state">
                    <?php icon_analisis('inbox', 28); ?>
                    <p>Belum ada pengajuan.</p>
                </div>
                <?php } else { ?>
                <div class="tabel-scroll">
                    <table class="riwayat">
                        <thead><tr><th>Tanggal</th><th>Program</th><th>Departemen</th><th>Periode</th><th>Status</th><th>Aksi</th></tr></thead>
                        <tbody>
                            <?php
                            $peta_status = array('pending' => array('Menunggu keputusan', 'status-pending'),
                                'accept' => array('Diterima', 'status-diterima'), 'rejected' => array('Tidak dapat diproses', 'status-ditolak'),
                                'inactive' => array('Selesai', 'status-tidakaktif'), 'cancelled' => array('Dibatalkan', 'status-tidakaktif'));
                            foreach ($daftar as $p) {
                                $s = isset($peta_status[strtolower($p->status)]) ? $peta_status[strtolower($p->status)] : array($p->status, 'status-tidakaktif');
                            ?>
                            <tr>
                                <td><?php echo tanggal_indo($p->tgl_mulai); ?></td>
                                <td><?php echo htmlspecialchars($p->kegiatan); ?></td>
                                <td><?php echo htmlspecialchars($p->nama_depag ?: '-'); ?></td>
                                <td><?php echo tanggal_indo($p->tgl_mulai); ?> – <?php echo tanggal_indo($p->tgl_selesai); ?></td>
                                <td><span class="status-pill <?php echo $s[1]; ?>"><?php echo $s[0]; ?></span></td>
                                <td><a class="btn-outline" style="padding:6px 12px; font-size:11px;" href="<?php echo base_url(); ?>pelamar/pengajuan/<?php echo (int) $p->id; ?>">Detail</a></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
            </div>
        </main>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/analisis-form.js"></script>
</body>
</html>
