<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Satu-satunya titik masuk untuk mengubah pkl_data.status (spesifikasi teknis bagian 6.1).
// Menulis prk_audit untuk setiap perubahan. Nilai status persis yang dipakai di project
// ini (dikonfirmasi dari dropdown manual v_transaksi.php): Pending, Accept, Rejected,
// Inactive -- bukan 'pending'/'Reject' seperti sebagian kode lama (lihat
// DOKUMENTASI_ANALISIS.md Tahap 2, dua bug yang sudah diperbaiki).
class Analisis_status {

    const PENDING = 'Pending';
    const ACCEPT = 'Accept';
    const REJECTED = 'Rejected';
    const INACTIVE = 'Inactive';

    // Transisi yang diizinkan (bagian 6.1). 'reject' dari Accept dipisah jadi aksi
    // 'batalkan' di kode pemanggil karena efek sampingnya beda (hapus penempatan+kontrak).
    private static $transisi = array(
        'Pending' => array('Accept', 'Rejected'),
        'Rejected' => array('Pending'),
        'Accept' => array('Inactive', 'Rejected'), // Rejected di sini = "batalkan penerimaan"
    );

    protected $ci;

    function __construct() {
        $this->ci = &get_instance();
        $this->ci->load->model('model_transaksi');
        $this->ci->load->model('model_prk_audit');
    }

    // Bandingkan status tanpa peduli huruf besar/kecil -- kode lama tidak konsisten
    // menulis 'pending' vs 'Pending' dst (lihat sql/001_analisis_schema.sql catatan).
    function statusSekarang($id_transaksi) {
        $rows = $this->ci->model_transaksi->getSingleData($id_transaksi);
        $baris = !empty($rows) ? $rows[0] : null;
        return $baris ? $baris->status : null;
    }

    function bolehUbah($dari, $ke) {
        $dari_norm = $this->_normalisasi($dari);
        if (!isset(self::$transisi[$dari_norm])) {
            return false;
        }
        return in_array($ke, self::$transisi[$dari_norm], true);
    }

    // ubah() -- validasi transisi, update pkl_data.status, catat audit. Return true/false.
    // $pelaku: 'hr:12' atau 'sistem' (bagian 2 spesifikasi). $alasan opsional, disimpan
    // di prk_audit.alasan.
    function ubah($id_transaksi, $ke, $pelaku, $alasan = null) {
        $sebelum = $this->statusSekarang($id_transaksi);
        if ($sebelum === null) {
            return false;
        }
        if (!$this->bolehUbah($sebelum, $ke)) {
            return false;
        }

        $this->ci->model_transaksi->update($id_transaksi, array('status' => $ke));
        $this->ci->model_prk_audit->catat(
            $pelaku, 'status', $id_transaksi, 'ubah_status',
            array('status' => $sebelum), array('status' => $ke), $alasan
        );
        return true;
    }

    private function _normalisasi($status) {
        $peta = array('pending' => self::PENDING, 'accept' => self::ACCEPT,
            'rejected' => self::REJECTED, 'reject' => self::REJECTED, 'inactive' => self::INACTIVE);
        $kunci = strtolower((string) $status);
        return isset($peta[$kunci]) ? $peta[$kunci] : $status;
    }
}
