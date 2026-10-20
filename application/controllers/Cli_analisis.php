<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Perintah CLI modul Analisis Penempatan. Hanya bisa dijalankan dari command line
// (php index.php cli_analisis antrean), bukan lewat browser -- lihat __construct().
// Dipanggil cron/Windows Task Scheduler tiap menit (bagian 11.1 spesifikasi teknis):
//   * * * * * php /path/index.php cli_analisis antrean
class Cli_analisis extends CI_Controller {

    function __construct() {
        parent::__construct();
        if (!$this->input->is_cli_request()) {
            show_404();
        }
        $this->config->load('analisis');
        $this->load->model('model_prk_job');
        $this->load->model('model_prk_master');
        $this->load->model('model_prk_notifikasi');
        $this->load->model('model_transaksi');
        $this->load->model('model_prk_kontrak');
        $this->load->model('model_prk_penempatan');
        $this->load->library('analisis_status');
    }

    // php index.php cli_analisis antrean
    // Ambil sampai 5 job 'antre', panggil layanan Python, tulis hasil. Satu job per
    // iterasi -- klaim atomik mencegah dua proses cron yang tumpang tindih mengerjakan
    // job yang sama (spesifikasi teknis bagian 3).
    function antrean() {
        if (!$this->config->item('analisis_aktif')) {
            echo "analisis_aktif = false, tidak ada yang dikerjakan.\n";
            return;
        }

        $pulih = $this->model_prk_job->pulihkanJobMacet(10);
        if ($pulih > 0) {
            echo "{$pulih} job macet dikembalikan ke antre.\n";
        }

        $job_list = $this->model_prk_job->listAntre(5);
        echo count($job_list) . " job diambil dari antrean.\n";

        foreach ($job_list as $job) {
            $this->_prosesJob($job);
        }
    }

    // php index.php cli_analisis harian
    // Dijalankan sekali sehari (disarankan 00:10 WIB, bagian 6.3 spesifikasi teknis):
    // auto-tolak Pending yang mepet/lewat tanggal mulai, perbarui status kontrak
    // turunan, auto-Inactive peserta yang kontraknya selesai, lalu notifikasi H-7/H-3/
    // hari-H. Aman dijalankan ulang -- notifikasi pakai dedupe_key per tanggal.
    function harian() {
        if (!$this->config->item('analisis_aktif')) {
            echo "analisis_aktif = false, tidak ada yang dikerjakan.\n";
            return;
        }

        $jml_tolak = $this->_autoTolakMepetLewat();
        echo "{$jml_tolak} pelamar Pending ditolak otomatis (mepet/lewat tanggal mulai).\n";

        $jml_kontrak = $this->model_prk_kontrak->perbaruiSemuaStatus();
        echo "{$jml_kontrak} status kontrak diperbarui.\n";

        $jml_inactive = $this->_autoInactive();
        echo "{$jml_inactive} peserta diubah ke Inactive (kontrak selesai).\n";

        $jml_notif = $this->model_prk_notifikasi->generatePengingatKontrak();
        echo "{$jml_notif} notifikasi H-7/H-3 dibuat.\n";

        $jml_hari_h = $this->_notifikasiHariH();
        echo "{$jml_hari_h} notifikasi hari-H dibuat.\n";
    }

