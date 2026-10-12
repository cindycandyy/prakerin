<?php
/**
* Simple example script using PHPMailer with exceptions enabled
* @package phpmailer
* @version $Id$
*/
require '../class.phpmailer.php';

try {
	$mail = new PHPMailer(true); //New instance, with exceptions enabled

	$body             = file_get_contents('contents.html');
	$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

	$mail->IsSMTP();                           // tell the class to use SMTP
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
    $mail->SMTPSecure = 'tls';
	$mail->Port       = 587;                    // set the SMTP server port
	$mail->Host       = "smtp.gmail.com"; // SMTP server
	$mail->Username   = "webmaster@dp-pertamina.com";     // SMTP server username
	$mail->Password   = "satuhati";            // SMTP server password

	//$mail->IsSendmail();  // tell the class to use Sendmail
	$mail->AddReplyTo("webmaster@dp-pertamina.com","Dana Pensiun Pertamina");
	$mail->From       = "webmaster@dp-pertamina.com";
	$mail->FromName   = "Dana Pensiun Pertamina";

	$to = "roy@dp-pertamina.com";

	$mail->AddAddress($to);

	$mail->Subject  = "Selamat Ulang Tahun";

	//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	//$mail->WordWrap   = 80; // set word wrap

	$mail->MsgHTML($body);

	$mail->IsHTML(true); // send as HTML

	$mail->Send();
	echo 'Message has been sent.';
} catch (phpmailerException $e) {
	echo $e->errorMessage();
}
?>