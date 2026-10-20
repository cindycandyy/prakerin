<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar magang - E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,500,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-form.css" rel="stylesheet">
    <?php include APPPATH . 'views/pelamar/_style.php'; ?>
    <style>
        .langkah { display: none; }
        .langkah.aktif { display: block; }
    </style>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'daftar'; include APPPATH . 'views/pelamar/_sidebar.php'; ?>
    <div class="main">
        <?php $breadcrumb_akhir = 'Daftar magang'; include APPPATH . 'views/pelamar/_topbar.php'; ?>
        <main class="content">
            <div class="page-head"><div><h1>Daftar Magang/Penelitian</h1><p>Lengkapi empat langkah berikut. Isian akan tersimpan secara otomatis pada perangkat ini.</p></div></div>

            <?php $CI = &get_instance(); if ($CI->session->flashdata('error')) { ?>
            <div style="background:var(--merah-100); color:var(--merah-700); border-radius:10px; padding:12px 14px; font-size:12px;"><?php echo htmlspecialchars($CI->session->flashdata('error')); ?></div>
            <?php } ?>

            <div class="panel">
                <div class="stepper">
                    <div class="stepper-item aktif" data-stepper="1"><span class="stepper-num">1</span> Program &amp; jadwal</div>
                    <div class="stepper-item" data-stepper="2"><span class="stepper-num">2</span> Data diri</div>
                    <div class="stepper-item" data-stepper="3"><span class="stepper-num">3</span> Pendidikan</div>
                    <div class="stepper-item" data-stepper="4"><span class="stepper-num">4</span> Berkas</div>
                </div>

                <form id="form-daftar" method="post" enctype="multipart/form-data" action="<?php echo base_url(); ?>pelamar/daftar">
                    <input type="hidden" name="kirim" value="1">

                    <div class="langkah aktif" data-langkah="1">
                        <div class="form-grid">
                            <div><label class="field-label wajib">Jenis kegiatan</label>
                                <select class="field-select" name="kegiatan" required>
                                    <option value="Magang">Magang</option>
                                    <option value="Penelitian">Penelitian</option>
                                </select>
                            </div>
                            <div><label class="field-label wajib">Departemen tujuan</label>
                                <select class="field-select" name="id_depag" required>
                                    <option value="">Pilih departemen</option>
                                    <?php foreach ($depag as $d) {
                                        $penuh = $d->sisa !== null && $d->sisa <= 0;
                                        if ($d->sisa === null) { $kelas_status = 'netral'; $info = 'Belum diatur'; }
                                        else if ($penuh) { $kelas_status = 'penuh'; $info = 'Penuh'; }
                                        else if ($d->sisa <= 2) { $kelas_status = 'terbatas'; $info = 'Terbatas'; }
                                        else { $kelas_status = 'tersedia'; $info = 'Tersedia'; }
                                    ?>
                                    <option value="<?php echo (int) $d->id; ?>" <?php echo $penuh ? 'disabled' : ''; ?> data-status="<?php echo $kelas_status; ?>" data-status-label="<?php echo $info; ?>"><?php echo htmlspecialchars($d->nama_depag); ?></option>
                                    <?php } ?>
                                </select>
                                <p class="field-note">Departemen yang penuh tidak bisa dipilih. Penempatan divisi akhir tetap ditentukan HR.</p>
                            </div>
                            <div><label class="field-label wajib">Tanggal mulai</label><input class="field-input" type="date" name="tgl_mulai" required min="<?php echo date('Y-m-d', strtotime('+' . ($ambang_mepet_hari + 1) . ' days')); ?>">
                                <p class="field-note">Paling cepat mulai <?php echo tanggal_indo(date('Y-m-d', strtotime('+' . ($ambang_mepet_hari + 1) . ' days'))); ?>.</p>
                            </div>
                            <div><label class="field-label wajib">Tanggal selesai</label><input class="field-input" type="date" name="tgl_selesai" data-link-min-from="tgl_mulai" required>
                                <p class="field-note">Durasi minimal 30 hari.</p>
                            </div>
                        </div>
                    </div>

                    <?php if ($salinan) { ?>
                    <div class="field-note" style="background:var(--hijau-100); color:var(--hijau-800); border-radius:10px; padding:10px 12px; margin-bottom:16px;">Biodata dan data pendidikan disalin dari pengajuan sebelumnya. Mohon periksa kembali sebelum mengirim.</div>
                    <?php } ?>

                    <div class="langkah" data-langkah="2">
                        <div class="form-grid">
                            <div><label class="field-label wajib">Nama lengkap</label><input class="field-input" name="nama" value="<?php echo $salinan ? htmlspecialchars($salinan->nama) : ''; ?>" required></div>
                            <div><label class="field-label wajib">Jenis kelamin</label>
                                <select class="field-select" name="jk" required>
                                    <option value="L" <?php echo ($salinan && $salinan->jk === 'L') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="P" <?php echo ($salinan && $salinan->jk === 'P') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                            <div><label class="field-label wajib">Tempat lahir</label><input class="field-input" name="tmpt_lahir" value="<?php echo $salinan ? htmlspecialchars($salinan->tmpt_lahir) : ''; ?>" required></div>
                            <div><label class="field-label wajib">Tanggal lahir</label><input class="field-input" type="date" name="tgl_lahir" value="<?php echo $salinan ? htmlspecialchars($salinan->tgl_lahir) : ''; ?>" required></div>
                            <div style="grid-column:1/-1;"><label class="field-label wajib">Alamat</label><input class="field-input" name="alamat" value="<?php echo $salinan ? htmlspecialchars($salinan->alamat) : ''; ?>" required></div>
                            <div><label class="field-label wajib">Telepon</label><input class="field-input" name="telp" value="<?php echo $salinan ? htmlspecialchars($salinan->telp) : ''; ?>" pattern="(0|62)[0-9]{8,14}" required></div>
                            <div><label class="field-label wajib">Email</label><input class="field-input" type="email" name="email" value="<?php echo $salinan ? htmlspecialchars($salinan->email) : ''; ?>" required></div>
                            <div><label class="field-label wajib">Nama orang tua</label><input class="field-input" name="nm_ortu" value="<?php echo $salinan ? htmlspecialchars($salinan->nm_ortu) : ''; ?>" required></div>
                            <div><label class="field-label wajib">HP orang tua</label><input class="field-input" name="hp_ortu" value="<?php echo $salinan ? htmlspecialchars($salinan->hp_ortu) : ''; ?>" required></div>
                        </div>
                    </div>

                    <div class="langkah" data-langkah="3">
                        <div class="form-grid">
                            <div><label class="field-label wajib">Jenjang</label>
                                <select class="field-select" name="pendidikan" required>
                                    <?php foreach (array('SMK', 'D3', 'D4', 'S1') as $jenjang) { ?>
                                    <option value="<?php echo $jenjang; ?>" <?php echo ($salinan && $salinan->pendidikan === $jenjang) ? 'selected' : ''; ?>><?php echo $jenjang; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div><label class="field-label wajib">Institusi</label><input class="field-input" name="institusi" value="<?php echo $salinan ? htmlspecialchars($salinan->institusi) : ''; ?>" required></div>
                            <div><label class="field-label wajib">Jurusan</label><input class="field-input" name="jurusan" value="<?php echo $salinan ? htmlspecialchars($salinan->jurusan) : ''; ?>" required></div>
                            <div><label class="field-label wajib">Alamat institusi</label><input class="field-input" name="almt_institusi" value="<?php echo $salinan ? htmlspecialchars($salinan->almt_institusi) : ''; ?>" required></div>
                            <div><label class="field-label wajib">Pembimbing</label><input class="field-input" name="pembimbing" value="<?php echo $salinan ? htmlspecialchars($salinan->pembimbing) : ''; ?>" required></div>
                            <div><label class="field-label wajib">HP pembimbing</label><input class="field-input" name="hp_pembimbing" value="<?php echo $salinan ? htmlspecialchars($salinan->hp_pembimbing) : ''; ?>" required></div>
                        </div>

                        <label class="field-label" style="margin-top:10px;">Media sosial (opsional)</label>
                        <p class="field-note" style="margin-bottom:10px;">Tempel link profil lengkap, bukan username.</p>
                        <div class="form-grid">
                            <?php
                            $sosmed_input = array(
                                'sosmed_linkedin' => array('linkedin', 'LinkedIn', 'https://linkedin.com/in/namamu'),
                                'sosmed_instagram' => array('instagram', 'Instagram', 'https://instagram.com/namamu'),
                                'sosmed_tiktok' => array('tiktok', 'TikTok', 'https://tiktok.com/@namamu'),
                                'sosmed_twitter' => array('twitter', 'Twitter/X', 'https://twitter.com/namamu'),
                            );
                            foreach ($sosmed_input as $nama_field => $info) { ?>
                            <div>
                                <label class="field-label"><?php echo $info[1]; ?></label>
                                <div class="field-icon-wrap">
                                    <span class="field-icon"><?php icon_analisis($info[0], 15); ?></span>
                                    <input class="field-input" type="url" name="<?php echo $nama_field; ?>" placeholder="<?php echo $info[2]; ?>" value="<?php echo $salinan ? htmlspecialchars($salinan->{$nama_field} ?: '') : ''; ?>">
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="langkah" data-langkah="4">
                        <?php
                        $berkas_input = array(
                            'file_1' => array('Surat pengantar', 'application/pdf,.pdf', null),
                            'file_2' => array('Proposal', 'application/pdf,.pdf', 'Gunakan struktur bab baku (Latar Belakang, Tujuan, Ruang Lingkup/Bidang PKL, Mata Kuliah, Objek PKL) dan cantumkan tools/software yang dikuasai. Proposal akan dibaca sistem untuk menilai kecocokan divisi. Lihat <a href="' . base_url() . 'pelamar/panduan" target="_blank" style="color:var(--hijau-700); font-weight:600;">format lengkap pada Syarat &amp; Panduan</a>.'),
                            'file_3' => array('Transkrip', 'application/pdf,.pdf', null),
                            'file_5' => array('Kartu asuransi', 'application/pdf,.pdf', null),
                            'file_6' => array('Kartu pelajar', 'application/pdf,.pdf', null),
                            'file_7' => array('Surat pernyataan', 'application/pdf,.pdf', null),
                            'foto' => array('Foto (JPG/PNG, maks 2MB)', '.jpg,.jpeg,.png', 'Foto terbaru, formal, setengah badan (bukan close-up wajah). Bukan foto KTM/kartu pelajar, dan bukan hasil AI/edit berlebihan.'),
                        );
                        foreach ($berkas_input as $input_name => $info) { ?>
                        <div class="berkas-kartu" style="align-items:flex-start;">
                            <span class="berkas-kartu-icon"><?php icon_analisis($input_name === 'foto' ? 'image' : 'file-text', 16); ?></span>
                            <div style="flex:1;">
                                <div class="berkas-kartu-nama"><?php echo $info[0]; ?></div>
                                <div class="berkas-kartu-status" data-nama-file="<?php echo $input_name; ?>">Belum diunggah</div>
                                <?php if ($info[2]) { ?>
                                <div class="field-note" style="margin-top:6px;"><?php echo $info[2]; ?></div>
                                <?php } ?>
                            </div>
                            <label class="btn-outline" style="padding:6px 10px; font-size:11px; cursor:pointer; flex-shrink:0;">
                                Pilih <?php echo $input_name === 'foto' ? 'foto' : 'PDF'; ?>
                                <input type="file" name="<?php echo $input_name; ?>" accept="<?php echo $info[1]; ?>" required style="display:none;"
                                    onchange="document.querySelector('[data-nama-file=\'<?php echo $input_name; ?>\']').textContent = this.files[0] ? this.files[0].name : 'Belum diunggah'; this.closest('.berkas-kartu').classList.remove('field-invalid');">
                            </label>
                        </div>
                        <?php } ?>
                        <p class="field-note">Maks 5MB untuk PDF, 2MB untuk foto.</p>

                        <div id="kartu-persetujuan" class="berkas-kartu" style="align-items:flex-start; cursor:pointer;" onclick="if(event.target.tagName!=='INPUT'){document.getElementById('cek-persetujuan').click();}">
                            <input type="checkbox" id="cek-persetujuan" name="persetujuan_privasi" value="1" style="margin-top:2px; width:16px; height:16px; flex-shrink:0;" onchange="this.closest('.berkas-kartu').classList.remove('field-invalid');">
                            <div style="flex:1;">
                                <div class="berkas-kartu-nama">Saya menyetujui analisis otomatis terhadap dokumen yang diunggah</div>
                                <div class="field-note" style="margin-top:4px;">Proposal dan transkrip akan dibaca secara otomatis oleh sistem untuk membantu HR menilai kecocokan divisi. Dokumen lainnya akan ditinjau secara manual oleh HR.</div>
                            </div>
                        </div>
                    </div>

                    <div class="footer-aksi" style="display:flex; gap:10px; margin-top:20px;">
                        <button type="button" class="btn-outline" id="btn-kembali" style="display:none;">Kembali</button>
                        <button type="button" class="btn-primary" id="btn-lanjut">Lanjut</button>
                        <button type="submit" class="btn-primary" id="btn-kirim" style="display:none;">Kirim pengajuan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<div id="modal-kirim" class="modal-overlay" style="display:none;">
    <div class="modal-kartu">
        <h3>Kirim Pengajuan Ini?</h3>
        <p class="field-note" style="margin-top:6px;">Data dan berkas akan terkunci segera setelah pengajuan dikirim dan tidak dapat diubah kembali.</p>
        <div class="footer-aksi" style="margin-top:16px;">
            <button type="button" class="btn-outline" id="modal-kirim-batal">Batal</button>
            <button type="button" class="btn-primary" id="modal-kirim-ok">Kirim</button>
        </div>
    </div>
</div>
<script>
(function () {
    var langkahSekarang = 1;
    var total = 4;
    var form = document.getElementById('form-daftar');
    var KUNCI_DRAF = 'pelamar_draf_daftar';

    function tampilkanLangkah(n) {
        document.querySelectorAll('.langkah').forEach(function (el) { el.classList.toggle('aktif', +el.dataset.langkah === n); });
        document.querySelectorAll('.stepper-item').forEach(function (el) {
            var i = +el.dataset.stepper;
            el.classList.toggle('aktif', i === n);
            el.classList.toggle('selesai', i < n);
        });
        document.getElementById('btn-kembali').style.display = n > 1 ? 'inline-block' : 'none';
        document.getElementById('btn-lanjut').style.display = n < total ? 'inline-block' : 'none';
        document.getElementById('btn-kirim').style.display = n === total ? 'inline-block' : 'none';
    }

    // Validasi halus: field kosong/invalid digaris merah + goyang sebentar, bukan
    // balon validasi bawaan browser (reportValidity()) yang terlihat kasar.
    function tandaiInvalid(el, invalid) {
        var pembungkus = el.closest('div');
        if (!pembungkus) { return; }
        pembungkus.classList.remove('field-invalid');
        if (invalid) {
            // reflow supaya animasi goyang bisa diulang kalau field sama ditandai invalid dua kali berturut-turut
            void pembungkus.offsetWidth;
            pembungkus.classList.add('field-invalid');
        }
    }

    document.getElementById('btn-lanjut').addEventListener('click', function () {
        var aktif = document.querySelector('.langkah.aktif');
        var wajib = aktif.querySelectorAll('[required]');
        var field_pertama_invalid = null;
        wajib.forEach(function (el) {
            var invalid = !el.checkValidity();
            tandaiInvalid(el, invalid);
            if (invalid && !field_pertama_invalid) { field_pertama_invalid = el; }
        });
        if (field_pertama_invalid) {
            field_pertama_invalid.closest('div').scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        simpanDraf();
        langkahSekarang = Math.min(total, langkahSekarang + 1);
        tampilkanLangkah(langkahSekarang);
    });
    document.querySelectorAll('[required]').forEach(function (el) {
        el.addEventListener('input', function () { tandaiInvalid(el, false); });
        el.addEventListener('change', function () { tandaiInvalid(el, false); });
    });
    document.getElementById('btn-kembali').addEventListener('click', function () {
        langkahSekarang = Math.max(1, langkahSekarang - 1);
        tampilkanLangkah(langkahSekarang);
        simpanDraf();
    });

    // Auto-save tiap field berubah (bukan cuma saat pindah langkah) supaya ketikan
    // di langkah yang sama tidak hilang kalau tab di-refresh tanpa sempat klik Lanjut.
    form.addEventListener('input', simpanDraf);
    form.addEventListener('change', simpanDraf);

    function simpanDraf() {
        try {
            var data = { _langkah: langkahSekarang };
            form.querySelectorAll('input:not([type=file]), select, textarea').forEach(function (el) { data[el.name] = el.value; });
            localStorage.setItem(KUNCI_DRAF, JSON.stringify(data));
        } catch (e) {}
    }

    // Draft dipulihkan setelah seluruh halaman (termasuk analisis-form.js yang
    // memperkaya select/date) selesai dimuat, supaya tampilan dropdown custom &
    // kalender ikut tersinkron -- bukan cuma <select>/<input> aslinya yang
    // disembunyikan.
    function pulihkanDraf() {
        try {
            var draf = JSON.parse(localStorage.getItem(KUNCI_DRAF) || '{}');
            Object.keys(draf).forEach(function (nama) {
                if (nama === '_langkah') { return; }
                var el = form.querySelector('[name="' + nama + '"]');
                if (el && draf[nama]) { el.value = draf[nama]; el.dispatchEvent(new Event('change', { bubbles: true })); }
            });
            if (draf._langkah) {
                langkahSekarang = Math.min(total, Math.max(1, draf._langkah));
                tampilkanLangkah(langkahSekarang);
            }
        } catch (e) {}
    }
    window.addEventListener('load', pulihkanDraf);

    // Modal konfirmasi custom bergaya GSM, pengganti confirm() bawaan browser yang
    // tidak bisa di-styling sama sekali.
    var modalKirim = document.getElementById('modal-kirim');
    var sudahDikonfirmasi = false;

    function bukaModal() {
        modalKirim.style.display = 'flex';
        requestAnimationFrame(function () { modalKirim.classList.add('tampil'); });
    }
    function tutupModal() {
        modalKirim.classList.remove('tampil');
        setTimeout(function () { modalKirim.style.display = 'none'; }, 150);
    }
    document.getElementById('modal-kirim-batal').addEventListener('click', tutupModal);
    document.getElementById('modal-kirim-ok').addEventListener('click', function () {
        sudahDikonfirmasi = true;
        tutupModal();
        // Draf TIDAK dihapus di sini -- kalau server menolak pengajuan (mis. berkas
        // tidak valid), halaman ini dimuat ulang dan draf harus tetap ada supaya
        // langkah 1-3 tidak perlu diisi ulang. Draf baru dihapus setelah submit
        // benar-benar sukses, lewat script di pelamar/detail.php yang jalan saat
        // mendarat di halaman pengajuan baru (ditandai flashdata sukses).
        simpanDraf();
        form.submit();
    });

    form.addEventListener('submit', function (e) {
        var berkas_kosong = null;
        document.querySelectorAll('.langkah[data-langkah="4"] input[type=file][required]').forEach(function (el) {
            var kartu = el.closest('.berkas-kartu');
            var invalid = el.files.length === 0;
            kartu.classList.toggle('field-invalid', invalid);
            if (invalid && !berkas_kosong) { berkas_kosong = kartu; }
        });
        if (berkas_kosong) {
            e.preventDefault();
            berkas_kosong.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        var kartuPersetujuan = document.getElementById('kartu-persetujuan');
        if (!document.getElementById('cek-persetujuan').checked) {
            e.preventDefault();
            kartuPersetujuan.classList.add('field-invalid');
            kartuPersetujuan.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }
        if (!sudahDikonfirmasi) {
            e.preventDefault();
            bukaModal();
        }
    });
})();
</script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/analisis-form.js"></script>
</body>
</html>
