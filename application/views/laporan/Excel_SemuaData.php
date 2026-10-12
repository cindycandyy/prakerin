<?php
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
 
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
 
date_default_timezone_set('Asia/Jakarta');
 
/** Include*/
require_once '../../../assets/excel_reader.php';
require_once '../../../assets/Classes/PHPExcel.php';

// Membuat documen excel baru
$objPHPExcel = new PHPExcel();
 
// Set Properti Documen excel yang akan dibuat
$objPHPExcel->getProperties()->setCreator("-")
                             ->setLastModifiedBy("Admin PSG")
                             ->setTitle("Report Data PKL Office 2007 XLSX")
                             ->setSubject("Data PKL Office 2007 XLSX")
                             ->setDescription("Detail laporan")
                             ->setKeywords("office 2007 openxml php")
                             ->setCategory("Laporan");

$default_border = array(
    'style' => PHPExcel_Style_Border::BORDER_THIN,
    'color' => array('rgb'=>'000000')
);
$style_border = array(
    'borders' => array(
        'bottom' => $default_border,
        'left' => $default_border,
        'top' => $default_border,
        'right' => $default_border,
    )
);

$styleArray = array( 
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
             	'vertical'   => PHPExcel_Style_Alignment::VERTICAL_TOP,
             	'rotation'   => 0,
			),
			'borders' => 
			array( 'allborders' => 
				array( 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => '00000000'), 
					), 
				), 
			);
 
		// melakukan pengaturan pada header kolom
		$fontHeader = array( 
			'font' => array(
				'bold' => true
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
             	'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
             	'rotation'   => 0,
			),
			'fill' => array(
            	'type' => PHPExcel_Style_Fill::FILL_SOLID,
            	'color' => array('rgb' => 'cccccc')
        	),
			'borders' => 
			array( 'allborders' => 
				array( 'style' => PHPExcel_Style_Border::BORDER_THIN, 'color' => array('argb' => '00000000'), 
					), 
				), 
		);

		$objWorksheet = $objPHPExcel->getActiveSheet();
//Baris
$awal = 3;
//Header
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'LAPORAN PESERTA MAGANG');
$objPHPExcel->getActiveSheet()->mergeCells('A1:R1');$objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getAlignment()->applyFromArray(
    array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
$objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getAlignment()->applyFromArray(
    array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,));
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

//========== Data Pribadi
$objPHPExcel->getActiveSheet()->setCellValue('A'.$awal, 'NO');
$objPHPExcel->getActiveSheet()->setCellValue('B'.$awal, 'USER ID');
$objPHPExcel->getActiveSheet()->setCellValue('C'.$awal, 'YANG MENDAFTARKAN');
$objPHPExcel->getActiveSheet()->setCellValue('D'.$awal, 'BAGDE');
$objPHPExcel->getActiveSheet()->setCellValue('E'.$awal, 'NAMA');
$objPHPExcel->getActiveSheet()->setCellValue('F'.$awal, 'ALAMAT');
$objPHPExcel->getActiveSheet()->setCellValue('G'.$awal, 'RT');
$objPHPExcel->getActiveSheet()->setCellValue('H'.$awal, 'RW');
$objPHPExcel->getActiveSheet()->setCellValue('I'.$awal, 'KELURAHAN');
$objPHPExcel->getActiveSheet()->setCellValue('J'.$awal, 'KECAMATAN');
$objPHPExcel->getActiveSheet()->setCellValue('K'.$awal, 'KABUPATEN');
$objPHPExcel->getActiveSheet()->setCellValue('L'.$awal, 'PENDIDIKAN');
$objPHPExcel->getActiveSheet()->setCellValue('M'.$awal, 'JURUSAN');
$objPHPExcel->getActiveSheet()->setCellValue('N'.$awal, 'INSTITUSI');
$objPHPExcel->getActiveSheet()->setCellValue('O'.$awal, 'DEP / BAG YANG DI PILIH');
$objPHPExcel->getActiveSheet()->setCellValue('P'.$awal, 'TANGGAL MULAI');
$objPHPExcel->getActiveSheet()->setCellValue('Q'.$awal, 'TANGGAL SELESAI');
$objPHPExcel->getActiveSheet()->setCellValue('R'.$awal, 'STATUS');
//==========

$objWorksheet->getStyle('A'.$awal.':R'.$awal)->applyFromArray($fontHeader);

//start data from row 2
$i = $awal+1;
$no= 1;

// Add some data
//$sql = "SELECT a.*, b.`nama_depag`, c.`name` FROM pkl_data a INNER JOIN depag b ON(a.`id_depag`=b.`id`) INNER JOIN `user` c ON (a.`user_id`=c.`user_id`)
//WHERE tgl_mulai >= '".$_GET['tahun']."-01-01' AND tgl_selesai <= '".$_GET['tahun']."2020-12-31'";
//$query = $db_object->db_query($sql);

foreach ($data as $key => $rowdata) {

    $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $no);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $rowdata->user_id);
    $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $rowdata->name);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $rowdata->no_pkl);
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $rowdata->nama);
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $rowdata->alamat);
    $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $rowdata->rt);
    $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $rowdata->rw);
    $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $rowdata->kelurahan);
    $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $rowdata->kecamatan);
    $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $rowdata->kabupaten);
    $objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $rowdata->pendidikan);
    $objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $rowdata->jurusan);
    $objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $rowdata->institusi);
    $objPHPExcel->getActiveSheet()->setCellValue('O'.$i, $rowdata->depag);
    $objPHPExcel->getActiveSheet()->setCellValue('P'.$i, $rowdata->tgl_mulai);
    $objPHPExcel->getActiveSheet()->setCellValue('Q'.$i, $rowdata->tgl_selesai);
    $objPHPExcel->getActiveSheet()->setCellValue('R'.$i, $rowdata->status);
	
    $i++;
    $no++;
}
$i = ($i-1);

		$objWorksheet->getStyle('A'.$awal.':R'.$i)->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->freezePane('A'.($awal+1));

//$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());

//Mengatur lebar cell pada documen excel
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Laporan PKL');

// Set sheet yang aktif pada documen excel
$objPHPExcel->setActiveSheetIndex(0);
 
// Redirect output to a client�s web browser (Excel2007)
//header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//header('Content-Disposition: attachment;filename="DataPegawai.xlsx"');
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="DataPesertaMagang.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');