    // Pending dengan (tanggal_mulai - hari ini) <= ambang_mepet_hari, atau tanggal_mulai
    // sudah lewat -- ditolak otomatis oleh 'sistem' (bagian 6.1 baris "pending -> reject,
    // penjadwal otomatis").
    private function _autoTolakMepetLewat() {
        $ambang = (int) $this->model_prk_master->getPengaturan('ambang_mepet_hari', 3);
        $batas_tanggal = date('Y-m-d', strtotime("+{$ambang} days"));

        $daftar = $this->db
            ->select('id, user_id, nama, tgl_mulai')
            ->from('pkl_data')
            ->where('LOWER(status)', 'pending')
            ->where('tgl_mulai <=', $batas_tanggal)
            ->get()->result();

        $jumlah = 0;
        foreach ($daftar as $p) {
            $lewat = $p->tgl_mulai < date('Y-m-d');
            $alasan = $lewat ? 'Otomatis: tanggal mulai sudah lewat.' : "Otomatis: tanggal mulai tinggal <= {$ambang} hari.";
            $ok = $this->analisis_status->ubah($p->id, Analisis_status::REJECTED, 'sistem', $alasan);
            if ($ok) {
                $this->model_prk_notifikasi->buat(
                    $lewat ? 'lewat' : 'mepet', $p->id,
                    ($lewat ? 'Ditolak otomatis (lewat): ' : 'Ditolak otomatis (mepet): ') . $p->nama,
                    $alasan, base_url() . 'analisis/peserta/' . $p->id,
                    ($lewat ? 'lewat' : 'mepet') . ':' . $p->id . ':' . date('Y-m-d')
                );
                // Pelamar juga wajib tahu pengajuannya ditolak otomatis (bagian B7
                // spesifikasi sisi pelamar), dengan tautan ke form daftar ulang,
                // bukan ke halaman detail HR yang tidak bisa dia buka.
                $this->model_prk_notifikasi->buat(
                    $lewat ? 'lewat' : 'mepet', $p->id, 'Pengajuan Ditolak Secara Otomatis',
                    'Tanggal mulai terlalu dekat dengan hari ini atau telah terlewat. Silakan ajukan kembali dengan jadwal baru.',
                    base_url() . 'pelamar/daftar',
                    'pelamar_' . ($lewat ? 'lewat' : 'mepet') . ':' . $p->id . ':' . date('Y-m-d'),
                    null, 'pelamar', $p->user_id
                );
                $jumlah++;
            }
        }
        return $jumlah;
    }

    // Accept yang semua kontraknya 'selesai' -> Inactive (bagian 6.1 baris
    // "accept -> inactive, penjadwal otomatis").
    private function _autoInactive() {
        $daftar = $this->db
            ->select('id, nama')
            ->from('pkl_data')
            ->where('LOWER(status)', 'accept')
            ->get()->result();

        $jumlah = 0;
        foreach ($daftar as $p) {
            $penempatan = $this->model_prk_penempatan->getPenempatan($p->id);
            if (!$penempatan) {
                continue;
            }
            $kontrak = $this->model_prk_kontrak->getByPenempatan($penempatan->id);
            if (!$kontrak || $kontrak->status !== 'selesai') {
                continue;
            }
            $ok = $this->analisis_status->ubah($p->id, Analisis_status::INACTIVE, 'sistem', 'Otomatis: kontrak selesai.');
            if ($ok) {
                $this->model_prk_notifikasi->buat(
                    'status_otomatis', $p->id, "{$p->nama} selesai magang",
                    "{$p->nama} otomatis diubah ke Tidak aktif karena kontrak sudah selesai.",
                    base_url() . 'analisis/peserta/' . $p->id,
                    'status_otomatis:' . $p->id . ':' . $kontrak->id
                );
                $jumlah++;
            }
        }
        return $jumlah;
    }

    // Notifikasi hari-H (tanggal_selesai = hari ini), terpisah dari H-7/H-3 yang
    // sudah ditangani Model_prk_notifikasi::generatePengingatKontrak().
    private function _notifikasiHariH() {
        $hari_ini = date('Y-m-d');
        $daftar = $this->db
            ->select('id, user_id, nama, tgl_selesai')
            ->from('pkl_data')
            ->where('LOWER(status)', 'accept')
            ->where('tgl_selesai', $hari_ini)
            ->get()->result();

        $jumlah = 0;
        foreach ($daftar as $p) {
            $baru = $this->model_prk_notifikasi->buat(
                'hari_h', $p->id, "{$p->nama} selesai hari ini",
                "{$p->nama} menyelesaikan magang hari ini (" . date('d M Y', strtotime($p->tgl_selesai)) . ").",
                base_url() . 'analisis/peserta/' . $p->id,
                'hari_h:' . $p->id . ':' . $hari_ini
            );
            if ($baru) {
                $jumlah++;
            }
            // Pelamar juga diberi tahu (bagian B7 spesifikasi sisi pelamar).
            $this->model_prk_notifikasi->buat(
                'hari_h', $p->id, 'Magang Telah Berakhir',
                'Masa magang Anda resmi berakhir hari ini (' . date('d M Y', strtotime($p->tgl_selesai)) . ').',
                base_url() . 'pelamar/pengajuan/' . $p->id,
                'pelamar_hari_h:' . $p->id . ':' . $hari_ini,
                null, 'pelamar', $p->user_id
            );
        }
        return $jumlah;
    }

