<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pelamar — Analisis Penempatan | E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <style>
        .panel { padding: 28px; }

        .back-link { display: inline-flex; align-items: center; gap: 6px; color: var(--abu-600); font-size: 13px; font-weight: 600; margin-bottom: 10px; }
        .back-link svg { flex-shrink: 0; }
        .back-link:hover { color: var(--hijau-700); }

        .filters { display: flex; gap: 8px; align-items: center; margin-bottom: 10px; flex-wrap: wrap; }
        .search-box {
            border: 1px solid var(--abu-200); border-radius: 8px; padding: 10px 12px; font-size: 12px;
            flex: 1; min-width: 180px; color: var(--abu-900); font-family: inherit; height: 38px; box-sizing: border-box;
        }
        /* Tom Select: dropdown kustom, putih saat kosong, hijau saat ada pilihan/hover */
        .ts-wrapper { min-width: 170px; font-family: inherit; font-size: 12px; }
        .ts-wrapper.single .ts-control {
            border: 1px solid var(--abu-200); border-radius: 8px; background: #fff; color: var(--abu-900);
            height: 38px; min-height: 38px; padding: 0 28px 0 12px; display: flex; align-items: center;
            box-shadow: none;
        }
        .ts-wrapper.single.input-active .ts-control, .ts-wrapper.focus .ts-control { border-color: var(--hijau-700); box-shadow: none; }
        .ts-wrapper .ts-control:after { border-color: var(--abu-600) transparent transparent transparent; }
        .ts-wrapper.dropdown-active .ts-control:after { border-color: transparent transparent var(--abu-600) transparent; }
        .ts-dropdown {
            border: 1px solid var(--abu-200); border-radius: 8px; font-family: inherit; font-size: 12px;
            overflow: hidden; background: #fff; box-shadow: 0 8px 20px rgba(23,45,39,0.12); z-index: 50;
        }
        .ts-dropdown .ts-dropdown-content { background: #fff; }
        .ts-dropdown .option { padding: 8px 12px; background: #fff; color: var(--abu-900); }
        .ts-dropdown .option.active { background: var(--hijau-100); color: var(--hijau-700); }
        .ts-dropdown .option[data-selected] { background: var(--hijau-700); color: #fff; }
        .select-mini + .ts-wrapper .ts-control { min-width: 170px; }

        .btn-terapkan {
            background: var(--hijau-700); color: #fff; border: none; border-radius: 8px;
            padding: 10px 18px; font-size: 12px; font-weight: 600; cursor: pointer; height: 38px; box-sizing: border-box;
        }
        .btn-terapkan:hover { background: var(--hijau-800); }

        .tabs { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }
        .tab {
            display: inline-flex; align-items: center; padding: 8px 14px; font-size: 12px; font-weight: 600;
            border-radius: 100px; border: 1px solid var(--abu-200); color: var(--abu-600); background: #fff;
        }
        .tab-count { background: var(--abu-100); color: var(--abu-600); border-radius: 10px; padding: 1px 7px; font-size: 11px; margin-left: 6px; }

        .tab-pending.active { background: var(--kuning-bg); border-color: var(--kuning-bg); color: var(--kuning-teks); }
        .tab-pending.active .tab-count { background: rgba(165,105,12,0.15); color: var(--kuning-teks); }
        .tab-diterima.active { background: var(--hijau-100); border-color: var(--hijau-100); color: var(--hijau-700); }
        .tab-diterima.active .tab-count { background: rgba(8,127,91,0.15); color: var(--hijau-700); }
        .tab-ditolak.active { background: var(--merah-100); border-color: var(--merah-100); color: var(--merah-700); }
        .tab-ditolak.active .tab-count { background: rgba(186,66,57,0.15); color: var(--merah-700); }
        .tab-tidak_aktif.active { background: var(--abu-100); border-color: var(--abu-100); color: var(--abu-900); }
        .tab-tidak_aktif.active .tab-count { background: var(--abu-200); color: var(--abu-900); }

        table.pelamar { width: 100%; border-collapse: collapse; font-size: 12px; }
        table.pelamar th { text-align: left; color: var(--abu-600); font-weight: 600; padding: 8px 10px; border-bottom: 1px solid var(--abu-200); font-size: 11px; text-transform: uppercase; }
        table.pelamar td { padding: 12px 10px; border-bottom: 1px solid var(--abu-100); vertical-align: top; }
        .peserta-cell { display: flex; align-items: center; gap: 10px; }
        .peserta-foto { width: 36px; height: 36px; border-radius: 100px; background: var(--abu-200); flex-shrink: 0; object-fit: cover; }
        .peserta-nama { font-weight: 600; }
        .peserta-sub { color: var(--abu-600); font-size: 11px; }
        .status-sub { margin-top: 2px; }
        .conf-pill { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 10px; font-weight: 600; margin-top: 4px; }
        .conf-tinggi { background: var(--hijau-100); color: var(--hijau-700); }
        .conf-sedang { background: var(--kuning-bg); color: var(--kuning-teks); }
        .conf-rendah { background: var(--merah-100); color: var(--merah-700); }
        .skor-cell strong { display: block; color: var(--hijau-700); }
        .skor-cell .status-sub { margin-top: 1px; }
        .aksi-cell { display: flex; gap: 6px; white-space: nowrap; justify-content: center; align-items: center; }
        th.aksi-head { text-align: center; }
        .btn-aksi { border: none; border-radius: 6px; padding: 6px 10px; font-size: 11px; font-weight: 600; cursor: pointer; }
        .btn-terima { background: var(--hijau-700); color: #fff; }
        .btn-tolak { background: var(--merah-100); color: var(--merah-700); }
        .btn-detail { background: var(--abu-100); color: var(--abu-900); }
        .pager { display: flex; justify-content: flex-end; gap: 6px; margin-top: 14px; }
        .pager button { border: 1px solid var(--abu-200); background: #fff; border-radius: 6px; width: 26px; height: 26px; font-size: 12px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; color: var(--abu-600); }
        .pager button.active { background: var(--hijau-700); color: #fff; border-color: var(--hijau-700); }
    </style>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'pelamar'; include APPPATH . 'views/analisis/_sidebar.php'; ?>

    <div class="main">
        <?php $breadcrumb_akhir = 'Pelamar'; include APPPATH . 'views/analisis/_topbar.php'; ?>

        <main class="content">
            <a class="back-link" href="<?php echo base_url(); ?>analisis/dashboard"><?php icon_analisis('arrow-left', 16); ?> Kembali ke dashboard</a>
            <div class="page-head">
                <div>
                    <h1>Pelamar</h1>
                    <p>Tinjau tiap pelamar dan saran divisinya, lalu terima atau tolak. Keputusan akhir tetap di tangan kamu.</p>
                </div>
            </div>

            <div class="panel" style="width:100%">
                <?php
                $daftar_tab = array(
                    'pending' => 'Pending',
                    'diterima' => 'Diterima',
                    'ditolak' => 'Ditolak',
                    'tidak_aktif' => 'Tidak aktif',
                );
                $tab_aktif = isset($tab) ? $tab : 'pending';
                ?>
                <div class="tabs">
                    <?php foreach ($daftar_tab as $kode => $label) { ?>
                        <a class="tab tab-<?php echo $kode; ?> <?php echo $tab_aktif === $kode ? 'active' : ''; ?>"
                           href="<?php echo base_url(); ?>analisis/pelamar?tab=<?php echo $kode; ?>">
                            <?php echo $label; ?>
                            <span class="tab-count"><?php echo isset($jumlah_status[$kode]) ? $jumlah_status[$kode] : 0; ?></span>
                        </a>
                    <?php } ?>
                </div>

                <form class="filters" method="get" action="<?php echo base_url(); ?>analisis/pelamar">
                    <input type="hidden" name="tab" value="<?php echo htmlspecialchars($tab_aktif); ?>">
                    <input class="search-box" type="text" name="cari" placeholder="Cari nama, sekolah, atau kampus"
                           value="<?php echo htmlspecialchars(isset($cari) ? $cari : ''); ?>">
                    <select class="select-mini" name="periode">
                        <option value="">Semua periode</option>
                        <option>Nov 2026 – Jan 2027</option>
                        <option>Okt 2026 – Des 2026</option>
                    </select>
                    <select class="select-mini" name="jenjang">
                        <option value="">Semua jenjang</option>
                        <option>S1</option>
                        <option>D3</option>
                        <option>SMK</option>
                    </select>
                    <select class="select-mini" name="status_analisis">
                        <option value="">Semua status analisis</option>
                        <option>Siap ditinjau</option>
                        <option>Analisis diproses</option>
                        <option>Perlu tinjau manual</option>
                    </select>
                    <select class="select-mini" name="confidence">
                        <option value="">Semua keyakinan</option>
                        <option>Tinggi</option>
                        <option>Sedang</option>
                        <option>Rendah</option>
                    </select>
                    <button class="btn-terapkan" type="submit">Terapkan</button>
                </form>

                <table class="pelamar">
                    <thead>
                        <tr>
                            <th>Pelamar / Institusi</th>
                            <th>Periode usulan</th>
                            <th>Dept. diminta</th>
                            <th>Status / Kesiapan</th>
                            <th>Saran divisi &amp; skor</th>
                            <th>Keyakinan</th>
                            <th class="aksi-head">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftar_pelamar)) { ?>
                        <tr><td colspan="7" style="text-align:center; color:var(--abu-600); padding:24px 10px;">
                            Tidak ada pelamar pada tab dan filter ini.
                        </td></tr>
                        <?php
                        }

                        foreach ($daftar_pelamar as $p) {
                            $pillClass = 'status-pending';
                            $pillText = 'Pending';
                            if ($tab_aktif === 'diterima') { $pillClass = 'status-diterima'; $pillText = 'Diterima'; }
                            elseif ($tab_aktif === 'ditolak') { $pillClass = 'status-ditolak'; $pillText = 'Ditolak'; }
                            elseif ($tab_aktif === 'tidak_aktif') { $pillClass = 'status-tidakaktif'; $pillText = 'Tidak aktif'; }

                            $confClass = '';
                            $confLabel = 'Belum tersedia';
                            if (!empty($p->top1_keyakinan)) {
                                $confClass = 'conf-' . $p->top1_keyakinan;
                                $confLabel = ucfirst($p->top1_keyakinan);
                            }
                        ?>
                        <tr>
                            <td>
                                <div class="peserta-cell">
                                    <img class="peserta-foto" src="<?php echo base_url(); ?>assets/images/avatar-default.png" alt="">
                                    <div>
                                        <div class="peserta-nama"><?php echo htmlspecialchars($p->nama); ?></div>
                                        <div class="peserta-sub"><?php echo htmlspecialchars($p->institusi); ?><br><?php echo htmlspecialchars($p->jurusan); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $p->tgl_mulai ? date('d M Y', strtotime($p->tgl_mulai)) : '-'; ?><br>sampai<br><?php echo $p->tgl_selesai ? date('d M Y', strtotime($p->tgl_selesai)) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($p->nama_depag ? $p->nama_depag : '-'); ?></td>
                            <td>
                                <span class="status-pill <?php echo $pillClass; ?>"><?php echo $pillText; ?></span>
                            </td>
                            <td class="skor-cell">
                                <?php if (!empty($p->top1_nama_divisi)) { ?>
                                    <strong><?php echo htmlspecialchars($p->top1_nama_divisi); ?> · <?php echo number_format($p->top1_skor, 1, ',', '.'); ?></strong>
                                <?php } else { ?>
                                    <span class="status-sub">Hasil belum tersedia</span>
                                <?php } ?>
                            </td>
                            <td>
                                <?php if ($confClass) { ?>
                                    <span class="conf-pill <?php echo $confClass; ?>"><?php echo htmlspecialchars($confLabel); ?></span>
                                <?php } else { ?>
                                    <span class="status-sub"><?php echo htmlspecialchars($confLabel); ?></span>
                                <?php } ?>
                            </td>
                            <td class="aksi-cell">
                                <?php if ($tab_aktif === 'pending') { ?>
                                <a class="btn-aksi btn-terima" href="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $p->id; ?>">Terima</a>
                                <form method="post" action="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $p->id; ?>/tolak" style="display:inline;">
                                    <button type="submit" class="btn-aksi btn-tolak">Tolak</button>
                                </form>
                                <?php } ?>
                                <a class="btn-aksi btn-detail" href="<?php echo base_url(); ?>analisis/peserta/<?php echo (int) $p->id; ?>">Lihat detail</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;">
                    <span class="status-sub">Menampilkan <?php echo count($daftar_pelamar); ?> pelamar pada tab ini · urut berdasarkan tanggal mulai terdekat · saran bukan penetapan</span>
                    <div class="pager">
                        <button><?php icon_analisis('chevron-left', 14); ?></button>
                        <button class="active">1</button>
                        <button><?php icon_analisis('chevron-right', 14); ?></button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script>
    document.querySelectorAll('select.select-mini').forEach(function (el) {
        new TomSelect(el, { controlInput: null, allowEmptyOption: true });
    });
</script>
</body>
</html>
