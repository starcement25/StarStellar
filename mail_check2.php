<?php

function send_the_mail($to_email,$subject,$bodyml){
error_reporting(E_STRICT);
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
require_once('class.phpmailer.php');
require_once('class.smtp.php');
$sts = "FALSE";
$to_email_arr = array();
$to_email = $to_email ? trim($to_email) : "";
$subject = $subject ? trim($subject) : "";
$bodyml = $bodyml ? trim($bodyml) : "";
if($to_email!="" && $subject!="" && $bodyml!=""){
$to_email_arr = explode(",",$to_email);
if(count($to_email_arr)>0){
$mail             = new PHPMailer();
$bodyml             = $bodyml;
//$bodyml             = eregi_replace("[\]",'',$bodyml);
$mail->IsSMTP(); // telling the class to use SMTP
$mail->Host       = "mail.starcement.co.in"; // SMTP server (For gmail "mail.coral.in")
$mail->SMTPDebug  = "";                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
//$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
//$mail->Host       = "103.87.174.95";      // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
$mail->Host       = "96.45.76.75";       // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
$mail->Port       = 587;                   // set the SMTP port for the GMAIL server (For gmail 465 )
//$mail->Username   = "dev@starsaathi.com";  // GMAIL username
$mail->Username   = "starsaathi-starcement";
//$mail->Password   = "google3d33#";            // GMAIL password
$mail->Password   = "BVhf@_745hw";
//$mail->SetFrom('starsaathi@starcement.co.in', 'Starsaathi');
$mail->SetFrom('starsaathi@starcement.co.in', 'Starsaathi');
$mail->Subject    = $subject;
$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($bodyml);
foreach($to_email_arr as $to_email_arr_val){
	if(trim($to_email_arr_val)!=""){
	if (filter_var(trim($to_email_arr_val), FILTER_VALIDATE_EMAIL)) {
		$mail->AddAddress(trim($to_email_arr_val), $to_email_arr_val);
	}
	}
}
$mlsts = $mail->Send();
if(!$mlsts) {
  $sts = "FALSE";
} else {
 $sts = "TRUE";
}
}
}
return $sts;
}


$to_email = "suranjitd@coral.in,mriduj@coral.in";
$subject = "Test from Star Saathi credentials";
$bodyml = 'Test Body';
$mail_send_sts = send_the_mail($to_email,$subject,$bodyml);	

echo $mail_send_sts;

?>