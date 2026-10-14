<?php include APPPATH . 'views/analisis/_icon.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar magang — E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,500,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <?php include 'pelamar/_style.php'; ?>
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
            <div class="page-head"><div><h1>Daftar magang/penelitian</h1><p>Lengkapi 4 langkah berikut. Isian tersimpan otomatis di perangkatmu.</p></div></div>

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
                            <div><label class="field-label">Jenis kegiatan *</label>
                                <select class="field-select" name="kegiatan" required>
                                    <option value="Magang">Magang</option>
                                    <option value="Penelitian">Penelitian</option>
                                </select>
                            </div>
                            <div><label class="field-label">Departemen tujuan *</label>
                                <select class="field-select" name="id_depag" required>
                                    <option value="">Pilih departemen</option>
                                    <?php foreach ($depag as $d) {
                                        $info = $d->sisa === null ? 'Belum diatur' : ($d->sisa <= 0 ? 'Penuh' : ($d->sisa <= 2 ? 'Terbatas' : 'Tersedia'));
                                    ?>
                                    <option value="<?php echo (int) $d->id; ?>"><?php echo htmlspecialchars($d->nama_depag); ?> — <?php echo $info; ?></option>
                                    <?php } ?>
                                </select>
                                <p class="field-note">Status tempat bersifat informasi; keputusan akhir ditentukan HR.</p>
                            </div>
                            <div><label class="field-label">Tanggal mulai *</label><input class="field-input" type="date" name="tgl_mulai" required min="<?php echo date('Y-m-d', strtotime('+' . ($ambang_mepet_hari + 1) . ' days')); ?>">
                                <p class="field-note">Paling cepat mulai <?php echo tanggal_indo(date('Y-m-d', strtotime('+' . ($ambang_mepet_hari + 1) . ' days'))); ?>.</p>
                            </div>
                            <div><label class="field-label">Tanggal selesai *</label><input class="field-input" type="date" name="tgl_selesai" required>
                                <p class="field-note">Durasi 30–180 hari.</p>
                            </div>
                        </div>
                    </div>

                    <div class="langkah" data-langkah="2">
                        <div class="form-grid">
                            <div><label class="field-label">Nama lengkap *</label><input class="field-input" name="nama" required></div>
                            <div><label class="field-label">Jenis kelamin *</label>
                                <select class="field-select" name="jk" required><option value="L">Laki-laki</option><option value="P">Perempuan</option></select>
                            </div>
                            <div><label class="field-label">Tempat lahir *</label><input class="field-input" name="tmpt_lahir" required></div>
                            <div><label class="field-label">Tanggal lahir *</label><input class="field-input" type="date" name="tgl_lahir" required></div>
                            <div style="grid-column:1/-1;"><label class="field-label">Alamat *</label><input class="field-input" name="alamat" required></div>
                            <div><label class="field-label">Telepon *</label><input class="field-input" name="telp" pattern="(0|62)[0-9]{8,14}" required></div>
                            <div><label class="field-label">Email *</label><input class="field-input" type="email" name="email" required></div>
                            <div><label class="field-label">Nama orang tua *</label><input class="field-input" name="nm_ortu" required></div>
                            <div><label class="field-label">HP orang tua *</label><input class="field-input" name="hp_ortu" required></div>
                        </div>
                    </div>

                    <div class="langkah" data-langkah="3">
                        <div class="form-grid">
                            <div><label class="field-label">Jenjang *</label>
                                <select class="field-select" name="pendidikan" required>
                                    <option value="SMK">SMK</option><option value="D3">D3</option><option value="D4">D4</option><option value="S1">S1</option><option value="S2">S2</option>
                                </select>
                            </div>
                            <div><label class="field-label">Institusi *</label><input class="field-input" name="institusi" required></div>
                            <div><label class="field-label">Jurusan *</label><input class="field-input" name="jurusan" required></div>
                            <div><label class="field-label">Alamat institusi *</label><input class="field-input" name="almt_institusi" required></div>
                            <div><label class="field-label">Pembimbing *</label><input class="field-input" name="pembimbing" required></div>
                            <div><label class="field-label">HP pembimbing *</label><input class="field-input" name="hp_pembimbing" required></div>
                        </div>
                    </div>

                    <div class="langkah" data-langkah="4">
                        <?php
                        $berkas_input = array('file_1' => array('Surat pengantar', 'application/pdf,.pdf'), 'file_2' => array('Proposal', 'application/pdf,.pdf'),
                            'file_3' => array('Transkrip', 'application/pdf,.pdf'), 'file_5' => array('Kartu asuransi', 'application/pdf,.pdf'),
                            'file_6' => array('Kartu pelajar', 'application/pdf,.pdf'), 'file_7' => array('Surat pernyataan', 'application/pdf,.pdf'),
                            'foto' => array('Foto (JPG, maks 2MB)', '.jpg,.jpeg'));
                        foreach ($berkas_input as $input_name => $info) { ?>
                        <div class="berkas-kartu">
                            <span class="berkas-kartu-icon"><?php icon_analisis($input_name === 'foto' ? 'image' : 'file-text', 16); ?></span>
                            <div style="flex:1;">
                                <div class="berkas-kartu-nama"><?php echo $info[0]; ?></div>
                                <div class="berkas-kartu-status" data-nama-file="<?php echo $input_name; ?>">Belum diunggah</div>
                            </div>
                            <label class="btn-outline" style="padding:6px 10px; font-size:11px; cursor:pointer;">
                                Pilih <?php echo $input_name === 'foto' ? 'JPG' : 'PDF'; ?>
                                <input type="file" name="<?php echo $input_name; ?>" accept="<?php echo $info[1]; ?>" required style="display:none;"
                                    onchange="document.querySelector('[data-nama-file=\'<?php echo $input_name; ?>\']').textContent = this.files[0] ? this.files[0].name : 'Belum diunggah';">
                            </label>
                        </div>
                        <?php } ?>
                        <p class="field-note">Maks 5MB untuk PDF, 2MB untuk foto.</p>
                    </div>

                    <div class="footer-aksi" style="display:flex; gap:10px; margin-top:20px;">
                        <button type="button" class="btn-chip" id="btn-kembali" style="display:none;">Kembali</button>
                        <button type="button" class="btn-primary" id="btn-lanjut">Lanjut</button>
                        <button type="submit" class="btn-primary" id="btn-kirim" style="display:none;">Kirim pengajuan</button>
                    </div>
                </form>
            </div>
        </main>
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

    document.getElementById('btn-lanjut').addEventListener('click', function () {
        var aktif = document.querySelector('.langkah.aktif');
        var wajib = aktif.querySelectorAll('[required]');
        for (var i = 0; i < wajib.length; i++) { if (!wajib[i].reportValidity()) { return; } }
        simpanDraf();
        langkahSekarang = Math.min(total, langkahSekarang + 1);
        tampilkanLangkah(langkahSekarang);
    });
    document.getElementById('btn-kembali').addEventListener('click', function () {
        langkahSekarang = Math.max(1, langkahSekarang - 1);
        tampilkanLangkah(langkahSekarang);
    });

    function simpanDraf() {
        try {
            var data = {};
            form.querySelectorAll('input:not([type=file]), select, textarea').forEach(function (el) { data[el.name] = el.value; });
            localStorage.setItem(KUNCI_DRAF, JSON.stringify(data));
        } catch (e) {}
    }
    try {
        var draf = JSON.parse(localStorage.getItem(KUNCI_DRAF) || '{}');
        Object.keys(draf).forEach(function (nama) {
            var el = form.querySelector('[name="' + nama + '"]');
            if (el) { el.value = draf[nama]; }
        });
    } catch (e) {}

    form.addEventListener('submit', function (e) {
        if (!confirm('Kirim pengajuan ini? Data tidak bisa diubah lagi setelah diproses HR.')) { e.preventDefault(); return; }
        try { localStorage.removeItem(KUNCI_DRAF); } catch (e2) {}
    });
})();
</script>
</body>
</html>
