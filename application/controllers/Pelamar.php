<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Controller sisi pelamar (SPESIFIKASI_PELAMAR.md). Hanya SES_PRIV 3/4; satu-satunya
// jalur TULIS ke pkl_data untuk pelamar -- Transaksi.php lama (C2: user_id dari POST,
// tanpa cek kepemilikan) tidak lagi dipakai pelamar (diblok lewat redirect di
// konstruktornya, lihat Transaksi::data()). Kepemilikan ditegakkan di setiap query lewat
// Model_pelamar (user_id = SES_LOGIN selalu dari sesi, tidak pernah dari POST -- C2).
class Pelamar extends CI_Controller {

    const BERKAS_SLOT = array('doc_1', 'doc_2', 'doc_3', 'doc_5', 'doc_6', 'doc_7', 'foto');
    const WHITELIST_UBAH = array('kegiatan', 'nama', 'alamat', 'jk', 'tmpt_lahir', 'tgl_lahir',
        'nm_ortu', 'hp_ortu', 'telp', 'email', 'pendidikan', 'jurusan', 'institusi',
        'almt_institusi', 'id_depag', 'pembimbing', 'hp_pembimbing', 'tgl_mulai', 'tgl_selesai');

    protected $user_id;

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->config->load('analisis');
        $this->load->model('model_pelamar');
        $this->load->model('model_prk_master');
        $this->load->model('model_prk_notifikasi');
        $this->load->model('model_prk_surat');
        $this->load->library('analisis_status');
        $this->load->helper('analisis_helper');

