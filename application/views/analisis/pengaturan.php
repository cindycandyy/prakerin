<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengaturan — Analisis Penempatan | E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,500,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <style>
        .panel-pengaturan .panel-head { margin-bottom: 4px; }
        .panel-pengaturan .panel-head h2 { font-size: 16px; font-weight: 700; color: var(--abu-900); }
        .panel-pengaturan .panel-sub { font-size: 12px; color: var(--abu-600); margin: 0 0 18px; }

        .field-label { font-size: 12px; font-weight: 600; margin-bottom: 6px; display: block; color: var(--abu-900); }
        .field-input {
            width: 100%; border: 1px solid var(--abu-200); border-radius: 8px; padding: 10px 12px; font-size: 12px;
            font-family: inherit; color: var(--abu-900); box-sizing: border-box;
        }
        .field-input:focus { outline: none; border-color: var(--hijau-700); box-shadow: 0 0 0 3px var(--hijau-100); }
        .field-input:disabled { background: var(--abu-100); color: var(--abu-600); cursor: not-allowed; }
        .field-note { font-size: 11px; color: var(--abu-600); margin-top: 6px; }

        .bobot-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 6px; }
        .bobot-total {
            font-size: 12px; font-weight: 600; margin-top: 10px; padding: 10px 12px; border-radius: 8px;
            background: var(--hijau-100); color: var(--hijau-700);
        }
        .bobot-total.salah { background: var(--merah-100); color: var(--merah-700); }

        .pengaturan-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }

        .form-footer {
            display: flex; justify-content: flex-end; align-items: center; margin-top: 24px;
            border-top: 1px solid var(--abu-100); padding-top: 16px;
        }
        .btn-simpan-pengaturan {
            background: var(--hijau-700); color: #fff; border: none; border-radius: 8px;
            padding: 10px 20px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit;
        }

        .ronde-info {
            display: flex; gap: 10px; align-items: flex-start; background: var(--hijau-100);
            border-radius: 10px; padding: 12px 14px; font-size: 12px; color: var(--hijau-800); margin-bottom: 20px;
        }
        .ronde-info svg { flex-shrink: 0; margin-top: 1px; }

        @media (max-width: 900px) {
            .bobot-grid, .pengaturan-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'pengaturan'; include APPPATH . 'views/analisis/_sidebar.php'; ?>

    <div class="main">
        <?php $breadcrumb_akhir = 'Pengaturan'; include APPPATH . 'views/analisis/_topbar.php'; ?>

        <main class="content">
            <div class="page-head">
                <div>
                    <h1>Pengaturan analisis</h1>
                    <p>Atur bobot faktor skor dan ambang pengingat kontrak. Hanya Admin yang bisa mengubah.</p>
                </div>
            </div>

            <?php $CI = &get_instance(); ?>
            <?php if ($CI->session->flashdata('sukses')) { ?>
            <div class="profil-note" style="margin:0 0 16px;background:var(--hijau-100);border-radius:8px;padding:10px 14px;font-size:12px;color:var(--hijau-700);">
                <?php echo htmlspecialchars($CI->session->flashdata('sukses')); ?>
            </div>
            <?php } ?>
            <?php if ($CI->session->flashdata('error')) { ?>
            <div style="margin:0 0 16px;background:var(--merah-100);border-radius:8px;padding:10px 14px;font-size:12px;color:var(--merah-700);">
                <?php echo htmlspecialchars($CI->session->flashdata('error')); ?>
            </div>
            <?php } ?>

            <?php if ($hanya_lihat) { ?>
            <div class="ronde-info">
                <?php icon_analisis('info', 16); ?>
                <span>HR hanya bisa melihat pengaturan ini. Hubungi Admin untuk mengubah bobot atau ambang.</span>
            </div>
            <?php } ?>

            <form method="post" action="<?php echo base_url(); ?>analisis/pengaturan_simpan">
                <div class="panel panel-pengaturan" style="width:100%">
                    <div class="panel-head">
                        <h2>Bobot faktor skor</h2>
                    </div>
                    <div class="panel-sub">Menentukan seberapa besar pengaruh tiap faktor terhadap skor rekomendasi divisi. Total harus 100.</div>
                    <div class="bobot-grid">
                        <div>
                            <label class="field-label">Jurusan &amp; peminatan (F1)</label>
                            <input type="number" min="0" max="100" name="bobot_f1" class="field-input" value="<?php echo (int) $pengaturan['bobot_f1']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                        </div>
                        <div>
                            <label class="field-label">Skill &amp; tools (F2)</label>
                            <input type="number" min="0" max="100" name="bobot_f2" class="field-input" value="<?php echo (int) $pengaturan['bobot_f2']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                        </div>
                        <div>
                            <label class="field-label">Proposal &amp; minat (F3)</label>
                            <input type="number" min="0" max="100" name="bobot_f3" class="field-input" value="<?php echo (int) $pengaturan['bobot_f3']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                        </div>
                        <div>
                            <label class="field-label">Ketersediaan kuota (F4)</label>
                            <input type="number" min="0" max="100" name="bobot_f4" class="field-input" value="<?php echo (int) $pengaturan['bobot_f4']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                        </div>
                    </div>
                    <div class="bobot-total" id="bobot-total">Total: <?php echo (int) $pengaturan['bobot_f1'] + (int) $pengaturan['bobot_f2'] + (int) $pengaturan['bobot_f3'] + (int) $pengaturan['bobot_f4']; ?>/100</div>
                </div>

                <div class="panel panel-pengaturan" style="width:100%; margin-top:20px;">
                    <div class="panel-head">
                        <h2>Ambang keyakinan &amp; notifikasi</h2>
                    </div>
                    <div class="panel-sub">Batas skor untuk label keyakinan, serta jadwal pengingat kontrak dan status pengajuan mepet.</div>
                    <div class="pengaturan-grid">
                        <div>
                            <label class="field-label">Ambang keyakinan tinggi (≥)</label>
                            <input type="number" min="0" max="100" name="ambang_conf_tinggi" class="field-input" value="<?php echo (int) $pengaturan['ambang_conf_tinggi']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                            <p class="field-note">Skor di atas ini dianggap keyakinan "Tinggi".</p>
                        </div>
                        <div>
                            <label class="field-label">Ambang keyakinan sedang (≥)</label>
                            <input type="number" min="0" max="100" name="ambang_conf_sedang" class="field-input" value="<?php echo (int) $pengaturan['ambang_conf_sedang']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                            <p class="field-note">Di bawah ini otomatis "Rendah" · perlu tinjau manual.</p>
                        </div>
                        <div>
                            <label class="field-label">Pengajuan dianggap mepet (hari)</label>
                            <input type="number" min="1" max="30" name="ambang_mepet_hari" class="field-input" value="<?php echo (int) $pengaturan['ambang_mepet_hari']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                            <p class="field-note">Pending ditolak otomatis saat tanggal mulai tinggal segini hari.</p>
                        </div>
                        <div>
                            <label class="field-label">Pengingat kontrak H-7 (hari)</label>
                            <input type="number" min="1" max="30" name="h7_hari" class="field-input" value="<?php echo (int) $pengaturan['h7_hari']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                            <p class="field-note">Notifikasi pertama sebelum kontrak berakhir.</p>
                        </div>
                        <div>
                            <label class="field-label">Pengingat kontrak H-3 (hari)</label>
                            <input type="number" min="1" max="30" name="h3_hari" class="field-input" value="<?php echo (int) $pengaturan['h3_hari']; ?>" <?php echo $hanya_lihat ? 'disabled' : ''; ?>>
                            <p class="field-note">Notifikasi susulan bila belum ada tindak lanjut HR.</p>
                        </div>
                    </div>
                </div>

                <?php if (!$hanya_lihat) { ?>
                <div class="form-footer">
                    <button type="submit" class="btn-simpan-pengaturan">Simpan pengaturan</button>
                </div>
                <?php } ?>
            </form>
        </main>
    </div>
</div>

<script>
    (function () {
        var inputs = document.querySelectorAll('.bobot-grid input');
        var total = document.getElementById('bobot-total');
        function segarkan() {
            var jumlah = 0;
            inputs.forEach(function (el) { jumlah += parseInt(el.value, 10) || 0; });
            total.textContent = 'Total: ' + jumlah + '/100';
            total.classList.toggle('salah', jumlah !== 100);
        }
        inputs.forEach(function (el) { el.addEventListener('input', segarkan); });
    })();
</script>
</body>
</html>
