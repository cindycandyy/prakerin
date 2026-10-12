<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Controller halaman Analisis Penempatan (Tahap 1: kerangka saja).
// Fitur analisis, rekomendasi, surat balasan, dan notifikasi ditambahkan di tahap
// berikutnya (lihat DOKUMENTASI_ANALISIS.md). Untuk sekarang controller ini hanya
// menegakkan saklar aktif/nonaktif dan hak akses, plus layar Master Data dan Kuota
// yang datanya sudah bisa diisi HR lewat migrasi 002.

class Analisis extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->config->load('analisis');
        $this->load->model('model_prk_master');
        $this->load->model('model_prk_audit');
        $this->load->model('model_prk_pelamar');
        $this->load->model('model_prk_penempatan');

        if (!$this->config->item('analisis_aktif')) {
            show_404();
        }

        $auth = $this->session->userdata('SES_PRIV');
        if ($auth != 1 && $auth != 2) {
            redirect(base_url());
        }
    }

    function index() {
        redirect(base_url() . 'analisis/dashboard');
    }

    // Halaman mandiri (bukan tema sb-admin-2 lama): desain Figma punya sidebar dan
    // header sendiri, jadi tidak memuat header.php/footer.php.
    function dashboard() {
        $data['departemen'] = $this->model_prk_master->listDepartemen();
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $data['pelamar_perhatian'] = $this->model_prk_pelamar->listPelamar('pending', null, 5);
        $data['jumlah_status'] = $this->model_prk_pelamar->hitungPerStatus();
        $this->load->view('analisis/dashboard', $data);
    }

    // GET /analisis/pelamar — Halaman A (daftar pendaftar + top 3) dari spesifikasi
    // bagian 7.3. Data asli dari pkl_data + prk_rekomendasi (Tahap 2); kolom top-3
    // skor tampil "belum tersedia" selama mesin skoring belum mengisi prk_rekomendasi.
    function pelamar() {
        $tab = $this->input->get('tab') ? $this->input->get('tab') : 'pending';
        $cari = $this->input->get('cari');

        $data['tab'] = $tab;
        $data['cari'] = $cari;
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $data['daftar_pelamar'] = $this->model_prk_pelamar->listPelamar($tab, $cari);
        $data['jumlah_status'] = $this->model_prk_pelamar->hitungPerStatus();
        $this->load->view('analisis/pelamar', $data);
    }

    // GET /analisis/peserta/{id} — Halaman B (detail pelamar) dari spesifikasi.
    // Data asli dari pkl_data; rekomendasi dari prk_rekomendasi kalau sudah ada
    // (kosong sampai mesin skoring Tahap 2 selesai).
    function peserta($id = null) {
        $pelamar = $this->model_prk_pelamar->getPelamar($id);
        if (!$pelamar) {
            show_404();
        }
        $data['id_transaksi'] = $id;
        $data['pelamar'] = $pelamar;
        $data['rekomendasi'] = $this->model_prk_pelamar->listRekomendasi($id);
        $this->load->view('analisis/detail_pelamar', $data);
    }

    // POST /analisis/peserta/{id}/terima — simpan penempatan (prk_penempatan)
    // dan ubah status pkl_data jadi Accept. Validasi kuota dan kontrak otomatis
    // belum dikerjakan (lihat DOKUMENTASI_ANALISIS.md).
    function terima($id = null) {
        $pelamar = $this->model_prk_pelamar->getPelamar($id);
        if (!$pelamar) {
            show_404();
        }

        $id_divisi = $this->input->post('id_divisi');
        $alasan_ubah = trim((string) $this->input->post('alasan_ubah'));
        $auth_user = $this->session->userdata('SES_ID');

        if (empty($id_divisi)) {
            $this->session->set_flashdata('error', 'Pilih divisi sebelum menetapkan penempatan.');
            redirect(base_url() . 'analisis/peserta/' . $id);
            return;
        }

        $rekomendasi = $this->model_prk_pelamar->listRekomendasi($id);
        $peringkat_dipilih = null;
        $cocok_rekomendasi = false;
        foreach ($rekomendasi as $r) {
            if ((int) $r->id_divisi === (int) $id_divisi) {
                $peringkat_dipilih = $r->peringkat;
                $cocok_rekomendasi = ($r->peringkat == 1);
                break;
            }
        }
        if (!$cocok_rekomendasi && $alasan_ubah === '') {
            $this->session->set_flashdata('error', 'Alasan wajib diisi jika memilih divisi berbeda dari rekomendasi #1.');
            redirect(base_url() . 'analisis/peserta/' . $id);
            return;
        }

        $sebelum = $this->model_prk_penempatan->getPenempatan($id);
        $this->model_prk_penempatan->simpan($id, $id_divisi, $peringkat_dipilih, $alasan_ubah ?: null, $auth_user);
        $this->model_prk_penempatan->ubahStatusPelamar($id, 'Accept');

        $this->model_prk_audit->catat(
            'hr:' . $auth_user, 'penempatan', $id, 'terima',
            $sebelum ? array('id_divisi' => $sebelum->id_divisi) : null,
            array('id_divisi' => $id_divisi, 'alasan_ubah' => $alasan_ubah ?: null)
        );

        $this->session->set_flashdata('sukses', 'Pelamar diterima dan penempatan tersimpan.');
        redirect(base_url() . 'analisis/peserta/' . $id);
    }

    // POST /analisis/peserta/{id}/tolak — ubah status pkl_data jadi Reject.
    function tolak($id = null) {
        $pelamar = $this->model_prk_pelamar->getPelamar($id);
        if (!$pelamar) {
            show_404();
        }

        $auth_user = $this->session->userdata('SES_ID');
        $this->model_prk_penempatan->ubahStatusPelamar($id, 'Reject');
        $this->model_prk_audit->catat(
            'hr:' . $auth_user, 'pelamar', $id, 'tolak',
            array('status' => $pelamar->status), array('status' => 'Reject')
        );

        $this->session->set_flashdata('sukses', 'Pelamar ditolak.');
        redirect(base_url() . 'analisis/pelamar');
    }

    // GET /analisis/riwayat — Riwayat keputusan HR (terima/tolak), dari prk_audit.
    function riwayat() {
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $data['daftar_riwayat'] = $this->model_prk_audit->listKeputusanPelamar();
        $this->load->view('analisis/riwayat', $data);
    }

    // GET /analisis/kuota
    function kuota() {
        $data['divisi'] = $this->model_prk_master->listDivisiDenganDepartemen();
        foreach ($data['divisi'] as $d) {
            $kuota = $this->model_prk_master->getKuota($d->id);
            $d->kuota_maks = $kuota ? $kuota->kuota_maks : 0;
        }
        $this->load->view('header');
        $this->load->view('analisis/kuota', $data);
        $this->load->view('footer');
    }

    // POST /analisis/kuota_simpan
    function kuota_simpan() {
        $id_divisi = $this->input->post('id_divisi');
        $kuota_maks = (int) $this->input->post('kuota_maks');
        $auth_user = $this->session->userdata('SES_ID');

        if ($id_divisi === NULL || $kuota_maks < 0 || $kuota_maks > 99) {
            echo json_encode(array('ok' => false, 'kode' => 'KUOTA_TIDAK_VALID', 'pesan' => 'Kuota harus 0-99.'));
            return;
        }

        $sebelum = $this->model_prk_master->getKuota($id_divisi);
        $this->model_prk_master->setKuota($id_divisi, $kuota_maks, $auth_user);
        $this->model_prk_audit->catat(
            'hr:' . $auth_user, 'kuota', $id_divisi, 'ubah_kuota',
            $sebelum ? array('kuota_maks' => $sebelum->kuota_maks) : NULL,
            array('kuota_maks' => $kuota_maks)
        );

        echo json_encode(array('ok' => true));
    }

    // GET /analisis/pengaturan — hanya Admin (SES_PRIV 1), HR hanya lihat
    function pengaturan() {
        $kunci = array(
            'bobot_f1', 'bobot_f2', 'bobot_f3', 'bobot_f4',
            'ambang_conf_tinggi', 'ambang_conf_sedang', 'ambang_mepet_hari',
            'h7_hari', 'h3_hari',
        );
        $data['pengaturan'] = array();
        foreach ($kunci as $k) {
            $data['pengaturan'][$k] = $this->model_prk_master->getPengaturan($k);
        }
        $data['hanya_lihat'] = ($this->session->userdata('SES_PRIV') != 1);

        $this->load->view('header');
        $this->load->view('analisis/pengaturan', $data);
        $this->load->view('footer');
    }

    // POST /analisis/pengaturan_simpan — hanya Admin
    function pengaturan_simpan() {
        if ($this->session->userdata('SES_PRIV') != 1) {
            redirect(base_url() . 'analisis/pengaturan');
            return;
        }

        $bobot = array(
            'bobot_f1' => (int) $this->input->post('bobot_f1'),
            'bobot_f2' => (int) $this->input->post('bobot_f2'),
            'bobot_f3' => (int) $this->input->post('bobot_f3'),
            'bobot_f4' => (int) $this->input->post('bobot_f4'),
        );
        if (array_sum($bobot) != 100) {
            $this->session->set_flashdata('error', 'Jumlah bobot F1-F4 harus 100.');
            redirect(base_url() . 'analisis/pengaturan');
            return;
        }

        $auth_user = $this->session->userdata('SES_ID');
        foreach ($bobot as $k => $v) {
            $this->model_prk_master->setPengaturan($k, $v, $auth_user);
        }
        $this->model_prk_audit->catat('hr:' . $auth_user, 'pengaturan', 0, 'ubah_bobot', NULL, $bobot);

        $this->session->set_flashdata('sukses', 'Pengaturan tersimpan.');
        redirect(base_url() . 'analisis/pengaturan');
    }
}
