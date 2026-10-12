<?php
ini_set('max_execution_time', 3600);
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
session_start();
include 'phpmailer/class.phpmailer.php';
//include 'conn-db.php';

// Membuat script koneksi
require_once 'conn-db.php';

if(isset($_GET['jml_data'])){
    $jml_data = $_GET['jml_data'];
}else{
    $jml_data = 0;
}
$max = 30;
$data = 0+$jml_data;

?>
<style type="text/css">
	#Refresh:hover{
		background-color:#9C0;
		color:#000;
		font-weight:bold;
	}
	#Refresh{
		padding:3px 3px 3px 3px;
		color:#FFFFFF;
		cursor:pointer;
		font-size:12px;
		text-decoration:none;
		text-transform:uppercase;
		background-color:#333333;
		border-radius:5px 5px 5px 5px;
		-moz-border-radius:5px 5px 5px 5px;
		-webkit-border-radius:5px 5px 5px 5px;
	}
	button, .button{
		background: #F3F3F3;
		border: 1px solid #DCDCDC;
		border-radius: 2px;
		color: #444444;
		cursor: pointer;
		display: inline-block;
		font: 700 11px Tahoma, Arial, sans-serif;
		margin-right: 10px;
		padding: 7px 12px 7px 12px;
		position: relative;
		text-decoration: none;
		text-shadow: 0px 1px 0px #FFFFFF;
	}
	button:hover, .button:hover{
		border-bottom-color: #999999;
		border-left-color: #999999;
		border-right-color: #999999;
		border-top-color: #999999;
		color: #333333;
		text-decoration: none;
	}
</style>
<table border="1" style="border-collapse:collapse">
<tr>
<th>NO</th>
<th>NAMA</th>
<th>NIK</th>
<th>UNIT KERJA</th>
</tr>
<?php

$sql = "select a.*, b.`email` FROM rgaji_tetap a INNER JOIN email_gaji b ON(a.`nik`=b.`nik`) ORDER BY a.id_gaji LIMIT ".$data.",".$max."";//  where a.nik = 'TP07205'
$query = mysql_query($sql);
$numRow = mysql_num_rows($query);

