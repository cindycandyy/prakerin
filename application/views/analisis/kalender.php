<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kalender Magang — Analisis Penempatan | E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <style>
        table.agenda { width: 100%; border-collapse: collapse; font-size: 12px; }
        table.agenda th { text-align: left; color: var(--abu-600); font-weight: 600; padding: 8px 10px; border-bottom: 1px solid var(--abu-200); font-size: 11px; text-transform: uppercase; }
        table.agenda td { padding: 12px 10px; border-bottom: 1px solid var(--abu-100); vertical-align: top; }
        .peserta-nama { font-weight: 600; }
        .tag-pill { display: inline-block; padding: 4px 9px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .tag-mulai { background: var(--kuning-bg); color: var(--kuning-teks); }
        .tag-selesai { background: var(--hijau-100); color: var(--hijau-700); }
        .tabel-scroll { width: 100%; overflow-x: auto; }
    </style>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'kalender'; include APPPATH . 'views/analisis/_sidebar.php'; ?>

    <div class="main">
        <?php $breadcrumb_akhir = 'Kalender magang'; include APPPATH . 'views/analisis/_topbar.php'; ?>

        <main class="content">
            <div class="page-head">
                <div>
                    <h1>Kalender magang</h1>
                    <p>Peserta yang mulai atau selesai magang dalam 30 hari ke depan.</p>
                </div>
            </div>

            <div class="panel" style="width:100%">
                <div class="panel-sub">Menampilkan <?php echo count($daftar_agenda); ?> peserta · urut tanggal terdekat</div>
                <div class="tabel-scroll">
                <table class="agenda">
                    <thead>
                        <tr>
                            <th>Peserta</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Agenda terdekat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftar_agenda)) { ?>
                        <tr><td colspan="4" style="text-align:center; color:var(--abu-600); padding:24px 10px;">
                            Tidak ada peserta yang mulai/selesai dalam 30 hari ke depan.
                        </td></tr>
                        <?php }
                        $hari_ini = date('Y-m-d');
                        foreach ($daftar_agenda as $a) {
                            $mulai_dekat = $a->tgl_mulai >= $hari_ini;
                            $selesai_dekat = $a->tgl_selesai >= $hari_ini;
                        ?>
                        <tr>
                            <td class="peserta-nama"><?php echo htmlspecialchars($a->nama); ?></td>
                            <td><?php echo $a->tgl_mulai ? date('d M Y', strtotime($a->tgl_mulai)) : '-'; ?></td>
                            <td><?php echo $a->tgl_selesai ? date('d M Y', strtotime($a->tgl_selesai)) : '-'; ?></td>
                            <td>
                                <?php if ($mulai_dekat && $a->tgl_mulai >= $hari_ini) { ?>
                                    <span class="tag-pill tag-mulai">Mulai <?php echo date('d M', strtotime($a->tgl_mulai)); ?></span>
                                <?php } elseif ($selesai_dekat) { ?>
                                    <span class="tag-pill tag-selesai">Selesai <?php echo date('d M', strtotime($a->tgl_selesai)); ?></span>
                                <?php } ?>
                            </td>
                        </tr>
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
