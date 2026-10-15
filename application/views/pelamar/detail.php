<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail pengajuan - E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,500,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-form.css" rel="stylesheet">
    <?php include APPPATH . 'views/pelamar/_style.php'; ?>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'pengajuan'; include APPPATH . 'views/pelamar/_sidebar.php'; ?>
    <div class="main">
        <?php $breadcrumb_akhir = 'Detail pengajuan'; include APPPATH . 'views/pelamar/_topbar.php'; ?>
        <main class="content">
            <a class="back-link" href="<?php echo base_url(); ?>pelamar/pengajuan" style="display:flex; align-items:center; gap:8px; color:var(--hijau-700); font-size:12px;">
                <?php icon_analisis('arrow-left', 16); ?> Kembali ke pengajuan saya
            </a>

            <?php $CI = &get_instance();
            if ($CI->session->flashdata('sukses')) { ?>
            <div style="background:var(--hijau-100); color:var(--hijau-800); border-radius:10px; padding:12px 14px; font-size:12px;"><?php echo htmlspecialchars($CI->session->flashdata('sukses')); ?></div>
            <?php } if ($CI->session->flashdata('error')) { ?>
            <div style="background:var(--merah-100); color:var(--merah-700); border-radius:10px; padding:12px 14px; font-size:12px;"><?php echo htmlspecialchars($CI->session->flashdata('error')); ?></div>
            <?php }

            $status_l = strtolower($p->status);
            $peta_status = array('pending' => array('Menunggu keputusan', 'status-pending'),
                'accept' => array('Diterima', 'status-diterima'), 'rejected' => array('Tidak dapat diproses', 'status-ditolak'),
                'inactive' => array('Selesai', 'status-tidakaktif'), 'cancelled' => array('Dibatalkan', 'status-tidakaktif'));
            $s = isset($peta_status[$status_l]) ? $peta_status[$status_l] : array($p->status, 'status-tidakaktif');

            $langkah = array('dikirim' => true, 'berkas_dicek' => true,
                'keputusan' => in_array($status_l, array('accept', 'rejected', 'inactive')),
                'surat' => in_array($status_l, array('accept', 'inactive')) && !empty($surat),
                'berjalan' => in_array($status_l, array('accept', 'inactive')),
                'selesai' => $status_l === 'inactive');
            ?>

            <div class="page-head"><div><h1><?php echo htmlspecialchars($p->nama); ?></h1><p><?php echo htmlspecialchars($p->kegiatan); ?> · <?php echo htmlspecialchars($p->nama_depag ?: '-'); ?></p></div>
                <span class="status-pill <?php echo $s[1]; ?>"><?php echo $s[0]; ?></span>
            </div>

            <div class="two-col">
                <div class="panel" style="flex:1.1;">
                    <div class="panel-head"><h2>Riwayat status</h2></div>
                    <ul class="timeline">
                        <li class="selesai"><div class="timeline-dot"></div><div><div class="timeline-judul">Dikirim</div><div class="timeline-sub">Pengajuan diterima sistem</div></div></li>
                        <li class="selesai"><div class="timeline-dot"></div><div><div class="timeline-judul">Berkas dicek</div><div class="timeline-sub">Sedang/sudah ditinjau</div></div></li>
                        <li class="<?php echo $langkah['keputusan'] ? 'selesai' : ''; ?>"><div class="timeline-dot"></div><div><div class="timeline-judul">Keputusan</div><div class="timeline-sub"><?php echo $langkah['keputusan'] ? $s[0] : 'Menunggu'; ?></div></div></li>
                        <li class="<?php echo $langkah['surat'] ? 'selesai' : ''; ?>"><div class="timeline-dot"></div><div><div class="timeline-judul">Surat balasan</div><div class="timeline-sub"><?php echo $langkah['surat'] ? 'Tersedia' : 'Belum tersedia'; ?></div></div></li>
                        <li class="<?php echo $langkah['berjalan'] ? 'selesai' : ''; ?>"><div class="timeline-dot"></div><div><div class="timeline-judul">Magang berjalan</div><div class="timeline-sub"><?php echo tanggal_indo($p->tgl_mulai); ?> – <?php echo tanggal_indo($p->tgl_selesai); ?></div></div></li>
                        <li class="<?php echo $langkah['selesai'] ? 'selesai' : ''; ?>"><div class="timeline-dot"></div><div><div class="timeline-judul">Selesai</div></div></li>
                    </ul>

                    <?php if ($status_l === 'rejected' && !empty($p->alasan_pelamar)) { ?>
                    <div style="background:var(--merah-100); color:var(--merah-700); border-radius:10px; padding:12px 14px; font-size:12px; margin-top:14px;">Alasan: <?php echo htmlspecialchars($p->alasan_pelamar); ?></div>
                    <?php } ?>

                    <h3 style="margin-top:20px;">Data pengajuan</h3>
                    <div class="form-grid" style="margin-top:10px;">
                        <div><span class="field-note">Nama</span><div><?php echo htmlspecialchars($p->nama); ?></div></div>
                        <div><span class="field-note">Jenis kelamin</span><div><?php echo $p->jk === 'L' ? 'Laki-laki' : 'Perempuan'; ?></div></div>
                        <div><span class="field-note">Pendidikan</span><div><?php echo htmlspecialchars($p->pendidikan); ?> · <?php echo htmlspecialchars($p->institusi); ?></div></div>
                        <div><span class="field-note">Jurusan</span><div><?php echo htmlspecialchars($p->jurusan ?: '-'); ?></div></div>
                        <div><span class="field-note">Telepon</span><div><?php echo htmlspecialchars($p->telp); ?></div></div>
                        <div><span class="field-note">Email</span><div><?php echo htmlspecialchars($p->email); ?></div></div>
                    </div>

                    <?php if ($status_l === 'pending') { ?>
                    <form method="post" action="<?php echo base_url(); ?>pelamar/pengajuan/<?php echo (int) $p->id; ?>/ubah" style="margin-top:16px;">
                        <div class="form-grid">
                            <div><label class="field-label">Alamat</label><input class="field-input" name="alamat" value="<?php echo htmlspecialchars($p->alamat); ?>"></div>
                            <div><label class="field-label">Telepon</label><input class="field-input" name="telp" value="<?php echo htmlspecialchars($p->telp); ?>"></div>
                            <div><label class="field-label">Email</label><input class="field-input" type="email" name="email" value="<?php echo htmlspecialchars($p->email); ?>"></div>
                            <div><label class="field-label">Pembimbing</label><input class="field-input" name="pembimbing" value="<?php echo htmlspecialchars($p->pembimbing); ?>"></div>
                        </div>
                        <input type="hidden" name="kegiatan" value="<?php echo htmlspecialchars($p->kegiatan); ?>">
                        <input type="hidden" name="nama" value="<?php echo htmlspecialchars($p->nama); ?>">
                        <input type="hidden" name="jk" value="<?php echo htmlspecialchars($p->jk); ?>">
                        <input type="hidden" name="tmpt_lahir" value="<?php echo htmlspecialchars($p->tmpt_lahir); ?>">
                        <input type="hidden" name="tgl_lahir" value="<?php echo htmlspecialchars($p->tgl_lahir); ?>">
                        <input type="hidden" name="nm_ortu" value="<?php echo htmlspecialchars($p->nm_ortu); ?>">
                        <input type="hidden" name="hp_ortu" value="<?php echo htmlspecialchars($p->hp_ortu); ?>">
                        <input type="hidden" name="pendidikan" value="<?php echo htmlspecialchars($p->pendidikan); ?>">
                        <input type="hidden" name="jurusan" value="<?php echo htmlspecialchars($p->jurusan); ?>">
                        <input type="hidden" name="institusi" value="<?php echo htmlspecialchars($p->institusi); ?>">
                        <input type="hidden" name="almt_institusi" value="<?php echo htmlspecialchars($p->almt_institusi); ?>">
                        <input type="hidden" name="id_depag" value="<?php echo (int) $p->id_depag; ?>">
                        <input type="hidden" name="hp_pembimbing" value="<?php echo htmlspecialchars($p->hp_pembimbing); ?>">
                        <input type="hidden" name="tgl_mulai" value="<?php echo htmlspecialchars($p->tgl_mulai); ?>">
                        <input type="hidden" name="tgl_selesai" value="<?php echo htmlspecialchars($p->tgl_selesai); ?>">
                        <button type="submit" class="btn-outline">Simpan perubahan</button>
                    </form>
                    <form method="post" action="<?php echo base_url(); ?>pelamar/pengajuan/<?php echo (int) $p->id; ?>/batal" style="margin-top:10px;" onsubmit="return confirm('Batalkan pengajuan ini?');">
                        <button type="submit" class="btn-outline" style="color:var(--merah-700); border-color:var(--merah-100);">Batalkan pengajuan</button>
                    </form>
                    <?php } ?>
                </div>

                <div class="panel" style="flex:0.9;">
                    <div class="panel-head"><h2>Berkas</h2></div>
                    <?php
                    $label = array('doc_1' => 'Surat pengantar', 'doc_2' => 'Proposal', 'doc_3' => 'Transkrip',
                        'doc_5' => 'Kartu asuransi', 'doc_6' => 'Kartu pelajar', 'doc_7' => 'Surat pernyataan', 'foto' => 'Foto');
                    foreach ($label as $slot => $nama_label) {
                        $ada = !empty($p->{$slot});
                        $bisa_ganti = $status_l === 'pending' || ($status_l === 'accept' && $slot === 'doc_7' && !$ada);
                    ?>
                    <div class="berkas-kartu">
                        <span class="berkas-kartu-icon"><?php icon_analisis($slot === 'foto' ? 'image' : 'file-text', 16); ?></span>
                        <div style="flex:1;">
                            <div class="berkas-kartu-nama"><?php echo $nama_label; ?></div>
                            <div class="berkas-kartu-status <?php echo $ada ? 'ok' : ''; ?>"><?php echo $ada ? 'Terunggah' : 'Belum diunggah'; ?></div>
                        </div>
                        <?php if ($ada) { ?>
                        <a class="btn-outline" style="padding:6px 10px; font-size:11px;" target="_blank" href="<?php echo base_url(); ?>pelamar/berkas/<?php echo (int) $p->id; ?>/<?php echo $slot; ?>">Lihat</a>
                        <?php } ?>
                        <?php if ($bisa_ganti) { ?>
                        <form method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>pelamar/pengajuan/<?php echo (int) $p->id; ?>/berkas/<?php echo $slot; ?>">
                            <label class="btn-outline" style="padding:6px 10px; font-size:11px; cursor:pointer;">
                                <?php echo $ada ? 'Ganti' : 'Unggah'; ?>
                                <input type="file" name="berkas" accept="<?php echo $slot === 'foto' ? '.jpg,.jpeg' : '.pdf'; ?>" style="display:none;" onchange="this.form.submit()">
                            </label>
                        </form>
                        <?php } ?>
                    </div>
                    <?php } ?>

                    <?php if ($status_l === 'accept' || $status_l === 'inactive') { ?>
                    <div class="panel-head" style="margin-top:14px;"><h2>Dokumen magang</h2></div>
                    <?php if ($surat) { ?>
                    <a class="btn-outline" style="width:100%; text-align:center; box-sizing:border-box; margin-bottom:8px;" href="<?php echo base_url(); ?>pelamar/surat/<?php echo (int) $p->id; ?>/unduh">Unduh surat balasan</a>
                    <?php } else { ?>
                    <p class="field-note">Surat balasan belum tersedia.</p>
                    <?php } ?>
                    <?php if (!empty($p->no_pkl)) { ?>
                    <a class="btn-outline" style="width:100%; text-align:center; box-sizing:border-box;" href="<?php echo base_url(); ?>pelamar/kartu/<?php echo (int) $p->id; ?>">Unduh kartu magang</a>
                    <?php } else { ?>
                    <p class="field-note">Kartu magang belum tersedia.</p>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </main>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/analisis-form.js"></script>
</body>
</html>
