<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pelamar — Analisis Penempatan | E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <style>
        .back-link { display: flex; align-items: center; gap: 8px; color: var(--hijau-700); font-size: 13px; font-weight: 700; }
        .page-head .status-sub { margin: 0; }

        /* Profil pelamar */
        .profil-card { display: flex; gap: 24px; }
        .profil-foto { width: 64px; height: 64px; border-radius: 100px; object-fit: cover; background: var(--abu-200); flex-shrink: 0; }
        .profil-info { flex: 1.2; }
        .profil-nama-besar { font-size: 16px; font-weight: 700; margin: 0 0 3px; color: var(--abu-900); }
        .profil-institusi { color: var(--abu-600); font-size: 12px; margin: 0 0 2px; }
        .profil-jurusan { color: var(--abu-600); font-size: 12px; margin: 0 0 8px; }
        .profil-status-pill {
            display: inline-block; background: var(--hijau-100); color: var(--hijau-700); font-size: 11px;
            font-weight: 600; padding: 3px 9px; border-radius: 6px; margin-bottom: 6px;
        }
        .profil-meta { font-size: 11px; color: var(--abu-600); margin-top: 6px; }
        .profil-kontak { flex: 1.1; font-size: 12px; display: flex; flex-direction: column; gap: 12px; }
        .profil-kontak .lbl { color: var(--abu-600); font-size: 11px; display: block; margin-bottom: 2px; }
        .profil-kontak .val { color: var(--abu-900); display: block; }
        .profil-side {
            background: var(--hijau-100); border-radius: 10px; padding: 14px; font-size: 11px;
            color: var(--abu-900); width: 230px; flex-shrink: 0; display: flex; flex-direction: column; gap: 10px;
        }
        .profil-side .lbl { color: var(--hijau-700); font-weight: 600; margin-bottom: 2px; }
        .profil-side .val { color: var(--abu-900); font-size: 12px; }
        .profil-side .catatan { color: var(--hijau-700); font-size: 10px; line-height: 1.5; }
        .profil-footer {
            border-top: 1px solid var(--abu-100); margin-top: 18px; padding-top: 14px; font-size: 12px;
        }
        .profil-footer-lbl { font-weight: 600; color: var(--abu-900); margin-bottom: 4px; }
        .sosmed-list { color: var(--abu-600); font-size: 12px; }
        .sosmed-item a { color: var(--hijau-700); font-weight: 600; }
        .sosmed-item a:hover { text-decoration: underline; }
        .sosmed-item.kosong { color: var(--abu-400, #a8b3af); }
        .sosmed-sep { color: var(--abu-400, #a8b3af); margin: 0 4px; }

        /* Berkas persyaratan */
        .panel-berkas .panel-head { margin-bottom: 4px; }
        .panel-berkas .panel-head h2 { font-size: 16px; font-weight: 700; color: var(--abu-900); }
        .panel-berkas .panel-sub { font-size: 12px; color: var(--abu-600); margin: 0 0 14px; }
        .berkas-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; align-items: stretch; }
        .berkas-card { border: 1px solid var(--abu-200); border-radius: 10px; padding: 14px; background: #fff; display: flex; flex-direction: column; }
        .berkas-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
        .berkas-judul { display: flex; align-items: center; gap: 8px; }
        .berkas-icon {
            width: 30px; height: 30px; border-radius: 7px; background: var(--abu-100);
            display: flex; align-items: center; justify-content: center; color: var(--abu-600); flex-shrink: 0;
        }
        .berkas-nama { font-size: 12px; font-weight: 600; color: var(--abu-900); }
        .berkas-check { color: var(--hijau-700); flex-shrink: 0; }
        .berkas-kosong-ikon { color: var(--abu-400, #a8b3af); flex-shrink: 0; }
        .berkas-file { font-size: 11px; color: var(--abu-600); margin: 0 0 4px; }
        .berkas-sub { font-size: 10px; color: var(--hijau-700); font-weight: 600; margin-bottom: 4px; }
        .berkas-aksi { display: flex; gap: 8px; margin-top: auto; padding-top: 10px; }
        .btn-mini {
            border: 1px solid var(--abu-200); background: #fff; border-radius: 6px; padding: 0 10px;
            font-size: 11px; font-weight: 600; cursor: pointer; color: var(--abu-900); flex: 1;
            height: 32px; box-sizing: border-box; display: inline-flex; align-items: center; justify-content: center;
        }
        .btn-mini:hover { background: var(--hijau-100); border-color: var(--hijau-100); color: var(--hijau-700); }

        /* Analisis */
        .panel-analisis .panel-head { margin-bottom: 4px; }
        .panel-analisis .panel-head h2 { font-size: 16px; font-weight: 700; color: var(--abu-900); }
        .panel-analisis .panel-sub { font-size: 12px; color: var(--abu-600); margin: 0 0 16px; }
        .btn-analisis-ulang {
            background: #fff; border: 1px solid var(--abu-200); color: var(--hijau-700); border-radius: 8px;
            padding: 6px 12px; font-size: 11px; font-weight: 600; cursor: pointer; white-space: nowrap;
        }
        .btn-analisis-ulang:hover { background: var(--hijau-100); border-color: var(--hijau-100); }
        .skill-tags { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 16px; }
        .tag { background: var(--hijau-100); color: var(--hijau-700); border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 600; }
        .analisis-label { font-size: 11px; color: var(--abu-600); font-weight: 600; margin-bottom: 8px; letter-spacing: .02em; }
        .analisis-col { margin-bottom: 16px; }
        .analisis-col .lbl { font-size: 11px; color: var(--abu-600); margin-bottom: 4px; }
        .analisis-col .val { font-size: 12px; line-height: 1.6; }
        .analisis-note {
            background: var(--hijau-100); border-radius: 8px; padding: 12px 14px; font-size: 11px;
            color: var(--hijau-700); line-height: 1.6; margin-top: 4px;
        }
        .faktor-row { margin-bottom: 16px; }
        .faktor-row:last-child { margin-bottom: 0; }
        .faktor-top { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 6px; }
        .faktor-top .pct { font-weight: 600; }
        .faktor-track { background: var(--abu-100); border-radius: 6px; height: 7px; overflow: hidden; }
        .faktor-fill { height: 100%; background: var(--hijau-700); border-radius: 6px; }

        /* Rekomendasi */
        .rekom-card { border: 2px solid var(--hijau-700); border-radius: 12px; padding: 20px; width: 100%; }
        .rekom-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; }
        .rekom-skor { display: flex; align-items: baseline; gap: 4px; }
        .rekom-skor .num { font-size: 28px; font-weight: 700; color: var(--hijau-700); }
        .rekom-skor .denom { font-size: 11px; color: var(--abu-600); }
        .rekom-divisi { font-size: 18px; font-weight: 600; margin: 0 0 2px; }
        .rekom-dept { font-size: 12px; color: var(--abu-600); margin: 0 0 12px; }
        .rekom-alasan { font-size: 12px; margin: 10px 0; }
        .rekom-sub-lbl { font-size: 11px; font-weight: 600; margin-bottom: 2px; }
        .rekom-sub-lbl.hijau { color: var(--hijau-700); }
        .rekom-sub-lbl.kuning { color: var(--kuning-teks); }
        .rekom-sub-val { font-size: 12px; margin-bottom: 10px; }
        .rekom-footer {
            background: var(--hijau-100); border-radius: 8px; padding: 10px 12px; margin-top: 8px;
        }
        .rekom-footer strong { display: block; font-size: 12px; color: var(--hijau-700); }
        .rekom-footer span { font-size: 10px; color: var(--abu-600); }

        .divisi-penuh {
            background: #fdebc8; border: 1px solid #f3cd87; border-radius: 8px; padding: 12px 14px; display: flex; gap: 10px;
            align-items: center; font-size: 12px; color: #8a5209; font-weight: 500;
        }
        .divisi-penuh svg { flex-shrink: 0; }

        /* Form keputusan */
        .keputusan-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .field-label { font-size: 12px; font-weight: 600; margin-bottom: 6px; display: flex; align-items: center; gap: 6px; }
        .field-wajib {
            display: inline-flex; align-items: center; background: var(--merah-100); color: var(--merah-700);
            font-size: 9px; font-weight: 700; letter-spacing: .02em; padding: 2px 6px; border-radius: 4px;
            text-transform: uppercase;
        }
        .field-select, .field-textarea {
            width: 100%; border: 1px solid var(--abu-200); border-radius: 8px; padding: 10px 12px; font-size: 12px;
            font-family: inherit; color: var(--abu-900); box-sizing: border-box;
        }
        .field-textarea {
            min-height: 80px; resize: vertical; background: #fbfcfc; transition: border-color .15s ease, background .15s ease, box-shadow .15s ease;
        }
        .field-textarea:hover { border-color: var(--abu-400, #a8b3af); }
        .field-textarea:focus {
            outline: none; border-color: var(--hijau-700); background: #fff;
            box-shadow: 0 0 0 3px var(--hijau-100);
        }
        .field-textarea::placeholder { color: var(--abu-400, #a8b3af); }
        .field-note { font-size: 11px; color: var(--abu-600); margin-top: 6px; }
        .periode-usulan {
            display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600;
            color: var(--hijau-700); background: var(--hijau-100); padding: 6px 10px; border-radius: 6px; margin-top: 10px;
        }
        .keputusan-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; border-top: 1px solid var(--abu-100); padding-top: 16px; }
        .btn-primary { background: var(--hijau-700); color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn-outline { background: #fff; border: 1px solid var(--abu-200); color: var(--merah-700); border-radius: 8px; padding: 10px 18px; font-size: 13px; font-weight: 600; cursor: pointer; }

        /* Tom Select: samakan dengan dropdown di halaman Pelamar */
        .ts-wrapper.single .ts-control {
            border: 1px solid var(--abu-200); border-radius: 8px; background: #fff; color: var(--abu-900);
            min-height: 38px; padding: 8px 28px 8px 12px; box-shadow: none; font-size: 12px; font-family: inherit;
        }
        .ts-wrapper.single.input-active .ts-control, .ts-wrapper.focus .ts-control { border-color: var(--hijau-700); box-shadow: none; }
        .ts-wrapper .ts-control:after { border-color: var(--abu-600) transparent transparent transparent; }
        .ts-wrapper.dropdown-active .ts-control:after { border-color: transparent transparent var(--abu-600) transparent; }
        .ts-dropdown {
            border: 1px solid var(--abu-200); border-radius: 8px; font-family: inherit; font-size: 12px;
            overflow: hidden; background: #fff; box-shadow: 0 8px 20px rgba(23,45,39,0.12); z-index: 50;
        }
        .ts-dropdown .ts-dropdown-content { background: #fff; }
        .ts-dropdown .option { padding: 8px 12px; background: #fff; color: var(--abu-900); white-space: normal; }
        .ts-dropdown .option.active { background: var(--hijau-100); color: var(--hijau-700); }
        .ts-dropdown .option[data-selected] { background: var(--hijau-700); color: #fff; }
    </style>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'pelamar'; include APPPATH . 'views/analisis/_sidebar.php'; ?>

    <div class="main">
        <?php $breadcrumb_akhir = 'Pelamar / Detail pelamar'; include APPPATH . 'views/analisis/_topbar.php'; ?>

        <main class="content">
            <a class="back-link" href="<?php echo base_url(); ?>analisis/pelamar">
                <?php icon_analisis('arrow-left', 16); ?> Kembali ke daftar pelamar
            </a>

            <?php $CI = &get_instance(); ?>
            <?php if ($CI->session->flashdata('sukses')) { ?>
            <div class="profil-note" style="margin:0;"><?php echo htmlspecialchars($CI->session->flashdata('sukses')); ?></div>
            <?php } ?>
            <?php /* Pesan error (termasuk "Alasan penolakan wajib diisi...") disembunyikan sementara atas
                     permintaan — aktifkan lagi dengan menghapus komentar ini bila warning submit-gagal
                     perlu ditampilkan lagi. */ ?>
            <?php // if ($CI->session->flashdata('error')) { ?>
            <?php // } ?>

            <div class="page-head">
                <div>
                    <h1>Detail pelamar</h1>
                    <p>Tinjau profil, berkas, dan rekomendasi sebelum menentukan penempatan.</p>
                </div>
                <?php
                $statusBadge = array(
                    'Accept' => array('badge-green', 'Diterima'),
                    'Rejected' => array('badge', 'Ditolak'),
                    'Inactive' => array('badge', 'Tidak aktif'),
                );
                list($badgeClass, $badgeLabel) = isset($statusBadge[$pelamar->status]) ? $statusBadge[$pelamar->status] : array('badge-warn', 'Pending · menunggu keputusan');
                ?>
                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($badgeLabel); ?></span>
            </div>

            <!-- Profil pelamar -->
            <div class="panel" style="width:100%">
                <div class="profil-card">
                    <img class="profil-foto" src="<?php echo base_url(); ?>assets/images/avatar-default.png" alt="">
                    <div class="profil-info">
                        <p class="profil-nama-besar"><?php echo htmlspecialchars($pelamar->nama); ?></p>
                        <p class="profil-institusi"><?php echo htmlspecialchars($pelamar->institusi); ?></p>
                        <p class="profil-jurusan"><?php echo htmlspecialchars($pelamar->pendidikan); ?> · peminatan <?php echo htmlspecialchars($pelamar->jurusan); ?></p>
                        <span class="profil-status-pill"><?php echo !empty($rekomendasi) ? 'Analisis siap' : 'Analisis belum tersedia'; ?></span>
                        <div class="profil-meta">Kode pendaftaran <?php echo htmlspecialchars($pelamar->no_pkl); ?></div>
                    </div>
                    <div class="profil-kontak">
                        <div><span class="lbl">NIM / nomor pelajar</span><span class="val"><?php echo htmlspecialchars($pelamar->user_id); ?></span></div>
                        <div><span class="lbl">Email</span><span class="val"><?php echo htmlspecialchars($pelamar->email); ?></span></div>
                        <div><span class="lbl">Nomor telepon</span><span class="val"><?php echo htmlspecialchars($pelamar->telp); ?></span></div>
                    </div>
                    <div class="profil-side">
                        <div>
                            <div class="lbl">Periode usulan yang diajukan</div>
                            <div class="val"><?php echo $pelamar->tgl_mulai ? date('d M Y', strtotime($pelamar->tgl_mulai)) : '-'; ?> – <?php echo $pelamar->tgl_selesai ? date('d M Y', strtotime($pelamar->tgl_selesai)) : '-'; ?></div>
                        </div>
                        <div>
                            <div class="lbl">Departemen yang diminta pelamar</div>
                            <div class="val"><?php echo htmlspecialchars($pelamar->nama_depag ? $pelamar->nama_depag : '-'); ?></div>
                        </div>
                        <div class="catatan">Permintaan pelamar ditampilkan terpisah; tidak menjadi faktor skor rekomendasi.</div>
                    </div>
                </div>
                <div class="profil-footer">
                    <?php
                    // Daftar contoh/ilustrasi (Tahap 1). Platform yang tidak diisi
                    // pelamar tetap ditampilkan tapi abu-abu, bukan dihilangkan.
                    // url_dasar menyusun link profil asli dari handle yang tersimpan.
                    $sosmed = array(
                        'LinkedIn' => array('akun' => 'linkedin.com/in/ayu-putri-lestari', 'url_dasar' => 'https://'),
                        'Instagram' => array('akun' => '@ayu.lestari', 'url_dasar' => 'https://instagram.com/'),
                        'TikTok' => array('akun' => '', 'url_dasar' => 'https://tiktok.com/@'),
                        'Twitter' => array('akun' => '', 'url_dasar' => 'https://twitter.com/'),
                    );
                    ?>
                    <div class="profil-footer-lbl">Media sosial</div>
                    <div class="sosmed-list">
                        <?php
                        $i = 0;
                        foreach ($sosmed as $platform => $s) {
                            $akun = $s['akun'];
                            $handle = ltrim($akun, '@');
                            if ($i > 0) { echo '<span class="sosmed-sep">·</span>'; }
                            $i++;
                        ?>
                            <span class="sosmed-item <?php echo $akun ? '' : 'kosong'; ?>">
                                <?php if ($akun) { ?>
                                    <a href="<?php echo htmlspecialchars($s['url_dasar'] . $handle); ?>" target="_blank" rel="noopener"><?php echo $platform; ?></a>
                                <?php } else { ?>
                                    <?php echo $platform; ?>
                                <?php } ?>
                            </span>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Berkas persyaratan -->
            <div class="panel panel-berkas" style="width:100%">
                <div class="panel-head">
                    <h2>Berkas persyaratan</h2>
                    <a class="btn-primary" style="text-decoration:none; display:inline-flex; align-items:center;" href="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $id_transaksi; ?>/berkas_unduh">Unduh semua berkas</a>
                </div>
                <?php
                $berkas = array(
                    array('Surat pengantar', $pelamar->doc_1, ''),
                    array('Proposal magang', $pelamar->doc_2, 'Sumber analisis otomatis'),
                    array('Transkrip nilai', $pelamar->doc_3, 'Sumber analisis otomatis'),
                    array('Foto', $pelamar->foto, ''),
                    array('Kartu asuransi', $pelamar->doc_5, ''),
                    array('Kartu pelajar', $pelamar->doc_4, ''),
                    array('Surat pernyataan', $pelamar->doc_6, ''),
                );
                $jumlah_lengkap = count(array_filter($berkas, function ($b) { return !empty($b[1]); }));
                ?>
                <div class="panel-sub"><?php echo $jumlah_lengkap; ?>/7 persyaratan lengkap · diunggah pelamar melalui E-Prakerin</div>
                <div class="berkas-grid">
                    <?php
                    foreach ($berkas as $b) {
                        list($nama, $file, $sub) = $b;
                        $ada = !empty($file);
                        $path_file = $ada ? FCPATH . 'gambar/' . $file : null;
                        $ekstensi = $ada ? strtoupper(pathinfo($file, PATHINFO_EXTENSION)) : '';
                        $adalah_gambar = in_array(strtolower($ekstensi), array('jpg', 'jpeg', 'png'));
                        $ukuran = ($ada && is_file($path_file)) ? round(filesize($path_file) / 1024) . ' KB' : null;
                    ?>
                    <div class="berkas-card">
                        <div class="berkas-top">
                            <div class="berkas-judul">
                                <span class="berkas-icon"><?php icon_analisis($adalah_gambar ? 'image' : 'file-text', 16); ?></span>
                                <span class="berkas-nama"><?php echo $nama; ?></span>
                            </div>
                            <?php if ($ada) { ?>
                            <span class="berkas-check"><?php icon_analisis('check-circle', 16); ?></span>
                            <?php } else { ?>
                            <span class="berkas-kosong-ikon">&mdash;</span>
                            <?php } ?>
                        </div>
                        <div class="berkas-file">
                            <?php if ($ada) { ?>
                                <?php echo htmlspecialchars($file); ?><?php echo $ukuran ? ' · ' . $ukuran : ''; ?><?php echo $ekstensi ? ' · ' . $ekstensi : ''; ?>
                            <?php } else { ?>
                                Belum diunggah
                            <?php } ?>
                        </div>
                        <?php if ($sub) { ?><div class="berkas-sub"><?php echo $sub; ?></div><?php } ?>
                        <?php if ($ada) { ?>
                        <div class="berkas-aksi">
                            <a class="btn-mini" href="<?php echo base_url() . 'gambar/' . rawurlencode($file); ?>" target="_blank" rel="noopener">Lihat</a>
                            <a class="btn-mini" href="<?php echo base_url() . 'gambar/' . rawurlencode($file); ?>" download>Unduh</a>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Analisis dan rekomendasi -->
            <div class="two-col">
                <div class="panel panel-analisis">
                    <div class="panel-head">
                        <h2>Ringkasan hasil analisis</h2>
                        <button type="button" class="btn-analisis-ulang">Analisis ulang</button>
                    </div>
                    <div class="panel-sub">Rangkuman dari proposal magang dan transkrip nilai</div>
                    <div class="analisis-label">KETERAMPILAN &amp; TOOLS</div>
                    <div class="skill-tags">
                        <span class="tag">Titrasi</span>
                        <span class="tag">UV-Vis</span>
                        <span class="tag">Preparasi sampel</span>
                        <span class="tag">Microsoft Excel</span>
                        <span class="tag">Olah data</span>
                    </div>
                    <div class="analisis-col">
                        <div class="lbl">Nilai relevan</div>
                        <div class="val">Analisis Instrumen: A<br>Kimia Analitik: A−<br>Statprobab: B+</div>
                    </div>
                    <div class="analisis-col">
                        <div class="lbl">Minat dalam proposal</div>
                        <div class="val">Pengujian produk jadi, analisis senyawa aktif, evaluasi formulasi pestisida.</div>
                    </div>
                    <div class="analisis-note">
                        Jika hasil ekstraksi kurang tepat, koreksi profil bisa dilakukan. Sistem menderivasi ulang HR menentukan.
                    </div>
                </div>

                <div class="panel panel-analisis">
                    <div class="panel-head">
                        <h2>Bagaimana skor dihitung?</h2>
                    </div>
                    <div class="panel-sub">Skor kecocokan (100) per keputusan otomatis</div>
                    <div class="faktor-row">
                        <div class="faktor-top"><span>Spesialisasi / peminatan</span><span class="pct">45%</span></div>
                        <div class="faktor-track"><div class="faktor-fill" style="width:90%"></div></div>
                    </div>
                    <div class="faktor-row">
                        <div class="faktor-top"><span>Keterampilan &amp; tools</span><span class="pct">25%</span></div>
                        <div class="faktor-track"><div class="faktor-fill" style="width:80%"></div></div>
                    </div>
                    <div class="faktor-row">
                        <div class="faktor-top"><span>Proposal &amp; minat</span><span class="pct">20%</span></div>
                        <div class="faktor-track"><div class="faktor-fill" style="width:95%"></div></div>
                    </div>
                    <div class="faktor-row">
                        <div class="faktor-top"><span>Ketersediaan kuota</span><span class="pct">10%</span></div>
                        <div class="faktor-track"><div class="faktor-fill" style="width:50%"></div></div>
                    </div>
                    <div class="analisis-note" style="margin-top:14px;">
                        Keyakinan tinggi ≥75%, sedang 55–74%, &lt;55% · Perlu tinjau manual.<br>
                        Departemen yang diminta tidak dihitung skor; hasil rekomendasi sistem untuk penempatan HR.
                    </div>
                </div>
            </div>

            <div style="width:100%">
                <div class="panel-head" style="margin-bottom:6px;">
                    <div>
                        <h2 style="font-size:18px; margin-bottom:3px;">Rekomendasi penempatan terbaik</h2>
                        <div class="panel-sub" style="margin:0;">Divisi paling sesuai berdasarkan profil pelamar</div>
                    </div>
                    <span class="badge badge-green">Saran sistem, keputusan HR</span>
                </div>

                <?php if (!empty($rekomendasi)) {
                    $top = $rekomendasi[0];
                ?>
                <div class="rekom-card">
                    <div class="rekom-top">
                        <span class="badge badge-green">#1 · paling sesuai</span>
                        <div class="rekom-skor"><span class="num"><?php echo number_format($top->skor, 1, ',', '.'); ?></span><span class="denom">/100</span></div>
                    </div>
                    <p class="rekom-divisi"><?php echo htmlspecialchars($top->nama_divisi); ?></p>
                    <p class="rekom-dept"><?php echo htmlspecialchars($top->nama_departemen); ?></p>
                    <span class="badge badge-green">Keyakinan <?php echo htmlspecialchars(ucfirst($top->keyakinan)); ?></span>
                    <p class="rekom-alasan"><?php echo htmlspecialchars($top->alasan); ?></p>
                    <div class="rekom-sub-lbl hijau">Kekuatan</div>
                    <div class="rekom-sub-val"><?php echo htmlspecialchars($top->kekuatan); ?></div>
                    <div class="rekom-sub-lbl kuning">Perlu dikembangkan</div>
                    <div class="rekom-sub-val"><?php echo htmlspecialchars($top->kekurangan); ?></div>
                </div>
                <?php } else { ?>
                <div class="divisi-penuh">
                    <?php icon_analisis('info', 18, '#8a5209'); ?>
                    <span>Hasil rekomendasi belum tersedia. Analisis otomatis belum dijalankan untuk pelamar ini.</span>
                </div>
                <?php } ?>
            </div>

            <!-- Form keputusan HR -->
            <div class="panel" style="width:100%">
                <div class="panel-head">
                    <h2>Tentukan keputusan dan penempatan</h2>
                </div>
                <div class="panel-sub">Pilih divisi berdasarkan rekomendasi. Tidak memengaruhi penempatan kepada departemen.</div>
                <form method="post" action="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $id_transaksi; ?>/terima">
                    <div class="keputusan-grid">
                        <div>
                            <label class="field-label">Divisi pilihan HR</label>
                            <select class="field-select" id="pilih-divisi" name="id_divisi">
                                <?php
                                $peringkat_per_divisi = array();
                                foreach ($rekomendasi as $r) {
                                    $peringkat_per_divisi[(int) $r->id_divisi] = $r;
                                }
                                $departemen_sekarang = null;
                                foreach ($semua_divisi as $d) {
                                    if ($departemen_sekarang !== $d->nama_departemen) {
                                        if ($departemen_sekarang !== null) { echo '</optgroup>'; }
                                        echo '<optgroup label="' . htmlspecialchars($d->nama_departemen) . '">';
                                        $departemen_sekarang = $d->nama_departemen;
                                    }
                                    $r = isset($peringkat_per_divisi[(int) $d->id]) ? $peringkat_per_divisi[(int) $d->id] : null;
                                    $label = htmlspecialchars($d->nama);
                                    if ($r) {
                                        $label = '#' . (int) $r->peringkat . ' · ' . $label . ' · skor ' . number_format($r->skor, 1, ',', '.');
                                    }
                                    echo '<option value="' . (int) $d->id . '">' . $label . '</option>';
                                }
                                if ($departemen_sekarang !== null) { echo '</optgroup>'; }
                                ?>
                            </select>
                            <p class="field-note">HR dapat memilih divisi apa pun yang masih tersedia kuotanya. Jika berbeda dari rekomendasi sistem, alasan wajib diisi dan dicatat untuk pembelajaran.</p>
                            <div class="periode-usulan">
                                <?php icon_analisis('calendar-days', 14); ?>
                                Periode usulan: <?php echo $pelamar->tgl_mulai ? date('d M Y', strtotime($pelamar->tgl_mulai)) : '-'; ?> – <?php echo $pelamar->tgl_selesai ? date('d M Y', strtotime($pelamar->tgl_selesai)) : '-'; ?>
                            </div>
                        </div>
                        <div>
                            <label class="field-label">Alasan jika memilih divisi berbeda dari rekomendasi sistem <span class="field-wajib">Wajib jika berbeda</span></label>
                            <textarea class="field-textarea" name="alasan_ubah" placeholder="Kosongkan jika mengikuti rekomendasi sistem. Isi alasan jika HR memilih divisi lain."></textarea>
                            <p class="field-note">Alasan wajib diisi untuk pilihan yang berbeda dari rekomendasi sistem untuk pembelajaran.</p>
                        </div>
                        <div>
                            <label class="field-label">Alasan penolakan <span class="field-wajib">Wajib bila menolak</span></label>
                            <textarea class="field-textarea" name="alasan" placeholder="Minimal 5 karakter, wajib diisi untuk menolak pelamar."></textarea>
                        </div>
                    </div>
                    <p class="field-note">Kuota dicek ulang saat penempatan. Pending otomatis ditolak jika H-3 atau sisa tanggal mulai terlewat.</p>
                    <div class="keputusan-footer">
                        <button type="submit" formaction="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $id_transaksi; ?>/tolak" formnovalidate class="btn-outline">Tolak pelamar</button>
                        <button type="submit" class="btn-primary">Terima &amp; tetapkan penempatan</button>
                    </div>
                </form>
            </div>

            <?php if (!empty($kontrak)) { ?>
            <!-- Kontrak magang -->
            <div class="panel" style="width:100%">
                <div class="panel-head">
                    <h2>Kontrak magang</h2>
                </div>
                <div class="panel-sub">
                    Urutan ke-<?php echo (int) $kontrak->urutan; ?> ·
                    <?php echo date('d M Y', strtotime($kontrak->tanggal_mulai)); ?> –
                    <?php echo date('d M Y', strtotime($kontrak->tanggal_selesai)); ?> ·
                    status <strong><?php echo htmlspecialchars(str_replace('_', ' ', $kontrak->status)); ?></strong>
                </div>

                <?php if ($kontrak->status !== 'selesai' && $kontrak->status !== 'diakhiri_awal') { ?>
                <div class="keputusan-grid">
                    <form method="post" action="<?php echo base_url(); ?>analisis/kontrak/<?php echo (int) $kontrak->id; ?>/perpanjang">
                        <label class="field-label">Perpanjang sampai tanggal</label>
                        <input class="field-select" type="date" name="tanggal_selesai_baru" min="<?php echo date('Y-m-d', strtotime($kontrak->tanggal_selesai . ' +1 day')); ?>">
                        <div class="keputusan-footer">
                            <button type="submit" class="btn-primary">Perpanjang kontrak</button>
                        </div>
                    </form>
                    <form method="post" action="<?php echo base_url(); ?>analisis/kontrak/<?php echo (int) $kontrak->id; ?>/akhiri">
                        <label class="field-label">Alasan akhiri lebih awal *</label>
                        <textarea class="field-textarea" name="alasan" placeholder="Minimal 5 karakter, wajib diisi."></textarea>
                        <div class="keputusan-footer">
                            <button type="submit" class="btn-outline">Akhiri lebih awal</button>
                        </div>
                    </form>
                </div>
                <?php } ?>
            </div>
            <?php } ?>

            <?php if (strtolower($pelamar->status) === 'accept') { ?>
            <!-- Surat balasan -->
            <div class="panel" style="width:100%">
                <div class="panel-head">
                    <h2>Surat balasan</h2>
                </div>
                <div class="panel-sub">Isi, pratinjau, dan kirim surat balasan penerimaan PKL untuk pelamar ini.</div>
                <form method="post" action="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $id_transaksi; ?>/surat_buat">
                    <div class="keputusan-footer">
                        <button type="submit" class="btn-primary">Buat / buka surat balasan</button>
                    </div>
                </form>
            </div>
            <?php } ?>
        </main>
    </div>
</div>
<script>
    var elPilihDivisi = document.getElementById('pilih-divisi');
    if (elPilihDivisi) {
        new TomSelect(elPilihDivisi, { controlInput: null });
    }
</script>
</body>
</html>
