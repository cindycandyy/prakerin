<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

// Eja angka ke kata (bagian 8.1 spesifikasi teknis, "1 (Satu) Mahasiswa"). Cukup untuk
// jumlah peserta satu surat (realistis 1-100-an), bukan terbilang rupiah/umum.
if (!function_exists('terbilang')) {

    function terbilang($angka) {
        $angka = (int) $angka;
        $satuan = array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh',
            'Sebelas');

        if ($angka < 12) {
            return $satuan[$angka];
        }
        if ($angka < 20) {
            return terbilang($angka - 10) . ' Belas';
        }
        if ($angka < 100) {
            $sisa = $angka % 10;
            return trim(terbilang((int) ($angka / 10)) . ' Puluh ' . ($sisa ? terbilang($sisa) : ''));
        }
        if ($angka < 200) {
            return trim('Seratus ' . terbilang($angka - 100));
        }
        if ($angka < 1000) {
            $sisa = $angka % 100;
            return trim(terbilang((int) ($angka / 100)) . ' Ratus ' . ($sisa ? terbilang($sisa) : ''));
        }
        // Tidak realistis untuk jumlah peserta satu surat, tapi tidak dibiarkan error.
        return (string) $angka;
    }
}

// Format tanggal Indonesia tanpa bergantung setlocale() (sering tidak tersedia/beda
// nama locale antar server). Dipakai templat surat balasan (bagian 8) supaya "27 Juli
// 2026", bukan "27 July 2026" bawaan date('d F Y').
if (!function_exists('tanggal_indo')) {

    function tanggal_indo($tanggal_sql, $dengan_hari = false) {
        if (empty($tanggal_sql)) {
            return '-';
        }
        $bulan = array('', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
        $hari = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');

        $ts = strtotime($tanggal_sql);
        $teks = date('d', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
        if ($dengan_hari) {
            $teks = $hari[(int) date('w', $ts)] . ', ' . $teks;
        }
        return $teks;
    }
}
