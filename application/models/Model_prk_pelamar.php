<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Query pelamar dari data asli (pkl_data + prk_rekomendasi), menggantikan
// array contoh hardcode yang dipakai Tahap 1 di dashboard/pelamar/detail_pelamar.
// prk_rekomendasi masih kosong sampai mesin skoring (Tahap 2) dibuat, jadi
// kolom skor/top-3 LEFT JOIN dan tampil "belum tersedia" kalau belum ada baris.
class Model_prk_pelamar extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // Satu baris pelamar + rekomendasi #1 (satu-satunya rekomendasi tersimpan),
    // dipakai di dashboard dan daftar pelamar. $tab menyaring status:
    // pending|diterima|ditolak|tidak_aktif.
    function listPelamar($tab = null, $cari = null, $batas = null) {
        $this->db->select('
                pkl_data.id, pkl_data.nama, pkl_data.institusi, pkl_data.jurusan,
                pkl_data.pendidikan, pkl_data.tgl_mulai, pkl_data.tgl_selesai,
                pkl_data.status, pkl_data.id_depag,
                depag.nama_depag,
                r1.id_divisi AS top1_id_divisi, r1.skor AS top1_skor, r1.keyakinan AS top1_keyakinan,
                divisi1.nama AS top1_nama_divisi
            ')
            ->from('pkl_data')
            ->join('depag', 'depag.id = pkl_data.id_depag', 'left')
            ->join('prk_rekomendasi r1', 'r1.id_transaksi = pkl_data.id AND r1.is_current = 1 AND r1.peringkat = 1', 'left')
            ->join('prk_divisi divisi1', 'divisi1.id = r1.id_divisi', 'left');

        if (!empty($tab)) {
            $this->db->where('pkl_data.status', $this->_statusDariTab($tab));
        }
        if (!empty($cari)) {
            $this->db->group_start()
                ->like('pkl_data.nama', $cari)
                ->or_like('pkl_data.institusi', $cari)
                ->group_end();
        }

        $this->db->order_by('pkl_data.tgl_mulai', 'ASC');
        if (!empty($batas)) {
            $this->db->limit($batas);
        }

        return $this->db->get()->result();
    }

    function hitungPerStatus() {
        $hasil = array('pending' => 0, 'diterima' => 0, 'ditolak' => 0, 'tidak_aktif' => 0);
        $rows = $this->db->select('status, COUNT(*) AS jumlah')->from('pkl_data')->group_by('status')->get()->result();
        foreach ($rows as $r) {
            foreach ($hasil as $tab => $v) {
                if ($this->_statusDariTab($tab) === $r->status) {
                    $hasil[$tab] = (int) $r->jumlah;
                }
            }
        }
        return $hasil;
    }

    // Satu pelamar lengkap (profil + semua dokumen) untuk Halaman B.
    function getPelamar($id) {
        return $this->db
            ->select('pkl_data.*, depag.nama_depag')
            ->from('pkl_data')
            ->join('depag', 'depag.id = pkl_data.id_depag', 'left')
            ->where('pkl_data.id', $id)
            ->get()->row();
    }

    // Semua baris prk_rekomendasi aktif untuk satu pelamar, urut peringkat.
    function listRekomendasi($id_transaksi) {
        return $this->db
            ->select('prk_rekomendasi.*, prk_divisi.nama AS nama_divisi, prk_departemen.nama AS nama_departemen')
            ->from('prk_rekomendasi')
            ->join('prk_divisi', 'prk_divisi.id = prk_rekomendasi.id_divisi')
            ->join('prk_departemen', 'prk_departemen.id = prk_divisi.id_departemen')
            ->where('prk_rekomendasi.id_transaksi', $id_transaksi)
            ->where('prk_rekomendasi.is_current', 1)
            ->order_by('prk_rekomendasi.peringkat', 'ASC')
            ->get()->result();
    }

    // Agenda magang: peserta Accept dengan tgl_mulai atau tgl_selesai di
    // rentang [hari_ini, hari_ini+$hari]. Dipakai dashboard & halaman Kalender.
    function listAgenda($hari = 30) {
        $hari_ini = date('Y-m-d');
        $batas = date('Y-m-d', strtotime("+{$hari} days"));
        return $this->db
            ->select('id, nama, tgl_mulai, tgl_selesai')
            ->from('pkl_data')
            ->where('status', 'Accept')
            ->group_start()
                ->group_start()->where('tgl_mulai >=', $hari_ini)->where('tgl_mulai <=', $batas)->group_end()
                ->or_group_start()->where('tgl_selesai >=', $hari_ini)->where('tgl_selesai <=', $batas)->group_end()
            ->group_end()
            ->order_by('LEAST(tgl_mulai, tgl_selesai)', 'ASC', FALSE)
            ->get()->result();
    }

    // Status asli di pkl_data tidak konsisten kapitalisasinya di kode lama
    // (lihat catatan di sql/001_analisis_schema.sql); disamakan di satu tempat ini.
    private function _statusDariTab($tab) {
        $peta = array(
            'pending' => 'Pending',
            'diterima' => 'Accept',
            'ditolak' => 'Rejected',
            'tidak_aktif' => 'Inactive',
        );
        return isset($peta[$tab]) ? $peta[$tab] : $tab;
    }
}
