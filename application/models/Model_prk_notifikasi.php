<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Notifikasi HR (prk_notifikasi + prk_notifikasi_baca). Dibuat dari aksi yang
// sudah ada (terima/tolak di Analisis.php) dan dari generatePengingatKontrak()
// yang dipanggil lewat endpoint /analisis/jalankan_pengingat (lihat DOKUMENTASI_ANALISIS.md
// untuk cara menjadwalkannya -- scheduled task Windows/cron memanggil endpoint itu,
// bukan proses background terpisah).
class Model_prk_notifikasi extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // buat() - satu titik masuk, dedupe_key mencegah notifikasi dobel untuk
    // kejadian yang sama (mis. H-3 kontrak #5 cuma dibuat sekali). $untuk/$id_pelamar
    // (bagian B6/B7 spesifikasi pelamar): default 'hr'/null supaya pemanggil lama
    // (notifikasi HR) tidak perlu diubah; notifikasi pelamar mengisi id_pelamar =
    // pkl_data.user_id supaya topbar pelamar hanya menampilkan miliknya sendiri.
    function buat($jenis, $id_transaksi, $judul, $isi, $tautan, $dedupe_key, $id_kontrak = null, $untuk = 'hr', $id_pelamar = null) {
        $ada = $this->db->where('dedupe_key', $dedupe_key)->get('prk_notifikasi')->row();
        if ($ada) {
            return false;
        }
        $this->db->insert('prk_notifikasi', array(
            'jenis' => $jenis,
            'id_transaksi' => $id_transaksi,
            'id_kontrak' => $id_kontrak,
            'judul' => $judul,
            'isi' => $isi,
            'tautan' => $tautan,
            'dedupe_key' => $dedupe_key,
            'untuk' => $untuk,
            'id_pelamar' => $id_pelamar,
            'dibuat_pada' => date('Y-m-d H:i:s'),
        ));
        return true;
    }

    function hitungBelumDibaca($id_user) {
        return (int) $this->db
            ->from('prk_notifikasi n')
            ->join('prk_notifikasi_baca b', 'b.id_notifikasi = n.id AND b.id_user = ' . (int) $id_user, 'left')
            ->where('n.untuk', 'hr')
            ->where('b.id_notifikasi', null)
            ->count_all_results();
    }

    function listTerbaru($id_user, $batas = 8) {
        return $this->db
            ->select('n.*, (b.id_user IS NOT NULL) AS sudah_dibaca', false)
            ->from('prk_notifikasi n')
            ->join('prk_notifikasi_baca b', 'b.id_notifikasi = n.id AND b.id_user = ' . (int) $id_user, 'left')
            ->where('n.untuk', 'hr')
            ->order_by('n.dibuat_pada', 'DESC')
            ->limit($batas)
            ->get()->result();
    }

    // Notifikasi topbar pelamar (B7): $id_user tetap dipakai untuk join tabel baca
    // (prk_notifikasi_baca.id_user = user.id, sama seperti HR), $id_pelamar memfilter
    // notifikasi yang kejadiannya milik pelamar ini (prk_notifikasi.id_pelamar =
    // pkl_data.user_id / SES_LOGIN).
    function hitungBelumDibacaPelamar($id_user, $id_pelamar) {
        return (int) $this->db
            ->from('prk_notifikasi n')
            ->join('prk_notifikasi_baca b', 'b.id_notifikasi = n.id AND b.id_user = ' . (int) $id_user, 'left')
            ->where('n.untuk', 'pelamar')
            ->where('n.id_pelamar', $id_pelamar)
            ->where('b.id_notifikasi', null)
            ->count_all_results();
    }

    function listTerbaruPelamar($id_user, $id_pelamar, $batas = 8) {
        return $this->db
            ->select('n.*, (b.id_user IS NOT NULL) AS sudah_dibaca', false)
            ->from('prk_notifikasi n')
            ->join('prk_notifikasi_baca b', 'b.id_notifikasi = n.id AND b.id_user = ' . (int) $id_user, 'left')
            ->where('n.untuk', 'pelamar')
            ->where('n.id_pelamar', $id_pelamar)
            ->order_by('n.dibuat_pada', 'DESC')
            ->limit($batas)
            ->get()->result();
    }

    function tandaiDibaca($id_notifikasi, $id_user) {
        $ada = $this->db->where('id_notifikasi', $id_notifikasi)->where('id_user', $id_user)->get('prk_notifikasi_baca')->row();
        if ($ada) {
            return;
        }
        $this->db->insert('prk_notifikasi_baca', array(
            'id_notifikasi' => $id_notifikasi,
            'id_user' => $id_user,
            'dibaca_pada' => date('Y-m-d H:i:s'),
        ));
    }

    // Pengingat kontrak H-7/H-3: peserta Accept yang tgl_selesai jatuh persis
    // 7 atau 3 hari dari hari ini. dedupe_key berisi tanggal supaya tidak
    // double-notif kalau endpoint ini dipanggil berkali-kali di hari yang sama.
    function generatePengingatKontrak() {
        $dibuat = 0;
        foreach (array(7 => 'h7', 3 => 'h3') as $h => $jenis) {
            $tanggal = date('Y-m-d', strtotime("+{$h} days"));
            $peserta = $this->db
                ->select('id, user_id, nama, tgl_selesai')
                ->from('pkl_data')
                ->where('status', 'Accept')
                ->where('tgl_selesai', $tanggal)
                ->get()->result();
            foreach ($peserta as $p) {
                $baru = $this->buat(
                    $jenis, $p->id,
                    "Kontrak H-{$h}: {$p->nama}",
                    "{$p->nama} akan menyelesaikan magang pada " . date('d M Y', strtotime($p->tgl_selesai)) . ". Segera tinjau kontrak.",
                    base_url() . 'analisis/peserta/' . $p->id,
                    "{$jenis}:{$p->id}:{$tanggal}"
                );
                if ($baru) {
                    $dibuat++;
                }
                // Pelamar juga diberi tahu magangnya akan berakhir (bagian B7
                // spesifikasi sisi pelamar).
                $this->buat(
                    $jenis, $p->id, "Magang berakhir dalam {$h} hari",
                    "Magangmu berakhir pada " . date('d M Y', strtotime($p->tgl_selesai)) . ".",
                    base_url() . 'pelamar/pengajuan/' . $p->id,
                    "pelamar_{$jenis}:{$p->id}:{$tanggal}",
                    null, 'pelamar', $p->user_id
                );
            }
        }
        return $dibuat;
    }
}
