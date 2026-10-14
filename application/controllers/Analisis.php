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
        $this->load->model('model_prk_notifikasi');
        $this->load->model('model_prk_kontrak');
        $this->load->model('model_prk_surat');
        $this->load->library('analisis_status');
        $this->load->helper('analisis_helper');

        if (!$this->config->item('analisis_aktif')) {
            show_404();
        }

        $auth = $this->session->userdata('SES_PRIV');
        if ($auth != 1 && $auth != 2) {
            redirect(base_url() . 'pelamar/beranda');
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

        // KPI dashboard: dihitung dari data asli, bukan contoh. pkl_data tidak punya
        // kolom created_at (legacy), jadi "pendaftar minggu ini" tidak bisa dihitung
        // per tanggal pendaftaran -- dipakai total seluruh pelamar sebagai gantinya.
        $hari_ini_str = date('Y-m-d');
        $tujuh_hari = date('Y-m-d', strtotime('+7 days'));
        $pendaftar_minggu_ini = $this->db->count_all('pkl_data');

        $semua_kontrak = $this->model_prk_kontrak->listUntukKalender();
        $peserta_aktif = 0; $berakhir_7_hari = 0; $departemen_terisi = array();
        foreach ($semua_kontrak as $k) {
            if (in_array($k->status, array('aktif', 'akan_berakhir')) && $k->tanggal_mulai <= $hari_ini_str && $k->tanggal_selesai >= $hari_ini_str) {
                $peserta_aktif++;
                if ($k->id_departemen) { $departemen_terisi[$k->id_departemen] = true; }
            }
            if (in_array($k->status, array('aktif', 'akan_berakhir')) && $k->tanggal_selesai >= $hari_ini_str && $k->tanggal_selesai <= $tujuh_hari) {
                $berakhir_7_hari++;
            }
        }

        $data['kpi_pendaftar_minggu_ini'] = $pendaftar_minggu_ini;
        $data['kpi_peserta_aktif'] = $peserta_aktif;
        $data['kpi_departemen_terisi'] = count($departemen_terisi);
        $data['kpi_menunggu_keputusan'] = isset($data['jumlah_status']['pending']) ? $data['jumlah_status']['pending'] : 0;
        $data['kpi_berakhir_7_hari'] = $berakhir_7_hari;

        // "Agenda magang terdekat": 4 hari ke depan (termasuk hari ini) + event
        // mulai/selesai yang jatuh di tiap hari itu, dari kontrak yang sama
        // ($semua_kontrak) -- bukan data contoh terpisah.
        $agenda_per_hari = array();
        for ($i = 0; $i < 4; $i++) {
            $tgl = date('Y-m-d', strtotime("+{$i} day"));
            $agenda_per_hari[$tgl] = array();
        }
        foreach ($semua_kontrak as $k) {
            if (isset($agenda_per_hari[$k->tanggal_mulai])) {
                $agenda_per_hari[$k->tanggal_mulai][] = 'Mulai · ' . explode(' ', $k->nama)[0];
            }
            if (in_array($k->status, array('selesai', 'diakhiri_awal', 'akan_berakhir')) && isset($agenda_per_hari[$k->tanggal_selesai])) {
                $agenda_per_hari[$k->tanggal_selesai][] = 'Selesai · ' . explode(' ', $k->nama)[0];
            }
        }
        $data['agenda_per_hari'] = $agenda_per_hari;

        // "Segera tinjau kontrak": kontrak akan_berakhir yang belum ada tindak
        // lanjut HR, sama logic dengan panel "Perlu tindakan" di Kalender magang.
        $data['perlu_tindakan'] = array_values(array_filter($semua_kontrak, function ($k) {
            return $k->status === 'akan_berakhir' && $k->tindak_lanjut === 'belum';
        }));

        // Navigasi minggu (Senin-Minggu) relatif ke hari ini, digeser lewat ?minggu=-1/0/1/dst.
        $offset_minggu = (int) $this->input->get('minggu');
        $hari_ini = new DateTime();
        $senin = clone $hari_ini;
        $senin->modify('monday this week')->modify($offset_minggu . ' week');
        $minggu = clone $senin;
        $minggu->modify('+6 days');
        $data['hari_ini'] = $hari_ini;
        $data['minggu_mulai'] = $senin;
        $data['minggu_selesai'] = $minggu;
        $data['offset_minggu'] = $offset_minggu;

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
        $data['profil'] = $this->model_prk_pelamar->getProfil($id);
        $data['foto_cek'] = $this->model_prk_pelamar->getFotoCek($id);
        $data['rekomendasi'] = $this->model_prk_pelamar->listRekomendasi($id);
        $data['semua_divisi'] = $this->model_prk_master->listDivisiDenganDepartemen();

        $penempatan = $this->model_prk_penempatan->getPenempatan($id);
        $data['kontrak'] = $penempatan ? $this->model_prk_kontrak->getByPenempatan($penempatan->id) : null;

        $this->load->view('analisis/detail_pelamar', $data);
    }

    // POST /analisis/peserta/{id}/analisis_ulang — tombol "Analisis ulang" (FR-04):
    // paksa job baru masuk antrean meski berkas sama persis dengan analisis
    // terakhir. Hasil baru baru muncul setelah Cli_analisis::antrean() berjalan
    // (cron tiap menit), bukan seketika -- makanya cuma pesan status, bukan hasil.
    function analisis_ulang($id = null) {
        $pelamar = $this->model_prk_pelamar->getPelamar($id);
        if (!$pelamar) {
            show_404();
        }

        $this->load->library('analisis_queue');
        $masuk_antrean = $this->analisis_queue->enqueue($id, true);

        if ($masuk_antrean) {
            $this->session->set_flashdata('sukses', 'Analisis ulang diantrekan. Hasil baru akan muncul dalam beberapa menit.');
        } else {
            $this->session->set_flashdata('error', 'Belum bisa mengantrekan analisis ulang — proposal/transkrip belum lengkap, atau masih ada proses analisis yang berjalan.');
        }

        redirect(base_url() . 'analisis/peserta/' . $id);
    }

    // GET /analisis/peserta/{id}/berkas_unduh — zip semua berkas pelamar yang
    // ada secara fisik di gambar/, untuk tombol "Unduh semua berkas".
    // GET /analisis/peserta/{id}/berkas/{slot} — satu berkas pelamar (FR-25/FR-28,
    // bagian 7.7): disajikan lewat PHP setelah controller ini mengecek SES_PRIV 1/2
    // (lihat constructor), bukan tautan langsung ke gambar/ yang bisa ditebak siapa
    // pun. $slot dicocokkan ke whitelist lalu nama filenya diambil dari pkl_data --
    // tidak pernah dari parameter URL -- supaya tidak bisa dipakai membaca file lain.
    function berkas_lihat($id = null, $slot = null) {
        $pelamar = $this->model_prk_pelamar->getPelamar($id);
        if (!$pelamar) {
            show_404();
        }

        $peta_slot = array(
            'doc_1' => $pelamar->doc_1, 'doc_2' => $pelamar->doc_2, 'doc_3' => $pelamar->doc_3,
            'doc_4' => $pelamar->doc_4, 'doc_5' => $pelamar->doc_5, 'doc_6' => $pelamar->doc_6,
            'foto' => $pelamar->foto,
        );
        if (!array_key_exists($slot, $peta_slot) || empty($peta_slot[$slot])) {
            show_404();
        }

        $nama_file = $peta_slot[$slot];
        $path_absolut = FCPATH . 'gambar/' . $nama_file;
        if (!is_file($path_absolut)) {
            show_404();
        }

        $unduh = (bool) $this->input->get('unduh');
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $mime = array('pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png');
        header('Content-Type: ' . (isset($mime[$ekstensi]) ? $mime[$ekstensi] : 'application/octet-stream'));
        header('Content-Disposition: ' . ($unduh ? 'attachment' : 'inline') . '; filename="' . $nama_file . '"');
        header('Content-Length: ' . filesize($path_absolut));
        readfile($path_absolut);
    }

    function berkas_unduh($id = null) {
        $pelamar = $this->model_prk_pelamar->getPelamar($id);
        if (!$pelamar) {
            show_404();
        }

        $berkas = array(
            'surat_pengantar' => $pelamar->doc_1,
            'proposal_magang' => $pelamar->doc_2,
            'transkrip_nilai' => $pelamar->doc_3,
            'kartu_pelajar' => $pelamar->doc_4,
            'kartu_asuransi' => $pelamar->doc_5,
            'surat_pernyataan' => $pelamar->doc_6,
            'foto' => $pelamar->foto,
        );

        $zip = new ZipArchive();
        $path_zip = sys_get_temp_dir() . '/berkas-' . (int) $id . '-' . uniqid() . '.zip';
        if ($zip->open($path_zip, ZipArchive::CREATE) !== true) {
            show_404();
        }

        $ada_isi = false;
        foreach ($berkas as $label => $file) {
            if (empty($file)) {
                continue;
            }
            $path_sumber = FCPATH . 'gambar/' . $file;
            if (is_file($path_sumber)) {
                $zip->addFile($path_sumber, $label . '-' . $file);
                $ada_isi = true;
            }
        }
        $zip->close();

        if (!$ada_isi) {
            unlink($path_zip);
            show_404();
        }

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="berkas-' . (int) $id . '.zip"');
        header('Content-Length: ' . filesize($path_zip));
        readfile($path_zip);
        unlink($path_zip);
    }

    // POST /analisis/peserta/{id}/terima — cek sisa kuota divisi (row lock, bagian 9.2),
    // simpan penempatan (prk_penempatan), ubah status pkl_data jadi Accept lewat
    // Analisis_status, dan buat kontrak ke-1 (bagian 6.1, baris "pending -> accept").
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
        if (!$this->analisis_status->bolehUbah($pelamar->status, Analisis_status::ACCEPT)) {
            $this->session->set_flashdata('error', 'Status pelamar saat ini tidak bisa diubah menjadi Accept.');
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

        // Kunci baris prk_kuota divisi ini dalam satu transaksi DB supaya dua HR yang
        // menerima bersamaan ke divisi dengan sisa kuota 1 tidak lolos dua-duanya
        // (bagian 9.2, KUOTA_HABIS). Penempatan yang sudah ada (ganti divisi) tidak
        // ikut dihitung sebagai "terisi" dua kali karena sebelum() belum di-update.
        $this->db->trans_start();
        $sisa_kuota = $this->model_prk_master->sisaKuotaDivisiUntukUpdate($id_divisi, $pelamar->tgl_mulai, $pelamar->tgl_selesai);
        $penempatan_lama = $this->model_prk_penempatan->getPenempatan($id);
        $sudah_di_divisi_ini = $penempatan_lama && (int) $penempatan_lama->id_divisi === (int) $id_divisi;
        if ($sisa_kuota < 1 && !$sudah_di_divisi_ini) {
            $this->db->trans_complete();
            $this->session->set_flashdata('error', 'Kuota divisi ini sudah habis untuk periode tersebut.');
            redirect(base_url() . 'analisis/peserta/' . $id);
            return;
        }

        $sebelum = $penempatan_lama;
        $this->model_prk_penempatan->simpan($id, $id_divisi, $peringkat_dipilih, $alasan_ubah ?: null, $auth_user);
        $this->analisis_status->ubah($id, Analisis_status::ACCEPT, 'hr:' . $auth_user, $alasan_ubah ?: null);

        $penempatan = $this->model_prk_penempatan->getPenempatan($id);
        if ($penempatan && !$this->model_prk_kontrak->getByPenempatan($penempatan->id)) {
            $this->model_prk_kontrak->buatPertama($penempatan->id, $pelamar->tgl_mulai, $pelamar->tgl_selesai);
        }
        $this->db->trans_complete();

        $this->model_prk_audit->catat(
            'hr:' . $auth_user, 'penempatan', $id, 'terima',
            $sebelum ? array('id_divisi' => $sebelum->id_divisi) : null,
            array('id_divisi' => $id_divisi, 'alasan_ubah' => $alasan_ubah ?: null)
        );
        $this->model_prk_notifikasi->buat(
            'status_otomatis', $id, "Pelamar diterima: {$pelamar->nama}",
            "{$pelamar->nama} diterima dan ditempatkan.",
            base_url() . 'analisis/peserta/' . $id,
            'terima:' . $id . ':' . date('Y-m-d H:i:s')
        );
        $this->model_prk_notifikasi->buat(
            'diterima', $id, 'Pengajuanmu diterima',
            'Selamat! Periode magang ' . tanggal_indo($pelamar->tgl_mulai) . ' - ' . tanggal_indo($pelamar->tgl_selesai) . '.',
            base_url() . 'pelamar/pengajuan/' . $id,
            'pelamar_diterima:' . $id . ':' . date('Y-m-d H:i:s'),
            null, 'pelamar', $pelamar->user_id
        );

        $this->session->set_flashdata('sukses', 'Pelamar diterima, penempatan dan kontrak tersimpan.');
        redirect(base_url() . 'analisis/peserta/' . $id);
    }

    // POST /analisis/peserta/{id}/tolak — alasan wajib (min. 5 karakter, bagian 6.1),
    // ubah status pkl_data jadi Rejected lewat Analisis_status.
    function tolak($id = null) {
        $pelamar = $this->model_prk_pelamar->getPelamar($id);
        if (!$pelamar) {
            show_404();
        }

        $alasan = trim((string) $this->input->post('alasan'));
        if (strlen($alasan) < 5) {
            $this->session->set_flashdata('error', 'Alasan penolakan wajib diisi (minimal 5 karakter).');
            redirect(base_url() . 'analisis/peserta/' . $id);
            return;
        }
        $auth_user = $this->session->userdata('SES_ID');
        if (!$this->analisis_status->ubah($id, Analisis_status::REJECTED, 'hr:' . $auth_user, $alasan)) {
            $this->session->set_flashdata('error', 'Status pelamar saat ini tidak bisa diubah menjadi Rejected.');
            redirect(base_url() . 'analisis/peserta/' . $id);
            return;
        }
        // D2: versi ringkas alasan yang boleh dilihat pelamar sendiri, ditulis HR saat menolak.
        $this->db->where('id', $id)->update('pkl_data', array('alasan_pelamar' => $alasan));

        $this->model_prk_notifikasi->buat(
            'status_otomatis', $id, "Pelamar ditolak: {$pelamar->nama}",
            "{$pelamar->nama} ditolak.",
            base_url() . 'analisis/peserta/' . $id,
            'tolak:' . $id . ':' . date('Y-m-d H:i:s')
        );
        $this->model_prk_notifikasi->buat(
            'ditolak', $id, 'Pengajuan belum dapat diproses', $alasan,
            base_url() . 'pelamar/pengajuan/' . $id,
            'pelamar_ditolak:' . $id . ':' . date('Y-m-d H:i:s'),
            null, 'pelamar', $pelamar->user_id
        );

        $this->session->set_flashdata('sukses', 'Pelamar ditolak.');
        redirect(base_url() . 'analisis/pelamar');
    }

    // POST /analisis/kontrak/{id}/perpanjang — kontrak lama ditandai diperpanjang,
    // kontrak baru urutan berikutnya dibuat (bagian 6.1 & 7.1 spesifikasi).
    function kontrak_perpanjang($id_kontrak = null) {
        $kontrak = $this->model_prk_kontrak->getByIdDenganPelamar($id_kontrak);
        if (!$kontrak) {
            show_404();
        }

        $tanggal_selesai_baru = trim((string) $this->input->post('tanggal_selesai_baru'));
        $tanggal_mulai_baru = date('Y-m-d', strtotime($kontrak->tanggal_selesai . ' +1 day'));
        if ($tanggal_selesai_baru === '' || $tanggal_selesai_baru <= $tanggal_mulai_baru) {
            $this->session->set_flashdata('error', 'Tanggal selesai baru wajib diisi dan harus setelah tanggal mulai perpanjangan.');
            redirect(base_url() . 'analisis/peserta/' . $kontrak->id_transaksi);
            return;
        }
        if ($kontrak->status === 'selesai' || $kontrak->status === 'diakhiri_awal') {
            $this->session->set_flashdata('error', 'Kontrak yang sudah selesai/diakhiri tidak bisa diperpanjang.');
            redirect(base_url() . 'analisis/peserta/' . $kontrak->id_transaksi);
            return;
        }

        $auth_user = $this->session->userdata('SES_ID');
        $id_baru = $this->model_prk_kontrak->perpanjang($id_kontrak, $tanggal_selesai_baru);
        if ($id_baru === false) {
            $this->session->set_flashdata('error', 'Gagal memperpanjang kontrak.');
            redirect(base_url() . 'analisis/peserta/' . $kontrak->id_transaksi);
            return;
        }

        $this->model_prk_audit->catat(
            'hr:' . $auth_user, 'kontrak', $id_baru, 'perpanjang',
            array('kontrak_lama' => $id_kontrak, 'tanggal_selesai_lama' => $kontrak->tanggal_selesai),
            array('tanggal_mulai' => $tanggal_mulai_baru, 'tanggal_selesai' => $tanggal_selesai_baru)
        );
        $this->model_prk_notifikasi->buat(
            'status_otomatis', $kontrak->id_transaksi, "Kontrak diperpanjang: {$kontrak->nama}",
            "{$kontrak->nama} diperpanjang sampai " . date('d M Y', strtotime($tanggal_selesai_baru)) . ".",
            base_url() . 'analisis/peserta/' . $kontrak->id_transaksi,
            'perpanjang:' . $id_baru . ':' . date('Y-m-d H:i:s')
        );

        $this->session->set_flashdata('sukses', 'Kontrak diperpanjang.');
        redirect(base_url() . 'analisis/peserta/' . $kontrak->id_transaksi);
    }

    // POST /analisis/kontrak/{id}/akhiri — akhiri lebih awal, alasan wajib (bagian 6.1),
    // ubah status pkl_data jadi Inactive lewat Analisis_status.
    function kontrak_akhiri($id_kontrak = null) {
        $kontrak = $this->model_prk_kontrak->getByIdDenganPelamar($id_kontrak);
        if (!$kontrak) {
            show_404();
        }

        $alasan = trim((string) $this->input->post('alasan'));
        if (strlen($alasan) < 5) {
            $this->session->set_flashdata('error', 'Alasan wajib diisi (minimal 5 karakter) untuk mengakhiri kontrak lebih awal.');
            redirect(base_url() . 'analisis/peserta/' . $kontrak->id_transaksi);
            return;
        }
        if ($kontrak->status === 'selesai' || $kontrak->status === 'diakhiri_awal') {
            $this->session->set_flashdata('error', 'Kontrak ini sudah berakhir.');
            redirect(base_url() . 'analisis/peserta/' . $kontrak->id_transaksi);
            return;
        }

        $auth_user = $this->session->userdata('SES_ID');
        $this->model_prk_kontrak->akhiriLebihAwal($id_kontrak, $alasan);
        $this->analisis_status->ubah($kontrak->id_transaksi, Analisis_status::INACTIVE, 'hr:' . $auth_user, $alasan);

        $this->model_prk_audit->catat(
            'hr:' . $auth_user, 'kontrak', $id_kontrak, 'akhiri_lebih_awal',
            array('status' => $kontrak->status), array('status' => 'diakhiri_awal'), $alasan
        );
        $this->model_prk_notifikasi->buat(
            'status_otomatis', $kontrak->id_transaksi, "Kontrak diakhiri lebih awal: {$kontrak->nama}",
            "{$kontrak->nama} diakhiri lebih awal. Alasan: {$alasan}",
            base_url() . 'analisis/peserta/' . $kontrak->id_transaksi,
            'akhiri:' . $id_kontrak . ':' . date('Y-m-d H:i:s')
        );

        $this->session->set_flashdata('sukses', 'Kontrak diakhiri lebih awal.');
        redirect(base_url() . 'analisis/peserta/' . $kontrak->id_transaksi);
    }

    // POST /analisis/peserta/{id}/surat_buat — tombol "Buat surat balasan" di Halaman B
    // (bagian 8.3 langkah 1). Kalau pelamar lain dari instansi+rujukan sama sudah punya
    // surat draf, gabung ke situ; kalau pelamar ini sendiri sudah punya draf, buka itu;
    // selain itu buat surat baru.
    function surat_buat($id = null) {
        $pelamar = $this->model_prk_pelamar->getPelamar($id);
        if (!$pelamar) {
            show_404();
        }
        if (strtolower($pelamar->status) !== 'accept') {
            $this->session->set_flashdata('error', 'Surat balasan hanya bisa dibuat untuk pelamar yang sudah diterima.');
            redirect(base_url() . 'analisis/peserta/' . $id);
            return;
        }

        $draf_ada = $this->model_prk_surat->getSuratDrafUntukPelamar($id);
        if ($draf_ada) {
            redirect(base_url() . 'analisis/surat/' . $draf_ada->id);
            return;
        }

        $auth_user = $this->session->userdata('SES_ID');
        $id_surat = $this->model_prk_surat->buatDari(array($id), $auth_user);
        if ($id_surat === false) {
            $this->session->set_flashdata('error', 'Gagal membuat surat balasan.');
            redirect(base_url() . 'analisis/peserta/' . $id);
            return;
        }

        $this->model_prk_audit->catat('hr:' . $auth_user, 'surat', $id_surat, 'buat', null, array('peserta_awal' => $id));
        redirect(base_url() . 'analisis/surat/' . $id_surat);
    }

    // POST /analisis/surat/{id}/gabung — tambahkan satu kandidat (instansi sama, sudah
    // Accept, belum punya surat) ke surat draf ini (bagian 8.3, "gabung peserta").
    function surat_gabung($id_surat = null) {
        $surat = $this->model_prk_surat->getById($id_surat);
        if (!$surat) {
            show_404();
        }
        if ($surat->status !== 'draf') {
            $this->session->set_flashdata('error', 'Surat yang sudah terkirim tidak bisa diedit.');
            redirect(base_url() . 'analisis/surat/' . $id_surat);
            return;
        }

        $id_transaksi = (int) $this->input->post('id_transaksi');
        $peserta = $this->model_prk_surat->listPeserta($id_surat);
        $valid = false;
        if ($id_transaksi && !empty($peserta)) {
            $kandidat = $this->model_prk_surat->listKandidatGabung($peserta[0]->institusi, $peserta[0]->id);
            foreach ($kandidat as $k) {
                if ((int) $k->id === $id_transaksi) {
                    $valid = true;
                    break;
                }
            }
        }
        if (!$valid) {
            $this->session->set_flashdata('error', 'Pelamar ini tidak bisa digabung (sudah punya surat atau bukan dari instansi yang sama).');
            redirect(base_url() . 'analisis/surat/' . $id_surat);
            return;
        }

        $this->model_prk_surat->tambahPeserta($id_surat, $id_transaksi);
        $auth_user = $this->session->userdata('SES_ID');
        $this->model_prk_audit->catat('hr:' . $auth_user, 'surat', $id_surat, 'gabung_peserta', null, array('id_transaksi' => $id_transaksi));
        $this->session->set_flashdata('sukses', 'Pelamar berhasil digabungkan ke surat ini.');
        redirect(base_url() . 'analisis/surat/' . $id_surat);
    }

    // GET /analisis/surat/{id} — Halaman C (form isian surat balasan, bagian 8.3).
    function surat($id_surat = null) {
        $surat = $this->model_prk_surat->getById($id_surat);
        if (!$surat) {
            show_404();
        }
        $data['surat'] = $surat;
        $data['peserta'] = $this->model_prk_surat->listPeserta($id_surat);
        $data['kandidat_gabung'] = array();
        if ($surat->status === 'draf' && !empty($data['peserta'])) {
            $data['kandidat_gabung'] = $this->model_prk_surat->listKandidatGabung($data['peserta'][0]->institusi, $data['peserta'][0]->id);
        }
        $data['saran_nomor'] = $this->model_prk_surat->saranNomorBerikutnya(date('Y'));
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $this->load->view('analisis/surat', $data);
    }

    // POST /analisis/surat/{id}/simpan — simpan isian, tetap draf (bagian 8.3 langkah 2).
    function surat_simpan($id_surat = null) {
        $surat = $this->model_prk_surat->getById($id_surat);
        if (!$surat) {
            show_404();
        }
        if ($surat->status !== 'draf') {
            $this->session->set_flashdata('error', 'Surat yang sudah terkirim tidak bisa diedit. Buat surat pengganti.');
            redirect(base_url() . 'analisis/surat/' . $id_surat);
            return;
        }

        $nomor_urut = trim((string) $this->input->post('nomor_urut'));
        $tanggal_surat = $this->input->post('tanggal_surat') ?: date('Y-m-d');
        $pembekalan_tanggal = $this->input->post('pembekalan_tanggal');

        $data = array(
            'tanggal_surat' => $tanggal_surat,
            'rujukan_nomor' => trim((string) $this->input->post('rujukan_nomor')) ?: null,
            'rujukan_tanggal' => $this->input->post('rujukan_tanggal') ?: null,
            'pembekalan_tanggal' => $pembekalan_tanggal ?: null,
            'pembekalan_pukul' => trim((string) $this->input->post('pembekalan_pukul')) ?: $this->model_prk_master->getPengaturan('surat_pembekalan_pukul'),
            'pembekalan_tempat' => trim((string) $this->input->post('pembekalan_tempat')) ?: null,
            'pembekalan_keterangan' => trim((string) $this->input->post('pembekalan_keterangan')) ?: null,
            'kepada_override' => trim((string) $this->input->post('kepada_override')) ?: null,
        );

        if ($nomor_urut !== '') {
            $segmen_tengah = $this->model_prk_master->getPengaturan('surat_pola_nomor', 'B2/NK.03.02/SU/DR');
            $tahun = date('Y', strtotime($tanggal_surat));
            $nomor_lengkap = "{$nomor_urut}/{$segmen_tengah}/{$tahun}";
            if (!preg_match('#^\d{5}/[A-Z0-9./]+/\d{4}$#', $nomor_lengkap)) {
                $this->session->set_flashdata('error', 'Format nomor urut tidak valid, gunakan 5 digit angka.');
                redirect(base_url() . 'analisis/surat/' . $id_surat);
                return;
            }
            if ($this->model_prk_surat->nomorSudahDipakai($nomor_lengkap, $id_surat)) {
                $this->session->set_flashdata('error', 'Nomor surat ini sudah dipakai surat lain.');
                redirect(base_url() . 'analisis/surat/' . $id_surat);
                return;
            }
            $data['nomor_surat'] = $nomor_lengkap;
        }

        $this->model_prk_surat->simpanIsian($id_surat, $data);
        $this->session->set_flashdata('sukses', 'Isian surat tersimpan.');
        redirect(base_url() . 'analisis/surat/' . $id_surat);
    }

    // GET /analisis/surat/{id}/pratinjau — PDF inline dengan watermark DRAF (bagian 8.3
    // langkah 3), dari isian terakhir yang tersimpan.
    function surat_pratinjau($id_surat = null) {
        $surat = $this->model_prk_surat->getById($id_surat);
        $peserta = $this->model_prk_surat->listPeserta($id_surat);
        if (!$surat || empty($peserta)) {
            show_404();
        }

        $this->load->library('analisis_surat');
        $pdf = $this->analisis_surat->render($surat, $peserta, true);

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="pratinjau-surat-' . $id_surat . '.pdf"');
        echo $pdf;
    }

    // POST /analisis/surat/{id}/kirim — finalkan: PDF tanpa watermark disimpan,
    // status jadi terkirim, isian terkunci (bagian 8.3 langkah 4).
    function surat_kirim($id_surat = null) {
        $surat = $this->model_prk_surat->getById($id_surat);
        $peserta = $this->model_prk_surat->listPeserta($id_surat);
        if (!$surat) {
            show_404();
        }
        if ($surat->status !== 'draf') {
            $this->session->set_flashdata('error', 'Surat ini sudah terkirim.');
            redirect(base_url() . 'analisis/surat/' . $id_surat);
            return;
        }
        if (empty($surat->nomor_surat) || empty($peserta)) {
            $this->session->set_flashdata('error', 'Lengkapi nomor surat sebelum mengirim.');
            redirect(base_url() . 'analisis/surat/' . $id_surat);
            return;
        }
        if (empty($surat->pembekalan_tanggal) || empty($surat->pembekalan_pukul)) {
            $this->session->set_flashdata('error', 'Tanggal dan pukul pembekalan wajib diisi sebelum mengirim.');
            redirect(base_url() . 'analisis/surat/' . $id_surat);
            return;
        }

        $this->load->library('analisis_surat');
        $pdf = $this->analisis_surat->render($surat, $peserta, false);
        $file_path = $this->analisis_surat->simpanFinal($id_surat, $pdf);

        $ok = $this->model_prk_surat->tandaiTerkirim($id_surat, $file_path);
        if (!$ok) {
            $this->session->set_flashdata('error', 'Gagal mengirim surat (kemungkinan sudah dikirim sebelumnya).');
            redirect(base_url() . 'analisis/surat/' . $id_surat);
            return;
        }

        $auth_user = $this->session->userdata('SES_ID');
        $this->model_prk_audit->catat('hr:' . $auth_user, 'surat', $id_surat, 'kirim', array('status' => 'draf'), array('status' => 'terkirim'));

        foreach ($peserta as $p) {
            $this->model_prk_notifikasi->buat(
                'surat_terkirim', $p->id, 'Surat balasan tersedia',
                'Unduh surat balasan untuk ' . $p->institusi . '.',
                base_url() . 'pelamar/pengajuan/' . $p->id,
                'pelamar_surat:' . $id_surat . ':' . $p->id,
                null, 'pelamar', $p->user_id
            );
        }

        $this->session->set_flashdata('sukses', 'Surat balasan terkirim.');
        redirect(base_url() . 'analisis/surat/' . $id_surat);
    }

    // GET /analisis/surat/{id}/unduh — PDF final, hanya untuk surat berstatus terkirim.
    function surat_unduh($id_surat = null) {
        $surat = $this->model_prk_surat->getById($id_surat);
        if (!$surat || $surat->status === 'draf' || empty($surat->file_path)) {
            show_404();
        }

        $this->load->library('analisis_surat');
        $path_absolut = $this->analisis_surat->pathAbsolut($surat->file_path);
        if (!is_file($path_absolut)) {
            show_404();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="surat-balasan-' . $id_surat . '.pdf"');
        header('Content-Length: ' . filesize($path_absolut));
        readfile($path_absolut);
    }

    // POST /analisis/surat/{id}/pengganti — surat lama -> diganti, surat baru draf
    // dengan peserta sama (bagian 8.3 langkah 5).
    function surat_pengganti($id_surat = null) {
        $auth_user = $this->session->userdata('SES_ID');
        $id_baru = $this->model_prk_surat->buatPengganti($id_surat, $auth_user);
        if ($id_baru === false) {
            $this->session->set_flashdata('error', 'Gagal membuat surat pengganti.');
            redirect(base_url() . 'analisis/surat/' . $id_surat);
            return;
        }

        $this->model_prk_audit->catat('hr:' . $auth_user, 'surat', $id_baru, 'buat_pengganti', array('surat_lama' => $id_surat), null);
        $this->session->set_flashdata('sukses', 'Surat pengganti dibuat, silakan lengkapi nomor baru.');
        redirect(base_url() . 'analisis/surat/' . $id_baru);
    }

    // GET /analisis/riwayat — Riwayat keputusan HR (terima/tolak), dari prk_audit.
    function riwayat() {
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $data['daftar_riwayat'] = $this->model_prk_audit->listKeputusanPelamar();
        $this->load->view('analisis/riwayat', $data);
    }

    // GET /analisis/kalender — peserta Accept yang mulai/selesai magang dalam 30 hari.
    function kalender() {
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');

        $tahun = (int) $this->input->get('tahun');
        $bulan = (int) $this->input->get('bulan');
        if ($tahun < 2000 || $bulan < 1 || $bulan > 12) {
            $tahun = (int) date('Y');
            $bulan = (int) date('n');
        }
        $id_departemen_filter = (int) $this->input->get('departemen');

        $awal_bulan = new DateTime("{$tahun}-{$bulan}-01");
        $akhir_bulan = (clone $awal_bulan)->modify('last day of this month');
        $str_awal = $awal_bulan->format('Y-m-d');
        $str_akhir = $akhir_bulan->format('Y-m-d');
        $hari_ini = date('Y-m-d');

        $semua_kontrak = $this->model_prk_kontrak->listUntukKalender();
        $data['departemen'] = $this->model_prk_master->listDepartemen();

        $kontrak_tampil = $id_departemen_filter
            ? array_values(array_filter($semua_kontrak, function ($k) use ($id_departemen_filter) {
                return (int) $k->id_departemen === $id_departemen_filter;
            }))
            : $semua_kontrak;

        // KPI: dihitung dari SEMUA kontrak (tidak ikut filter departemen), supaya
        // angka ringkasan tetap mewakili keseluruhan meski tabel/grid difilter.
        $kpi_aktif_hari_ini = 0; $kpi_akan_mulai_bulan_ini = 0; $kpi_berakhir_7_hari = 0; $kpi_selesai_bulan_ini = 0;
        $batas_7_hari = date('Y-m-d', strtotime('+7 days'));
        foreach ($semua_kontrak as $k) {
            if ($k->status === 'aktif' || $k->status === 'akan_berakhir') {
                if ($k->tanggal_mulai <= $hari_ini && $k->tanggal_selesai >= $hari_ini) {
                    $kpi_aktif_hari_ini++;
                }
            }
            if ($k->status === 'akan_mulai' && $k->tanggal_mulai >= $str_awal && $k->tanggal_mulai <= $str_akhir) {
                $kpi_akan_mulai_bulan_ini++;
            }
            if (($k->status === 'aktif' || $k->status === 'akan_berakhir') && $k->tanggal_selesai >= $hari_ini && $k->tanggal_selesai <= $batas_7_hari) {
                $kpi_berakhir_7_hari++;
            }
            if (($k->status === 'selesai' || $k->status === 'diakhiri_awal') && $k->tanggal_selesai >= $str_awal && $k->tanggal_selesai <= $str_akhir) {
                $kpi_selesai_bulan_ini++;
            }
        }
        $data['kpi'] = array(
            'aktif_hari_ini' => $kpi_aktif_hari_ini,
            'akan_mulai_bulan_ini' => $kpi_akan_mulai_bulan_ini,
            'berakhir_7_hari' => $kpi_berakhir_7_hari,
            'selesai_bulan_ini' => $kpi_selesai_bulan_ini,
        );

        // Perlu tindakan: kontrak akan_berakhir, belum ada tindak lanjut HR.
        $data['perlu_tindakan'] = array_values(array_filter($kontrak_tampil, function ($k) {
            return $k->status === 'akan_berakhir' && $k->tindak_lanjut === 'belum';
        }));

        // Grid kalender: kelompokkan per tanggal (mulai & selesai bisa jatuh di
        // bulan yang sama untuk kontrak pendek, makanya dicek keduanya).
        $per_tanggal = array();
        foreach ($kontrak_tampil as $k) {
            if ($k->tanggal_mulai >= $str_awal && $k->tanggal_mulai <= $str_akhir) {
                $per_tanggal[$k->tanggal_mulai][] = array('nama' => $k->nama, 'institusi' => $k->institusi, 'jenis' => 'mulai');
            }
            if ($k->tanggal_selesai >= $str_awal && $k->tanggal_selesai <= $str_akhir
                && in_array($k->status, array('selesai', 'diakhiri_awal', 'akan_berakhir'))) {
                $per_tanggal[$k->tanggal_selesai][] = array('nama' => $k->nama, 'institusi' => $k->institusi, 'jenis' => 'selesai');
            }
        }

        $data['tahun'] = $tahun;
        $data['bulan'] = $bulan;
        $data['awal_bulan'] = $awal_bulan;
        $data['akhir_bulan'] = $akhir_bulan;
        $data['per_tanggal'] = $per_tanggal;
        $data['daftar_kontrak'] = $kontrak_tampil;
        $data['id_departemen_filter'] = $id_departemen_filter;
        $data['hari_ini'] = $hari_ini;

        $this->load->view('analisis/kalender', $data);
    }

    // GET /analisis/jalankan_pengingat — generate notifikasi H-7/H-3 kontrak.
    // Dipanggil berkala lewat Windows Task Scheduler/cron (curl ke URL ini),
    // bukan proses background bawaan app. Aman dipanggil berkali-kali sehari
    // karena dedupe_key per tanggal mencegah notifikasi dobel.
    function jalankan_pengingat() {
        $jumlah = $this->model_prk_notifikasi->generatePengingatKontrak();
        echo json_encode(array('ok' => true, 'notifikasi_dibuat' => $jumlah));
    }

    // POST /analisis/notifikasi_baca — tandai satu notifikasi sudah dibaca user ini.
    function notifikasi_baca() {
        $id_notifikasi = (int) $this->input->post('id_notifikasi');
        $auth_user = $this->session->userdata('SES_ID');
        if ($id_notifikasi) {
            $this->model_prk_notifikasi->tandaiDibaca($id_notifikasi, $auth_user);
        }
        echo json_encode(array('ok' => true));
    }

    // GET /analisis/kuota
    function kuota() {
        $data['divisi'] = $this->model_prk_master->listDivisiDenganDepartemen();
        $terisi_per_divisi = $this->model_prk_master->listTerisiPerDivisi();
        foreach ($data['divisi'] as $d) {
            $kuota = $this->model_prk_master->getKuota($d->id);
            $d->kuota_maks = $kuota ? $kuota->kuota_maks : 0;
            $d->terisi = isset($terisi_per_divisi[(int) $d->id]) ? $terisi_per_divisi[(int) $d->id] : 0;
        }
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $this->load->view('analisis/kuota', $data);
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
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');

        $this->load->view('analisis/pengaturan', $data);
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

        $lainnya = array(
            'ambang_conf_tinggi' => (int) $this->input->post('ambang_conf_tinggi'),
            'ambang_conf_sedang' => (int) $this->input->post('ambang_conf_sedang'),
            'ambang_mepet_hari' => (int) $this->input->post('ambang_mepet_hari'),
            'h7_hari' => (int) $this->input->post('h7_hari'),
            'h3_hari' => (int) $this->input->post('h3_hari'),
        );
        if ($lainnya['ambang_conf_sedang'] >= $lainnya['ambang_conf_tinggi']) {
            $this->session->set_flashdata('error', 'Ambang keyakinan sedang harus lebih kecil dari ambang tinggi.');
            redirect(base_url() . 'analisis/pengaturan');
            return;
        }

        $semua = array_merge($bobot, $lainnya);
        $auth_user = $this->session->userdata('SES_ID');
        foreach ($semua as $k => $v) {
            $this->model_prk_master->setPengaturan($k, $v, $auth_user);
        }
        $this->model_prk_audit->catat('hr:' . $auth_user, 'pengaturan', 0, 'ubah_pengaturan', NULL, $semua);

        $this->session->set_flashdata('sukses', 'Pengaturan tersimpan.');
        redirect(base_url() . 'analisis/pengaturan');
    }
}
