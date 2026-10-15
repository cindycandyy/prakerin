<?php
include APPPATH . 'views/analisis/_icon.php';

// Kelompokkan divisi per departemen + hitung ringkasan untuk KPI.
$per_departemen = array();
$total_kuota = 0;
$total_terisi = 0;
$divisi_penuh = 0;
foreach ($divisi as $d) {
    $per_departemen[$d->nama_departemen][] = $d;
    $total_kuota += (int) $d->kuota_maks;
    $total_terisi += min((int) $d->terisi, (int) $d->kuota_maks);
    if ((int) $d->kuota_maks > 0 && (int) $d->terisi >= (int) $d->kuota_maks) {
        $divisi_penuh++;
    }
}
$total_tersedia = max(0, $total_kuota - $total_terisi);

// Status satu divisi: kelas CSS + label, mengikuti aturan warna panel "Kuota departemen" di dashboard.
function kuota_status_analisis($kuota, $terisi) {
    $sisa = max(0, $kuota - $terisi);
    if ($kuota <= 0) {
        return array('', 'Belum diatur');
    }
    if ($sisa === 0) {
        return array('full', 'Penuh');
    }
    if ($sisa <= 2) {
        return array('low', 'Sisa ' . $sisa);
    }
    return array('ok', 'Sisa ' . $sisa);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuota Divisi - Analisis Penempatan | E-Prakerin</title>
    <link href="//fonts.googleapis.com/css?family=Inter:400,500,600,700,800" rel="stylesheet">
    <link href="<?php echo base_url(); ?>assets/css/analisis-shell.css" rel="stylesheet">
    <style>
        .kpi-row { display: flex; gap: 16px; }
        .kpi-card {
            flex: 1; background: #fff; border: 1px solid var(--abu-200); border-radius: 12px;
            padding: 20px; display: flex; flex-direction: column; gap: 12px;
        }
        .kpi-top { display: flex; align-items: center; justify-content: space-between; }
        .kpi-label { color: var(--abu-600); font-size: 13px; }
        .kpi-icon { background: var(--hijau-100); border-radius: 8px; padding: 8px; display: flex; }
        .kpi-icon.warn { background: var(--kuning-bg); }
        .kpi-icon.bad { background: var(--merah-100); }
        .kpi-value { font-size: 32px; font-weight: 700; }
        .kpi-note { color: var(--abu-600); font-size: 12px; }
        .kpi-note.warn { color: var(--kuning-teks); }

        .kuota-info {
            display: flex; gap: 10px; align-items: flex-start; background: var(--hijau-100);
            border-radius: 10px; padding: 12px 14px; font-size: 12px; color: var(--hijau-800);
        }
        .kuota-info svg { flex-shrink: 0; margin-top: 1px; }

        .panel-head h2 { font-size: 16px; font-weight: 700; color: var(--abu-900); }

        .tabel-scroll { width: 100%; overflow-x: auto; }
        table.kuota { width: 100%; min-width: 640px; border-collapse: collapse; font-size: 12px; }
        table.kuota th {
            text-align: left; color: var(--abu-600); font-weight: 600; padding: 8px 10px;
            border-bottom: 1px solid var(--abu-200); font-size: 11px; text-transform: uppercase;
        }
        table.kuota td { padding: 14px 10px; border-bottom: 1px solid var(--abu-100); vertical-align: middle; }
        table.kuota tr:last-child td { border-bottom: none; }
        .col-divisi { width: 30%; }
        .col-terisi { width: 36%; }
        .col-kuota { width: 18%; }
        .col-aksi { width: 16%; text-align: right; }
        table.kuota th.col-aksi { text-align: right; }
        .divisi-nama { font-weight: 600; color: var(--abu-900); }

        .quota-top { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px; }
        .quota-status { font-size: 11px; color: var(--merah-700); font-weight: 600; }
        .quota-status.ok { color: var(--hijau-700); }
        .quota-status.low { color: var(--kuning-teks); }
        .quota-status.none { color: var(--abu-600); }
        .quota-track { background: var(--abu-100); border-radius: 6px; height: 8px; overflow: hidden; }
        .quota-fill { height: 100%; border-radius: 6px; background: var(--hijau-700); transition: width .2s ease; }
        .quota-fill.full { background: var(--merah-700); }
        .quota-fill.low { background: #c08a2b; }

        .stepper { display: inline-flex; align-items: center; border: 1px solid var(--abu-200); border-radius: 8px; background: #fff; overflow: hidden; }
        .stepper:focus-within { border-color: var(--hijau-700); box-shadow: 0 0 0 3px var(--hijau-100); }
        .stepper button {
            border: none; background: var(--abu-100); width: 30px; height: 34px; font-size: 16px;
            font-weight: 600; color: var(--abu-900); cursor: pointer; font-family: inherit;
        }
        .stepper button:hover { background: var(--abu-200); transform: none; filter: none; }
        .stepper input {
            border: none; width: 48px; height: 34px; text-align: center; font-size: 13px; font-weight: 600;
            font-family: inherit; color: var(--abu-900); -moz-appearance: textfield; background: #fff;
        }
        .stepper input::-webkit-outer-spin-button, .stepper input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .stepper input:focus { outline: none; }

        .btn-simpan {
            border: none; border-radius: 8px; padding: 9px 16px; font-size: 12px; font-weight: 600;
            background: var(--hijau-700); color: #fff; cursor: pointer; font-family: inherit; min-width: 84px;
        }
        .btn-simpan:disabled { background: var(--abu-100); color: var(--abu-400); cursor: default; transform: none; filter: none; }
        .btn-simpan.sukses { background: var(--hijau-100); color: var(--hijau-700); }
        .btn-simpan.gagal { background: var(--merah-100); color: var(--merah-700); }

        .kosong { text-align: center; color: var(--abu-600); padding: 32px 10px; font-size: 12px; }
        .grup-panel { display: flex; flex-direction: column; gap: 24px; }

        @media (max-width: 900px) {
            .kpi-row { flex-wrap: wrap; }
            .kpi-card { flex: 1 1 calc(50% - 16px); }
        }
    </style>
</head>
<body>
<div class="layout">
    <?php $halaman_aktif = 'kuota'; include APPPATH . 'views/analisis/_sidebar.php'; ?>

    <div class="main">
        <?php $breadcrumb_akhir = 'Kuota penempatan'; include APPPATH . 'views/analisis/_topbar.php'; ?>

        <main class="content">
            <div class="page-head">
                <div>
                    <h1>Kuota divisi</h1>
                    <p>Atur jumlah tempat magang tiap divisi. Perubahan langsung dipakai saat HR menerima pelamar.</p>
                </div>
            </div>

            <div class="kpi-row">
                <div class="kpi-card">
                    <div class="kpi-top">
                        <span class="kpi-label">Total kuota</span>
                        <span class="kpi-icon"><?php icon_analisis('chart-no-axes-combined', 18); ?></span>
                    </div>
                    <div class="kpi-value"><?php echo $total_kuota; ?></div>
                    <div class="kpi-note">Di <?php echo count($divisi); ?> divisi · <?php echo count($per_departemen); ?> departemen</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-top">
                        <span class="kpi-label">Tempat terisi</span>
                        <span class="kpi-icon"><?php icon_analisis('users-round', 18); ?></span>
                    </div>
                    <div class="kpi-value"><?php echo $total_terisi; ?></div>
                    <div class="kpi-note">Peserta magang aktif hari ini</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-top">
                        <span class="kpi-label">Tempat tersedia</span>
                        <span class="kpi-icon"><?php icon_analisis('check-circle', 18); ?></span>
                    </div>
                    <div class="kpi-value"><?php echo $total_tersedia; ?></div>
                    <div class="kpi-note">Bisa diisi pelamar baru</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-top">
                        <span class="kpi-label">Divisi penuh</span>
                        <span class="kpi-icon <?php echo $divisi_penuh > 0 ? 'bad' : ''; ?>"><?php icon_analisis('clipboard-check', 18); ?></span>
                    </div>
                    <div class="kpi-value"><?php echo $divisi_penuh; ?></div>
                    <div class="kpi-note <?php echo $divisi_penuh > 0 ? 'warn' : ''; ?>"><?php echo $divisi_penuh > 0 ? 'Tidak bisa menerima pelamar baru' : 'Semua divisi masih punya ruang'; ?></div>
                </div>
            </div>

            <div class="kuota-info">
                <?php icon_analisis('info', 16); ?>
                <span>Isi 0 untuk menutup divisi dari penempatan baru. Nilai kuota yang valid 0–99. Peserta yang sudah ditempatkan tidak ikut berubah.</span>
            </div>

            <div class="grup-panel">
            <?php if (empty($per_departemen)) { ?>
                <div class="panel"><div class="kosong">Belum ada divisi yang terdaftar.</div></div>
            <?php } ?>
            <?php foreach ($per_departemen as $nama_dept => $daftar) {
                $kuota_dept = 0; $terisi_dept = 0;
                foreach ($daftar as $d) { $kuota_dept += (int) $d->kuota_maks; $terisi_dept += min((int) $d->terisi, (int) $d->kuota_maks); }
                $sisa_dept = max(0, $kuota_dept - $terisi_dept);
            ?>
                <div class="panel">
                    <div class="panel-head">
                        <h2><?php echo htmlspecialchars($nama_dept); ?></h2>
                        <span class="badge <?php echo $sisa_dept > 0 ? 'badge-green' : 'badge-warn'; ?>"><?php echo $sisa_dept; ?> tersedia</span>
                    </div>
                    <div class="panel-sub"><?php echo $terisi_dept; ?> dari <?php echo $kuota_dept; ?> tempat terisi · <?php echo count($daftar); ?> divisi</div>
                    <div class="tabel-scroll">
                        <table class="kuota">
                            <thead>
                                <tr>
                                    <th class="col-divisi">Divisi</th>
                                    <th class="col-terisi">Keterisian</th>
                                    <th class="col-kuota">Kuota</th>
                                    <th class="col-aksi">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($daftar as $d) {
                                $k = (int) $d->kuota_maks; $t = (int) $d->terisi;
                                list($kelas, $label) = kuota_status_analisis($k, $t);
                                $persen = $k > 0 ? min(100, round($t / $k * 100)) : 0;
                            ?>
                                <tr data-id="<?php echo (int) $d->id; ?>" data-awal="<?php echo $k; ?>" data-terisi="<?php echo $t; ?>">
                                    <td><div class="divisi-nama"><?php echo htmlspecialchars($d->nama); ?></div></td>
                                    <td>
                                        <div class="quota-top">
                                            <span class="quota-teks"><?php echo $t; ?>/<?php echo $k; ?></span>
                                            <span class="quota-status <?php echo $kelas === '' ? 'none' : $kelas; ?>"><?php echo $label; ?></span>
                                        </div>
                                        <div class="quota-track"><div class="quota-fill <?php echo $kelas === 'full' || $kelas === 'low' ? $kelas : ''; ?>" style="width:<?php echo $persen; ?>%"></div></div>
                                    </td>
                                    <td>
                                        <div class="stepper">
                                            <button type="button" class="kurang" aria-label="Kurangi kuota <?php echo htmlspecialchars($d->nama); ?>">−</button>
                                            <input type="number" min="0" max="99" class="kuota-input" value="<?php echo $k; ?>" aria-label="Kuota <?php echo htmlspecialchars($d->nama); ?>">
                                            <button type="button" class="tambah" aria-label="Tambah kuota <?php echo htmlspecialchars($d->nama); ?>">+</button>
                                        </div>
                                    </td>
                                    <td class="col-aksi"><button type="button" class="btn-simpan" disabled>Simpan</button></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
            </div>
        </main>
    </div>
</div>

<script>
(function () {
    var URL_SIMPAN = '<?php echo base_url(); ?>analisis/kuota_simpan';

    function barisDari(el) { return el.closest('tr'); }
    function nilai(input) {
        var n = parseInt(input.value, 10);
        return isNaN(n) ? 0 : Math.max(0, Math.min(99, n));
    }

    // Tombol Simpan aktif hanya kalau nilai berubah dan tidak di bawah peserta aktif.
    function segarkanTombol(tr) {
        var input = tr.querySelector('.kuota-input');
        var tombol = tr.querySelector('.btn-simpan');
        var berubah = nilai(input) !== parseInt(tr.getAttribute('data-awal'), 10);
        tombol.disabled = !berubah;
        tombol.className = 'btn-simpan';
        tombol.textContent = 'Simpan';
    }

    function segarkanKeterisian(tr, kuota) {
        var terisi = parseInt(tr.getAttribute('data-terisi'), 10);
        var sisa = Math.max(0, kuota - terisi);
        var kelas = '', label;
        if (kuota <= 0) { label = 'Belum diatur'; }
        else if (sisa === 0) { kelas = 'full'; label = 'Penuh'; }
        else if (sisa <= 2) { kelas = 'low'; label = 'Sisa ' + sisa; }
        else { kelas = 'ok'; label = 'Sisa ' + sisa; }
        var status = tr.querySelector('.quota-status');
        status.className = 'quota-status ' + (kelas === '' ? 'none' : kelas);
        status.textContent = label;
        tr.querySelector('.quota-teks').textContent = terisi + '/' + kuota;
        var fill = tr.querySelector('.quota-fill');
        fill.className = 'quota-fill' + (kelas === 'full' || kelas === 'low' ? ' ' + kelas : '');
        fill.style.width = (kuota > 0 ? Math.min(100, Math.round(terisi / kuota * 100)) : 0) + '%';
    }

    document.addEventListener('click', function (e) {
        var tr = e.target.closest('tr[data-id]');
        if (!tr) return;
        var input = tr.querySelector('.kuota-input');

        if (e.target.classList.contains('kurang')) {
            input.value = Math.max(0, nilai(input) - 1);
            segarkanTombol(tr);
            return;
        }
        if (e.target.classList.contains('tambah')) {
            input.value = Math.min(99, nilai(input) + 1);
            segarkanTombol(tr);
            return;
        }
        if (!e.target.classList.contains('btn-simpan') || e.target.disabled) return;

        var tombol = e.target;
        var baru = nilai(input);
        input.value = baru;
        tombol.disabled = true;
        tombol.textContent = 'Menyimpan…';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', URL_SIMPAN, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            var ok = false;
            try { ok = xhr.status === 200 && JSON.parse(xhr.responseText).ok === true; } catch (err) {}
            if (ok) {
                tr.setAttribute('data-awal', baru);
                segarkanKeterisian(tr, baru);
                tombol.className = 'btn-simpan sukses';
                tombol.textContent = 'Tersimpan ✓';
                setTimeout(function () { segarkanTombol(tr); }, 1800);
            } else {
                tombol.disabled = false;
                tombol.className = 'btn-simpan gagal';
                tombol.textContent = 'Gagal, ulangi';
            }
        };
        xhr.onerror = function () {
            tombol.disabled = false;
            tombol.className = 'btn-simpan gagal';
            tombol.textContent = 'Gagal, ulangi';
        };
        xhr.send('id_divisi=' + encodeURIComponent(tr.getAttribute('data-id')) + '&kuota_maks=' + encodeURIComponent(baru));
    });

    document.addEventListener('input', function (e) {
        if (!e.target.classList.contains('kuota-input')) return;
        segarkanTombol(barisDari(e.target));
    });
})();
</script>
</body>
</html>
