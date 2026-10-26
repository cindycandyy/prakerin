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
    // Link sosmed (opsional, bagian "Media sosial" di Halaman B HR -- detail_pelamar.php):
    // domain wajib cocok nama platform supaya link yang disalin memang punya akun itu,
    // bukan link asal.
    const SOSMED_DOMAIN = array('sosmed_linkedin' => 'linkedin.com', 'sosmed_instagram' => 'instagram.com',
        'sosmed_tiktok' => 'tiktok.com', 'sosmed_twitter' => array('twitter.com', 'x.com'));

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
    // Data pengajuan terkunci sejak dikirim (tidak ada status yang mengizinkan ubah
    // lagi) -- pelamar yang salah isi wajib batal() lalu ajukan ulang. Endpoint ini
    // sengaja ditutup total, bukan dihapus, supaya POST langsung ke URL lama tetap
    // ditolak dengan pesan jelas alih-alih 404.
    function ubah($id = null) {
        $p = $this->model_pelamar->getPengajuanMilik($id, $this->user_id);
        if (!$p) {
            show_404();
        }
        $this->session->set_flashdata('error', 'Data pengajuan terkunci sejak dikirim dan tidak dapat diubah. Batalkan pengajuan ini untuk mengajukan kembali.');
        redirect(base_url() . 'pelamar/pengajuan/' . $id);
    }

    // POST /pelamar/pengajuan/{id}/berkas/{slot} - berkas terkunci sejak dikirim,
    // sama seperti data (lihat ubah()). Satu-satunya pengecualian: doc_7 (surat
    // pernyataan) boleh DILENGKAPI setelah Accept kalau belum pernah diunggah --
    // itu bukan "mengganti" berkas yang sudah dikirim, melainkan melengkapi dokumen
    // yang memang belum diminta saat Pending.
    function berkas($id = null, $slot = null) {
        $p = $this->model_pelamar->getPengajuanMilik($id, $this->user_id);
        if (!$p || !in_array($slot, self::BERKAS_SLOT, true)) {
            show_404();
        }

        $status = strtolower($p->status);
        if ($status === 'accept' && $slot === 'doc_7' && empty($p->doc_7)) {
            // unggah surat pernyataan pertama kali
        } else {
            $this->session->set_flashdata('error', 'Berkas ini tidak dapat diganti pada status pengajuan saat ini.');
            redirect(base_url() . 'pelamar/pengajuan/' . $id);
            return;
        }

        if (empty($_FILES['berkas']['name'])) {
            $this->session->set_flashdata('error', 'Silakan pilih berkas terlebih dahulu.');
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
            $this->session->set_flashdata('error', 'Pengajuan ini tidak dapat dibatalkan.');
            redirect(base_url() . 'pelamar/pengajuan/' . $id);
            return;
        }
        $this->session->set_flashdata('sukses', 'Pengajuan berhasil dibatalkan.');
        redirect(base_url() . 'pelamar/pengajuan');
    }

    // P2 - wizard pendaftaran baru.
    function daftar() {
        if ($this->input->post('kirim')) {
            $this->_simpanDaftar();
            return;
        }
        $riwayat = $this->model_pelamar->listPengajuan($this->user_id);
        $aktif = null;
        foreach ($riwayat as $r) {
            if (in_array(strtolower($r->status), array('pending', 'accept'), true)) {
                $aktif = $r;
                break;
            }
        }

        // Pengajuan aktif: wizard tetap dimuat (bukan redirect diam-diam ke halaman
        // lain) supaya pelamar mendapat pemberitahuan jelas lewat modal, dengan jalan
        // keluar langsung ke pengajuan yang sedang berjalan itu.
        $data['pengajuan_aktif'] = $aktif;
        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $data['depag'] = $this->model_prk_master->listSisaTempatDepag();
        $data['ambang_mepet_hari'] = (int) $this->model_prk_master->getPengaturan('ambang_mepet_hari', 3);
        // B5: pengajuan baru menyalin biodata dari pengajuan terakhir (Rejected/Inactive/
        // Cancelled) supaya pelamar tidak mengetik ulang semua -- bukan jadwal/berkas,
        // itu tetap harus diisi baru.
        $data['salinan'] = !$aktif && !empty($riwayat) ? $riwayat[0] : null;
        $this->load->view('pelamar/daftar', $data);
    }

    private function _simpanDaftar() {
        // CI3 flashdata bisa "nyangkut" ke request setelahnya kalau dibaca di halaman
        // tampilan lalu langsung di-submit ulang (bukan GET baru) -- bersihkan dulu
        // supaya error dari percobaan submit sebelumnya tidak ikut tampil bareng
        // pesan sukses percobaan yang berhasil.
        $this->session->unset_userdata('error');

        if ($this->model_pelamar->adaPengajuanAktif($this->user_id)) {
            $this->session->set_flashdata('error', 'Terdapat pengajuan aktif yang masih berlangsung.');
            redirect(base_url() . 'pelamar/daftar');
            return;
        }

        $data = array('user_id' => $this->user_id, 'status' => 'Pending');
        foreach (array_merge(self::WHITELIST_UBAH, array('nm_ortu', 'hp_ortu')) as $kolom) {
            $data[$kolom] = trim((string) $this->input->post($kolom));
        }
        foreach (self::SOSMED_DOMAIN as $kolom => $domain) {
            $url = trim((string) $this->input->post($kolom));
            $data[$kolom] = ($url !== '' && !preg_match('#^https?://#i', $url)) ? 'https://' . $url : ($url ?: null);
        }
        $data['idkey'] = $this->user_id . $data['nama'] . $data['telp'];

        $error = $this->_validasiData($data, null);
        if (!$error) {
            $error = $this->_validasiSosmed($data);
        }
        if (!$error && $this->model_prk_master->depagPenuh($data['id_depag'])) {
            $error = 'Departemen tujuan sudah penuh. Pilih departemen lain.';
        }
        if (!$error && $this->model_pelamar->idkeyDipakai($data['idkey'])) {
            $error = 'Pengajuan dengan data ini sudah pernah dikirim dan masih diproses.';
        }
        $peta_berkas = array('file_1' => 'doc_1', 'file_2' => 'doc_2', 'file_3' => 'doc_3',
            'file_5' => 'doc_5', 'file_6' => 'doc_6', 'file_7' => 'doc_7', 'foto' => 'foto');
        foreach ($peta_berkas as $input_name => $kolom) {
            if (!$error && empty($_FILES[$input_name]['name'])) {
                $error = 'Semua berkas wajib diunggah.';
            }
        }
        // D7: persetujuan analisis otomatis dokumen wajib dicentang, dicek ulang di
        // server karena checkbox hanya ditegakkan di klien.
        if (!$error && !$this->input->post('persetujuan_privasi')) {
            $error = 'Persetujuan analisis otomatis dokumen wajib dicentang sebelum pengajuan dikirim.';
        }
        if ($error) {
            $this->session->set_flashdata('error', $error);
            redirect(base_url() . 'pelamar/daftar');
            return;
        }

        // Validasi SEMUA berkas (format/ukuran) sebelum baris DB dibuat -- kalau satu
        // saja gagal, batalkan seluruh pengajuan dengan pesan jelas. Sebelumnya baris
        // DB dibuat duluan lalu berkas diunggah belakangan tanpa pelamar diberi tahu
        // kalau ada yang gagal, jadi pengajuan "sukses" padahal foto/dokumen hilang.
        $hasil_berkas = array();
        foreach ($peta_berkas as $input_name => $kolom) {
            $hasil = $this->_unggahBerkas($input_name, $kolom);
            if (isset($hasil['error'])) {
                $error = $hasil['error'];
                break;
            }
            $hasil_berkas[$kolom] = $hasil['nama_file'];
        }
        if ($error) {
            foreach ($hasil_berkas as $nama_file) {
                @unlink(FCPATH . 'gambar/' . $nama_file);
            }
            $this->session->set_flashdata('error', $error);
            redirect(base_url() . 'pelamar/daftar');
            return;
        }

        $data = array_merge($data, $hasil_berkas);
        $id = $this->model_pelamar->buat($data);

        $this->_antrekanAnalisis($id);
        $this->_periksaFoto($id);

        $this->model_prk_notifikasi->buat(
            'pengajuan_terkirim', $id, 'Pengajuan Diterima Sistem',
            'Berkas telah diterima dan sedang menunggu tinjauan HR.',
            base_url() . 'pelamar/pengajuan/' . $id,
            'pelamar_kirim:' . $id,
            null, 'pelamar', $this->user_id
        );

        $this->session->set_flashdata('sukses', 'Pengajuan berhasil dikirim. HR akan segera meninjau berkas yang telah diunggah.');
        redirect(base_url() . 'pelamar/pengajuan/' . $id);
    }

    // GET /pelamar/kartu/{id} - kartu magang (B5). Accept ATAU Inactive (bagian B3:
    // "Inactive - kartu & surat tetap bisa diunduh"), dengan no_pkl terisi. Reuse
    // persis tata letak PHPExcel dari Transaksi::cetak (D4: tetap Excel, bukan PDF
    // baru), tapi ambil data lewat getPengajuanMilik() (ownership-checked) -- bukan
    // trik session SES_01..SES_04 yang dipakai endpoint HR lama.
    function kartu($id = null) {
        $p = $this->model_pelamar->getPengajuanMilik($id, $this->user_id);
        if (!$p || !in_array(strtolower($p->status), array('accept', 'inactive'), true) || empty($p->no_pkl)) {
            show_404();
        }

        $this->load->model('model_all_function');

        $nama = $p->nama;
        $badge = $p->no_pkl;
        $institusi = $p->institusi;
        $waktu = $this->model_all_function->sql2date($p->tgl_mulai) . ' s/d ' . $this->model_all_function->sql2date($p->tgl_selesai);

        include APPPATH . 'third_party/Classes/PHPExcel.php';
        $excel = new PHPExcel();
        $excel->getProperties()->setCreator('Admin SDM')
            ->setLastModifiedBy('Admin SDM')
            ->setTitle('Data Siswa Magang')
            ->setSubject('PKL')
            ->setDescription('Laporan Semua Data Magang')
            ->setKeywords('Data PKL');

        $default_border = array('style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('rgb' => '000000'));
        $style_border = array('borders' => array(
            'bottom' => $default_border, 'left' => $default_border, 'top' => $default_border, 'right' => $default_border,
        ));
        $excel->getSheet(0)->getStyle('A2:L19')->applyFromArray($style_border);
        $excel->getSheet(0)->getStyle('B12:B18')->applyFromArray($style_border);
        $excel->getSheet(0)->getStyle('D12:H15')->applyFromArray($style_border);
        $excel->getSheet(0)->getStyle('J12:K15')->applyFromArray($style_border);

        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(1);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(11);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(1);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(2);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(2);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(9);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(2);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(2);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(1);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(14);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(4);
        $excel->getActiveSheet()->getColumnDimension('L')->setWidth(1);
        $excel->getActiveSheet()->getColumnDimension('M')->setWidth(1);

        $excel->getActiveSheet()->getRowDimension(1)->setRowHeight(5);
        $excel->getActiveSheet()->getRowDimension(2)->setRowHeight(5);
        $excel->getActiveSheet()->getRowDimension(3)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(4)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(5)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(6)->setRowHeight(5);
        $excel->getActiveSheet()->getRowDimension(7)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(8)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(9)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(10)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(11)->setRowHeight(5);
        $excel->getActiveSheet()->getRowDimension(12)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(13)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(14)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(15)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(16)->setRowHeight(5);
        $excel->getActiveSheet()->getRowDimension(17)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(18)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(19)->setRowHeight(10);
        $excel->getActiveSheet()->getRowDimension(20)->setRowHeight(10);

        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Data SDM Dan Umum');
        $objDrawing->setDescription('Image logo');
        $objDrawing->setPath('gambar\Logo Petrosidax.png');
        $objDrawing->setHeight(39);
        $objDrawing->setCoordinates('B3');
        $objDrawing->setWorksheet($excel->getActiveSheet());

        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName('Data SDM Dan Umum');
        $objDrawing->setDescription('Image footer');
        $objDrawing->setPath('gambar\Footer.png');
        $objDrawing->setHeight(31);
        $objDrawing->setCoordinates('D17');
        $objDrawing->setWorksheet($excel->getActiveSheet());

        $excel->getActiveSheet()->setCellValue('E4', 'KARTU TANDA PENGENAL');
        $excel->getActiveSheet()->getStyle('E4')->getFont()->setSize(10);
        $excel->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
        $excel->getSheet(0)->getStyle('E4')->getFont()->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);

        $excel->getActiveSheet()->setCellValue('B7', 'Nama');
        $excel->getActiveSheet()->setCellValue('B8', 'No. Badge');
        $excel->getActiveSheet()->setCellValue('B9', 'Sekolah/PT');
        $excel->getActiveSheet()->setCellValue('B10', 'Masa Berlaku');
        $excel->getActiveSheet()->setCellValue('D7', ':');
        $excel->getActiveSheet()->setCellValue('D8', ':');
        $excel->getActiveSheet()->setCellValue('D9', ':');
        $excel->getActiveSheet()->setCellValue('D10', ':');
        $excel->getActiveSheet()->setCellValue('E7', $nama);
        $excel->getActiveSheet()->setCellValue('E8', $badge);
        $excel->getActiveSheet()->setCellValue('E9', $institusi);
        $excel->getActiveSheet()->setCellValue('E10', $waktu);

        foreach (array('B7', 'B8', 'B9', 'B10', 'D7', 'D8', 'D9', 'D10', 'E7', 'E8', 'E9', 'E10') as $sel) {
            $excel->getActiveSheet()->getStyle($sel)->getFont()->setSize(8);
        }

        $excel->getActiveSheet()->mergeCells('B12:B18');
        $excel->getActiveSheet()->getStyle('B12:B18')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $excel->getActiveSheet()->getStyle('B12:B18')->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
        $excel->getActiveSheet()->setCellValue('B12', 'Foto');

        $excel->getActiveSheet()->mergeCells('D12:H12');
        $excel->getActiveSheet()->getStyle('D12:H12')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $excel->getActiveSheet()->setCellValue('D12', 'Peserta Magang');
        $excel->getActiveSheet()->getStyle('D12')->getFont()->setSize(9);

        $excel->getActiveSheet()->mergeCells('J12:K12');
        $excel->getActiveSheet()->getStyle('J12:K12')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $excel->getActiveSheet()->setCellValue('J12', 'Manager SDM & Umum');
        $excel->getActiveSheet()->getStyle('J12')->getFont()->setSize(9);

        $excel->getActiveSheet()->mergeCells('D15:H15');
        $excel->getActiveSheet()->getStyle('D15:H15')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $excel->getActiveSheet()->getStyle('D15:H15')->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
        $excel->getActiveSheet()->setCellValue('D15', $nama);
        $excel->getActiveSheet()->getStyle('D15')->getFont()->setSize(9);

        $excel->getActiveSheet()->mergeCells('J15:K15');
        $excel->getActiveSheet()->getStyle('J15:K15')->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER));
        $excel->getActiveSheet()->getStyle('J15:K15')->getAlignment()->applyFromArray(array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER));
        $excel->getActiveSheet()->setCellValue('J15', $this->model_prk_master->getPengaturan('surat_penandatangan_nama', '-'));
        $excel->getActiveSheet()->getStyle('J15')->getFont()->setSize(9);

        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
        $excel->getActiveSheet(0)->setTitle('Kartu Siswa Magang');
        $excel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Kartu Siswa Magang.xlsx"');
        header('Cache-Control: max-age=0');
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
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
        $cocok = $user && (strpos($user->password, '$2y$') === 0
            ? password_verify($lama, $user->password)
            : hash_equals($user->password, $lama));
        if (!$cocok) {
            $this->session->set_flashdata('error', 'Kata sandi lama tidak sesuai.');
            redirect(base_url() . 'pelamar/akun');
            return;
        }
        if (strlen($baru) < 8) {
            $this->session->set_flashdata('error', 'Kata sandi baru harus terdiri dari minimal 8 karakter.');
            redirect(base_url() . 'pelamar/akun');
            return;
        }
        $this->db->where('id', $user->id)->update('user', array('password' => password_hash($baru, PASSWORD_DEFAULT)));
        $this->session->set_flashdata('sukses', 'Kata sandi berhasil diperbarui.');
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

    // GET /pelamar/pengajuan/{id}/lihat/{slot} - halaman pembungkus berkas (bukan file
    // mentah): dibuka di tab yang sama, ada tombol "Kembali" di bar atas, supaya
    // pelamar tidak perlu paham konsep tab baru untuk kembali ke E-Prakerin. File
    // sebenarnya tetap disajikan lewat berkas_lihat() di dalam <iframe>/<img>.
    function berkas_pratinjau($id = null, $slot = null) {
        if (!in_array($slot, self::BERKAS_SLOT, true)) {
            show_404();
        }
        $p = $this->model_pelamar->getPengajuanMilik($id, $this->user_id);
        if (!$p || empty($p->{$slot})) {
            show_404();
        }

        $label = array('doc_1' => 'Surat Pengantar', 'doc_2' => 'Proposal', 'doc_3' => 'Transkrip',
            'doc_5' => 'Kartu Asuransi', 'doc_6' => 'Kartu Pelajar', 'doc_7' => 'Surat Pernyataan', 'foto' => 'Foto');
        $ekstensi = strtolower(pathinfo($p->{$slot}, PATHINFO_EXTENSION));

        $data['nama_user'] = $this->session->userdata('SES_USERNAME');
        $data['id_pengajuan'] = (int) $id;
        $data['nama_label'] = $label[$slot];
        $data['is_gambar'] = $ekstensi !== 'pdf';
        $data['url_file'] = base_url() . 'pelamar/berkas/' . (int) $id . '/' . $slot;
        $this->load->view('pelamar/berkas_pratinjau', $data);
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
                return 'Seluruh kolom wajib diisi.';
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
        if ($durasi_hari < 30) {
            return 'Durasi magang minimal 30 hari.';
        }

        $telp = $ambil('telp');
        if (!preg_match('/^(0|62)[0-9]{8,14}$/', $telp)) {
            return 'Nomor telepon tidak valid. Gunakan hanya angka dan diawali 0 atau 62.';
        }
        $email = $ambil('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Format email tidak valid.';
        }

        return null;
    }

    // Link sosmed (opsional): kalau diisi, harus URL valid dan domainnya cocok
    // platformnya -- supaya "LinkedIn" di Halaman B tidak pernah menampilkan link
    // Instagram, dst.
    private function _validasiSosmed($data) {
        $label = array('sosmed_linkedin' => 'LinkedIn', 'sosmed_instagram' => 'Instagram',
            'sosmed_tiktok' => 'TikTok', 'sosmed_twitter' => 'Twitter/X');
        foreach (self::SOSMED_DOMAIN as $kolom => $domain) {
            $url = $data[$kolom];
            if (empty($url)) {
                continue;
            }
            $host = strtolower((string) parse_url($url, PHP_URL_HOST));
            $domain_list = is_array($domain) ? $domain : array($domain);
            $cocok = false;
            foreach ($domain_list as $d) {
                if ($host === $d || substr($host, -strlen('.' . $d)) === '.' . $d) {
                    $cocok = true;
                    break;
                }
            }
            if (!$cocok) {
                return 'Link ' . $label[$kolom] . ' harus berupa URL ' . reset($domain_list) . '.';
            }
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

        $mime = function_exists('mime_content_type') ? mime_content_type($file['tmp_name']) : $file['type'];
        if ($is_foto) {
            // Terima JPG dan PNG (dua format paling umum dari kamera HP/screenshot) --
            // simpan dengan ekstensi dari MIME yang TERDETEKSI, bukan klaim nama file
            // user, supaya foto HEIC/WebP yang cuma di-rename .jpg tetap ketahuan.
            $peta_mime_foto = array('image/jpeg' => 'jpg', 'image/png' => 'png');
            if (!isset($peta_mime_foto[$mime])) {
                return array('error' => 'Foto harus berformat JPG atau PNG.');
            }
            $ekstensi = $peta_mime_foto[$mime];
        } else {
            $ekstensi_asli = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ekstensi_asli !== 'pdf' || $mime !== 'application/pdf') {
                return array('error' => 'Berkas harus berformat PDF.');
            }
            $ekstensi = 'pdf';
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