$no = 1;
while($q = mysql_fetch_array($query)){

try {

$mail      = new PHPMailer(true); //New instance, with exceptions enabled
if($_SESSION['setting'] == 'default'){
$body     =   '<html>

<head>

</head>

<body>';
$body     .='
<table width="700" style="border:1px solid; padding:35px 35px 35px 35px">
<tr>
<td colspan="2">'.$_SESSION['subject'].'</td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
</tr>
<tr>
<td colspan="2">'.$_SESSION['catatan'].'</td>
</tr>
</table>
';

$body     .='
</body></html>';    
}else{
$body     =   '<html>

<head>

</head>

<body>';
$body     .='
<table width="700" style="border:1px solid; padding:35px 35px 35px 35px">
<tr>
<td colspan="2">SLIP PENGHASILAN KARYAWAN BULAN '.$q['bulanth'].'</td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
</tr>
<tr>
<td colspan="2">Kepada: YTH</td>
</tr>
<tr>
<td colspan="2"><b>'.$q['nama'].'</b></td>
</tr>
<tr>
<td colspan="2">Di Tempat.</td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
</tr>
<tr>
<td colspan="2">Dengan Hormat<br>
Terlampir kami sampaikan Slip Gaji dan Slip Potongan. Untuk proses selanjutnya silahkan download attachment  Slip Gaji dan Potongan Yayasan (NIK masing-masing) yang kami kirimkan pada email ini, lalu untuk mengeprint Slip tersebut jangan lupa untuk Print Area terlebih dahulu.</td>
</tr>
<tr>
<td colspan="2">&nbsp;</td>
</tr>
<tr>
<td colspan="2">Terima Kasih.</td>
</tr>
</table>
';

$body     .='
<br>
<br>
<u><h2>Rincian Gaji</h2></u>
<br>
<table width="100%">
    <tr>
        <td style="background-color: #43B5AD; padding: 5px;border: 1px solid #000;" align="center" rowspan="2">Gaji Pokok</td>
        <td style="background-color: #43B5AD; padding: 5px;border: 1px solid #000;" align="center" colspan="3">Tunjangan</td>
        <td style="background-color: #43B5AD; padding: 5px;border: 1px solid #000;" align="center" rowspan="2">Lembur</td>
        <td style="background-color: #43B5AD; padding: 5px;border: 1px solid #000;" align="center" colspan="2">Potongan</td>
        <td style="background-color: #43B5AD; padding: 5px;border: 1px solid #000;" align="center" rowspan="2">Gaji Kotor</td>
        <td style="background-color: #43B5AD; padding: 5px;border: 1px solid #000;" align="center" rowspan="2">Gaji Bersih</td>
    </tr>
    <tr>
        <td style="background-color: #43B5AD; padding: 5px;border: 1px solid #000;" align="center">Tetap</td>
        <td style="background-color: #43B5AD; padding: 5px;border: 1px solid #000;" align="center">Tidak Tetap</td>
        <td style="background-color: #43B5AD; padding: 5px;border: 1px solid #000;" align="center">Lainnya</td>
        <td style="background-color: #43B5AD; padding: 5px;border: 1px solid #000;" align="center">Tetap</td>
        <td style="background-color: #43B5AD; padding: 5px;border: 1px solid #000;" align="center">Tidak Tetap</td>
    </tr>
    <tr>
        <td style="padding: 5px;border: 1px solid #000;" align="right">'.number_format($q['gpokok'],0,',','.').'</td>
        <td style="padding: 5px;border: 1px solid #000;" align="right">'.number_format($q['tot_tetap'],0,',','.').'</td>
        <td style="padding: 5px;border: 1px solid #000;" align="right">'.number_format($q['tot_ttetap'],0,',','.').'</td>
        <td style="padding: 5px;border: 1px solid #000;" align="right">'.number_format($q['tot_lain'],0,',','.').'</td>
        <td style="padding: 5px;border: 1px solid #000;" align="right">'.number_format($q['tot_lembur'],0,',','.').'</td>
        <td style="padding: 5px;border: 1px solid #000;" align="right">'.number_format($q['pot_tetap'],0,',','.').'</td>
        <td style="padding: 5px;border: 1px solid #000;" align="right">'.number_format($q['pot_ttetap'],0,',','.').'</td>
        <td style="padding: 5px;border: 1px solid #000;" align="right">'.number_format($q['gkotor'],0,',','.').'</td>
        <td style="padding: 5px;border: 1px solid #000;" align="right">'.number_format($q['gbersih'],0,',','.').'</td>
    </tr>
</table>
';

$body     .='
</body></html>';
}

$mail->IsSMTP(); 

$mail->Mailer = "smtp";                          // tell the class to use SMTP

$mail->SMTPAuth   = true;                  // enable SMTP authentication

$mail->SMTPSecure = 'ssl';

$mail->SMTPDebug = 1;

$mail->Port  = 465;                    // set the SMTP server port

$mail->Host  = "smtp.gmail.com"; // SMTP server

$mail->Username   = "payroll.petrosida@gmail.com";     // SMTP server username

$mail->Password   = "administrasipersonalia";            // SMTP server password
//$mail->IsSendmail();  // tell the class to use Sendmail

$mail->AddReplyTo("payroll.petrosida@gmail.com","Personalia PT. Petrosida Gresik");

$mail->From   = "payroll.petrosida@gmail.com";

$mail->FromName   = " Personalia PT. Petrosida Gresik ";

//$to = "muchsin.acer@gmail.com";

$to = $q['email'];
//$bcc = "broadcast@YourDomain.com";

$mail->AddAddress($to);

//$mail->AddBCC($bcc);
if($_SESSION['setting'] == 'default'){
    $mail->Subject  = $_SESSION['subject'];
}else{
    $mail->Subject  = "SLIP PENGHASILAN KARYAWAN BULAN ".$q['bulanth'];
}
//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";

// optional, comment out and test

//$mail->WordWrap   = 80; // set word wrap
$mail->MsgHTML($body);

$filename_gaji = 'file-Unggah/'.$q['nik'].'.xlsx';
$filename = 'file-Unggah/potongan/YYS-'.$q['nik'].'.xlsx';
$filename2 = 'file-Unggah/potongan-lain/YYS-'.$q['nik'].'.xlsx';
$filename3 = 'file-Unggah/bbc/BBC-'.$q['nik'].'.xlsx';
$filename4 = 'file-Unggah/insentif/'.$q['nik'].'.xlsx';
if(file_exists($filename_gaji)) {
        $mail->AddAttachment('file-Unggah/'.$q['nik'].'.xlsx'); // Gift Attachment
}
if(file_exists($filename)) {
	$mail->AddAttachment('file-Unggah/potongan/YYS-'.$q['nik'].'.xlsx'); // Gift Attachment
}
if(file_exists($filename2)) {
	$mail->AddAttachment('file-Unggah/potongan-lain/YYS-'.$q['nik'].'.xlsx'); // Gift Attachment
}
if(file_exists($filename3)) {
	$mail->AddAttachment('file-Unggah/bbc/BBC-'.$q['nik'].'.xlsx'); // Gift Attachment
}
if(file_exists($filename4)) {
	$mail->AddAttachment('file-Unggah/insentif/'.$q['nik'].'.xlsx'); // Gift Attachment
}

$mail->IsHTML(true); // send as HTML

$mail->Send();

/*$_SESSION['isi'] .= '
<tr>
<td>'.$no.'</td><td>'.$row['nik'].'</td><td>'.$row['nama'].'</td><td>Message has been sent</td>
</tr>
';*/


} catch (phpmailerException $e) {

echo $e->errorMessage();

}

if($numRow == $no){
	?>
    <tr>
    <td><?=$q['no_urut']?></td>
    <td><?=$q['nama']?></td>
    <td><?=$q['nik']?></td>
    <td><?=$q['ket_dep']?></td>
    </tr>
    <tr>
	<td colspan="4" height="50px" style="padding:10px">
    <a href="email.php?jml_data=<?=($data+$max)?>" title="Lanjutkan untuk memproses data slip gaji selanjutnya"><button type="button" name="proses">PROSES DATA SELANJUTNYA</button></a>
    </td>
    </tr>
    <?php
}else{
?>
<tr>
<td><?=$q['no_urut']?></td>
<td><?=$q['nama']?></td>
<td><?=$q['nik']?></td>
<td><?=$q['ket_dep']?></td>
</tr>
<?php	
}
$no++;
}
?>
</table>