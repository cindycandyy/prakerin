<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Akses pkl_data khusus sisi pelamar (SPESIFIKASI_PELAMAR.md). Semua query dibatasi
// user_id = SES_LOGIN lewat parameter, bukan string concat (menutup C2/C3 untuk jalur
// baru ini -- Model_transaksi lama yang rawan tetap tidak dipakai di sini).
class Model_pelamar extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function listPengajuan($user_id) {
        return $this->db
            ->select('pkl_data.*, depag.nama_depag')
            ->from('pkl_data')
            ->join('depag', 'depag.id = pkl_data.id_depag', 'left')
            ->where('pkl_data.user_id', $user_id)
            ->order_by('pkl_data.id', 'DESC')
            ->get()->result();
    }

    // Satu pengajuan, DENGAN cek kepemilikan (dipakai di tiap aksi tulis/baca detail --
    // kalau baris tidak ditemukan untuk kombinasi id+user_id ini, pemanggil wajib 404,
    // bukan asumsi "tidak ada data sama sekali").
    function getPengajuanMilik($id, $user_id) {
        return $this->db
            ->select('pkl_data.*, depag.nama_depag')
            ->from('pkl_data')
            ->join('depag', 'depag.id = pkl_data.id_depag', 'left')
            ->where('pkl_data.id', $id)
            ->where('pkl_data.user_id', $user_id)
            ->get()->row();
    }

    // D5: satu pelamar maksimal satu pengajuan Pending/Accept pada satu waktu.
    function adaPengajuanAktif($user_id) {
        return (bool) $this->db
            ->where('user_id', $user_id)
            ->where_in('status', array('Pending', 'Accept'))
            ->get('pkl_data')->row();
    }

    function idkeyDipakai($idkey) {
        return (bool) $this->db
            ->where('idkey', $idkey)
            ->where_in('status', array('Pending', 'Accept'))
            ->get('pkl_data')->row();
    }

    function buat($data) {
        $this->db->insert('pkl_data', $data);
        return $this->db->insert_id();
    }

    // Whitelist kolom (bagian B6) ditegakkan di controller sebelum sampai sini; fungsi
    // ini hanya menambahkan syarat where user_id supaya tidak mungkin update baris
    // pelamar lain meski id ditebak lewat POST.
    function ubah($id, $user_id, $data) {
        $this->db->where('id', $id)->where('user_id', $user_id)->update('pkl_data', $data);
        return $this->db->affected_rows() === 1;
    }

    function simpanBerkas($id, $user_id, $slot, $nama_file) {
        return $this->ubah($id, $user_id, array($slot => $nama_file));
    }

    function getNamaBerkasLama($id, $user_id, $slot) {
        $row = $this->getPengajuanMilik($id, $user_id);
        return $row ? $row->{$slot} : null;
    }
}
