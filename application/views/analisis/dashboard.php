<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — Analisis Penempatan | E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <style>
        .period-controls { display: flex; gap: 8px; align-items: center; }
        .period-controls a.btn-chip { text-decoration: none; }
        .period-controls .btn-chip-label { min-width: 150px; text-align: center; cursor: default; }
        .snapshot-line { font-size: 12px; color: var(--abu-600); margin-top: -16px; }

        .kpi-row { display: flex; gap: 16px; }
        .kpi-card {
            flex: 1; background: #fff; border: 1px solid var(--abu-200); border-radius: 12px;
            padding: 20px; display: flex; flex-direction: column; gap: 12px;
        }
        .kpi-top { display: flex; align-items: center; justify-content: space-between; }
        .kpi-label { color: var(--abu-600); font-size: 13px; }
        .kpi-icon { background: var(--hijau-100); border-radius: 8px; padding: 8px; display: flex; }
        .kpi-icon.warn { background: var(--kuning-bg); }
        .kpi-value { font-size: 32px; font-weight: 700; }
        .kpi-note { color: var(--abu-600); font-size: 12px; }
        .kpi-note.warn { color: var(--kuning-teks); }

        /* Bar chart (pendaftaran) */
        .chart-value { font-size: 28px; font-weight: 700; margin-bottom: 2px; }
        .chart-delta { color: var(--hijau-700); font-size: 12px; margin-bottom: 14px; }
        .bars { display: flex; align-items: flex-end; gap: 10px; height: 110px; }
        .bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%; }
        .bar-count { font-size: 11px; color: var(--abu-600); margin-bottom: 4px; }
        .bar { width: 100%; max-width: 28px; background: #bfe3d2; border-radius: 4px 4px 0 0; }
        .bar.today { background: var(--hijau-700); }
        .bar-day { font-size: 11px; color: var(--abu-600); margin-top: 6px; }
        .bar-day.today { color: var(--hijau-700); font-weight: 600; }
        .chart-footer {
            display: flex; justify-content: space-around; background: var(--abu-100); border-radius: 8px;
            padding: 12px; margin-top: 14px; text-align: center;
        }
        .chart-footer .num { font-weight: 700; font-size: 14px; }
        .chart-footer .num.warn { color: var(--kuning-teks); }
        .chart-footer .lbl { font-size: 11px; color: var(--abu-600); }

        /* Kuota departemen */
        .quota-row { margin-bottom: 14px; }
        .quota-row:last-child { margin-bottom: 0; }
        .quota-top { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px; }
        .quota-name { font-weight: 600; }
        .quota-status { font-size: 11px; color: var(--merah-700); }
        .quota-status.ok { color: var(--hijau-700); }
        .quota-track { background: var(--abu-100); border-radius: 6px; height: 8px; overflow: hidden; }
        .quota-fill { height: 100%; border-radius: 6px; background: var(--hijau-700); }
        .quota-fill.full { background: var(--merah-700); }
        .quota-fill.low { background: #c08a2b; }

        /* Tabel pelamar */
        .filters { display: flex; gap: 8px; align-items: center; margin-bottom: 16px; flex-wrap: wrap; }
        .search-box-wrap { position: relative; flex: 1; min-width: 180px; }
        .search-box-wrap svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--abu-600); pointer-events: none; }
        .search-box {
            border: 1px solid var(--abu-200); border-radius: 8px; padding: 9px 12px 9px 36px; font-size: 12px;
            width: 100%; color: var(--abu-900); font-family: inherit;
        }
        .search-box:focus { outline: none; border-color: var(--hijau-700); box-shadow: 0 0 0 3px var(--hijau-100); }
        .chip {
            display: inline-flex; align-items: center; border: 1px solid var(--abu-200); border-radius: 20px;
            padding: 8px 14px; font-size: 12px; font-weight: 600; background: #fff; cursor: pointer;
            color: var(--abu-600); white-space: nowrap; height: 36px; box-sizing: border-box; text-decoration: none;
        }
        .chip-pending:hover { background: var(--kuning-bg); border-color: var(--kuning-bg); color: var(--kuning-teks); }
        .chip-diterima:hover { background: var(--hijau-100); border-color: var(--hijau-100); color: var(--hijau-700); }
        .chip-ditolak:hover { background: var(--merah-100); border-color: var(--merah-100); color: var(--merah-700); }
        .tabel-scroll { width: 100%; overflow-x: auto; }
        table.pelamar { width: 100%; min-width: 760px; border-collapse: collapse; font-size: 12px; }
        table.pelamar th { text-align: left; color: var(--abu-600); font-weight: 600; padding: 8px 10px; border-bottom: 1px solid var(--abu-200); font-size: 11px; text-transform: uppercase; }
        table.pelamar td { padding: 12px 10px; border-bottom: 1px solid var(--abu-100); vertical-align: top; }
        .peserta-cell { display: flex; align-items: center; gap: 10px; }
        .peserta-foto { width: 36px; height: 36px; border-radius: 100px; background: var(--abu-200); flex-shrink: 0; object-fit: cover; }
        .peserta-nama { font-weight: 600; color: var(--abu-900); }
        .peserta-sub { color: var(--abu-600); font-size: 11px; }
        .peserta-sub .institusi { color: var(--abu-900); font-weight: 500; }
        .peserta-sub .jurusan { color: var(--abu-600); font-weight: 400; }

        .period-controls .btn-chip { display: inline-flex; align-items: center; justify-content: center; }
        .panel-perhatian .panel-head { margin-bottom: 4px; }
        .panel-perhatian .panel-head h2 { font-size: 16px; font-weight: 700; color: var(--abu-900); }
        .panel-perhatian .panel-sub { font-size: 12px; color: var(--abu-600); margin-top: 0; }
        .link-arrow { display: inline-flex; align-items: center; gap: 4px; }
        .link-arrow svg { position: relative; top: 1px; }
        .status-sub { margin-top: 2px; }
        .skor-cell strong { display: block; color: var(--hijau-700); }
        .skor-cell .status-sub { margin-top: 1px; }
        .aksi-cell { display: flex; gap: 6px; white-space: nowrap; }
        .btn-aksi { border: none; border-radius: 6px; padding: 6px 10px; font-size: 11px; font-weight: 600; cursor: pointer; }
        .btn-terima { background: var(--hijau-700); color: #fff; }
        .btn-tolak { background: var(--merah-100); color: var(--merah-700); }
        .btn-detail { background: var(--abu-100); color: var(--abu-900); }
        .pager { display: flex; justify-content: flex-end; gap: 6px; margin-top: 14px; }
        .pager button { border: 1px solid var(--abu-200); background: #fff; border-radius: 6px; width: 26px; height: 26px; font-size: 12px; cursor: pointer; }
        .pager button.active { background: var(--hijau-700); color: #fff; border-color: var(--hijau-700); }

        /* Agenda & pengingat */
        .agenda-grid { display: flex; gap: 10px; }
        .agenda-day { flex: 1; border-radius: 10px; padding: 12px; text-align: center; background: var(--abu-100); }
        .agenda-day.today { background: var(--hijau-100); border: 1px solid var(--hijau-700); }
        .agenda-weekday { font-size: 11px; color: var(--abu-600); }
        .agenda-date { font-size: 22px; font-weight: 700; margin: 4px 0; }
        .agenda-tag { font-size: 10px; color: var(--abu-600); }
        .agenda-tag.today { color: var(--hijau-700); font-weight: 600; }
        .agenda-tag.warn { color: var(--kuning-teks); font-weight: 600; }
        .reminder-item { display: flex; gap: 10px; padding: 10px 0; border-bottom: 1px solid var(--abu-100); align-items: flex-start; }
        .reminder-item:last-child { border-bottom: none; }
        .reminder-foto { width: 32px; height: 32px; border-radius: 100px; background: var(--abu-200); flex-shrink: 0; object-fit: cover; }
        .reminder-nama { font-weight: 600; font-size: 12px; }
        .reminder-sub { font-size: 11px; color: var(--abu-600); }
        .reminder-h { margin-left: auto; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 6px; background: var(--kuning-bg); color: var(--kuning-teks); white-space: nowrap; }
    </style>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'dashboard'; include APPPATH . 'views/analisis/_sidebar.php'; ?>

    <div class="main">
        <?php $breadcrumb_akhir = 'Dashboard'; include APPPATH . 'views/analisis/_topbar.php'; ?>

        <main class="content">
            <div class="page-head">
                <div>
                    <h1>Selamat pagi, Bu Dewi 👋</h1>
                    <p>Pantau pelamar baru, kapasitas penempatan, dan jadwal magang dalam satu tempat.</p>
                </div>
                <div class="period-controls">
                    <a class="btn-chip" href="<?php echo base_url(); ?>analisis/dashboard?minggu=<?php echo $offset_minggu - 1; ?>"><?php icon_analisis('chevron-left', 14); ?></a>
                    <span class="btn-chip btn-chip-label"><?php echo $minggu_mulai->format('j'); ?>–<?php echo tanggal_indo($minggu_selesai->format('Y-m-d')); ?></span>
                    <a class="btn-chip" href="<?php echo base_url(); ?>analisis/dashboard?minggu=<?php echo $offset_minggu + 1; ?>"><?php icon_analisis('chevron-right', 14); ?></a>
                </div>
            </div>
            <div class="snapshot-line">
                <span class="status-sub">Data diperbarui hari ini, <?php echo htmlspecialchars(tanggal_indo($hari_ini->format('Y-m-d'), true)); ?></span>
            </div>

            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-top">
                        <span class="kpi-label">Pendaftar minggu ini</span>
                        <span class="kpi-icon"><?php icon_analisis('inbox', 18); ?></span>
                    </div>
                    <div class="kpi-value">18</div>
                    <div class="kpi-note">↑ 4 dari minggu lalu (14 pendaftar)</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-top">
                        <span class="kpi-label">Peserta magang aktif</span>
                        <span class="kpi-icon"><?php icon_analisis('users-round', 18); ?></span>
                    </div>
                    <div class="kpi-value">48</div>
                    <div class="kpi-note">Tersebar di 6 departemen</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-top">
                        <span class="kpi-label">Menunggu keputusan HR</span>
                        <span class="kpi-icon warn"><?php icon_analisis('clipboard-check', 18); ?></span>
                    </div>
                    <div class="kpi-value">11</div>
                    <div class="kpi-note warn">Termasuk 3 perlu tinjau manual</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-top">
                        <span class="kpi-label">Berakhir dalam 7 hari</span>
                        <span class="kpi-icon warn"><?php icon_analisis('calendar-clock', 18); ?></span>
                    </div>
                    <div class="kpi-value">2</div>
                    <div class="kpi-note warn">1 H-3 dan 1 H-7 · perlu tindakan</div>
                </div>
            </div>

            <div class="two-col">
                <div class="panel">
                    <div class="panel-head">
                        <h2>Pendaftaran masuk minggu ini</h2>
                        <span class="badge">Minggu ini</span>
                    </div>
                    <div class="panel-sub">5–11 Oktober 2026 · 18 pelamar sampai Kamis</div>
                    <div class="chart-value">18</div>
                    <div class="chart-delta">+28,6% dibanding minggu lalu</div>
                    <div class="bars">
                        <div class="bar-col"><span class="bar-count">5</span><div class="bar" style="height:55%"></div><span class="bar-day">Sen, 5</span></div>
                        <div class="bar-col"><span class="bar-count">4</span><div class="bar" style="height:44%"></div><span class="bar-day">Sel, 6</span></div>
                        <div class="bar-col"><span class="bar-count">6</span><div class="bar" style="height:66%"></div><span class="bar-day">Rab, 7</span></div>
                        <div class="bar-col"><span class="bar-count">3</span><div class="bar today" style="height:33%"></div><span class="bar-day today">Kam, 8</span></div>
                        <div class="bar-col"><span class="bar-count">–</span><div class="bar" style="height:2%"></div><span class="bar-day">Jum, 9</span></div>
                        <div class="bar-col"><span class="bar-count">–</span><div class="bar" style="height:2%"></div><span class="bar-day">Sab, 10</span></div>
                        <div class="bar-col"><span class="bar-count">–</span><div class="bar" style="height:2%"></div><span class="bar-day">Min, 11</span></div>
                    </div>
                    <div class="chart-footer">
                        <div><div class="num">11</div><div class="lbl">Siap ditinjau</div></div>
                        <div><div class="num">4</div><div class="lbl">Analisis diproses</div></div>
                        <div><div class="num warn">3</div><div class="lbl">Tinjau manual</div></div>
                    </div>
                </div>

                <div class="panel">
                    <div class="panel-head">
                        <h2>Kuota departemen</h2>
                        <span class="badge">12 tersedia</span>
                    </div>
                    <div class="panel-sub">48 dari 60 tempat terisi · periode aktif 8 Oktober</div>

                    <div class="quota-row">
                        <div class="quota-top"><span class="quota-name">Riset &amp; Pengembangan</span><span class="quota-status">12/12 · Penuh</span></div>
                        <div class="quota-track"><div class="quota-fill full" style="width:100%"></div></div>
                    </div>
                    <div class="quota-row">
                        <div class="quota-top"><span class="quota-name">Produksi</span><span class="quota-status low">10/12 · Sisa 2</span></div>
                        <div class="quota-track"><div class="quota-fill low" style="width:83%"></div></div>
                    </div>
                    <div class="quota-row">
                        <div class="quota-top"><span class="quota-name">Komersil</span><span class="quota-status low">7/8 · Sisa 1</span></div>
                        <div class="quota-track"><div class="quota-fill low" style="width:87%"></div></div>
                    </div>
                    <div class="quota-row">
                        <div class="quota-top"><span class="quota-name">Rantai Pasok</span><span class="quota-status ok">8/12 · Sisa 4</span></div>
                        <div class="quota-track"><div class="quota-fill" style="width:67%"></div></div>
                    </div>
                    <div class="quota-row">
                        <div class="quota-top"><span class="quota-name">SDM &amp; Tata Kelola</span><span class="quota-status low">6/8 · Sisa 2</span></div>
                        <div class="quota-track"><div class="quota-fill low" style="width:75%"></div></div>
                    </div>
                    <div class="quota-row">
                        <div class="quota-top"><span class="quota-name">Administrasi &amp; Keuangan</span><span class="quota-status ok">5/8 · Sisa 3</span></div>
                        <div class="quota-track"><div class="quota-fill" style="width:62%"></div></div>
                    </div>
                </div>
            </div>

            <div class="panel panel-perhatian" style="width:100%">
                <div class="panel-head">
                    <h2>Pelamar yang perlu perhatian</h2>
                    <a class="panel-head link link-arrow" href="<?php echo base_url(); ?>analisis/pelamar">Semua pelamar <?php icon_analisis('arrow-right', 14); ?></a>
                </div>
                <div class="panel-sub">Pelamar pending yang paling lama menunggu keputusan kamu.</div>
                <form class="filters" method="get" action="<?php echo base_url(); ?>analisis/pelamar">
                    <div class="search-box-wrap">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input class="search-box" type="text" name="cari" placeholder="Cari nama, sekolah, atau kampus">
                    </div>
                    <a class="chip chip-pending" href="<?php echo base_url(); ?>analisis/pelamar?tab=pending">Pending · <?php echo isset($jumlah_status['pending']) ? $jumlah_status['pending'] : 0; ?></a>
                    <a class="chip chip-diterima" href="<?php echo base_url(); ?>analisis/pelamar?tab=diterima">Diterima · <?php echo isset($jumlah_status['diterima']) ? $jumlah_status['diterima'] : 0; ?></a>
                    <a class="chip chip-ditolak" href="<?php echo base_url(); ?>analisis/pelamar?tab=ditolak">Ditolak · <?php echo isset($jumlah_status['ditolak']) ? $jumlah_status['ditolak'] : 0; ?></a>
                    <a class="chip" href="<?php echo base_url(); ?>analisis/pelamar?tab=tidak_aktif">Tidak aktif · <?php echo isset($jumlah_status['tidak_aktif']) ? $jumlah_status['tidak_aktif'] : 0; ?></a>
                </form>
                <div class="tabel-scroll">
                <table class="pelamar">
                    <thead>
                        <tr>
                            <th>Pelamar / Institusi</th>
                            <th>Periode usulan</th>
                            <th>Dept. diminta</th>
                            <th>Status / Kesiapan</th>
                            <th>Saran divisi teratas / Top</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pelamar_perhatian)) { ?>
                        <tr><td colspan="6" style="text-align:center; color:var(--abu-600); padding:24px 10px;">
                            Tidak ada pelamar pending saat ini.
                        </td></tr>
                        <?php }
                        foreach ($pelamar_perhatian as $p) {
                        ?>
                        <tr>
                            <td>
                                <div class="peserta-cell">
                                    <img class="peserta-foto" src="<?php echo base_url(); ?>assets/images/avatar-default.png" alt="">
                                    <div>
                                        <div class="peserta-nama"><?php echo htmlspecialchars($p->nama); ?></div>
                                        <div class="peserta-sub"><span class="institusi"><?php echo htmlspecialchars($p->institusi); ?></span><br><span class="jurusan"><?php echo htmlspecialchars($p->jurusan); ?></span></div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $p->tgl_mulai ? date('d M Y', strtotime($p->tgl_mulai)) : '-'; ?><br>sampai<br><?php echo $p->tgl_selesai ? date('d M Y', strtotime($p->tgl_selesai)) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($p->nama_depag ? $p->nama_depag : '-'); ?></td>
                            <td>
                                <span class="status-pill status-pending">Pending</span>
                            </td>
                            <td class="skor-cell">
                                <?php if (!empty($p->top1_nama_divisi)) { ?>
                                    <strong><?php echo htmlspecialchars($p->top1_nama_divisi); ?> · <?php echo number_format($p->top1_skor, 1, ',', '.'); ?></strong>
                                    <div class="status-sub">Keyakinan <?php echo htmlspecialchars($p->top1_keyakinan); ?></div>
                                <?php } else { ?>
                                    <span class="status-sub">Hasil belum tersedia</span>
                                <?php } ?>
                            </td>
                            <td class="aksi-cell">
                                <a class="btn-aksi btn-terima" href="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $p->id; ?>">Terima</a>
                                <form method="post" action="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $p->id; ?>/tolak" style="display:inline;">
                                    <button type="submit" class="btn-aksi btn-tolak">Tolak</button>
                                </form>
                                <a class="btn-aksi btn-detail" href="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $p->id; ?>">Lihat detail</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                </div>
                <div style="margin-top:10px;">
                    <span class="status-sub">Menampilkan <?php echo count($pelamar_perhatian); ?> pelamar pending teratas · saran bukan penetapan</span>
                </div>
            </div>

            <div class="two-col">
                <div class="panel">
                    <div class="panel-head">
                        <h2>Agenda magang terdekat</h2>
                        <a class="panel-head link link-arrow" href="<?php echo base_url(); ?>analisis/kalender">Buka kalender <?php icon_analisis('arrow-right', 14); ?></a>
                    </div>
                    <div class="panel-sub">Oktober 2026 · mulai dan berakhir</div>
                    <div class="agenda-grid">
                        <div class="agenda-day today">
                            <div class="agenda-weekday">Kam</div>
                            <div class="agenda-date">8</div>
                            <div class="agenda-tag today">Hari ini</div>
                        </div>
                        <div class="agenda-day">
                            <div class="agenda-weekday">Min</div>
                            <div class="agenda-date">11</div>
                            <div class="agenda-tag">Selesai · Nadia</div>
                        </div>
                        <div class="agenda-day">
                            <div class="agenda-weekday">Sen</div>
                            <div class="agenda-date">12</div>
                            <div class="agenda-tag warn">Mulai · Rizky</div>
                        </div>
                        <div class="agenda-day">
                            <div class="agenda-weekday">Kam</div>
                            <div class="agenda-date">15</div>
                            <div class="agenda-tag warn">Selesai · Fajar</div>
                        </div>
                    </div>
                    <div class="status-sub" style="margin-top:12px;">48 peserta aktif hari ini · 1 peserta disetujui akan mulai 12 Oktober.</div>
                </div>

                <div class="panel">
                    <div class="panel-head">
                        <h2>Segera tinjau kontrak</h2>
                        <span class="badge badge-warn">2 pengingat</span>
                    </div>
                    <div class="panel-sub">Notifikasi dalam aplikasi · hanya untuk HR</div>
                    <div class="reminder-item">
                        <img class="reminder-foto" src="<?php echo base_url(); ?>assets/images/avatar-default.png" alt="">
                        <div>
                            <div class="reminder-nama">Nadia Safitri</div>
                            <div class="reminder-sub">Berakhir 11 Okt 2026 · tinjau kontrak</div>
                        </div>
                        <span class="reminder-h">H-3</span>
                    </div>
                    <div class="reminder-item">
                        <img class="reminder-foto" src="<?php echo base_url(); ?>assets/images/avatar-default.png" alt="">
                        <div>
                            <div class="reminder-nama">Fajar Maulana</div>
                            <div class="reminder-sub">Berakhir 15 Okt 2026 · tinjau kontrak</div>
                        </div>
                        <span class="reminder-h">H-7</span>
                    </div>
                    <div class="status-sub" style="margin-top:8px;">Tentukan perpanjang atau akhiri sebelum atau pada tanggal selesai. Status pendaftaran dicek otomatis Tidak aktif setelah tanggal akhir.</div>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