    private function _prosesJob($job) {
        if (!$this->model_prk_job->klaim($job->id)) {
            echo "Job {$job->id}: gagal klaim (sudah diambil proses lain), lewati.\n";
            return;
        }

        $pelamar_rows = $this->model_transaksi->getSingleData($job->id_transaksi);
        $pelamar = !empty($pelamar_rows) ? $pelamar_rows[0] : null;
        if (!$pelamar) {
            $this->model_prk_job->tandaiSelesai($job->id, 'gagal', 'DATA_TIDAK_DITEMUKAN', 'pkl_data tidak ditemukan.');
            echo "Job {$job->id}: pkl_data #{$job->id_transaksi} tidak ditemukan.\n";
            return;
        }

        $payload = $this->_susunPayload($job, $pelamar);
        $hasil = $this->_panggilLayanan($payload);

        if ($hasil['ok']) {
            $tubuh = $hasil['tubuh'];
            $id_profil = $this->model_prk_job->simpanHasil($job->id_transaksi, $tubuh['profil'], $tubuh['rekomendasi']);
            if ($id_profil === false) {
                $this->model_prk_job->tandaiSelesai($job->id, 'gagal', 'GALAT_SIMPAN', 'Gagal menyimpan hasil ke database.');
                echo "Job {$job->id}: gagal menyimpan hasil (transaksi DB rollback).\n";
                return;
            }

            $this->model_prk_job->tandaiSelesai($job->id, $tubuh['status']);
            $jenis_notif = ($tubuh['status'] === 'perlu_tinjau') ? 'perlu_tinjau' : 'analisis_selesai';
            $judul = ($tubuh['status'] === 'perlu_tinjau') ? "Perlu ditinjau: {$pelamar->nama}" : "Rekomendasi siap: {$pelamar->nama}";
            $this->model_prk_notifikasi->buat(
                $jenis_notif, $job->id_transaksi, $judul,
                $tubuh['status'] === 'perlu_tinjau' ? "Rekomendasi untuk {$pelamar->nama} perlu ditinjau manual." : "Rekomendasi untuk {$pelamar->nama} sudah siap.",
                base_url() . 'analisis/peserta/' . $job->id_transaksi,
                'analisis:' . $job->id_transaksi . ':' . $job->hash_proposal
            );
            echo "Job {$job->id}: selesai (status={$tubuh['status']}).\n";
            return;
        }

        if ($hasil['kode'] === 'HTTP_422') {
            // Galat terduga dari layanan (dokumen rusak/kosong dst) -- tidak diulang.
            $this->model_prk_job->tandaiSelesai($job->id, 'perlu_tinjau', $hasil['kode_dalam'], $hasil['pesan']);
            $this->model_prk_notifikasi->buat(
                'perlu_tinjau', $job->id_transaksi, "Perlu ditinjau: {$pelamar->nama}",
                $hasil['pesan'], base_url() . 'analisis/peserta/' . $job->id_transaksi,
                'analisis:' . $job->id_transaksi . ':' . $job->hash_proposal
            );
            echo "Job {$job->id}: {$hasil['kode_dalam']} - {$hasil['pesan']}\n";
            return;
        }

        // 5xx/timeout/layanan mati -- percobaan ulang bertahap (bagian 3 langkah 6).
        $maks = (int) $this->config->item('analisis_antrean_percobaan_maks');
        $status_baru = $this->model_prk_job->tandaiGagalSementara($job->id, (int) $job->percobaan, $maks, $hasil['pesan']);
        if ($status_baru === 'gagal') {
            $this->model_prk_notifikasi->buat(
                'gagal', $job->id_transaksi, "Analisis gagal: {$pelamar->nama}",
                "Analisis gagal setelah {$maks} percobaan: {$hasil['pesan']}",
                base_url() . 'analisis/peserta/' . $job->id_transaksi,
                'analisis_gagal:' . $job->id_transaksi . ':' . $job->hash_proposal
            );
        }
        echo "Job {$job->id}: {$hasil['pesan']} (status={$status_baru}).\n";
    }

