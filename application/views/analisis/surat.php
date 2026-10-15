<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Balasan - Analisis Penempatan | E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-form.css" rel="stylesheet">
    <style>
        .back-link { display: flex; align-items: center; gap: 8px; color: var(--hijau-700); font-size: 12px; }
        .field-label { font-size: 12px; font-weight: 600; margin-bottom: 6px; display: block; }
        .field-select, .field-textarea, .field-input {
            width: 100%; border: 1px solid var(--abu-200); border-radius: 8px; padding: 9px 12px; font-size: 12px; font-family: inherit;
        }
        .field-textarea { min-height: 70px; resize: vertical; }
        .field-note { font-size: 11px; color: var(--abu-600); margin: 4px 0 0; }
        .btn-primary { background: var(--hijau-700); color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn-outline { background: #fff; border: 1px solid var(--abu-200); color: var(--abu-900); border-radius: 8px; padding: 10px 18px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
        .surat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        .surat-grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 16px; }
        .peserta-list { font-size: 12px; margin: 10px 0; }
        .peserta-list li { margin-bottom: 4px; }
        .status-pill { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 100px; }
        .status-draf { background: var(--abu-100, #eee); color: var(--abu-600); }
        .status-terkirim { background: var(--hijau-100); color: var(--hijau-700); }
        .status-diganti { background: #fdecea; color: #a4231d; }
        .panel { background: #fff; border: 1px solid var(--abu-100); border-radius: 12px; padding: 18px; margin-bottom: 16px; }
        .panel-head { display: flex; justify-content: space-between; align-items: center; }
        .footer-aksi { display: flex; gap: 10px; margin-top: 16px; }
    </style>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'pelamar'; include APPPATH . 'views/analisis/_sidebar.php'; ?>

    <div class="main">
        <?php $breadcrumb_akhir = 'Pelamar / Surat balasan'; include APPPATH . 'views/analisis/_topbar.php'; ?>

        <main class="content">
            <?php if (!empty($peserta)) { ?>
            <a class="back-link" href="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $peserta[0]->id; ?>">
                <?php icon_analisis('arrow-left', 16); ?> Kembali ke detail pelamar
            </a>
            <?php } ?>

            <?php $CI = &get_instance(); ?>
            <?php if ($CI->session->flashdata('sukses')) { ?>
            <div class="profil-note" style="margin:12px 0;"><?php echo htmlspecialchars($CI->session->flashdata('sukses')); ?></div>
            <?php } ?>
            <?php if ($CI->session->flashdata('error')) { ?>
            <div class="profil-note" style="margin:12px 0; background:#fdecea; color:#a4231d;"><?php echo htmlspecialchars($CI->session->flashdata('error')); ?></div>
            <?php } ?>

            <div class="panel">
                <div class="panel-head">
                    <h2>Surat balasan #<?php echo (int) $surat->id; ?></h2>
                    <span class="status-pill status-<?php echo htmlspecialchars($surat->status); ?>"><?php echo htmlspecialchars(ucfirst($surat->status)); ?></span>
                </div>

                <label class="field-label" style="margin-top:14px;">Peserta dalam surat ini</label>
                <ul class="peserta-list">
                    <?php foreach ($peserta as $p) { ?>
                    <li><?php echo htmlspecialchars($p->nama); ?> - <?php echo htmlspecialchars($p->institusi); ?> (<?php echo htmlspecialchars($p->jurusan); ?>)</li>
                    <?php } ?>
                </ul>

                <?php if ($surat->status === 'draf' && !empty($kandidat_gabung)) { ?>
                <label class="field-label" style="margin-top:14px;">Gabungkan pelamar lain dari instansi yang sama</label>
                <p class="field-note" style="margin-bottom:8px;">Sudah diterima, belum punya surat balasan lain.</p>
                <form method="post" action="<?php echo base_url(); ?>analisis/surat/<?php echo (int) $surat->id; ?>/gabung" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                    <select class="field-select" name="id_transaksi" style="width:auto;">
                        <?php foreach ($kandidat_gabung as $k) { ?>
                        <option value="<?php echo (int) $k->id; ?>"><?php echo htmlspecialchars($k->nama); ?></option>
                        <?php } ?>
                    </select>
                    <button type="submit" class="btn-outline">Gabungkan</button>
                </form>
                <?php } ?>
            </div>

            <?php if ($surat->status === 'draf') { ?>
            <div class="panel">
                <div class="panel-head"><h2>Isian surat</h2></div>
                <form method="post" action="<?php echo base_url(); ?>analisis/surat/<?php echo (int) $surat->id; ?>/simpan">
                    <div class="surat-grid">
                        <div>
                            <label class="field-label">Nomor urut surat (5 digit) *</label>
                            <input class="field-input" type="text" name="nomor_urut" maxlength="5" pattern="\d{5}" placeholder="<?php echo htmlspecialchars($saran_nomor); ?>" value="<?php echo $surat->nomor_surat ? htmlspecialchars(explode('/', $surat->nomor_surat)[0]) : ''; ?>">
                            <p class="field-note">Saran nomor urut berikutnya tahun ini: <?php echo htmlspecialchars($saran_nomor); ?>. Nomor lengkap disusun otomatis.</p>
                        </div>
                        <div>
                            <label class="field-label">Tanggal surat</label>
                            <input class="field-input" type="date" name="tanggal_surat" value="<?php echo htmlspecialchars($surat->tanggal_surat ?: date('Y-m-d')); ?>">
                        </div>
                    </div>
                    <div class="surat-grid">
                        <div>
                            <label class="field-label">Nomor surat permohonan instansi</label>
                            <input class="field-input" type="text" name="rujukan_nomor" value="<?php echo htmlspecialchars($surat->rujukan_nomor ?: ''); ?>">
                        </div>
                        <div>
                            <label class="field-label">Tanggal surat permohonan</label>
                            <input class="field-input" type="date" name="rujukan_tanggal" value="<?php echo htmlspecialchars($surat->rujukan_tanggal ?: ''); ?>">
                        </div>
                    </div>
                    <label class="field-label">Jadwal pembekalan</label>
                    <div class="surat-grid-3">
                        <div>
                            <input class="field-input" type="date" name="pembekalan_tanggal" value="<?php echo htmlspecialchars($surat->pembekalan_tanggal ?: ''); ?>">
                        </div>
                        <div>
                            <input class="field-input" type="text" name="pembekalan_pukul" placeholder="08.00 WIB - Selesai" value="<?php echo htmlspecialchars($surat->pembekalan_pukul ?: ''); ?>">
                        </div>
                        <div>
                            <input class="field-input" type="text" name="pembekalan_tempat" placeholder="Tempat pembekalan" value="<?php echo htmlspecialchars($surat->pembekalan_tempat ?: ''); ?>">
                        </div>
                    </div>
                    <label class="field-label" style="margin-top:10px;">Keterangan pembekalan (opsional)</label>
                    <textarea class="field-textarea" name="pembekalan_keterangan"><?php echo htmlspecialchars($surat->pembekalan_keterangan ?: ''); ?></textarea>

                    <label class="field-label" style="margin-top:10px;">Kepada (opsional, kosongkan untuk teks bawaan)</label>
                    <textarea class="field-textarea" name="kepada_override" placeholder="Kepada bawaan disusun otomatis dari instansi &amp; jenjang peserta pertama."><?php echo htmlspecialchars($surat->kepada_override ?: ''); ?></textarea>

                    <div class="footer-aksi">
                        <button type="submit" class="btn-outline">Simpan draf</button>
                    </div>
                </form>

                <div class="footer-aksi">
                    <a class="btn-outline" target="_blank" href="<?php echo base_url(); ?>analisis/surat/<?php echo (int) $surat->id; ?>/pratinjau">Pratinjau PDF</a>
                    <form method="post" action="<?php echo base_url(); ?>analisis/surat/<?php echo (int) $surat->id; ?>/kirim" onsubmit="return confirm('Surat tidak bisa diubah setelah dikirim. Lanjutkan?');">
                        <button type="submit" class="btn-primary">Kirim surat</button>
                    </form>
                </div>
            </div>
            <?php } else { ?>
            <div class="panel">
                <div class="panel-head"><h2>Surat sudah <?php echo htmlspecialchars($surat->status); ?></h2></div>
                <p class="field-note">Dikirim pada <?php echo $surat->dikirim_pada ? date('d M Y H:i', strtotime($surat->dikirim_pada)) : '-'; ?>. Isian terkunci, buat surat pengganti untuk koreksi.</p>
                <div class="footer-aksi">
                    <?php if ($surat->status === 'terkirim') { ?>
                    <a class="btn-outline" href="<?php echo base_url(); ?>analisis/surat/<?php echo (int) $surat->id; ?>/unduh">Unduh PDF</a>
                    <form method="post" action="<?php echo base_url(); ?>analisis/surat/<?php echo (int) $surat->id; ?>/pengganti" onsubmit="return confirm('Buat surat pengganti? Surat ini akan ditandai diganti.');">
                        <button type="submit" class="btn-primary">Buat surat pengganti</button>
                    </form>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
        </main>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/analisis-form.js"></script>
</body>
</html>
