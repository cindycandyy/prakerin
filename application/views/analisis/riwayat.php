<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Keputusan - Analisis Penempatan | E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <style>
        table.riwayat { width: 100%; border-collapse: collapse; font-size: 12px; }
        table.riwayat th { text-align: left; color: var(--abu-600); font-weight: 600; padding: 8px 10px; border-bottom: 1px solid var(--abu-200); font-size: 11px; text-transform: uppercase; }
        table.riwayat td { padding: 12px 10px; border-bottom: 1px solid var(--abu-100); vertical-align: top; }
        .peserta-nama { font-weight: 600; }
        .peserta-sub { color: var(--abu-600); font-size: 11px; }
        .status-sub { margin-top: 2px; }
        .aksi-pill { display: inline-block; padding: 4px 9px; border-radius: 6px; font-size: 11px; font-weight: 600; }
        .aksi-terima { background: var(--hijau-100); color: var(--hijau-700); }
        .aksi-tolak { background: var(--merah-100); color: var(--merah-700); }
        .pelaku-cell { font-family: monospace; font-size: 11px; color: var(--abu-600); }
        .alasan-cell { max-width: 260px; font-size: 11px; color: var(--abu-600); }
        .tabel-scroll { width: 100%; overflow-x: auto; }
    </style>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'riwayat'; include APPPATH . 'views/analisis/_sidebar.php'; ?>

    <div class="main">
        <?php $breadcrumb_akhir = 'Riwayat keputusan'; include APPPATH . 'views/analisis/_topbar.php'; ?>

        <main class="content">
            <div class="page-head">
                <div>
                    <h1>Riwayat keputusan</h1>
                    <p>Jejak audit setiap keputusan Terima/Tolak pelamar yang sudah diambil HR.</p>
                </div>
            </div>

            <div class="panel" style="width:100%">
                <div class="panel-sub">Menampilkan <?php echo count($daftar_riwayat); ?> keputusan terbaru · urut dari yang paling baru</div>
                <div class="tabel-scroll">
                <table class="riwayat">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pelamar</th>
                            <th>Keputusan</th>
                            <th>Divisi / Status</th>
                            <th>Alasan</th>
                            <th>Dicatat oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftar_riwayat)) { ?>
                        <tr><td colspan="6" style="text-align:center; color:var(--abu-600); padding:24px 10px;">
                            Belum ada keputusan yang tercatat.
                        </td></tr>
                        <?php }
                        foreach ($daftar_riwayat as $r) {
                            $sesudah = $r->sesudah ? json_decode($r->sesudah, true) : array();
                            $aksiPill = $r->aksi === 'terima' ? 'aksi-terima' : 'aksi-tolak';
                            $aksiLabel = $r->aksi === 'terima' ? 'Diterima' : 'Ditolak';
                        ?>
                        <tr>
                            <td><?php echo date('d M Y, H:i', strtotime($r->waktu)); ?></td>
                            <td>
                                <div class="peserta-nama"><?php echo htmlspecialchars($r->nama); ?></div>
                                <div class="peserta-sub"><?php echo htmlspecialchars($r->institusi); ?></div>
                            </td>
                            <td><span class="aksi-pill <?php echo $aksiPill; ?>"><?php echo $aksiLabel; ?></span></td>
                            <td>
                                <?php if (!empty($sesudah['id_divisi'])) { ?>
                                    <div class="status-sub">Divisi #<?php echo (int) $sesudah['id_divisi']; ?></div>
                                <?php } elseif (!empty($sesudah['status'])) { ?>
                                    <div class="status-sub"><?php echo htmlspecialchars($sesudah['status']); ?></div>
                                <?php } else { ?>
                                    <div class="status-sub">-</div>
                                <?php } ?>
                            </td>
                            <td class="alasan-cell"><?php echo !empty($sesudah['alasan_ubah']) ? htmlspecialchars($sesudah['alasan_ubah']) : '-'; ?></td>
                            <td class="pelaku-cell"><?php echo htmlspecialchars($r->pelaku); ?></td>
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
