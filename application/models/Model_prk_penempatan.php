<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Keputusan HR (terima/tolak pelamar) -- mengisi prk_penempatan dan mengubah
// status pkl_data. Validasi kuota dan pembuatan kontrak otomatis belum
// dikerjakan di sini (lihat DOKUMENTASI_ANALISIS.md langkah berikutnya).
class Model_prk_penempatan extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function simpan($id_transaksi, $id_divisi, $peringkat_dipilih, $alasan_ubah, $ditetapkan_oleh) {
        $data = array(
            'id_transaksi' => $id_transaksi,
            'id_divisi' => $id_divisi,
            'peringkat_dipilih' => $peringkat_dipilih,
            'alasan_ubah' => $alasan_ubah,
            'ditetapkan_oleh' => $ditetapkan_oleh,
            'ditetapkan_pada' => date('Y-m-d H:i:s'),
        );
        $existing = $this->db->where('id_transaksi', $id_transaksi)->get('prk_penempatan')->row();
        if ($existing) {
            $this->db->where('id_transaksi', $id_transaksi)->update('prk_penempatan', $data);
        } else {
            $this->db->insert('prk_penempatan', $data);
        }
    }

    function getPenempatan($id_transaksi) {
        return $this->db->where('id_transaksi', $id_transaksi)->get('prk_penempatan')->row();
    }

    function ubahStatusPelamar($id_transaksi, $status) {
        $this->db->where('id', $id_transaksi)->update('pkl_data', array('status' => $status));
    }
}
