<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Pelamar — Analisis Penempatan | E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <style>
        .back-link { display: flex; align-items: center; gap: 8px; color: var(--hijau-700); font-size: 12px; }
        .page-head .status-sub { margin: 0; }

        /* Profil pelamar */
        .profil-card { display: flex; gap: 20px; }
        .profil-foto { width: 72px; height: 72px; border-radius: 100px; object-fit: cover; background: var(--abu-200); flex-shrink: 0; }
        .profil-info { flex: 1.3; }
        .profil-nama-besar { font-size: 18px; font-weight: 700; margin: 0 0 2px; }
        .profil-institusi { color: var(--abu-600); font-size: 12px; }
        .profil-jurusan { color: var(--abu-600); font-size: 12px; }
        .profil-meta { display: flex; gap: 6px; align-items: center; font-size: 11px; color: var(--abu-600); margin-top: 8px; }
        .profil-kontak { flex: 1; font-size: 12px; display: flex; flex-direction: column; gap: 6px; }
        .profil-kontak .lbl { color: var(--abu-600); font-size: 11px; }
        .profil-side {
            background: var(--hijau-100); border-radius: 8px; padding: 12px; font-size: 11px;
            color: var(--hijau-700); width: 220px; flex-shrink: 0;
        }
        .profil-side .lbl { color: var(--abu-600); margin-top: 8px; }
        .profil-side strong { display: block; color: var(--abu-900); font-size: 12px; font-weight: 600; }
        .profil-note {
            background: var(--hijau-100); border-radius: 8px; padding: 10px 12px; font-size: 11px;
            color: var(--hijau-700); display: flex; gap: 8px; align-items: flex-start; margin-top: 16px;
        }
        .profil-footer {
            display: flex; justify-content: space-between; align-items: center;
            border-top: 1px solid var(--abu-100); margin-top: 16px; padding-top: 12px; font-size: 11px; color: var(--abu-600);
        }
        .sosmed-list { display: flex; gap: 14px; }
        .sosmed-item { display: flex; align-items: center; gap: 5px; }
        .sosmed-item a { color: var(--hijau-700); font-weight: 600; }
        .sosmed-item.kosong { color: var(--abu-400, #a8b3af); }

        /* Berkas persyaratan */
        .berkas-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .berkas-card { border: 1px solid var(--abu-200); border-radius: 10px; padding: 12px; }
        .berkas-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
        .berkas-nama { font-size: 12px; font-weight: 600; }
        .berkas-file { font-size: 11px; color: var(--abu-600); margin: 2px 0 6px; }
        .berkas-sub { font-size: 10px; color: var(--hijau-700); }
        .berkas-aksi { display: flex; gap: 6px; margin-top: 8px; }
        .btn-mini { border: 1px solid var(--abu-200); background: #fff; border-radius: 6px; padding: 5px 10px; font-size: 11px; cursor: pointer; flex: 1; text-align: center; }
        .berkas-banner {
            background: var(--hijau-100); border-radius: 8px; padding: 10px 12px; font-size: 11px;
            color: var(--hijau-700); margin-top: 16px;
        }

        /* Analisis */
        .skill-tags { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
        .tag { background: var(--hijau-100); color: var(--hijau-700); border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 600; }
        .analisis-col { margin-bottom: 14px; }
        .analisis-col .lbl { font-size: 11px; color: var(--abu-600); margin-bottom: 4px; }
        .analisis-col .val { font-size: 12px; }
        .analisis-note {
            background: var(--hijau-100); border-radius: 8px; padding: 10px 12px; font-size: 11px;
            color: var(--hijau-700); margin-top: 10px;
        }
        .faktor-row { margin-bottom: 12px; }
        .faktor-row:last-child { margin-bottom: 0; }
        .faktor-top { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px; }
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
            background: var(--kuning-bg); border-radius: 8px; padding: 12px 14px; display: flex; gap: 10px;
            align-items: flex-start; font-size: 12px; color: var(--kuning-teks);
        }

        /* Form keputusan */
        .keputusan-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .field-label { font-size: 12px; font-weight: 600; margin-bottom: 6px; display: block; }
        .field-select, .field-textarea {
            width: 100%; border: 1px solid var(--abu-200); border-radius: 8px; padding: 10px 12px; font-size: 12px;
            font-family: inherit; color: var(--abu-900);
        }
        .field-textarea { min-height: 80px; resize: vertical; }
        .field-note { font-size: 11px; color: var(--abu-600); margin-top: 6px; }
        .keputusan-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; border-top: 1px solid var(--abu-100); padding-top: 16px; }
        .btn-primary { background: var(--hijau-700); color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn-outline { background: #fff; border: 1px solid var(--abu-200); color: var(--merah-700); border-radius: 8px; padding: 10px 18px; font-size: 13px; font-weight: 600; cursor: pointer; }
    </style>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'pelamar'; include APPPATH . 'views/analisis/_sidebar.php'; ?>

    <div class="main">
        <?php $breadcrumb_akhir = 'Pelamar / Detail pelamar'; include APPPATH . 'views/analisis/_topbar.php'; ?>

        <main class="content">
            <a class="back-link" href="<?php echo base_url(); ?>analisis/dashboard">
                <?php icon_analisis('arrow-left', 16); ?> Kembali ke daftar pelamar
            </a>

            <?php $CI = &get_instance(); ?>
            <?php if ($CI->session->flashdata('sukses')) { ?>
            <div class="profil-note" style="margin:0;"><?php echo htmlspecialchars($CI->session->flashdata('sukses')); ?></div>
            <?php } ?>
            <?php if ($CI->session->flashdata('error')) { ?>
            <div class="divisi-penuh" style="margin:0;"><?php echo htmlspecialchars($CI->session->flashdata('error')); ?></div>
            <?php } ?>

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
                        <p class="profil-jurusan"><?php echo htmlspecialchars($pelamar->pendidikan); ?> · <?php echo htmlspecialchars($pelamar->jurusan); ?></p>
                        <div class="profil-meta"><?php echo htmlspecialchars($pelamar->no_pkl); ?></div>
                    </div>
                    <div class="profil-kontak">
                        <div><span class="lbl">Email</span><br><?php echo htmlspecialchars($pelamar->email); ?></div>
                        <div><span class="lbl">Nomor telepon</span><br><?php echo htmlspecialchars($pelamar->telp); ?></div>
                    </div>
                    <div class="profil-side">
                        <strong>Periode usulan</strong>
                        <?php echo $pelamar->tgl_mulai ? date('d M Y', strtotime($pelamar->tgl_mulai)) : '-'; ?> – <?php echo $pelamar->tgl_selesai ? date('d M Y', strtotime($pelamar->tgl_selesai)) : '-'; ?>
                        <div class="lbl">Departemen yang diminta pelamar</div>
                        <strong><?php echo htmlspecialchars($pelamar->nama_depag ? $pelamar->nama_depag : '-'); ?></strong>
                        <div style="margin-top:8px;">Penempatan pelamar disimpan terakhir; tidak menjadi faktor dari rekomendasi.</div>
                    </div>
                </div>
                <div class="profil-footer">
                    <?php
                    // Daftar contoh/ilustrasi (Tahap 1). Platform yang tidak diisi
                    // pelamar tetap ditampilkan tapi abu-abu, bukan dihilangkan.
                    $sosmed = array(
                        'LinkedIn' => 'linkedin.com/in/ayu-putri-lestari',
                        'Instagram' => '@ayu.lestari',
                        'TikTok' => '',
                        'Twitter' => '',
                    );
                    ?>
                    <div class="sosmed-list">
                        <?php foreach ($sosmed as $platform => $akun) { ?>
                            <span class="sosmed-item <?php echo $akun ? '' : 'kosong'; ?>">
                                <?php echo $platform; ?>:
                                <?php if ($akun) { ?>
                                    <a href="#"><?php echo htmlspecialchars($akun); ?></a>
                                <?php } else { ?>
                                    belum diisi
                                <?php } ?>
                            </span>
                        <?php } ?>
                    </div>
                    <a href="#" style="color:var(--hijau-700); font-weight:600;">Koreksi profil</a>
                </div>
            </div>

            <!-- Berkas persyaratan -->
            <div class="panel" style="width:100%">
                <div class="panel-head">
                    <h2>Berkas persyaratan</h2>
                    <span class="panel-head link">Unduh semua berkas</span>
                </div>
                <?php
                $berkas = array(
                    array('Surat pengantar', $pelamar->doc_1, ''),
                    array('Proposal magang', $pelamar->doc_2, 'Sumber otomatis analisis'),
                    array('Transkrip nilai', $pelamar->doc_3, 'Sumber otomatis analisis'),
                    array('Kartu pelajar', $pelamar->doc_4, ''),
                    array('Kartu asuransi', $pelamar->doc_5, ''),
                    array('Surat pernyataan', $pelamar->doc_6, ''),
                    array('Foto', $pelamar->foto, ''),
                );
                $jumlah_lengkap = count(array_filter($berkas, function ($b) { return !empty($b[1]); }));
                ?>
                <div class="panel-sub"><?php echo $jumlah_lengkap; ?>/7 persyaratan terunggah · diunggah pelamar melalui E-Prakerin</div>
                <div class="berkas-grid">
                    <?php
                    foreach ($berkas as $b) {
                        list($nama, $file, $sub) = $b;
                        $ada = !empty($file);
                    ?>
                    <div class="berkas-card">
                        <div class="berkas-top">
                            <span class="berkas-nama"><?php echo $nama; ?></span>
                            <span style="color:<?php echo $ada ? 'var(--hijau-700)' : 'var(--abu-400)'; ?>"><?php echo $ada ? '✓' : '—'; ?></span>
                        </div>
                        <div class="berkas-file"><?php echo $ada ? htmlspecialchars($file) : 'Belum diunggah'; ?></div>
                        <?php if ($sub) { ?><div class="berkas-sub"><?php echo $sub; ?></div><?php } ?>
                        <?php if ($ada) { ?>
                        <div class="berkas-aksi">
                            <button class="btn-mini">Lihat</button>
                            <button class="btn-mini">Unduh</button>
                        </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
                <?php if ($jumlah_lengkap === 7) { ?>
                <div class="berkas-banner">
                    ✓ Semua 7 berkas sudah diunggah pelamar.
                </div>
                <?php } ?>
            </div>

            <!-- Analisis dan rekomendasi -->
            <div class="two-col">
                <div class="panel">
                    <div class="panel-head">
                        <h2>Ringkasan hasil analisis</h2>
                        <span class="panel-head link">Analisis ulang</span>
                    </div>
                    <div class="panel-sub">Rangkuman dari proposal magang dan transkrip nilai</div>
                    <div class="lbl" style="font-size:11px; color:var(--abu-600); margin-bottom:6px;">KETERAMPILAN &amp; TOOLS</div>
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

                <div class="panel">
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
                        <div class="panel-sub" style="margin:0;">Divisi paling sesuai berdasarkan profil pelamar · periode <?php echo $pelamar->tgl_mulai ? date('d M Y', strtotime($pelamar->tgl_mulai)) : '-'; ?> – <?php echo $pelamar->tgl_selesai ? date('d M Y', strtotime($pelamar->tgl_selesai)) : '-'; ?></div>
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
                    <?php icon_analisis('info', 18, '#a5690c'); ?>
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
                            <select class="field-select" name="id_divisi">
                                <?php if (!empty($rekomendasi)) { ?>
                                    <?php foreach ($rekomendasi as $r) { ?>
                                    <option value="<?php echo (int) $r->id_divisi; ?>">#<?php echo (int) $r->peringkat; ?> · <?php echo htmlspecialchars($r->nama_divisi); ?> · skor <?php echo number_format($r->skor, 1, ',', '.'); ?></option>
                                    <?php } ?>
                                <?php } else { ?>
                                    <option value="">Belum ada rekomendasi — pilih manual lewat halaman Kuota</option>
                                <?php } ?>
                            </select>
                            <p class="field-note">HR dapat memilih divisi apa pun yang masih tersedia kuotanya. Jika berbeda dari rekomendasi sistem, alasan wajib diisi dan dicatat untuk pembelajaran.</p>
                            <p class="field-note">Periode usulan: <?php echo $pelamar->tgl_mulai ? date('d M Y', strtotime($pelamar->tgl_mulai)) : '-'; ?> – <?php echo $pelamar->tgl_selesai ? date('d M Y', strtotime($pelamar->tgl_selesai)) : '-'; ?></p>
                        </div>
                        <div>
                            <label class="field-label">Alasan jika memilih divisi berbeda dari rekomendasi sistem *</label>
                            <textarea class="field-textarea" name="alasan_ubah" placeholder="Kosongkan jika mengikuti rekomendasi sistem. Isi alasan jika HR memilih divisi lain."></textarea>
                            <p class="field-note">Alasan wajib diisi untuk pilihan yang berbeda dari rekomendasi sistem untuk pembelajaran.</p>
                        </div>
                        <div>
                            <label class="field-label">Alasan penolakan (wajib bila menolak) *</label>
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
        </main>
    </div>
</div>
</body>
</html>
