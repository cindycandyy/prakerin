<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// prk_kontrak -- periode magang per penempatan (spesifikasi teknis bagian 6.1 & 6.2).
// Kontrak ke-1 dibuat otomatis saat pelamar diterima (Analisis::terima()); perpanjangan
// jadi kontrak baru urutan 2 dst (lihat perpanjang()), akhiri lebih awal menandai
// kontrak urutan terakhir 'diakhiri_awal' (lihat akhiriLebihAwal()).
class Model_prk_kontrak extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function buatPertama($id_penempatan, $tanggal_mulai, $tanggal_selesai) {
        $data = array(
            'id_penempatan' => $id_penempatan,
            'urutan' => 1,
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'status' => $this->hitungStatus($tanggal_mulai, $tanggal_selesai),
            'dibuat_pada' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('prk_kontrak', $data);
        return $this->db->insert_id();
    }

    function getByPenempatan($id_penempatan) {
        return $this->db->where('id_penempatan', $id_penempatan)
            ->order_by('urutan', 'DESC')->get('prk_kontrak')->row();
    }

    function getById($id_kontrak) {
        return $this->db->where('id', $id_kontrak)->get('prk_kontrak')->row();
    }

    // Kontrak + id_transaksi & nama pelamar, dipakai controller untuk notifikasi dan
    // redirect setelah aksi perpanjang/akhiri tanpa query tambahan terpisah.
    function getByIdDenganPelamar($id_kontrak) {
        return $this->db
            ->select('prk_kontrak.*, prk_penempatan.id_transaksi, pkl_data.nama')
            ->from('prk_kontrak')
            ->join('prk_penempatan', 'prk_penempatan.id = prk_kontrak.id_penempatan')
            ->join('pkl_data', 'pkl_data.id = prk_penempatan.id_transaksi')
            ->where('prk_kontrak.id', $id_kontrak)
            ->get()->row();
    }

    // Semua kontrak + nama pelamar + id_transaksi, dipakai cli_analisis harian supaya
    // tidak perlu query per baris.
    function listUntukPenjadwal() {
        return $this->db
            ->select('prk_kontrak.*, prk_penempatan.id_transaksi, pkl_data.nama, pkl_data.status AS status_pelamar')
            ->from('prk_kontrak')
            ->join('prk_penempatan', 'prk_penempatan.id = prk_kontrak.id_penempatan')
            ->join('pkl_data', 'pkl_data.id = prk_penempatan.id_transaksi')
            ->where_not_in('prk_kontrak.status', array('selesai', 'diakhiri_awal'))
            ->get()->result();
    }

    // Status turunan dari tanggal, T = hari ini (bagian 6.2). diakhiri_awal tidak pernah
    // dihitung ulang di sini -- hanya diisi manual lewat aksi "Akhiri lebih awal".
    function hitungStatus($tanggal_mulai, $tanggal_selesai, $hari_ini = null) {
        $t = $hari_ini ?: date('Y-m-d');
        if ($t < $tanggal_mulai) {
            return 'akan_mulai';
        }
        if ($t > $tanggal_selesai) {
            return 'selesai';
        }
        $tujuh_hari_sebelum = date('Y-m-d', strtotime($tanggal_selesai . ' -7 days'));
        if ($t >= $tujuh_hari_sebelum) {
            return 'akan_berakhir';
        }
        return 'aktif';
    }

    // Perbarui status turunan semua kontrak yang belum final. Dipanggil cli_analisis
    // harian. Return jumlah baris yang berubah statusnya.
    function perbaruiSemuaStatus() {
        $daftar = $this->listUntukPenjadwal();
        $berubah = 0;
        foreach ($daftar as $k) {
            if ($k->status === 'diakhiri_awal') {
                continue;
            }
            $baru = $this->hitungStatus($k->tanggal_mulai, $k->tanggal_selesai);
            if ($baru !== $k->status) {
                $this->db->where('id', $k->id)->update('prk_kontrak', array('status' => $baru));
                $berubah++;
            }
        }
        return $berubah;
    }

