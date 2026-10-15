<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Analisis_queue::enqueue() - dipanggil dari hook di Transaksi.php (bagian 1 spesifikasi
// teknis) setiap kali proposal (doc_2) atau transkrip (doc_3) selesai diunggah/diganti,
// dan setelah pendaftaran baru selesai. Hanya menyisipkan baris prk_analisis_job;
// pemrosesan sesungguhnya (panggil layanan Python) dilakukan Cli_analisis::antrean().
class Analisis_queue {

    protected $ci;

    function __construct() {
        $this->ci = &get_instance();
        $this->ci->load->model('model_prk_job');
    }

    // $id_transaksi: pkl_data.id. Baca doc_2 (proposal) dan doc_3 (transkrip) dari
    // baris itu sendiri -- aman dipanggil di tengah hook update sebelum/sesudah
    // kolom berubah selama dipanggil setelah $this->model_transaksi->update() sukses.
    // $paksa: lewati cek dedup hash -- dipakai tombol "Analisis ulang" HR (FR-04),
    // karena HR sengaja minta diproses ulang meski berkasnya sama persis dengan
    // analisis terakhir (mis. setelah Admin mengubah bobot/pengaturan).
    function enqueue($id_transaksi, $paksa = false) {
        if (empty($id_transaksi)) {
            return false;
        }

        $this->ci->load->model('model_transaksi');
        $baris = $this->ci->model_transaksi->getSingleData($id_transaksi);
        $pelamar = !empty($baris) ? $baris[0] : null;
        if (!$pelamar || empty($pelamar->doc_2) || empty($pelamar->doc_3)) {
            // Slot proposal/transkrip belum lengkap -- tidak diantrekan (spesifikasi
            // bagian 9.1: status tampil "menunggu dokumen" sampai keduanya ada).
            return false;
        }

        $path_proposal = FCPATH . 'gambar/' . $pelamar->doc_2;
        $path_transkrip = FCPATH . 'gambar/' . $pelamar->doc_3;
        if (!is_file($path_proposal) || !is_file($path_transkrip)) {
            log_message('error', "Analisis_queue::enqueue($id_transaksi): berkas proposal/transkrip tidak ditemukan di disk.");
            return false;
        }

        if ($this->ci->model_prk_job->jobBelumSelesai($id_transaksi)) {
            // Sudah ada job antre/jalan untuk pelamar ini -- jangan numpuk duplikat.
            return false;
        }

        $hash_proposal = hash_file('sha256', $path_proposal);
        $hash_transkrip = hash_file('sha256', $path_transkrip);

        if (!$paksa) {
            $terakhir = $this->ci->model_prk_job->jobSelesaiTerakhir($id_transaksi);
            if ($terakhir && $terakhir->hash_proposal === $hash_proposal && $terakhir->hash_transkrip === $hash_transkrip) {
                // Berkas sama persis dengan analisis terakhir yang sukses -- tidak perlu diulang.
                return false;
            }
        }

        return $this->ci->model_prk_job->enqueue($id_transaksi, $hash_proposal, $hash_transkrip);
    }
}
