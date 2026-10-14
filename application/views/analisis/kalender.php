<?php
include APPPATH . 'views/analisis/_icon.php';

$nama_bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
$nama_hari = array('Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');

// Grid 6 minggu x 7 hari, dimulai Senin. Sel di luar bulan (bulan sebelum/sesudah)
// tetap dirender supaya grid selalu persegi, ditandai abu-abu (".luar-bulan").
$hari_pertama_iso = (int) $awal_bulan->format('N'); // 1=Senin .. 7=Minggu
$offset_awal = $hari_pertama_iso - 1;

$sel = array();
$kursor = (clone $awal_bulan)->modify('-' . $offset_awal . ' days');
for ($i = 0; $i < 42; $i++) {
    $sel[] = clone $kursor;
    $kursor->modify('+1 day');
}

$bulan_prev = $bulan - 1; $tahun_prev = $tahun;
if ($bulan_prev < 1) { $bulan_prev = 12; $tahun_prev--; }
$bulan_next = $bulan + 1; $tahun_next = $tahun;
if ($bulan_next > 12) { $bulan_next = 1; $tahun_next++; }

$label_status = array(
    'akan_mulai' => array('Akan mulai', 'status-pending'),
    'aktif' => array('Aktif', 'status-diterima'),
    'akan_berakhir' => array('Akan berakhir', 'badge-warn-pill'),
    'selesai' => array('Selesai', 'status-tidakaktif'),
    'diakhiri_awal' => array('Diakhiri awal', 'status-ditolak'),
);

function persen_durasi_kalender($mulai, $selesai, $hari_ini) {
    $t_mulai = strtotime($mulai); $t_selesai = strtotime($selesai); $t_hari_ini = strtotime($hari_ini);
    if ($t_hari_ini <= $t_mulai) return 0;
    if ($t_hari_ini >= $t_selesai) return 100;
    $total = $t_selesai - $t_mulai;
    return $total > 0 ? (int) round((($t_hari_ini - $t_mulai) / $total) * 100) : 0;
}

