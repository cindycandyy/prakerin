<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// prk_surat_balasan + prk_surat_peserta (spesifikasi teknis bagian 8). Satu surat bisa
// memuat beberapa peserta dari instansi & rujukan yang sama (lihat buatDari() dan
// tambahPeserta()).
class Model_prk_surat extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function getById($id_surat) {
        return $this->db->where('id', $id_surat)->get('prk_surat_balasan')->row();
    }

    function listPeserta($id_surat) {
        return $this->db
            ->select('pkl_data.id, pkl_data.nama, pkl_data.institusi, pkl_data.jurusan, pkl_data.tgl_mulai, pkl_data.tgl_selesai')
            ->from('prk_surat_peserta')
            ->join('pkl_data', 'pkl_data.id = prk_surat_peserta.id_transaksi')
            ->where('prk_surat_peserta.id_surat', $id_surat)
            ->order_by('pkl_data.nama', 'ASC')
            ->get()->result();
    }

    // Surat draf yang sudah memuat pelamar ini (kalau ada) -- dipakai Halaman C supaya
    // tombol "Buat surat balasan" membuka surat yang sudah ada, bukan bikin duplikat.
    function getSuratDrafUntukPelamar($id_transaksi) {
        return $this->db
            ->select('prk_surat_balasan.*')
            ->from('prk_surat_balasan')
            ->join('prk_surat_peserta', 'prk_surat_peserta.id_surat = prk_surat_balasan.id')
            ->where('prk_surat_peserta.id_transaksi', $id_transaksi)
            ->where('prk_surat_balasan.status', 'draf')
            ->get()->row();
    }

    // Pelamar lain (status Accept) dari instansi yang sama, belum punya surat apa pun
    // (draf/terkirim) -- kandidat untuk digabung ke satu surat (bagian 8.3 langkah 1).
    function listKandidatGabung($instansi, $kecuali_id_transaksi) {
        return $this->db
            ->select('pkl_data.id, pkl_data.nama')
            ->from('pkl_data')
            ->where('LOWER(pkl_data.status)', 'accept')
            ->where('pkl_data.institusi', $instansi)
            ->where('pkl_data.id !=', $kecuali_id_transaksi)
            ->where('NOT EXISTS (SELECT 1 FROM prk_surat_peserta sp WHERE sp.id_transaksi = pkl_data.id)', null, false)
            ->get()->result();
    }

    // Buat surat draf baru + tautkan peserta (bagian 8.3 langkah 1). $id_transaksi_list:
    // array id pelamar yang mau digabung ke surat ini (minimal satu).
    function buatDari($id_transaksi_list, $dibuat_oleh) {
        if (empty($id_transaksi_list)) {
            return false;
        }
        $this->db->trans_start();
        $this->db->insert('prk_surat_balasan', array(
            'status' => 'draf',
            'dibuat_oleh' => $dibuat_oleh,
            'dibuat_pada' => date('Y-m-d H:i:s'),
        ));
        $id_surat = $this->db->insert_id();
        foreach ($id_transaksi_list as $id_transaksi) {
            $this->db->insert('prk_surat_peserta', array('id_surat' => $id_surat, 'id_transaksi' => $id_transaksi));
        }
        $this->db->trans_complete();
        return $this->db->trans_status() ? $id_surat : false;
    }

    // Tambahkan satu peserta ke surat draf yang sudah ada (bagian 8.3, "gabung peserta").
    // Validasi ulang status Accept + belum ada di surat lain dilakukan di controller
    // (data bisa berubah sejak halaman dimuat), fungsi ini hanya insert baris.
    function tambahPeserta($id_surat, $id_transaksi) {
        $this->db->insert('prk_surat_peserta', array('id_surat' => $id_surat, 'id_transaksi' => $id_transaksi));
        return $this->db->affected_rows() === 1;
    }

    // Simpan isian (hanya boleh saat status='draf' -- dicek di controller, bagian 8.3
    // langkah 5: "setelah terkirim, isian terkunci").
    function simpanIsian($id_surat, $data) {
        $this->db->where('id', $id_surat)->where('status', 'draf')->update('prk_surat_balasan', $data);
        return $this->db->affected_rows() === 1;
    }

    function nomorSudahDipakai($nomor_surat, $kecuali_id_surat = null) {
        $this->db->where('nomor_surat', $nomor_surat);
        if ($kecuali_id_surat) {
            $this->db->where('id !=', $kecuali_id_surat);
        }
        return (bool) $this->db->get('prk_surat_balasan')->row();
    }

    // Saran nomor urut berikutnya tahun ini (bagian 8.2): nomor terbesar tahun ini + 1,
    // HR boleh menimpa. Hanya mengambil 5 digit pertama sebelum '/' -- format lengkap
    // {5 digit}/{segmen tengah}/{tahun}.
    function saranNomorBerikutnya($tahun) {
        $row = $this->db
            ->select("MAX(CAST(SUBSTRING_INDEX(nomor_surat, '/', 1) AS UNSIGNED)) AS maks", false)
            ->from('prk_surat_balasan')
            ->where('nomor_surat LIKE', "%/{$tahun}")
            ->get()->row();
        $maks = ($row && $row->maks) ? (int) $row->maks : 0;
        return str_pad($maks + 1, 5, '0', STR_PAD_LEFT);
    }

    function tandaiTerkirim($id_surat, $file_path) {
        $this->db->where('id', $id_surat)->where('status', 'draf')->update('prk_surat_balasan', array(
            'status' => 'terkirim',
            'file_path' => $file_path,
            'dikirim_pada' => date('Y-m-d H:i:s'),
        ));
        return $this->db->affected_rows() === 1;
    }

    // Surat pengganti (bagian 8.3 langkah 5): surat lama ditandai 'diganti' +
    // id_pengganti, surat baru dibuat sebagai draf dengan peserta yang sama.
    function buatPengganti($id_surat_lama, $dibuat_oleh) {
        $lama = $this->getById($id_surat_lama);
        if (!$lama || $lama->status !== 'terkirim') {
            return false;
        }
        $peserta = $this->listPeserta($id_surat_lama);
        if (empty($peserta)) {
            return false;
        }

        $this->db->trans_start();
        $id_baru = $this->buatDari(array_map(function ($p) { return $p->id; }, $peserta), $dibuat_oleh);
        // Salin isian non-nomor dari surat lama supaya HR tidak mengetik ulang semua.
        $this->db->where('id', $id_baru)->update('prk_surat_balasan', array(
            'rujukan_nomor' => $lama->rujukan_nomor,
            'rujukan_tanggal' => $lama->rujukan_tanggal,
            'pembekalan_tanggal' => $lama->pembekalan_tanggal,
            'pembekalan_pukul' => $lama->pembekalan_pukul,
            'pembekalan_tempat' => $lama->pembekalan_tempat,
            'pembekalan_keterangan' => $lama->pembekalan_keterangan,
        ));
        $this->db->where('id', $id_surat_lama)->update('prk_surat_balasan', array(
            'status' => 'diganti',
            'id_pengganti' => $id_baru,
        ));
        $this->db->trans_complete();
        return $this->db->trans_status() ? $id_baru : false;
    }
}
