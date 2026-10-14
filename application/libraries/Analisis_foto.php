<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Panggil layanan Python /v1/validasi_foto (FR-29) dan simpan hasilnya ke
// prk_foto_cek. Dipanggil dari Transaksi.php setiap foto diunggah/diganti --
// dibungkus try/catch di titik panggilnya supaya kegagalan Python (mati, timeout)
// tidak pernah menggagalkan upload E-Prakerin lama (pola sama dengan antrekanAnalisis()).
class Analisis_foto {

    protected $ci;

    function __construct() {
        $this->ci = &get_instance();
        $this->ci->config->load('analisis');
        $this->ci->load->database();
    }

    // $id_transaksi: pkl_data.id. $nama_file: nilai kolom foto (bukan path lengkap).
    // Return true kalau berhasil memeriksa (hasil tersimpan, ok=true/false tetap
    // bisa keduanya), false kalau layanan tidak terhubung sama sekali.
    function periksaDanSimpan($id_transaksi, $nama_file) {
        if (empty($nama_file)) {
            return false;
        }
        $path_absolut = FCPATH . 'gambar/' . $nama_file;
        if (!is_file($path_absolut)) {
            return false;
        }

        $hasil = $this->_panggilLayanan($path_absolut);
        if ($hasil === null) {
            return false;
        }

        $this->ci->db->where('id_transaksi', $id_transaksi)->update('prk_foto_cek', array('is_current' => 0));
        $this->ci->db->insert('prk_foto_cek', array(
            'id_transaksi' => $id_transaksi,
            'is_current' => 1,
            'ok' => $hasil['ok'] ? 1 : 0,
            'catatan' => implode(' | ', $hasil['catatan']),
            'diperiksa_pada' => date('Y-m-d H:i:s'),
        ));
        return true;
    }

    private function _panggilLayanan($path_absolut) {
        $url = rtrim($this->ci->config->item('analisis_layanan_url'), '/') . '/v1/validasi_foto';
        $api_key = $this->ci->config->item('analisis_layanan_api_key');
        $timeout = (int) $this->ci->config->item('analisis_layanan_timeout_detik');

        $header = "Content-Type: application/json\r\n";
        if (!empty($api_key)) {
            $header .= "X-Api-Key: {$api_key}\r\n";
        }

        $context = stream_context_create(array('http' => array(
            'method' => 'POST',
            'header' => $header,
            'content' => json_encode(array('path' => str_replace('\\', '/', $path_absolut))),
            'timeout' => $timeout ?: 10,
            'ignore_errors' => true,
        )));

        $respons = @file_get_contents($url, false, $context);
        if ($respons === false) {
            return null;
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
        if ($kode_http !== 200) {
            return null;
        }

        $tubuh = json_decode($respons, true);
        if (!is_array($tubuh) || !array_key_exists('ok', $tubuh) || !array_key_exists('catatan', $tubuh)) {
            return null;
        }
        return $tubuh;
    }
}
