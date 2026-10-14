<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Syarat &amp; panduan — E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,500,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <?php include 'pelamar/_style.php'; ?>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'panduan'; include APPPATH . 'views/pelamar/_sidebar.php'; ?>
    <div class="main">
        <?php $breadcrumb_akhir = 'Syarat & panduan'; include APPPATH . 'views/pelamar/_topbar.php'; ?>
        <main class="content">
            <div class="page-head"><div><h1>Syarat &amp; panduan</h1><p>Baca sebelum mengajukan magang/penelitian.</p></div></div>

            <div class="panel">
                <div class="panel-head"><h2>Syarat umum</h2></div>
                <ul style="font-size:12px; color:var(--abu-900); margin:0; padding-left:18px; line-height:1.8;">
                    <li>Terdaftar aktif sebagai siswa/mahasiswa di institusi pengirim.</li>
                    <li>Mengajukan minimal beberapa hari sebelum tanggal mulai magang (lihat tanggal paling awal di formulir pendaftaran).</li>
                    <li>Durasi magang/penelitian 30–180 hari.</li>
                    <li>Melengkapi 6 berkas PDF (surat pengantar, proposal, transkrip, kartu asuransi, kartu pelajar, surat pernyataan) dan 1 foto JPG.</li>
                    <li>Data yang diajukan adalah data asli dan dapat dipertanggungjawabkan.</li>
                </ul>
            </div>

            <div class="panel">
                <div class="panel-head"><h2>Ketersediaan tempat per departemen</h2></div>
                <p class="panel-sub">Informasi, bukan jaminan diterima — keputusan akhir dan penempatan divisi ditentukan HR.</p>
                <div class="tabel-scroll">
                    <table class="riwayat">
                        <thead><tr><th>Departemen</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($depag as $d) {
                            $info = $d->sisa === null ? array('Belum diatur', 'status-tidakaktif') : ($d->sisa <= 0 ? array('Penuh', 'status-ditolak') : ($d->sisa <= 2 ? array('Terbatas', 'status-pending') : array('Tersedia', 'status-diterima')));
                        ?>
                        <tr><td><?php echo htmlspecialchars($d->nama_depag); ?></td><td><span class="status-pill <?php echo $info[1]; ?>"><?php echo $info[0]; ?></span></td></tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
