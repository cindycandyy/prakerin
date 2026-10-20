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

            <?php
            // Flashdata CI3 dibaca lalu langsung dibuang manual (unset_userdata) --
            // mengandalkan expiry otomatis "request berikutnya" membuat pesan lama bisa
            // nyangkut ke halaman lain yang kebetulan dibuka sebelum generasi flashdata
            // itu berputar, jadi pesan gagal dari percobaan sebelumnya ikut muncul lagi
            // di halaman yang tidak terkait.
            $CI = &get_instance();
            $pesan_sukses = $CI->session->flashdata('sukses');
            $pesan_error = $CI->session->flashdata('error');
            $CI->session->unset_userdata('sukses');
            $CI->session->unset_userdata('error');
            if ($pesan_sukses) { ?>
            <div style="background:var(--hijau-100); color:var(--hijau-800); border-radius:10px; padding:12px 14px; font-size:12px;"><?php echo htmlspecialchars($pesan_sukses); ?></div>
            <?php } if ($pesan_error) { ?>
            <div style="background:var(--merah-100); color:var(--merah-700); border-radius:10px; padding:12px 14px; font-size:12px;"><?php echo htmlspecialchars($pesan_error); ?></div>
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
                    <div style="background:var(--abu-100); border-radius:10px; padding:14px 16px; margin-top:20px;">
                        <p class="field-note" style="margin:0;">Data pengajuan terkunci sejak dikirim dan tidak dapat diubah. Apabila terdapat kesalahan, silakan batalkan pengajuan ini dan ajukan kembali dengan data yang benar.</p>
                        <form method="post" action="<?php echo base_url(); ?>pelamar/pengajuan/<?php echo (int) $p->id; ?>/batal" id="form-batal" style="margin-top:14px;">
                            <button type="button" class="btn-bahaya" id="btn-batal-buka">Batalkan Pengajuan</button>
                        </form>
                    </div>
                    <?php } ?>
                </div>

                <div class="panel" style="flex:0.9;">
                    <div class="panel-head"><h2>Berkas</h2></div>
                    <?php
                    $label = array('doc_1' => 'Surat pengantar', 'doc_2' => 'Proposal', 'doc_3' => 'Transkrip',
                        'doc_5' => 'Kartu asuransi', 'doc_6' => 'Kartu pelajar', 'doc_7' => 'Surat pernyataan', 'foto' => 'Foto');
                    foreach ($label as $slot => $nama_label) {
                        $ada = !empty($p->{$slot});
                        // Pending: berkas terkunci sejak dikirim, sama seperti data. Satu-satunya
                        // pengecualian: doc_7 (surat pernyataan) masih boleh DILENGKAPI setelah
                        // Accept kalau belum pernah ada -- itu bukan "mengganti", tapi menambah
                        // dokumen yang memang belum diminta saat Pending.
                        $bisa_ganti = ($status_l === 'accept' && $slot === 'doc_7' && !$ada);
                    ?>
                    <div class="berkas-kartu">
                        <span class="berkas-kartu-icon"><?php icon_analisis($slot === 'foto' ? 'image' : 'file-text', 16); ?></span>
                        <div style="flex:1;">
                            <div class="berkas-kartu-nama"><?php echo $nama_label; ?></div>
                            <div class="berkas-kartu-status <?php echo $ada ? 'ok' : ''; ?>"><?php echo $ada ? 'Terunggah' : 'Belum diunggah'; ?></div>
                        </div>
                        <?php if ($ada) { ?>
                        <a class="btn-outline" style="padding:6px 10px; font-size:11px;" href="<?php echo base_url(); ?>pelamar/pengajuan/<?php echo (int) $p->id; ?>/lihat/<?php echo $slot; ?>">Lihat</a>
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

<?php if ($status_l === 'pending') { ?>
<div id="modal-batal" class="modal-overlay" style="display:none;">
    <div class="modal-kartu">
        <h3>Batalkan Pengajuan Ini?</h3>
        <p class="field-note" style="margin-top:6px;">Pengajuan akan ditandai sebagai dibatalkan dan tidak dapat dipulihkan. Pengajuan baru dapat diajukan kembali kapan saja.</p>
        <div class="footer-aksi" style="margin-top:16px;">
            <button type="button" class="btn-outline" id="modal-batal-batal">Batal</button>
            <button type="button" class="btn-bahaya" id="modal-batal-ok">Batalkan Pengajuan</button>
        </div>
    </div>
</div>
<script>
(function () {
    var modal = document.getElementById('modal-batal');
    function buka() {
        modal.style.display = 'flex';
        requestAnimationFrame(function () { modal.classList.add('tampil'); });
    }
    function tutup() {
        modal.classList.remove('tampil');
        setTimeout(function () { modal.style.display = 'none'; }, 150);
    }
    document.getElementById('btn-batal-buka').addEventListener('click', buka);
    document.getElementById('modal-batal-batal').addEventListener('click', tutup);
    document.getElementById('modal-batal-ok').addEventListener('click', function () {
        document.getElementById('form-batal').submit();
    });
})();
</script>
<?php } ?>
    <?php if ($pesan_sukses) { ?>
    <script>
    // Pengajuan baru berhasil terkirim -- draf wizard (termasuk posisi langkah)
    // tidak dibutuhkan lagi, bersihkan supaya pengajuan berikutnya mulai kosong.
    try { localStorage.removeItem('pelamar_draf_daftar'); } catch (e) {}
    </script>
    <?php } ?>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/analisis-form.js"></script>
</body>
</html>
