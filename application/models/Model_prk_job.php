<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// prk_analisis_job (antrean) + penulisan hasil analisis ke prk_profil/prk_rekomendasi.
// Layanan Python (simagang-analyzer) tidak menyimpan apa pun -- semua tulis ke MySQL
// lewat model ini, dipanggil dari Cli_analisis::antrean() (lihat DOKUMENTASI_ANALISIS.md).
class Model_prk_job extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // Job selesai terakhir untuk id_transaksi ini (untuk cek dedup hash di enqueue()).
    function jobSelesaiTerakhir($id_transaksi) {
        return $this->db
            ->where('id_transaksi', $id_transaksi)
            ->where('status', 'selesai')
            ->order_by('selesai_pada', 'DESC')
            ->limit(1)
            ->get('prk_analisis_job')->row();
    }

    function enqueue($id_transaksi, $hash_proposal, $hash_transkrip) {
        $this->db->insert('prk_analisis_job', array(
            'id_transaksi' => $id_transaksi,
            'hash_proposal' => $hash_proposal,
            'hash_transkrip' => $hash_transkrip,
            'status' => 'antre',
            'diminta_pada' => date('Y-m-d H:i:s'),
        ));
        return $this->db->insert_id();
    }

    // Klaim atomik: hanya baris yang masih 'antre' yang terambil (cegah dua proses
    // antrean jalan bersamaan mengerjakan job yang sama). affected_rows() = 0 berarti
    // sudah diambil proses lain lebih dulu.
    function klaim($id_job) {
        $this->db->where('id', $id_job)->where('status', 'antre')
            ->update('prk_analisis_job', array(
                'status' => 'jalan',
                'mulai_pada' => date('Y-m-d H:i:s'),
            ));
        return $this->db->affected_rows() === 1;
    }

    function listAntre($batas = 5) {
        return $this->db
            ->where('status', 'antre')
            ->where('diminta_pada <=', date('Y-m-d H:i:s'))
            ->order_by('diminta_pada', 'ASC')
            ->limit($batas)
            ->get('prk_analisis_job')->result();
    }

    // Job 'jalan' lebih dari $menit dianggap macet (proses sebelumnya mati di tengah
    // jalan) -- dikembalikan ke 'antre' supaya dicoba lagi (spesifikasi teknis bagian 3
    // langkah 7).
    function pulihkanJobMacet($menit = 10) {
        $batas = date('Y-m-d H:i:s', strtotime("-{$menit} minutes"));
        $this->db->where('status', 'jalan')->where('mulai_pada <=', $batas)
            ->update('prk_analisis_job', array('status' => 'antre'));
        return $this->db->affected_rows();
    }

    function tandaiSelesai($id_job, $status, $kode_galat = null, $galat = null) {
        $this->db->where('id', $id_job)->update('prk_analisis_job', array(
            'status' => $status,
            'kode_galat' => $kode_galat,
            'galat' => $galat,
            'selesai_pada' => date('Y-m-d H:i:s'),
        ));
    }

    // Gagal sementara (timeout/5xx/layanan mati): tambah percobaan, kembalikan ke
    // antre dengan jeda, atau 'gagal' permanen setelah $maks percobaan.
    function tandaiGagalSementara($id_job, $percobaan_sekarang, $maks, $pesan) {
        $percobaan_baru = $percobaan_sekarang + 1;
        if ($percobaan_baru >= $maks) {
            $this->tandaiSelesai($id_job, 'gagal', 'LAYANAN_TIDAK_TERSEDIA', $pesan);
            return 'gagal';
        }
        $jeda_menit = array(1 => 1, 2 => 5, 3 => 15);
        $tunda = isset($jeda_menit[$percobaan_baru]) ? $jeda_menit[$percobaan_baru] : 15;
        $this->db->where('id', $id_job)->update('prk_analisis_job', array(
            'status' => 'antre',
            'percobaan' => $percobaan_baru,
            'galat' => $pesan,
            'diminta_pada' => date('Y-m-d H:i:s', strtotime("+{$tunda} minutes")),
        ));
        return 'antre';
    }

    // Simpan hasil sukses dari /v1/analisis: profil baru (versi+1, is_current=1,
    // versi lama di-nonaktifkan) dan sampai 3 rekomendasi. Satu transaksi DB.
    function simpanHasil($id_transaksi, $profil, $rekomendasi) {
        $this->db->trans_start();

        $this->db->where('id_transaksi', $id_transaksi)->update('prk_profil', array('is_current' => 0));
        $this->db->where('id_transaksi', $id_transaksi)->update('prk_rekomendasi', array('is_current' => 0));

        $versi_lama = $this->db->select_max('versi')->where('id_transaksi', $id_transaksi)
            ->get('prk_profil')->row();
        $versi_baru = $versi_lama && $versi_lama->versi ? ((int) $versi_lama->versi + 1) : 1;

        $this->db->insert('prk_profil', array(
            'id_transaksi' => $id_transaksi,
            'versi' => $versi_baru,
            'is_current' => 1,
            'jenis_institusi' => $profil['jenis_institusi'],
            'jurusan_norm' => $profil['jurusan_norm'],
            'peminatan' => $profil['peminatan'],
            'judul' => $profil['judul'],
            'bidang' => json_encode($profil['bidang']),
            'alat' => json_encode($profil['alat']),
            'tujuan' => $profil['tujuan'],
            'mata_kuliah' => json_encode($profil['mata_kuliah']),
            'nilai' => json_encode($profil['nilai']),
            'rata_nilai' => $profil['rata_nilai'],
            'sumber' => $profil['sumber'],
            'dibuat_pada' => date('Y-m-d H:i:s'),
        ));
        $id_profil = $this->db->insert_id();

        foreach ($rekomendasi as $r) {
            $this->db->insert('prk_rekomendasi', array(
                'id_transaksi' => $id_transaksi,
                'id_profil' => $id_profil,
                'is_current' => 1,
                'peringkat' => $r['peringkat'],
                'id_divisi' => $r['id_divisi'],
                'skor' => $r['skor'],
                'keyakinan' => $r['keyakinan'],
                'f_jurusan' => $r['faktor']['jurusan'],
                'f_skill' => $r['faktor']['skill'],
                'f_proposal' => $r['faktor']['proposal'],
                'f_kebutuhan' => $r['faktor']['kebutuhan'],
                'alasan' => $r['alasan'],
                'kekuatan' => $r['kekuatan'],
                'kekurangan' => $r['kekurangan'],
                'dibuat_pada' => date('Y-m-d H:i:s'),
            ));
        }

        $this->db->trans_complete();
        return $this->db->trans_status() ? $id_profil : false;
    }

    function simpanTeksDokumen($id_transaksi, $jenis, $hash_berkas, $teks_mentah) {
        $this->db->insert('prk_teks_dokumen', array(
            'id_transaksi' => $id_transaksi,
            'jenis' => $jenis,
            'hash_berkas' => $hash_berkas,
            'teks_mentah' => $teks_mentah,
            'dibuat_pada' => date('Y-m-d H:i:s'),
        ));
    }
}
