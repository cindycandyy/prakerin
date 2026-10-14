<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Render surat balasan ke PDF (bagian 8.4 spesifikasi teknis). Dompdf + templat HTML
// dipilih karena LibreOffice tidak tersedia di server ini (lihat DOKUMENTASI_ANALISIS.md).
// Berkas disimpan di luar web root (storage/analisis/surat/), hanya diunduh lewat rute
// PHP yang mengecek hak akses -- tidak pernah ditautkan langsung.
class Analisis_surat {

    protected $ci;

    function __construct() {
        $this->ci = &get_instance();
        $this->ci->load->helper('analisis_helper');
        $this->ci->config->load('analisis');
        $this->ci->load->model('model_prk_master');
    }

    // Render HTML templat + data jadi PDF, return isi berkas (string). $tanda_draf:
    // true untuk pratinjau (watermark DRAF), false untuk versi final yang dikirim.
    function render($surat, $peserta, $tanda_draf) {
        $jumlah = count($peserta);
        $jenjang = $this->_jenjangDariPeserta($peserta);

        $data = array(
            'surat' => $surat,
            'peserta' => $peserta,
            'jumlah_terbilang' => terbilang($jumlah),
            'kata_jenjang' => $jumlah > 1 ? ($jenjang === 'Siswa' ? 'Siswa' : 'Mahasiswa') : $jenjang,
            'kepada' => !empty($surat->kepada_override) ? $surat->kepada_override : $this->_susunKepada($peserta),
            'nomor_lengkap' => $this->_nomorLengkap($surat),
            'tanda_draf' => $tanda_draf,
            'penandatangan_nama' => $this->ci->model_prk_master->getPengaturan('surat_penandatangan_nama', '-'),
            'penandatangan_jabatan' => $this->ci->model_prk_master->getPengaturan('surat_penandatangan_jabatan', '-'),
        );

        $html = $this->ci->load->view('analisis/templat/surat_balasan', $data, true);

        $options = new Options();
        $options->set('isRemoteEnabled', false); // tidak memuat gambar/font dari luar
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        return $dompdf->output();
    }

    // Simpan PDF final ke storage/analisis/surat/{tahun}/{id_surat}.pdf (bagian 8.4).
    // Return path relatif yang disimpan di prk_surat_balasan.file_path.
    function simpanFinal($id_surat, $isi_pdf) {
        $tahun = date('Y');
        $folder = rtrim($this->ci->config->item('analisis_storage_surat'), '/\\') . "/{$tahun}";
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        $path_absolut = $folder . "/{$id_surat}.pdf";
        file_put_contents($path_absolut, $isi_pdf);
        return "{$tahun}/{$id_surat}.pdf";
    }

    function pathAbsolut($file_path_relatif) {
        return rtrim($this->ci->config->item('analisis_storage_surat'), '/\\') . '/' . $file_path_relatif;
    }

    // Jenis institusi mayoritas (SMK -> Siswa, selain itu Mahasiswa) -- dipakai hanya
    // saat $jumlah == 1; untuk >1 peserta campuran, kata "Mahasiswa" dipakai sebagai
    // bawaan (HR dapat mengedit teks "Kepada" secara manual kalau perlu).
    private function _jenjangDariPeserta($peserta) {
        if (empty($peserta)) {
            return 'Mahasiswa';
        }
        $satu = $peserta[0];
        $pendidikan = isset($satu->pendidikan) ? strtoupper((string) $satu->pendidikan) : '';
        if (strpos($pendidikan, 'SMK') === 0 || strpos($pendidikan, 'SMA') === 0) {
            return 'Siswa';
        }
        return 'Mahasiswa';
    }

    // "Kepada" bawaan dari instansi peserta pertama (bagian 8.1). HR dapat menimpanya
    // lewat kolom kepada_override di Halaman C -- dipakai duluan di render() kalau diisi.
    private function _susunKepada($peserta) {
        if (empty($peserta)) {
            return '-';
        }
        $instansi = $peserta[0]->institusi;
        $jenjang = $this->_jenjangDariPeserta($peserta);
        $sebutan = $jenjang === 'Siswa' ? 'Kepala Sekolah' : 'Kepala Departemen/Program Studi';
        return "{$sebutan}\n{$instansi}";
    }

    // nomor_surat sudah disimpan lengkap ({5 digit}/{segmen tengah}/{tahun}, bagian 8.2) --
    // disusun di Analisis::surat_simpan() sebelum masuk database, bukan di sini.
    private function _nomorLengkap($surat) {
        return !empty($surat->nomor_surat) ? $surat->nomor_surat : '(belum diisi)';
    }
}
