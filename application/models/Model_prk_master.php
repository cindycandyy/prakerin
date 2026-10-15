<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Model_prk_master extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function listDepartemen() {
        return $this->db->order_by('urutan', 'ASC')->get('prk_departemen')->result();
    }

    function listDivisi($id_departemen = NULL) {
        $this->db->order_by('urutan', 'ASC');
        if (!empty($id_departemen)) {
            $this->db->where('id_departemen', $id_departemen);
        }
        return $this->db->get('prk_divisi')->result();
    }

    function getDivisi($id_divisi) {
        return $this->db->where('id', $id_divisi)->get('prk_divisi')->row();
    }

    function listDivisiDenganDepartemen() {
        $this->db
            ->select('prk_divisi.*, prk_departemen.nama as nama_departemen, prk_departemen.kode as kode_departemen')
            ->from('prk_divisi')
            ->join('prk_departemen', 'prk_departemen.id = prk_divisi.id_departemen')
            ->order_by('prk_departemen.urutan', 'ASC')
            ->order_by('prk_divisi.urutan', 'ASC');
        return $this->db->get()->result();
    }

    function getKuota($id_divisi) {
        return $this->db->where('id_divisi', $id_divisi)->get('prk_kuota')->row();
    }

    function setKuota($id_divisi, $kuota_maks, $diubah_oleh) {
        $existing = $this->getKuota($id_divisi);
        $data = array(
            'kuota_maks' => $kuota_maks,
            'diubah_oleh' => $diubah_oleh,
            'diubah_pada' => date('Y-m-d H:i:s'),
        );
        if ($existing) {
            $this->db->where('id_divisi', $id_divisi)->update('prk_kuota', $data);
        } else {
            $data['id_divisi'] = $id_divisi;
            $this->db->insert('prk_kuota', $data);
        }
    }

    // Jumlah peserta aktif (kontrak belum selesai/diakhiri_awal) per divisi saat ini,
    // tanpa batas periode - dipakai halaman Kuota divisi untuk kolom "Keterisian".
    function listTerisiPerDivisi() {
        $rows = $this->db
            ->select('p.id_divisi, COUNT(*) AS jumlah')
            ->from('prk_penempatan p')
            ->join('prk_kontrak k', 'k.id_penempatan = p.id')
            ->where_not_in('k.status', array('selesai', 'diakhiri_awal'))
            ->group_by('p.id_divisi')
            ->get()->result();

        $hasil = array();
        foreach ($rows as $r) {
            $hasil[(int) $r->id_divisi] = (int) $r->jumlah;
        }
        return $hasil;
    }

    function getPengaturan($kunci, $default = NULL) {
        $row = $this->db->where('kunci', $kunci)->get('prk_pengaturan')->row();
        return $row ? $row->nilai : $default;
    }

    function setPengaturan($kunci, $nilai, $diubah_oleh) {
        $data = array(
            'nilai' => $nilai,
            'diubah_oleh' => $diubah_oleh,
            'diubah_pada' => date('Y-m-d H:i:s'),
        );
        $existing = $this->db->where('kunci', $kunci)->get('prk_pengaturan')->row();
        if ($existing) {
            $this->db->where('kunci', $kunci)->update('prk_pengaturan', $data);
        } else {
            $data['kunci'] = $kunci;
            $this->db->insert('prk_pengaturan', $data);
        }
    }

    // Sisa tempat per departemen (depag), untuk hint informasi di wizard pendaftaran
    // (bagian B4 spesifikasi sisi pelamar: "Tempat tersedia/terbatas/penuh", bukan
    // penghalang -- kuota penentu sebenarnya ada di divisi dan diputuskan HR).
    // Dihitung dari depag.kuota dikurangi jumlah pkl_data Pending/Accept pada depag itu.
    function listSisaTempatDepag() {
        $depag = $this->db->get('depag')->result();
        $terisi = $this->db
            ->select('id_depag, COUNT(*) AS jumlah')
            ->from('pkl_data')
            ->where_in('status', array('Pending', 'Accept'))
            ->group_by('id_depag')
            ->get()->result();
        $peta_terisi = array();
        foreach ($terisi as $t) {
            $peta_terisi[(int) $t->id_depag] = (int) $t->jumlah;
        }
        foreach ($depag as $d) {
            $jumlah_terisi = isset($peta_terisi[(int) $d->id]) ? $peta_terisi[(int) $d->id] : 0;
            $d->sisa = $d->kuota > 0 ? max(0, (int) $d->kuota - $jumlah_terisi) : null;
        }
        return $depag;
    }

    // Dipakai server-side sebelum menyimpan pengajuan baru (bagian B4 spesifikasi
    // pelamar): opsi "Penuh" sudah di-disable di dropdown, tapi POST langsung tetap
    // harus ditolak di server -- sumber kebenaran sisa tempat sama persis dengan
    // listSisaTempatDepag() supaya tidak bisa berbeda hasil.
    function depagPenuh($id_depag) {
        $depag = $this->db->where('id', $id_depag)->get('depag')->row();
        if (!$depag || (int) $depag->kuota <= 0) {
            return false; // "Belum diatur" tetap boleh dipilih, sama seperti di dropdown.
        }
        $terisi = (int) $this->db
            ->where('id_depag', $id_depag)
            ->where_in('status', array('Pending', 'Accept'))
            ->count_all_results('pkl_data');
        return $terisi >= (int) $depag->kuota;
    }

    function getIdDepartemenUntukDepag($id_depag) {
        $row = $this->db->where('id_depag', $id_depag)->get('prk_depag_map')->row();
        return $row ? $row->id_departemen : NULL;
    }

    // Sisa kuota per divisi pada periode [mulai, selesai] (spesifikasi teknis bagian 2):
    // kuota_maks dikurangi jumlah prk_penempatan pada divisi itu yang kontraknya belum
    // selesai/diakhiri_awal dan periodenya beririsan dengan periode yang diminta.
    // Dipakai saat menyusun payload master.divisi[].sisa_kuota untuk /v1/analisis.
    function sisaKuotaSemuaDivisi($tgl_mulai, $tgl_selesai) {
        $kuota = $this->db->get('prk_kuota')->result();
        $sisa = array();
        foreach ($kuota as $k) {
            $sisa[(int) $k->id_divisi] = (int) $k->kuota_maks;
        }

        $terisi = $this->db
            ->select('p.id_divisi, COUNT(*) AS jumlah')
            ->from('prk_penempatan p')
            ->join('prk_kontrak k', 'k.id_penempatan = p.id')
            ->where_not_in('k.status', array('selesai', 'diakhiri_awal'))
            ->where('k.tanggal_mulai <=', $tgl_selesai)
            ->where('k.tanggal_selesai >=', $tgl_mulai)
            ->group_by('p.id_divisi')
            ->get()->result();

        foreach ($terisi as $t) {
            $id = (int) $t->id_divisi;
            if (isset($sisa[$id])) {
                $sisa[$id] = max(0, $sisa[$id] - (int) $t->jumlah);
            }
        }
        return $sisa; // [id_divisi => sisa]
    }

    // Sisa kuota satu divisi, mengunci baris prk_kuota (SELECT ... FOR UPDATE) supaya
    // dua HR yang menerima pelamar ke divisi yang sama secara bersamaan tidak lolos
    // dua-duanya saat kuota tinggal 1 (spesifikasi teknis bagian 9.2, kasus KUOTA_HABIS).
    // Wajib dipanggil di dalam transaksi DB ($this->db->trans_start()); kalau dipanggil
    // tanpa transaksi, lock dilepas begitu kueri ini selesai dan tidak melindungi apa pun.
    function sisaKuotaDivisiUntukUpdate($id_divisi, $tgl_mulai, $tgl_selesai) {
        $kuota = $this->db->where('id_divisi', $id_divisi)->get('prk_kuota')->row();
        $kuota_maks = $kuota ? (int) $kuota->kuota_maks : 0;

        // FOR UPDATE: query manual karena query builder CI3 tidak punya helper untuk ini.
        $this->db->query('SELECT id_divisi FROM prk_kuota WHERE id_divisi = ? FOR UPDATE', array($id_divisi));

        $terisi = $this->db
            ->select('COUNT(*) AS jumlah')
            ->from('prk_penempatan p')
            ->join('prk_kontrak k', 'k.id_penempatan = p.id')
            ->where('p.id_divisi', $id_divisi)
            ->where_not_in('k.status', array('selesai', 'diakhiri_awal'))
            ->where('k.tanggal_mulai <=', $tgl_selesai)
            ->where('k.tanggal_selesai >=', $tgl_mulai)
            ->get()->row();

        $jumlah_terisi = $terisi ? (int) $terisi->jumlah : 0;
        return max(0, $kuota_maks - $jumlah_terisi);
    }

    // Kolom numerik di-cast eksplisit: MySQLi result_array() mengembalikan semua
    // kolom sebagai string, dan layanan Python membandingkan/menjumlahkan nilai ini
    // sebagai angka (id_divisi == int, sum(bobot) butuh int, skor dipakai sebagai
    // float). Tanpa cast ini "29" == 29 selalu False di Python (F1/F2/F3 gagal
    // match diam-diam) dan sum() atas string meledak TypeError -- bug yang sempat
    // terjadi di jalur HTTP nyata, lolos dari test unit karena fixture test selalu
    // pakai int/float Python murni.
    function listSinyalDivisi() {
        $rows = $this->db->get('prk_sinyal_divisi')->result_array();
        foreach ($rows as &$r) {
            $r['id_divisi'] = (int) $r['id_divisi'];
            $r['bobot'] = (int) $r['bobot'];
        }
        return $rows;
    }

    function listPemetaanJurusan() {
        $rows = $this->db->get('prk_pemetaan_jurusan')->result_array();
        foreach ($rows as &$r) {
            $r['id_divisi'] = (int) $r['id_divisi'];
            $r['skor'] = (int) $r['skor'];
        }
        return $rows;
    }

    function listMapelRelevan() {
        $rows = $this->db->get('prk_mapel_relevan')->result_array();
        foreach ($rows as &$r) {
            $r['id_divisi'] = (int) $r['id_divisi'];
            $r['bobot'] = (int) $r['bobot'];
        }
        return $rows;
    }

    // Bobot F1-F4 + ambang keyakinan, dengan nilai awal spesifikasi bagian 11.1
    // kalau baris prk_pengaturan belum ada (seeder 002 seharusnya sudah mengisi).
    function bobotDanAmbang() {
        return array(
            'bobot' => array(
                'jurusan' => (int) $this->getPengaturan('bobot_f1', 45),
                'skill' => (int) $this->getPengaturan('bobot_f2', 25),
                'proposal' => (int) $this->getPengaturan('bobot_f3', 20),
                'kebutuhan' => (int) $this->getPengaturan('bobot_f4', 10),
            ),
            'ambang' => array(
                'tinggi' => (int) $this->getPengaturan('ambang_conf_tinggi', 75),
                'sedang' => (int) $this->getPengaturan('ambang_conf_sedang', 55),
            ),
        );
    }
}