    function tandaiTindakLanjut($id_kontrak, $tindak_lanjut) {
        $this->db->where('id', $id_kontrak)->update('prk_kontrak', array('tindak_lanjut' => $tindak_lanjut));
    }

    // Perpanjangan (spesifikasi teknis bagian 6.1 & 7.1, "perpanjang"): kontrak lama
    // ditandai tindak_lanjut='diperpanjang' (berhenti muncul di pengingat H-7/H-3),
    // kontrak baru urutan berikutnya dibuat dengan tanggal_mulai = tanggal_selesai
    // kontrak lama + 1 hari supaya periode tidak tumpang tindih. tanggal_selesai_baru
    // wajib lebih besar dari tanggal_mulai kontrak baru, dicek di controller.
    function perpanjang($id_kontrak, $tanggal_selesai_baru) {
        $lama = $this->getById($id_kontrak);
        if (!$lama) {
            return false;
        }
        $this->db->trans_start();
        $this->tandaiTindakLanjut($id_kontrak, 'diperpanjang');
        $tanggal_mulai_baru = date('Y-m-d', strtotime($lama->tanggal_selesai . ' +1 day'));
        $data = array(
            'id_penempatan' => $lama->id_penempatan,
            'urutan' => (int) $lama->urutan + 1,
            'tanggal_mulai' => $tanggal_mulai_baru,
            'tanggal_selesai' => $tanggal_selesai_baru,
            'status' => $this->hitungStatus($tanggal_mulai_baru, $tanggal_selesai_baru),
            'dibuat_pada' => date('Y-m-d H:i:s'),
        );
        $this->db->insert('prk_kontrak', $data);
        $id_baru = $this->db->insert_id();
        $this->db->trans_complete();
        return $this->db->trans_status() ? $id_baru : false;
    }

    // Akhiri lebih awal (bagian 6.1, "accept -> inactive", alasan wajib): kontrak ini
    // ditandai selesai di luar jadwal normalnya. Status pelamar (pkl_data) diubah
    // terpisah oleh pemanggil lewat Analisis_status, bukan di sini.
    // Semua kontrak (urutan terakhir per penempatan) + data peserta & divisi/
    // departemen, dipakai Halaman Kalender magang (KPI, filter departemen, grid,
    // panel tindakan, tabel peserta) dalam satu query supaya halaman itu tidak
    // query berkali-kali. Raw SQL (bukan query builder) karena butuh subquery
    // korelasi MAX(urutan) per id_penempatan yang tidak praktis lewat builder CI3.
    function listUntukKalender() {
        $sql = "
            SELECT
                k.id, k.urutan, k.tanggal_mulai, k.tanggal_selesai, k.status, k.tindak_lanjut, k.catatan,
                p.id_transaksi, p.id_divisi,
                d.nama AS nama, d.institusi AS institusi, d.status AS status_pelamar,
                v.nama AS nama_divisi, dept.id AS id_departemen, dept.nama AS nama_departemen
            FROM prk_kontrak k
            JOIN prk_penempatan p ON p.id = k.id_penempatan
            JOIN pkl_data d ON d.id = p.id_transaksi
            LEFT JOIN prk_divisi v ON v.id = p.id_divisi
            LEFT JOIN prk_departemen dept ON dept.id = v.id_departemen
            JOIN (SELECT id_penempatan, MAX(urutan) AS urutan_max FROM prk_kontrak GROUP BY id_penempatan) terakhir
                ON terakhir.id_penempatan = k.id_penempatan AND terakhir.urutan_max = k.urutan
            ORDER BY k.tanggal_mulai ASC
        ";
        return $this->db->query($sql)->result();
    }

    function akhiriLebihAwal($id_kontrak, $catatan) {
        $this->db->where('id', $id_kontrak)->update('prk_kontrak', array(
            'status' => 'diakhiri_awal',
            'tindak_lanjut' => 'diakhiri',
            'catatan' => $catatan,
        ));
        return $this->db->affected_rows() === 1;
    }
}
