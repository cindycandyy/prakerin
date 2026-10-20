<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Syarat &amp; panduan - E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,500,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-form.css" rel="stylesheet">
    <?php include APPPATH . 'views/pelamar/_style.php'; ?>
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
                <div class="panel-head"><h2>Format proposal (wajib dibaca)</h2></div>
                <p class="panel-sub">Proposal dibaca otomatis oleh sistem untuk menilai kecocokan divisi, pakai struktur bab di bawah ini dengan judul bab persis seperti tertulis, supaya terbaca benar.</p>
                <ul style="font-size:12px; color:var(--abu-900); margin:0; padding-left:18px; line-height:1.9;">
                    <li><strong>Halaman sampul</strong>: cantumkan "Proposal [judul topik PKL/penelitian]" pada baris pertama.</li>
                    <li><strong>Bab/bagian "Latar Belakang"</strong>: alasan pemilihan bidang atau divisi tersebut.</li>
                    <li><strong>Bab/bagian "Tujuan"</strong>: tujuan PKL/penelitian secara spesifik (bukan "Tujuan Sekolah").</li>
                    <li><strong>Bab/bagian "Ruang Lingkup" atau "Bidang PKL"/"Bidang Kerja"</strong>: jelaskan bidang kerja yang diminati, serta sebutkan nama tools, software, alat, atau metode yang dikuasai secara eksplisit (mis. "AutoCAD", "Python", "HPLC", "SAP"), bukan hanya kategori umum seperti "komputer".</li>
                    <li><strong>Bab/bagian "Objek PKL"</strong> (dapat dicantumkan pada tabel surat permohonan): nama bagian, divisi, atau proses yang ingin dipelajari.</li>
                    <li><strong>Bab/bagian "Mata Kuliah"</strong>: daftar mata kuliah atau pelajaran yang relevan dengan bidang yang dituju, satu per baris.</li>
                </ul>
                <p class="field-note" style="margin-top:10px;">Skor kecocokan dihitung berdasarkan empat faktor: kesesuaian jurusan, tools/nilai relevan yang disebutkan, topik proposal, dan ketersediaan kuota divisi. Semakin spesifik proposal yang disusun, semakin akurat rekomendasi yang dihasilkan untuk HR.</p>
            </div>

            <div class="panel">
                <div class="panel-head"><h2>Ketersediaan tempat per departemen</h2></div>
                <p class="panel-sub">Informasi, bukan jaminan diterima. Keputusan akhir dan penempatan divisi ditentukan HR.</p>
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/analisis-form.js"></script>
</body>
</html>