$qs_bulan = function ($t, $b) use ($id_departemen_filter) {
    $q = 'tahun=' . $t . '&bulan=' . $b;
    if ($id_departemen_filter) { $q .= '&departemen=' . $id_departemen_filter; }
    return $q;
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kalender Magang — Analisis Penempatan | E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,500,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <style>
        .kpi-row { display: flex; gap: 16px; }
        .kpi-card { flex: 1; background: #fff; border: 1px solid var(--abu-200); border-radius: 12px; padding: 16px 18px; }
        .kpi-card .num { font-size: 24px; font-weight: 700; color: var(--abu-900); }
        .kpi-card .lbl { font-size: 12px; color: var(--abu-600); margin-top: 2px; }
        .kpi-card.aksen .num { color: var(--hijau-700); }

        .filter-depag { display: flex; gap: 8px; flex-wrap: wrap; margin: 18px 0; }
        .filter-depag a {
            display: inline-flex; align-items: center; padding: 7px 14px; border-radius: 100px; font-size: 12px;
            font-weight: 600; border: 1px solid var(--abu-200); background: #fff; color: var(--abu-600); text-decoration: none;
        }
        .filter-depag a.aktif { background: var(--hijau-700); color: #fff; border-color: var(--hijau-700); }

        .kalender-layout { display: grid; grid-template-columns: 1fr 300px; gap: 20px; align-items: start; }
        @media (max-width: 1100px) { .kalender-layout { grid-template-columns: 1fr; } }

        .kalender-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; flex-wrap: wrap; gap: 10px; }
        .kalender-bulan { font-size: 15px; font-weight: 700; color: var(--abu-900); min-width: 150px; }
        .kalender-nav { display: flex; align-items: center; gap: 6px; }
        .kalender-nav a.btn-chip { text-decoration: none; display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; padding: 0; }
        .kalender-legenda { display: flex; gap: 14px; font-size: 11px; color: var(--abu-600); }
        .legenda-item { display: flex; align-items: center; gap: 5px; }
        .legenda-dot { width: 8px; height: 8px; border-radius: 100px; display: inline-block; }
        .legenda-dot.mulai { background: var(--kuning-teks); }
        .legenda-dot.selesai { background: var(--hijau-700); }

        .kalender-grid { display: grid; grid-template-columns: repeat(7, 1fr); border: 1px solid var(--abu-200); border-radius: 10px; overflow: hidden; }
        .kalender-grid .hari-label {
            background: var(--abu-100); color: var(--abu-600); font-size: 10.5px; font-weight: 600;
            text-transform: uppercase; text-align: center; padding: 7px 0;
        }
        .kalender-sel {
            min-height: 84px; border-top: 1px solid var(--abu-100); border-left: 1px solid var(--abu-100);
            padding: 6px; display: flex; flex-direction: column; gap: 3px; background: #fff;
        }
        .kalender-sel:nth-child(7n+1) { border-left: none; }
        .kalender-sel.luar-bulan { background: var(--abu-bg); }
        .kalender-sel.luar-bulan .tgl-angka { color: var(--abu-400, #a8b3af); }
        .kalender-sel.hari-ini { background: var(--hijau-100); box-shadow: inset 0 0 0 1.5px var(--hijau-700); }
        .tgl-angka { font-size: 11px; font-weight: 600; color: var(--abu-900); }
        .tgl-label-hariini { font-size: 9px; color: var(--hijau-700); font-weight: 600; float: right; }
        .agenda-pill {
            font-size: 9px; font-weight: 600; padding: 2px 5px; border-radius: 5px; line-height: 1.5;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .agenda-pill.mulai { background: var(--kuning-bg); color: var(--kuning-teks); }
        .agenda-pill.selesai { background: var(--hijau-100); color: var(--hijau-700); }
        .agenda-lainnya { font-size: 9px; color: var(--abu-600); padding: 1px 5px; }
        .kalender-ket { font-size: 11px; color: var(--abu-600); margin-top: 10px; }

        /* Panel kanan */
        .panel-kanan { display: flex; flex-direction: column; gap: 16px; }
        .tindakan-item { border-bottom: 1px solid var(--abu-100); padding: 10px 0; }
        .tindakan-item:last-child { border-bottom: none; padding-bottom: 0; }
        .tindakan-item:first-child { padding-top: 0; }
        .tindakan-top { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
        .tindakan-avatar { width: 28px; height: 28px; border-radius: 100px; background: var(--abu-200); flex-shrink: 0; object-fit: cover; }
        .tindakan-nama { font-size: 12px; font-weight: 600; color: var(--abu-900); }
        .tindakan-sub { font-size: 10.5px; color: var(--abu-600); }
        .tindakan-h { font-size: 9px; font-weight: 700; padding: 2px 6px; border-radius: 5px; background: var(--merah-100); color: var(--merah-700); margin-left: auto; flex-shrink: 0; }
        .tindakan-aksi { display: flex; gap: 6px; margin-top: 6px; }
        .tindakan-aksi button {
            border: none; border-radius: 6px; padding: 5px 10px; font-size: 10.5px; font-weight: 600; cursor: pointer; font-family: inherit;
        }
        .tindakan-aksi .perpanjang { background: var(--hijau-700); color: #fff; }
        .tindakan-aksi .akhiri { background: var(--merah-100); color: var(--merah-700); }
        .tindakan-kosong { font-size: 11px; color: var(--abu-600); padding: 6px 0; }

        .status-ringkas-lbl { font-size: 11px; font-weight: 600; color: var(--abu-900); margin-bottom: 4px; }
        .status-ringkas-val { font-size: 11px; color: var(--abu-600); line-height: 1.6; }

        /* Tabel peserta */
        .filter-status { display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap; }
        .filter-status button {
            border: 1px solid var(--abu-200); background: #fff; border-radius: 100px; padding: 6px 13px;
            font-size: 11px; font-weight: 600; color: var(--abu-600); cursor: pointer; font-family: inherit;
        }
        .filter-status button.aktif { background: var(--hijau-700); color: #fff; border-color: var(--hijau-700); }
        table.peserta-kontrak { width: 100%; border-collapse: collapse; font-size: 12px; }
        table.peserta-kontrak th { text-align: left; color: var(--abu-600); font-weight: 600; padding: 8px 10px; border-bottom: 1px solid var(--abu-200); font-size: 10.5px; text-transform: uppercase; }
        table.peserta-kontrak td { padding: 12px 10px; border-bottom: 1px solid var(--abu-100); vertical-align: top; }
        .pk-peserta { display: flex; align-items: center; gap: 10px; }
        .pk-foto { width: 32px; height: 32px; border-radius: 100px; background: var(--abu-200); flex-shrink: 0; object-fit: cover; }
        .pk-nama { font-weight: 600; }
        .pk-sub { color: var(--abu-600); font-size: 11px; }
        .pk-progress { background: var(--abu-100); border-radius: 6px; height: 5px; overflow: hidden; margin-top: 5px; width: 140px; }
        .pk-progress-fill { height: 100%; border-radius: 6px; background: var(--hijau-700); }
        .pk-progress-fill.warn { background: #c08a2b; }
        .badge-warn-pill { background: var(--kuning-bg); color: var(--kuning-teks); }
        .pk-aksi { display: flex; gap: 6px; white-space: nowrap; }
        .pk-aksi form { margin: 0; }
        .pk-aksi button, .pk-aksi a {
            border: none; border-radius: 6px; padding: 6px 10px; font-size: 11px; font-weight: 600; cursor: pointer;
            font-family: inherit; text-decoration: none; display: inline-block;
        }
        .pk-aksi .perpanjang { background: var(--hijau-700); color: #fff; }
        .pk-aksi .akhiri { background: var(--merah-100); color: var(--merah-700); }
        .pk-aksi .lihat { background: var(--abu-100); color: var(--abu-900); }

        @media (max-width: 900px) {
            .kpi-row { flex-wrap: wrap; }
            .kpi-card { flex: 1 1 calc(50% - 16px); }
            .kalender-sel { min-height: 56px; }
            .agenda-pill, .agenda-lainnya { display: none; }
        }
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
                    <p>Lihat periode aktif, peserta yang akan mulai, dan tenggat akhir magang.</p>
                </div>
            </div>

            <div class="kpi-row">
                <div class="kpi-card aksen">
                    <div class="num"><?php echo $kpi['aktif_hari_ini']; ?></div>
                    <div class="lbl">Aktif hari ini</div>
                </div>
                <div class="kpi-card">
                    <div class="num"><?php echo $kpi['akan_mulai_bulan_ini']; ?></div>
                    <div class="lbl">Akan mulai bulan ini</div>
                </div>
                <div class="kpi-card">
                    <div class="num"><?php echo $kpi['berakhir_7_hari']; ?></div>
                    <div class="lbl">Berakhir dalam 7 hari</div>
                </div>
                <div class="kpi-card">
                    <div class="num"><?php echo $kpi['selesai_bulan_ini']; ?></div>
                    <div class="lbl">Sudah selesai bulan ini</div>
                </div>
            </div>

            <div class="filter-depag">
                <a class="<?php echo !$id_departemen_filter ? 'aktif' : ''; ?>" href="<?php echo base_url(); ?>analisis/kalender?<?php echo $qs_bulan($tahun, $bulan); ?>">Semua</a>
                <?php foreach ($departemen as $d) { ?>
                <a class="<?php echo $id_departemen_filter === (int) $d->id ? 'aktif' : ''; ?>"
                   href="<?php echo base_url(); ?>analisis/kalender?tahun=<?php echo $tahun; ?>&bulan=<?php echo $bulan; ?>&departemen=<?php echo (int) $d->id; ?>">
                    <?php echo htmlspecialchars($d->nama); ?>
                </a>
                <?php } ?>
            </div>

            <div class="kalender-layout">
                <div class="panel" style="width:100%">
                    <div class="kalender-head">
                        <div class="kalender-nav">
                            <a class="btn-chip" href="<?php echo base_url(); ?>analisis/kalender?<?php echo $qs_bulan($tahun_prev, $bulan_prev); ?>"><?php icon_analisis('chevron-left', 14); ?></a>
                            <div class="kalender-bulan"><?php echo $nama_bulan[$bulan] . ' ' . $tahun; ?></div>
                            <a class="btn-chip" href="<?php echo base_url(); ?>analisis/kalender?<?php echo $qs_bulan($tahun_next, $bulan_next); ?>"><?php icon_analisis('chevron-right', 14); ?></a>
                        </div>
                        <div class="kalender-legenda">
                            <span class="legenda-item"><span class="legenda-dot mulai"></span> Mulai magang</span>
                            <span class="legenda-item"><span class="legenda-dot selesai"></span> Akhir periode</span>
                        </div>
                    </div>

                    <div class="kalender-grid">
                        <?php foreach ($nama_hari as $h) { ?>
                        <div class="hari-label"><?php echo $h; ?></div>
                        <?php } ?>

                        <?php foreach ($sel as $tgl) {
                            $tgl_str = $tgl->format('Y-m-d');
                            $di_bulan_ini = ((int) $tgl->format('n') === $bulan);
                            $kelas = 'kalender-sel';
                            if (!$di_bulan_ini) { $kelas .= ' luar-bulan'; }
                            if ($tgl_str === $hari_ini) { $kelas .= ' hari-ini'; }
                            $agenda_hari = isset($per_tanggal[$tgl_str]) ? $per_tanggal[$tgl_str] : array();
                        ?>
                        <div class="<?php echo $kelas; ?>">
                            <span class="tgl-angka"><?php echo (int) $tgl->format('j'); ?></span>
                            <?php if ($tgl_str === $hari_ini) { ?><span class="tgl-label-hariini">Hari ini</span><?php } ?>
                            <?php foreach (array_slice($agenda_hari, 0, 2) as $a) { ?>
                            <span class="agenda-pill <?php echo $a['jenis']; ?>" title="<?php echo htmlspecialchars($a['nama'] . ' · ' . $a['institusi']); ?>">
                                <?php echo htmlspecialchars($a['nama']); ?>
                            </span>
                            <?php } ?>
                            <?php if (count($agenda_hari) > 2) { ?>
                            <span class="agenda-lainnya">+<?php echo count($agenda_hari) - 2; ?> lainnya</span>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="kalender-ket">Setiap agenda diberi nama &amp; label. Label "selesai" menandai tanggal akhir periode; status kontrak mengikuti tanggal hari ini.</div>
                </div>

                <div class="panel-kanan">
                    <div class="panel" style="width:100%">
                        <div class="panel-head">
                            <h2>Perlu tindakan</h2>
                        </div>
                        <div class="panel-sub">Hanya notifikasi dalam aplikasi untuk HR.</div>
                        <?php if (empty($perlu_tindakan)) { ?>
                        <div class="tindakan-kosong">Tidak ada kontrak yang perlu ditindaklanjuti saat ini.</div>
                        <?php } else { foreach ($perlu_tindakan as $k) {
                            $h_tersisa = (int) floor((strtotime($k->tanggal_selesai) - strtotime($hari_ini)) / 86400);
                        ?>
                        <div class="tindakan-item">
                            <div class="tindakan-top">
                                <img class="tindakan-avatar" src="<?php echo foto_pelamar_url($k->id_transaksi, $k->foto); ?>" alt="">
                                <div>
                                    <div class="tindakan-nama"><?php echo htmlspecialchars($k->nama); ?></div>
                                    <div class="tindakan-sub">Berakhir <?php echo date('d M Y', strtotime($k->tanggal_selesai)); ?> · <?php echo htmlspecialchars($k->nama_divisi ?: '-'); ?></div>
                                </div>
                                <span class="tindakan-h">H-<?php echo max(0, $h_tersisa); ?></span>
                            </div>
                            <div class="tindakan-aksi">
                                <a href="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $k->id_transaksi; ?>"><button type="button" class="perpanjang">Perpanjang</button></a>
                                <a href="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $k->id_transaksi; ?>"><button type="button" class="akhiri">Akhiri</button></a>
                            </div>
                        </div>
                        <?php } } ?>
                    </div>

                    <div class="panel" style="width:100%">
                        <div class="status-ringkas-lbl">Status mengikuti periode</div>
                        <div class="status-ringkas-val">Setelah tanggal akhir, kontrak menjadi Selesai dan pendaftaran Disetujui menjadi Tidak aktif. Pengingat H-7 / H-3 membantu HR menindaklanjuti.</div>
                    </div>
                </div>
            </div>

            <div class="panel" style="width:100%; margin-top:20px;">
                <div class="panel-head">
                    <h2>Peserta &amp; periode magang</h2>
                </div>
                <div class="panel-sub">Menampilkan <?php echo count($daftar_kontrak); ?> kontrak · urut tanggal mulai terdekat</div>
                <div class="tabel-scroll">
                <table class="peserta-kontrak">
                    <thead>
                        <tr>
                            <th>Peserta / institusi</th>
                            <th>Departemen / divisi</th>
                            <th>Periode magang</th>
                            <th>Status kontrak</th>
                            <th>Tindakan HR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftar_kontrak)) { ?>
                        <tr><td colspan="5" style="text-align:center; color:var(--abu-600); padding:24px 10px;">
                            Tidak ada kontrak pada filter ini.
                        </td></tr>
                        <?php }
                        foreach ($daftar_kontrak as $k) {
                            list($labelStatus, $kelasStatus) = isset($label_status[$k->status]) ? $label_status[$k->status] : array($k->status, '');
                            $persen = persen_durasi_kalender($k->tanggal_mulai, $k->tanggal_selesai, $hari_ini);
                            $warnPersen = in_array($k->status, array('akan_berakhir')) ? 'warn' : '';
                        ?>
                        <tr>
                            <td>
                                <div class="pk-peserta">
                                    <img class="pk-foto" src="<?php echo foto_pelamar_url($k->id_transaksi, $k->foto); ?>" alt="">
                                    <div>
                                        <div class="pk-nama"><?php echo htmlspecialchars($k->nama); ?></div>
                                        <div class="pk-sub"><?php echo htmlspecialchars($k->institusi); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><?php echo htmlspecialchars($k->nama_departemen ?: '-'); ?></div>
                                <div class="pk-sub"><?php echo htmlspecialchars($k->nama_divisi ?: '-'); ?></div>
                            </td>
                            <td>
                                <div><?php echo date('d M Y', strtotime($k->tanggal_mulai)); ?> – <?php echo date('d M Y', strtotime($k->tanggal_selesai)); ?></div>
                                <?php if (in_array($k->status, array('aktif', 'akan_berakhir'))) { ?>
                                <div class="pk-progress"><div class="pk-progress-fill <?php echo $warnPersen; ?>" style="width:<?php echo $persen; ?>%"></div></div>
                                <?php } ?>
                            </td>
                            <td><span class="status-pill <?php echo $kelasStatus; ?>"><?php echo $labelStatus; ?></span></td>
                            <td class="pk-aksi">
                                <?php if ($k->status === 'akan_berakhir' || $k->status === 'aktif') { ?>
                                <a class="perpanjang" href="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $k->id_transaksi; ?>">Perpanjang</a>
                                <a class="akhiri" href="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $k->id_transaksi; ?>">Akhiri</a>
                                <?php } else { ?>
                                <a class="lihat" href="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $k->id_transaksi; ?>">Lihat detail</a>
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
