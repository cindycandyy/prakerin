<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Laporan extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model("model_laporan");
    }

    
    public function index() {
        parent::__construct();
//        $tahun    = $this->input->post('tahun');
//        if(!empty($tahun)){
//            $data['data'] = $this->model_laporan->reportRekapPkl();
//            $this->load->view('laporan/Excel', $data);
//        }
    }

    public function data() {
        $auth    = $this->session->userdata("SES_PRIV");
        if ($auth) {
            $path1 = $this->uri->segment(3);
            if ($path1 == 'list') {

                $this->load->view('header');
                $this->load->view('laporan/v_laporan');
                $this->load->view('footer');
            } else if ($path1 == 'isi') {
                $this->session->set_userdata('SES_TH', $this->input->post('tahun'));
            } else if($path1 == 'cetak'){
                $tahun    = $this->session->userdata("SES_TH");
                $siswa = $this->model_laporan->reportRekapPkl($tahun);
                
                include APPPATH.'third_party/Classes/PHPExcel.php';
//                include APPPATH.'third_party/excel_reader.php';
                $excel = new PHPExcel();
                // Settingan awal fil excel
                $excel->getProperties()->setCreator('Admin SDM')
                             ->setLastModifiedBy('Admin SDM')
                             ->setTitle("Data Siswa Magang")
                             ->setSubject("PKL")
                             ->setDescription("Laporan Semua Data Magang")
                             ->setKeywords("Data PKL");
                // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
                $style_col = array(
                  'font' => array('bold' => true), // Set font nya jadi bold
                  'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                  ),
                  'borders' => array(
                    'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                    'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                    'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                    'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
                  ),
			'fill' => array(
            	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => 'cccccc')
        	)
                );
                // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
                $style_row = array(
                  'alignment' => array(
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                  ),
                  'borders' => array(
                    'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                    'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                    'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                    'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
                  )
                );
                $excel->setActiveSheetIndex(0)->setCellValue('A1', "REKAPITULASI DATA SISWA MAGANG"); // Set kolom A1 dengan tulisan "DATA SISWA"
                $excel->getActiveSheet()->mergeCells('A1:R1'); // Set Merge Cell pada kolom A1 sampai E1
                $excel->getActiveSheet()->getStyle('A1')->getFont()->setBold(TRUE); // Set bold kolom A1
                $excel->getActiveSheet()->getStyle('A1')->getFont()->setSize(15); // Set font size 15 untuk kolom A1
                $excel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); // Set text center untuk kolom A1
                // Buat header tabel nya pada baris ke 3
                 
                $excel->setActiveSheetIndex(0)->setCellValue('S3', "STATUS"); 
                $excel->setActiveSheetIndex(0)->setCellValue('A3', "NO"); 
                $excel->setActiveSheetIndex(0)->setCellValue('B3', "USER ID YANG MENDAFTARKAN"); 
                $excel->setActiveSheetIndex(0)->setCellValue('C3', "KEGIATAN"); 
                $excel->setActiveSheetIndex(0)->setCellValue('D3', "NAMA"); 
                $excel->setActiveSheetIndex(0)->setCellValue('E3', "JK"); 
                $excel->setActiveSheetIndex(0)->setCellValue('F3', "TEMPAT LAHIR"); 
                $excel->setActiveSheetIndex(0)->setCellValue('G3', "TANGGAL LAHIR"); 
                $excel->setActiveSheetIndex(0)->setCellValue('H3', "ALAMAT"); 
                $excel->setActiveSheetIndex(0)->setCellValue('I3', "TELP"); 
                $excel->setActiveSheetIndex(0)->setCellValue('J3', "EMAIL"); 
                $excel->setActiveSheetIndex(0)->setCellValue('K3', "NAMA ORTU"); 
                $excel->setActiveSheetIndex(0)->setCellValue('L3', "HP ORTU"); 
                $excel->setActiveSheetIndex(0)->setCellValue('M3', "INSTITUSI"); 
                $excel->setActiveSheetIndex(0)->setCellValue('N3', "ALAMAT INSTITUSI"); 
                $excel->setActiveSheetIndex(0)->setCellValue('O3', "PENDIDIKAN"); 
                $excel->setActiveSheetIndex(0)->setCellValue('P3', "JURUSAN"); 
                $excel->setActiveSheetIndex(0)->setCellValue('Q3', "PEMBIMBING"); 
                $excel->setActiveSheetIndex(0)->setCellValue('R3', "HP PEMBIMBING");
                $excel->setActiveSheetIndex(0)->setCellValue('T3', "FOTO"); 
                $excel->setActiveSheetIndex(0)->setCellValue('U3', "PENGANTAR"); 
                $excel->setActiveSheetIndex(0)->setCellValue('V3', "PROPOSAL"); 
                $excel->setActiveSheetIndex(0)->setCellValue('W3', "NILAI"); 
                $excel->setActiveSheetIndex(0)->setCellValue('X3', "ASKES"); 
                $excel->setActiveSheetIndex(0)->setCellValue('Y3', "KTP/KTM"); 
                $excel->setActiveSheetIndex(0)->setCellValue('Z3', "PERNYATAAN"); 
                $excel->setActiveSheetIndex(0)->setCellValue('AA3', "TGL MULAI");
                $excel->setActiveSheetIndex(0)->setCellValue('AB3', "TGL SELESAI");
                $excel->setActiveSheetIndex(0)->setCellValue('AC3', "UNIT KERJA");
                // Apply style header yang telah kita buat tadi ke masing-masing kolom header
                
                $excel->getActiveSheet()->getStyle('S3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('A3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('B3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('C3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('D3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('E3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('F3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('G3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('H3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('I3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('J3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('K3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('L3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('M3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('N3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('O3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('P3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('Q3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('R3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('T3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('U3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('V3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('W3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('X3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('Y3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('Z3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('AA3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('AB3')->applyFromArray($style_col);
                $excel->getActiveSheet()->getStyle('AC3')->applyFromArray($style_col);
                
                $excel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
                $excel->getActiveSheet()->getRowDimension(2)->setRowHeight(20);
                $excel->getActiveSheet()->getRowDimension(3)->setRowHeight(20);
                // Panggil function view yang ada di SiswaModel untuk menampilkan semua data siswanya
                $no = 1; // Untuk penomoran tabel, di awal set dengan 1
                $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
                foreach($siswa as $data){ // Lakukan looping pada variabel siswa
                    
                    $excel->getActiveSheet()->getRowDimension($numrow)->setRowHeight(20);
                    
                    $excel->setActiveSheetIndex(0)->setCellValue('S'.$numrow, $data->status);
                    $excel->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $no);
                    $excel->setActiveSheetIndex(0)->setCellValue('B'.$numrow, $data->user_id);
                    $excel->setActiveSheetIndex(0)->setCellValue('C'.$numrow, $data->kegiatan);
                    $excel->setActiveSheetIndex(0)->setCellValue('D'.$numrow, $data->nama);
                    $excel->setActiveSheetIndex(0)->setCellValue('E'.$numrow, $data->jk);
                    $excel->setActiveSheetIndex(0)->setCellValue('F'.$numrow, $data->tmpt_lahir);
                    $excel->setActiveSheetIndex(0)->setCellValue('G'.$numrow, $data->tgl_lahir);
                    $excel->setActiveSheetIndex(0)->setCellValue('H'.$numrow, $data->alamat);
                    $excel->setActiveSheetIndex(0)->setCellValue('I'.$numrow, $data->telp);
                    $excel->setActiveSheetIndex(0)->setCellValue('J'.$numrow, $data->email);
                    $excel->setActiveSheetIndex(0)->setCellValue('K'.$numrow, $data->nm_ortu);
                    $excel->setActiveSheetIndex(0)->setCellValue('L'.$numrow, $data->hp_ortu);
                    $excel->setActiveSheetIndex(0)->setCellValue('M'.$numrow, $data->institusi);
                    $excel->setActiveSheetIndex(0)->setCellValue('N'.$numrow, $data->almt_institusi);
                    $excel->setActiveSheetIndex(0)->setCellValue('O'.$numrow, $data->pendidikan);
                    $excel->setActiveSheetIndex(0)->setCellValue('P'.$numrow, $data->jurusan);
                    $excel->setActiveSheetIndex(0)->setCellValue('Q'.$numrow, $data->pembimbing);
                    $excel->setActiveSheetIndex(0)->setCellValue('R'.$numrow, $data->hp_pembimbing);
                    $excel->setActiveSheetIndex(0)->setCellValue('T'.$numrow, '=Hyperlink("https://prakerin.petrosida-gresik.online/gambar/'.$data->foto.'","Download")');
                    $excel->setActiveSheetIndex(0)->setCellValue('U'.$numrow, '=Hyperlink("https://prakerin.petrosida-gresik.online/gambar/'.$data->doc_1.'","Download")');
                    $excel->setActiveSheetIndex(0)->setCellValue('V'.$numrow, '=Hyperlink("https://prakerin.petrosida-gresik.online/gambar/'.$data->doc_2.'","Download")');
                    $excel->setActiveSheetIndex(0)->setCellValue('W'.$numrow, '=Hyperlink("https://prakerin.petrosida-gresik.online/gambar/'.$data->doc_3.'","Download")');
                    $excel->setActiveSheetIndex(0)->setCellValue('X'.$numrow, '=Hyperlink("https://prakerin.petrosida-gresik.online/gambar/'.$data->doc_5.'","Download")');
                    $excel->setActiveSheetIndex(0)->setCellValue('Y'.$numrow, '=Hyperlink("https://prakerin.petrosida-gresik.online/gambar/'.$data->doc_6.'","Download")');
                    $excel->setActiveSheetIndex(0)->setCellValue('Z'.$numrow, '=Hyperlink("https://prakerin.petrosida-gresik.online/gambar/'.$data->doc_7.'","Download")');
                    $excel->setActiveSheetIndex(0)->setCellValue('AA'.$numrow, $data->tgl_mulai);
                    $excel->setActiveSheetIndex(0)->setCellValue('AB'.$numrow, $data->tgl_selesai);
                    $excel->setActiveSheetIndex(0)->setCellValue('AC'.$numrow, $data->nama_depag);
                
                    // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
                    $excel->getActiveSheet()->getStyle('A'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('B'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('C'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('D'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('E'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('F'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('G'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('H'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('I'.$numrow)->getNumberFormat()->setFormatCode('0');
                    $excel->getActiveSheet()->getStyle('J'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('K'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('L'.$numrow)->getNumberFormat()->setFormatCode('0');
                    $excel->getActiveSheet()->getStyle('M'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('N'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('O'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('P'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('Q'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('R'.$numrow)->getNumberFormat()->setFormatCode('0');
                    $excel->getActiveSheet()->getStyle('S'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('T'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('U'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('V'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('W'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('X'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('Y'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('Z'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('AA'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('AB'.$numrow)->applyFromArray($style_row);
                    $excel->getActiveSheet()->getStyle('AC'.$numrow)->applyFromArray($style_row);


                    $no++; // Tambah 1 setiap kali looping
                    $numrow++; // Tambah 1 setiap kali looping
                }
                // Set width kolom
                  $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
                  $excel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                  $excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('D')->setWidth(50);
                  $excel->getActiveSheet()->getColumnDimension('E')->setWidth(5);
                  $excel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                  $excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
                  $excel->getActiveSheet()->getColumnDimension('H')->setWidth(50);
                  $excel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                  $excel->getActiveSheet()->getColumnDimension('K')->setWidth(25);
                  $excel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
                  $excel->getActiveSheet()->getColumnDimension('N')->setWidth(50);
                  $excel->getActiveSheet()->getColumnDimension('O')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('P')->setWidth(25);
                  $excel->getActiveSheet()->getColumnDimension('Q')->setWidth(25);
                  $excel->getActiveSheet()->getColumnDimension('R')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('S')->setWidth(10);
                  $excel->getActiveSheet()->getColumnDimension('T')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('U')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('V')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('W')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('X')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('Y')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('Z')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('AA')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('AB')->setWidth(15);
                  $excel->getActiveSheet()->getColumnDimension('AC')->setWidth(15);

                // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
                $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
                // Set orientasi kertas jadi LANDSCAPE
                $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                // Set judul file excel nya
                $excel->getActiveSheet(0)->setTitle("Laporan Data Siswa Magang");
                $excel->setActiveSheetIndex(0);
                // Proses file excel
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                //header('Content-Disposition: attachment; filename="Data Siswa Magang.xlsx"'); // Set nama file excel nya
                header('Cache-Control: max-age=0');
                
                ob_end_clean();
                $filename='Data Siswa Magang.xls';
                //header('Content-type: application/vnd.ms-excel');
                header('Content-Disposition: attachment; filename='.$filename);
               
               $write = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
               // $write = IOFactory::createWriter($excel, 'xlsx');
               $write->save('php://output');
               exit;
            }else {
                $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;&nbsp;Login Terlebih Dahulu !!!</div>');
                redirect(base_url() . "login/sistem/login_sistem");
            }
        } else {
            $this->session->set_flashdata('pesan', '<div class="alert alert-danger">&nbsp;&nbsp;Login Terlebih Dahulu !!!</div>');
            redirect(base_url() . "login/sistem/login_sistem");
        }
    }

}
?>