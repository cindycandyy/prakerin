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
                <ul style="font-size:12px; color:var(--abu-900); margin:0; padding-left:18px; line-height:1.9;">
                    <li>Terdaftar aktif sebagai siswa/mahasiswa di institusi pengirim, dengan kondisi sehat jasmani dan rohani.</li>
                    <li>Minimal nilai rapor rata-rata 7.00 (siswa) atau IPK 3.00 (mahasiswa), serta melampirkan transkrip/rapor semester terakhir.</li>
                    <li>Mengajukan minimal beberapa hari sebelum tanggal mulai magang (lihat tanggal paling awal pada formulir pendaftaran).</li>
                    <li>Durasi magang/penelitian minimal 30 hari.</li>
                    <li>Melengkapi 6 berkas PDF (surat pengantar, proposal, transkrip, kartu asuransi, kartu pelajar, surat pernyataan) dan 1 foto JPG/PNG.</li>
                    <li>Bagi peserta yang membawa kendaraan pribadi, wajib memiliki SIM (Surat Izin Mengemudi) yang masih berlaku.</li>
                    <li>Bersedia disiplin dan mematuhi seluruh peraturan yang berlaku selama masa magang/penelitian.</li>
                    <li>Data yang diajukan adalah data asli dan dapat dipertanggungjawabkan.</li>
                </ul>
                <div style="background:var(--hijau-100); color:var(--hijau-800); border-radius:10px; padding:12px 14px; margin-top:14px; font-size:12px; line-height:1.7;">
                    Proses peninjauan pengajuan tidak dapat dipastikan waktunya. Disarankan mengajukan pendaftaran minimal 2 bulan sebelum tanggal mulai magang/penelitian yang direncanakan.
                </div>
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
                <div class="panel-head"><h2>Syarat khusus per departemen</h2></div>
                <p class="panel-sub">Klik salah satu departemen untuk melihat jurusan yang dipersyaratkan.</p>
                <?php
                $syarat_khusus = array(
                    'Riset & Pengembangan' => array(
                        'deskripsi' => 'Bertanggung jawab atas perencanaan dan pengendalian operasional, riset dan pengembangan, perizinan, serta desain untuk mendukung inovasi dan pengembangan produk perusahaan.',
                        'posisi' => array(array(
                            'jurusan' => array('Kimia Analis', 'Kimia Industri', 'Pertanian', 'HPT/Agroekoteknologi', 'Distribusi dan Pengembangan Pasar'),
                            'catatan' => array('Semua Jurusan'),
                        )),
                    ),
                    'Produksi' => array(
                        'deskripsi' => 'Bertanggung jawab atas proses produksi pestisida, pupuk, produk kimia, dan produk hayati/bioindustri, termasuk enjiniring, inspeksi, pengendalian mutu, dan K3LH.',
                        'posisi' => array(array(
                            'jurusan' => array('Analis Kimia', 'Kimia Industri', 'Pertanian', 'Teknik'),
                            'catatan' => array('Semua Jurusan'),
                        )),
                    ),
                    'Komersil' => array(
                        'deskripsi' => 'Bertanggung jawab atas pemasaran dan penjualan produk perusahaan, pengembangan pasar, promosi, dan layanan pelanggan di seluruh wilayah Indonesia.',
                        'posisi' => array(
                            array('nama' => 'Departemen Penjualan 1', 'jurusan' => array('Administrasi Perkantoran')),
                            array('nama' => 'Penjualan Wilayah Jawa Tengah', 'ket' => 'Magang di bidang pemasaran, ditempatkan di wilayah Jawa Tengah. Diutamakan laki-laki.', 'jurusan' => array('Ekonomi Pertanian & Agrobisnis', 'Pertanian'), 'catatan' => array('Semua Jurusan')),
                            array('nama' => 'Penjualan Wilayah Jawa Timur', 'ket' => 'Magang di bidang pemasaran, ditempatkan di wilayah Jawa Timur. Diutamakan laki-laki.', 'jurusan' => array('Ekonomi Pertanian & Agrobisnis', 'Pertanian'), 'catatan' => array('Semua Jurusan')),
                            array('nama' => 'Penjualan Wilayah Jawa Barat', 'ket' => 'Magang di bidang pemasaran, ditempatkan di wilayah Jawa Barat. Diutamakan laki-laki.', 'jurusan' => array('Ekonomi Pertanian & Agrobisnis', 'Pertanian'), 'catatan' => array('Semua Jurusan')),
                            array('nama' => 'Distribusi & Pengembangan Pasar (Daerah)', 'ket' => 'Diutamakan laki-laki.', 'jurusan' => array('Ekonomi Pertanian & Agrobisnis', 'Pertanian', 'Perikanan'), 'catatan' => array('Semua Jurusan')),
                        ),
                    ),
                    'Rantai Pasok' => array(
                        'deskripsi' => 'Bertanggung jawab atas pengadaan barang dan jasa, pergudangan, penyimpanan, dan distribusi produk perusahaan.',
                        'posisi' => array(
                            array('jurusan' => array('Administrasi Perkantoran')),
                            array('nama' => 'PM Penagihan Piutang', 'ket' => 'Penagihan piutang macet distributor.', 'jurusan' => array('Administrasi Perkantoran')),
                        ),
                    ),
                    'SDM & Tata Kelola' => array(
                        'deskripsi' => 'Bertanggung jawab atas pengelolaan sumber daya manusia, pengembangan kompetensi pegawai, hubungan masyarakat, tata kelola, kesekretariatan, hukum dan perizinan, serta pengamanan aset perusahaan.',
                        'posisi' => array(array('catatan' => array('Semua Jurusan'))),
                    ),
                    'Administrasi & Keuangan' => array(
                        'deskripsi' => 'Bertanggung jawab atas pengelolaan keuangan perusahaan, meliputi anggaran, akuntansi, pelaporan keuangan, evaluasi strategis, dan pengelolaan big data.',
                        'posisi' => array(array('jurusan' => array('Akuntansi', 'Administrasi Perkantoran'))),
                    ),
                );
                foreach ($depag as $d) {
                    $sk = isset($syarat_khusus[$d->nama_depag]) ? $syarat_khusus[$d->nama_depag] : null;
                    if (!$sk) { continue; }
                ?>
                <details class="akordeon-item">
                    <summary>
                        <span><?php echo htmlspecialchars($d->nama_depag); ?></span>
                        <?php icon_analisis('chevron-down', 16, 'currentColor'); ?>
                    </summary>
                    <div class="akordeon-isi">
                        <p class="akordeon-desk"><?php echo htmlspecialchars($sk['deskripsi']); ?></p>
                        <?php foreach ($sk['posisi'] as $p) { ?>
                        <div class="akordeon-posisi">
                            <?php if (!empty($p['nama'])) { ?>
                            <div class="akordeon-posisi-nama"><?php echo htmlspecialchars($p['nama']); ?></div>
                            <?php } ?>
                            <?php if (!empty($p['ket'])) { ?>
                            <div class="akordeon-posisi-ket"><?php echo htmlspecialchars($p['ket']); ?></div>
                            <?php } ?>
                            <div>
                                <?php foreach (($p['jurusan'] ?? array()) as $j) { ?>
                                <span class="jurusan-chip"><?php echo htmlspecialchars($j); ?></span>
                                <?php } ?>
                                <?php foreach (($p['catatan'] ?? array()) as $c) { ?>
                                <span class="jurusan-catatan"><?php echo htmlspecialchars($c); ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </details>
                <?php } ?>
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
