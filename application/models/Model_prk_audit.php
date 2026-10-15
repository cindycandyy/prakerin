<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_prk_audit extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // catat() - satu titik masuk untuk semua pencatatan audit modul Analisis Penempatan.
    // $pelaku: string seperti 'hr:12' atau 'sistem' (lihat tab Spesifikasi teknis bagian 2).
    function catat($pelaku, $entitas, $id_entitas, $aksi, $sebelum = NULL, $sesudah = NULL, $alasan = NULL) {
        $this->db->insert('prk_audit', array(
            'waktu' => date('Y-m-d H:i:s'),
            'pelaku' => $pelaku,
            'entitas' => $entitas,
            'id_entitas' => $id_entitas,
            'aksi' => $aksi,
            'sebelum' => $sebelum !== NULL ? json_encode($sebelum) : NULL,
            'sesudah' => $sesudah !== NULL ? json_encode($sesudah) : NULL,
            'alasan' => $alasan,
        ));
    }

    function listUntukEntitas($entitas, $id_entitas) {
        return $this->db
            ->where('entitas', $entitas)
            ->where('id_entitas', $id_entitas)
            ->order_by('waktu', 'DESC')
            ->get('prk_audit')
            ->result();
    }

    // Riwayat keputusan HR (terima/tolak pelamar) untuk Halaman Riwayat keputusan.
    // entitas 'penempatan' (aksi terima) dan 'pelamar' (aksi tolak) sama-sama
    // merujuk ke pkl_data.id lewat id_entitas, lihat Analisis::terima()/tolak().
    function listKeputusanPelamar($batas = 50) {
        return $this->db
            ->select('prk_audit.*, pkl_data.nama, pkl_data.institusi')
            ->from('prk_audit')
            ->join('pkl_data', 'pkl_data.id = prk_audit.id_entitas')
            ->where_in('prk_audit.entitas', array('penempatan', 'pelamar'))
            ->where_in('prk_audit.aksi', array('terima', 'tolak'))
            ->order_by('prk_audit.waktu', 'DESC')
            ->limit($batas)
            ->get()->result();
    }
}