        $auth = $this->session->userdata('SES_PRIV');
        if (!$auth) {
            redirect(base_url() . 'login/sistem/login_sistem');
        }
        if ($auth != 3 && $auth != 4) {
            redirect(base_url() . 'analisis/dashboard');
        }
        $this->user_id = $this->session->userdata('SES_LOGIN');
    }

    function index() {
        redirect(base_url() . 'pelamar/beranda');
    }

    // P1 - sapaan, kartu status pengajuan terbaru, langkah berikutnya.
    function beranda() {
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $data['daftar'] = $this->model_pelamar->listPengajuan($this->user_id);
        $data['terbaru'] = !empty($data['daftar']) ? $data['daftar'][0] : null;
        $data['surat'] = $data['terbaru'] ? $this->model_prk_surat->getSuratTerkirimUntukPelamar($data['terbaru']->id) : null;
        $this->load->view('pelamar/beranda', $data);
    }

    // P3 - daftar semua pengajuan milik pelamar ini.
    function pengajuan($id = null) {
        if ($id !== null) {
            $this->detail($id);
            return;
        }
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $data['daftar'] = $this->model_pelamar->listPengajuan($this->user_id);
        $this->load->view('pelamar/pengajuan', $data);
    }

    // P4 - detail satu pengajuan: timeline, data ringkas, checklist berkas, aksi.
    private function detail($id) {
        $p = $this->model_pelamar->getPengajuanMilik($id, $this->user_id);
        if (!$p) {
            show_404();
        }
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $data['p'] = $p;
        $data['surat'] = strtolower($p->status) === 'accept' ? $this->model_prk_surat->getSuratTerkirimUntukPelamar($id) : null;
        $this->load->view('pelamar/detail', $data);
    }

    // POST /pelamar/pengajuan/{id}/ubah - whitelist kolom (B6), hanya saat Pending.
    function ubah($id = null) {
        $p = $this->model_pelamar->getPengajuanMilik($id, $this->user_id);
        if (!$p) {
            show_404();
        }
        if (strtolower($p->status) !== 'pending') {
            $this->session->set_flashdata('error', 'Data hanya bisa diubah selagi status masih menunggu keputusan.');
            redirect(base_url() . 'pelamar/pengajuan/' . $id);
            return;
        }

        $data = array();
        foreach (self::WHITELIST_UBAH as $kolom) {
            if ($this->input->post($kolom) !== null) {
                $data[$kolom] = trim((string) $this->input->post($kolom));
            }
        }
        $error = $this->_validasiData($data, $p);
        if ($error) {
            $this->session->set_flashdata('error', $error);
            redirect(base_url() . 'pelamar/pengajuan/' . $id);
            return;
        }

        $this->model_pelamar->ubah($id, $this->user_id, $data);
        $this->session->set_flashdata('sukses', 'Data pengajuan tersimpan.');
        redirect(base_url() . 'pelamar/pengajuan/' . $id);
    }

    // POST /pelamar/pengajuan/{id}/berkas/{slot} - ganti satu dokumen. Pending untuk
    // doc_1,2,3,5,6,7,foto; Accept khusus doc_7 kalau belum ada (unggah surat pernyataan).
    function berkas($id = null, $slot = null) {
        $p = $this->model_pelamar->getPengajuanMilik($id, $this->user_id);
        if (!$p || !in_array($slot, self::BERKAS_SLOT, true)) {
            show_404();
        }

        $status = strtolower($p->status);
        if ($status === 'pending') {
            // semua slot boleh
        } else if ($status === 'accept' && $slot === 'doc_7' && empty($p->doc_7)) {
            // unggah surat pernyataan pertama kali
        } else {
            $this->session->set_flashdata('error', 'Berkas ini tidak bisa diganti pada status pengajuan saat ini.');
            redirect(base_url() . 'pelamar/pengajuan/' . $id);
            return;
        }

        if (empty($_FILES['berkas']['name'])) {
            $this->session->set_flashdata('error', 'Pilih berkas terlebih dahulu.');
            redirect(base_url() . 'pelamar/pengajuan/' . $id);
            return;
        }

        $error = $this->_simpanBerkas($id, $slot, $p);
        if ($error) {
            $this->session->set_flashdata('error', $error);
            redirect(base_url() . 'pelamar/pengajuan/' . $id);
            return;
        }

        if ($slot === 'doc_2' || $slot === 'doc_3') {
            $this->_antrekanAnalisis($id);
        }
        if ($slot === 'foto') {
            $this->_periksaFoto($id);
        }

        $this->session->set_flashdata('sukses', 'Berkas berhasil diunggah.');
        redirect(base_url() . 'pelamar/pengajuan/' . $id);
    }

    // POST /pelamar/pengajuan/{id}/batal - D3: status baru 'Cancelled', bukan hapus baris.
    function batal($id = null) {
        $p = $this->model_pelamar->getPengajuanMilik($id, $this->user_id);
        if (!$p) {
            show_404();
        }
        if (!$this->analisis_status->ubah($id, Analisis_status::CANCELLED, 'pelamar:' . $this->user_id)) {
            $this->session->set_flashdata('error', 'Pengajuan ini tidak bisa dibatalkan.');
            redirect(base_url() . 'pelamar/pengajuan/' . $id);
            return;
        }
        $this->session->set_flashdata('sukses', 'Pengajuan dibatalkan.');
        redirect(base_url() . 'pelamar/pengajuan');
    }

    // P2 - wizard pendaftaran baru.
    function daftar() {
        if ($this->input->post('kirim')) {
            $this->_simpanDaftar();
            return;
        }
        if ($this->model_pelamar->adaPengajuanAktif($this->user_id)) {
            $this->session->set_flashdata('error', 'Kamu masih punya pengajuan yang menunggu keputusan atau sedang magang. Selesaikan atau batalkan dulu sebelum mengajukan lagi.');
            redirect(base_url() . 'pelamar/pengajuan');
            return;
        }
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $data['depag'] = $this->model_prk_master->listSisaTempatDepag();
        $data['ambang_mepet_hari'] = (int) $this->model_prk_master->getPengaturan('ambang_mepet_hari', 3);
        $this->load->view('pelamar/daftar', $data);
    }

    private function _simpanDaftar() {
        if ($this->model_pelamar->adaPengajuanAktif($this->user_id)) {
            $this->session->set_flashdata('error', 'Kamu masih punya pengajuan aktif.');
            redirect(base_url() . 'pelamar/daftar');
            return;
        }

        $data = array('user_id' => $this->user_id, 'status' => 'Pending');
        foreach (array_merge(self::WHITELIST_UBAH, array('nm_ortu', 'hp_ortu')) as $kolom) {
            $data[$kolom] = trim((string) $this->input->post($kolom));
        }
        $data['idkey'] = $this->user_id . $data['nama'] . $data['telp'];

        $error = $this->_validasiData($data, null);
        if (!$error && $this->model_prk_master->depagPenuh($data['id_depag'])) {
            $error = 'Departemen tujuan sudah penuh. Pilih departemen lain.';
        }
        if (!$error && $this->model_pelamar->idkeyDipakai($data['idkey'])) {
            $error = 'Pengajuan dengan data ini sudah pernah dikirim dan masih diproses.';
        }
        foreach (array('file_1' => 'doc_1', 'file_2' => 'doc_2', 'file_3' => 'doc_3',
                     'file_5' => 'doc_5', 'file_6' => 'doc_6', 'file_7' => 'doc_7', 'foto' => 'foto') as $input_name => $kolom) {
            if (!$error && empty($_FILES[$input_name]['name'])) {
                $error = 'Semua berkas wajib diunggah.';
            }
        }
        if ($error) {
            $this->session->set_flashdata('error', $error);
            redirect(base_url() . 'pelamar/daftar');
            return;
        }

        $id = $this->model_pelamar->buat($data);

        $gagal_berkas = array();
        foreach (array('file_1' => 'doc_1', 'file_2' => 'doc_2', 'file_3' => 'doc_3',
                     'file_5' => 'doc_5', 'file_6' => 'doc_6', 'file_7' => 'doc_7', 'foto' => 'foto') as $input_name => $kolom) {
            $hasil = $this->_unggahBerkas($input_name, $kolom);
            if (isset($hasil['error'])) {
                $gagal_berkas[] = $kolom;
                continue;
            }
            $this->model_pelamar->ubah($id, $this->user_id, array($kolom => $hasil['nama_file']));
        }

        $this->_antrekanAnalisis($id);
        if (!in_array('foto', $gagal_berkas, true)) {
            $this->_periksaFoto($id);
        }

        $this->model_prk_notifikasi->buat(
            'pengajuan_terkirim', $id, 'Pengajuan diterima sistem',
            'Berkas kamu sudah masuk dan menunggu tinjauan HR.',
            base_url() . 'pelamar/pengajuan/' . $id,
            'pelamar_kirim:' . $id,
            null, 'pelamar', $this->user_id
        );

        $this->session->set_flashdata('sukses', 'Pengajuan terkirim. HR akan meninjau berkas kamu.');
        redirect(base_url() . 'pelamar/pengajuan/' . $id);
    }

    // P5 - syarat umum + syarat khusus departemen.
    function panduan() {
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $data['depag'] = $this->model_prk_master->listSisaTempatDepag();
        $this->load->view('pelamar/panduan', $data);
    }

    // P6 - profil & akun.
    function akun() {
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $data['user'] = $this->db->where('user_id', $this->user_id)->get('user')->row();
        $this->load->view('pelamar/akun', $data);
    }

    function akun_ganti_password() {
        $lama = (string) $this->input->post('password_lama');
        $baru = (string) $this->input->post('password_baru');
        $user = $this->db->where('user_id', $this->user_id)->get('user')->row();
        if (!$user || $user->password !== $lama) {
            $this->session->set_flashdata('error', 'Password lama salah.');
            redirect(base_url() . 'pelamar/akun');
            return;
        }
        if (strlen($baru) < 8) {
            $this->session->set_flashdata('error', 'Password baru minimal 8 karakter.');
            redirect(base_url() . 'pelamar/akun');
            return;
        }
        $this->db->where('id', $user->id)->update('user', array('password' => $baru));
        $this->session->set_flashdata('sukses', 'Password berhasil diganti.');
        redirect(base_url() . 'pelamar/akun');
    }

    // GET /pelamar/berkas/{id}/{slot} - B8: hanya pemilik (atau HR/Admin) yang boleh
    // membaca, nama file selalu dari DB (tidak pernah dari URL), tidak pernah tautan
    // langsung ke gambar/.
    function berkas_lihat($id = null, $slot = null) {
        if (!in_array($slot, self::BERKAS_SLOT, true)) {
            show_404();
        }
        $auth = $this->session->userdata('SES_PRIV');
        if ($auth == 1 || $auth == 2) {
            $p = $this->db->where('id', $id)->get('pkl_data')->row();
        } else {
            $p = $this->model_pelamar->getPengajuanMilik($id, $this->user_id);
        }
        if (!$p || empty($p->{$slot})) {
            show_404();
        }

        $path_absolut = FCPATH . 'gambar/' . $p->{$slot};
        if (!is_file($path_absolut)) {
            show_404();
        }

        $ekstensi = strtolower(pathinfo($path_absolut, PATHINFO_EXTENSION));
        $content_type = $ekstensi === 'pdf' ? 'application/pdf' : 'image/jpeg';
        header('Content-Type: ' . $content_type);
        header('Content-Disposition: inline; filename="' . $slot . '-' . $id . '.' . $ekstensi . '"');
        header('Content-Length: ' . filesize($path_absolut));
        readfile($path_absolut);
    }

    // GET /pelamar/surat/{id}/unduh - C7: pelamar baru punya jalur resmi mengunduh
    // surat balasan (sebelumnya hanya peran 1-2 lewat Analisis::surat_unduh).
    function surat_unduh($id = null) {
        $p = $this->model_pelamar->getPengajuanMilik($id, $this->user_id);
        if (!$p) {
            show_404();
        }
        $surat = $this->model_prk_surat->getSuratTerkirimUntukPelamar($id);
        if (!$surat || empty($surat->file_path)) {
            show_404();
        }

        $this->load->library('analisis_surat');
        $path_absolut = $this->analisis_surat->pathAbsolut($surat->file_path);
        if (!is_file($path_absolut)) {
            show_404();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="SuratBalasan-' . preg_replace('/[^A-Za-z0-9]+/', '', $p->nama) . '.pdf"');
        header('Content-Length: ' . filesize($path_absolut));
        readfile($path_absolut);
    }

    function notifikasi_baca() {
        $id_notifikasi = (int) $this->input->post('id_notifikasi');
        $this->model_prk_notifikasi->tandaiDibaca($id_notifikasi, $this->session->userdata('SES_ID'));
        echo json_encode(array('ok' => true));
    }

    // Validasi server (B4): tanggal, durasi, telepon, email. $p = baris lama (saat ubah)
    // untuk melengkapi field yang tidak dikirim whitelist parsial, null saat daftar baru.
    private function _validasiData($data, $p) {
        $ambil = function ($kolom) use ($data, $p) {
            if (array_key_exists($kolom, $data)) {
                return $data[$kolom];
            }
            return $p ? $p->{$kolom} : '';
        };

        foreach (self::WHITELIST_UBAH as $kolom) {
            if ($ambil($kolom) === '') {
                return 'Semua field wajib diisi.';
            }
        }

        $tgl_mulai = $ambil('tgl_mulai');
        $tgl_selesai = $ambil('tgl_selesai');
        if ($tgl_selesai <= $tgl_mulai) {
            return 'Tanggal selesai harus setelah tanggal mulai.';
        }
        $ambang = (int) $this->model_prk_master->getPengaturan('ambang_mepet_hari', 3);
        $paling_awal = date('Y-m-d', strtotime('+' . ($ambang + 1) . ' days'));
        if ($tgl_mulai < $paling_awal) {
            return 'Tanggal mulai paling cepat ' . tanggal_indo($paling_awal) . '.';
        }
        $durasi_hari = (strtotime($tgl_selesai) - strtotime($tgl_mulai)) / 86400;
        if ($durasi_hari < 30 || $durasi_hari > 180) {
            return 'Durasi magang harus antara 30 dan 180 hari.';
        }

        $telp = $ambil('telp');
        if (!preg_match('/^(0|62)[0-9]{8,14}$/', $telp)) {
            return 'Nomor telepon tidak valid (hanya digit, diawali 0 atau 62).';
        }
        $email = $ambil('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Format email tidak valid.';
        }

        return null;
    }

    private function _simpanBerkas($id, $slot, $p) {
        $hasil = $this->_unggahBerkas('berkas', $slot);
        if (isset($hasil['error'])) {
            return $hasil['error'];
        }
        $lama = $p->{$slot};
        $this->model_pelamar->simpanBerkas($id, $this->user_id, $slot, $hasil['nama_file']);
        if (!empty($lama)) {
            @unlink(FCPATH . 'gambar/' . $lama);
        }
        return null;
    }

    // Validasi + simpan satu berkas (B4): PDF utk doc_*, JPG utk foto, cek MIME+ekstensi,
    // batas ukuran SEBELUM file dipindah (C5: kode lama menyimpan baris DB walau upload
    // gagal/ditolak -- di sini validasi terjadi lebih dulu, baris DB baru ditulis setelahnya).
    private function _unggahBerkas($input_name, $slot) {
        if (empty($_FILES[$input_name]['name'])) {
            return array('error' => null);
        }
        $file = $_FILES[$input_name];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return array('error' => 'Gagal mengunggah berkas.');
        }

        $is_foto = $slot === 'foto';
        $maks_byte = $is_foto ? 2 * 1024 * 1024 : 5 * 1024 * 1024;
        if ($file['size'] > $maks_byte) {
            return array('error' => 'Ukuran berkas melebihi batas (' . ($is_foto ? '2MB' : '5MB') . ').');
        }

        $ekstensi = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mime = function_exists('mime_content_type') ? mime_content_type($file['tmp_name']) : $file['type'];
        if ($is_foto) {
            if (!in_array($ekstensi, array('jpg', 'jpeg'), true) || $mime !== 'image/jpeg') {
                return array('error' => 'Foto harus berformat JPG.');
            }
        } else {
            if ($ekstensi !== 'pdf' || $mime !== 'application/pdf') {
                return array('error' => 'Berkas harus berformat PDF.');
            }
        }

        $nama_file = date('dmYHis') . '_' . bin2hex(random_bytes(4)) . '.' . $ekstensi;
        if (!move_uploaded_file($file['tmp_name'], FCPATH . 'gambar/' . $nama_file)) {
            return array('error' => 'Gagal menyimpan berkas.');
        }
        return array('nama_file' => $nama_file);
    }

    private function _antrekanAnalisis($id_transaksi) {
        try {
            if (!$this->config->item('analisis_aktif')) {
                return;
            }
            $this->load->library('analisis_queue');
            $this->analisis_queue->enqueue($id_transaksi);
        } catch (Exception $e) {
            log_message('error', 'Pelamar::_antrekanAnalisis gagal untuk id=' . $id_transaksi . ': ' . $e->getMessage());
        }
    }

    private function _periksaFoto($id_transaksi) {
        try {
            if (!$this->config->item('analisis_aktif')) {
                return;
            }
            $p = $this->db->where('id', $id_transaksi)->get('pkl_data')->row();
            if (!$p || empty($p->foto)) {
                return;
            }
            $this->load->library('analisis_foto');
            $this->analisis_foto->periksaDanSimpan($id_transaksi, $p->foto);
        } catch (Exception $e) {
            log_message('error', 'Pelamar::_periksaFoto gagal untuk id=' . $id_transaksi . ': ' . $e->getMessage());
        }
    }
}