    private function _susunPayload($job, $pelamar) {
        $sisa_kuota = $this->model_prk_master->sisaKuotaSemuaDivisi($pelamar->tgl_mulai, $pelamar->tgl_selesai);
        $divisi_mentah = $this->model_prk_master->listDivisiDenganDepartemen();
        $divisi = array();
        foreach ($divisi_mentah as $d) {
            if (!$d->aktif) {
                continue;
            }
            $divisi[] = array(
                'id' => (int) $d->id,
                'nama' => $d->nama,
                'id_departemen' => (int) $d->id_departemen,
                'sisa_kuota' => isset($sisa_kuota[(int) $d->id]) ? $sisa_kuota[(int) $d->id] : 0,
            );
        }

        $bobot_ambang = $this->model_prk_master->bobotDanAmbang();

        return array(
            'job_id' => $job->id,
            'id_transaksi' => (int) $job->id_transaksi,
            'berkas' => array(
                'proposal' => FCPATH . 'gambar/' . $pelamar->doc_2,
                'transkrip' => FCPATH . 'gambar/' . $pelamar->doc_3,
            ),
            'peserta' => array(
                'nama' => $pelamar->nama,
                'instansi' => $pelamar->institusi,
                'jurusan' => $pelamar->jurusan,
                'pendidikan' => $pelamar->pendidikan,
            ),
            'periode' => array('mulai' => $pelamar->tgl_mulai, 'selesai' => $pelamar->tgl_selesai),
            'master' => array(
                'divisi' => $divisi,
                'sinyal' => $this->model_prk_master->listSinyalDivisi(),
                'pemetaan_jurusan' => $this->model_prk_master->listPemetaanJurusan(),
                'mapel_relevan' => $this->model_prk_master->listMapelRelevan(),
                'bobot' => $bobot_ambang['bobot'],
                'ambang' => $bobot_ambang['ambang'],
            ),
            'opsi' => array('izinkan_llm' => (bool) $this->config->item('analisis_llm_aktif')),
        );
    }

    // POST ke layanan Python lewat stream context (tanpa dependency ekstensi curl).
    // Return array normalisasi: ok=true + tubuh, atau ok=false + kode/pesan.
    private function _panggilLayanan($payload) {
        $url = rtrim($this->config->item('analisis_layanan_url'), '/') . '/v1/analisis';
        $json = json_encode($payload);
        $timeout = (int) $this->config->item('analisis_layanan_timeout_detik');
        $api_key = $this->config->item('analisis_layanan_api_key');

        $header = "Content-Type: application/json\r\n";
        if (!empty($api_key)) {
            $header .= "X-Api-Key: {$api_key}\r\n";
        }

        $context = stream_context_create(array('http' => array(
            'method' => 'POST',
            'header' => $header,
            'content' => $json,
            'timeout' => $timeout,
            'ignore_errors' => true,
        )));

        $respons = @file_get_contents($url, false, $context);
        if ($respons === false) {
            return array('ok' => false, 'kode' => 'KONEKSI_GAGAL', 'pesan' => 'Tidak bisa menghubungi layanan analisis.');
        }

        $kode_http = 0;
        if (isset($http_response_header)) {
            foreach ($http_response_header as $h) {
                if (preg_match('#^HTTP/\S+\s+(\d+)#', $h, $m)) {
                    $kode_http = (int) $m[1];
                    break;
                }
            }
        }

        $tubuh = json_decode($respons, true);

        if ($kode_http === 200) {
            return array('ok' => true, 'tubuh' => $tubuh);
        }
        if ($kode_http === 422) {
            return array(
                'ok' => false, 'kode' => 'HTTP_422',
                'kode_dalam' => isset($tubuh['kode']) ? $tubuh['kode'] : 'GALAT_422',
                'pesan' => isset($tubuh['pesan']) ? $tubuh['pesan'] : 'Dokumen tidak bisa diproses.',
            );
        }
        return array('ok' => false, 'kode' => 'HTTP_' . $kode_http, 'pesan' => "Layanan analisis merespons HTTP {$kode_http}.");
    }
}
