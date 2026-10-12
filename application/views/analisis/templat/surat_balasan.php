<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Templat HTML surat balasan (bagian 8 spesifikasi teknis), dirender Dompdf.
// Variabel yang dipakai (disediakan Analisis_surat::render()): $surat, $peserta,
// $jumlah_terbilang, $kata_jenjang, $kepada, $nomor_lengkap, $tanda_draf,
// $penandatangan_nama, $penandatangan_jabatan.
// Surat tidak pernah menyebut departemen/divisi penempatan (bagian 8, 9.3).
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    @page { margin: 90px 60px 70px 60px; }
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }
    .kop { text-align: center; border-bottom: 2px solid #1a1a1a; padding-bottom: 8px; margin-bottom: 18px; }
    .kop h1 { font-size: 14px; margin: 0; }
    .kop p { font-size: 10px; margin: 2px 0 0; color: #444; }
    .meta-row { display: table; width: 100%; margin-bottom: 14px; }
    .meta-row .kiri { display: table-cell; width: 60%; vertical-align: top; }
    .meta-row .kanan { display: table-cell; width: 40%; vertical-align: top; text-align: right; }
    table.peserta { width: 100%; border-collapse: collapse; margin: 14px 0; }
    table.peserta th, table.peserta td { border: 1px solid #333; padding: 5px 7px; font-size: 10px; }
    table.peserta th { background: #eee; }
    .tanda-tangan { margin-top: 40px; }
    .tanda-tangan .nama { margin-top: 55px; font-weight: bold; text-decoration: underline; }
    .tembusan { margin-top: 24px; font-size: 10px; }
    .draf-watermark {
        position: fixed; top: 300px; left: 120px; font-size: 90px; color: #e0e0e0;
        transform: rotate(-30deg); z-index: -1;
    }
</style>
</head>
<body>

<?php if (!empty($tanda_draf)) { ?>
<div class="draf-watermark">DRAF</div>
<?php } ?>

<div class="kop">
    <h1>PT PETROSIDA GRESIK</h1>
    <p>Jl. Raya Gresik-Surabaya KM 3, Gresik, Jawa Timur</p>
</div>

<div class="meta-row">
    <div class="kiri">
        Nomor&nbsp;&nbsp;&nbsp;: <?php echo htmlspecialchars($nomor_lengkap); ?><br>
        Lampiran: -<br>
        Perihal&nbsp;&nbsp;: Penerimaan Praktik Kerja Industri
    </div>
    <div class="kanan">
        Gresik, <?php echo htmlspecialchars(tanggal_indo($surat->tanggal_surat ?: date('Y-m-d'))); ?>
    </div>
</div>

<p>
    Kepada Yth.<br>
    <?php echo nl2br(htmlspecialchars($kepada)); ?><br>
    di tempat
</p>

<p>
    Menindaklanjuti surat permohonan
    <?php if (!empty($surat->rujukan_nomor)) { ?>Nomor <?php echo htmlspecialchars($surat->rujukan_nomor); ?><?php } ?>
    <?php if (!empty($surat->rujukan_tanggal)) { ?>tanggal <?php echo htmlspecialchars(tanggal_indo($surat->rujukan_tanggal)); ?><?php } ?>,
    dengan ini kami sampaikan bahwa PT Petrosida Gresik menerima
    <strong><?php echo (int) count($peserta); ?> (<?php echo htmlspecialchars($jumlah_terbilang); ?>) <?php echo htmlspecialchars($kata_jenjang); ?></strong>
    untuk melaksanakan Praktik Kerja Industri, dengan rincian sebagai berikut:
</p>

<table class="peserta">
    <thead>
        <tr><th>No</th><th>Nama</th><th>Instansi</th><th>Program Studi</th><th>Periode</th></tr>
    </thead>
    <tbody>
        <?php foreach ($peserta as $i => $p) { ?>
        <tr>
            <td><?php echo $i + 1; ?></td>
            <td><?php echo htmlspecialchars($p->nama); ?></td>
            <td><?php echo htmlspecialchars($p->institusi); ?></td>
            <td><?php echo htmlspecialchars($p->jurusan); ?></td>
            <td>
                <?php echo htmlspecialchars(tanggal_indo($p->tgl_mulai)); ?> -
                <?php echo htmlspecialchars(tanggal_indo($p->tgl_selesai)); ?>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<p>
    Pembekalan akan dilaksanakan pada:<br>
    Hari/Tanggal&nbsp;&nbsp;: <?php echo htmlspecialchars(tanggal_indo($surat->pembekalan_tanggal, true)); ?><br>
    Pukul&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo htmlspecialchars($surat->pembekalan_pukul ?: '-'); ?><br>
    Tempat&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo htmlspecialchars($surat->pembekalan_tempat ?: '-'); ?>
    <?php if (!empty($surat->pembekalan_keterangan)) { ?>
    <br>Keterangan: <?php echo nl2br(htmlspecialchars($surat->pembekalan_keterangan)); ?>
    <?php } ?>
</p>

<p>Demikian surat ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.</p>

<div class="tanda-tangan">
    PT Petrosida Gresik,
    <div class="nama"><?php echo htmlspecialchars($penandatangan_nama); ?></div>
    <div><?php echo htmlspecialchars($penandatangan_jabatan); ?></div>
</div>

<div class="tembusan">Tembusan:<br>1. Arsip.</div>

</body>
</html>
