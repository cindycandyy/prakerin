<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Home';
$route['404_override'] = 'errors/';
$route['translate_uri_dashes'] = FALSE;
$route['email'] = 'Email';

// analisis/peserta/{id}/terima|tolak: routing default CI hanya memetakan
// 2 segmen pertama ke controller/method, jadi segmen ke-4 (terima/tolak)
// perlu rute eksplisit, bukan dibaca sebagai argumen tambahan ke peserta().
$route['analisis/peserta/(:num)/terima'] = 'analisis/terima/$1';
$route['analisis/peserta/(:num)/tolak'] = 'analisis/tolak/$1';
$route['analisis/peserta/(:num)/surat_buat'] = 'analisis/surat_buat/$1';
$route['analisis/peserta/(:num)/berkas_unduh'] = 'analisis/berkas_unduh/$1';
$route['analisis/peserta/(:num)/berkas/(:any)'] = 'analisis/berkas_lihat/$1/$2';
$route['analisis/peserta/(:num)/analisis_ulang'] = 'analisis/analisis_ulang/$1';
$route['analisis/kontrak/(:num)/perpanjang'] = 'analisis/kontrak_perpanjang/$1';
$route['analisis/kontrak/(:num)/akhiri'] = 'analisis/kontrak_akhiri/$1';
$route['analisis/surat/(:num)/simpan'] = 'analisis/surat_simpan/$1';
$route['analisis/surat/(:num)/pratinjau'] = 'analisis/surat_pratinjau/$1';
$route['analisis/surat/(:num)/kirim'] = 'analisis/surat_kirim/$1';
$route['analisis/surat/(:num)/unduh'] = 'analisis/surat_unduh/$1';
$route['analisis/surat/(:num)/pengganti'] = 'analisis/surat_pengganti/$1';
$route['analisis/surat/(:num)/gabung'] = 'analisis/surat_gabung/$1';

// pelamar/pengajuan/{id}/xxx: segmen ke-4, sama alasannya seperti rute analisis/* di atas.
$route['pelamar/pengajuan/(:num)/ubah'] = 'pelamar/ubah/$1';
$route['pelamar/pengajuan/(:num)/batal'] = 'pelamar/batal/$1';
$route['pelamar/pengajuan/(:num)/berkas/(:any)'] = 'pelamar/berkas/$1/$2';
$route['pelamar/berkas/(:num)/(:any)'] = 'pelamar/berkas_lihat/$1/$2';
$route['pelamar/surat/(:num)/unduh'] = 'pelamar/surat_unduh/$1';
$route['pelamar/akun/ganti_password'] = 'pelamar/akun_ganti_password';
