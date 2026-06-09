<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set("Asia/Kolkata");
$servername = "192.200.20.154";
$username = "root";
$password = "m7BuSvxTeuxE12mx";
$db_name = "starsaat_STELLAR";

$conn = mysqli_connect($servername, $username, $password,$db_name);

if (mysqli_connect_errno()){
die("Failed to connect to MySQL: " . mysqli_connect_error());
}
mysqli_set_charset($conn,"UTF8");

$server_url = "https://" . $_SERVER['SERVER_NAME']."/";
$home_url = $server_url;

function get_value_by_setting_key($conn,$keyname){
$keyvalue = "";
$setting_master = "setting_master";
if($keyname!=""){
$sql_gapc = "select `the_value` from $setting_master where `the_key_name`='$keyname'";
$res_gapc = mysqli_query($conn,$sql_gapc);
$totres_gapc = mysqli_num_rows($res_gapc);
if($totres_gapc>0){
$row_gapc=mysqli_fetch_assoc($res_gapc);
$keyvalue = trim($row_gapc["the_value"]);
}
}
return $keyvalue; 
}

function get_te_branchcode_by_tecode($conn,$tecode){
$te_branchcode = "";
$te_master = "te_master";
if($tecode!=""){
$sql_gapc = "select `branch_code` from $te_master where `te_code`='$tecode'";
$res_gapc = mysqli_query($conn,$sql_gapc);
$totres_gapc = mysqli_num_rows($res_gapc);
if($totres_gapc>0){
$row_gapc=mysqli_fetch_assoc($res_gapc);
$te_branchcode = trim($row_gapc["branch_code"]);
}
}
return $te_branchcode; 
}

function get_asm_data_by_id($conn,$aid){
$asm_master = "asm_master";
$asm_data = array("sts"=>"NO","asm_name"=>"","ph_no"=>"","email"=>"","branch_code"=>"","branch"=>"");
$aid = $aid ? trim($aid) : "";
if($aid!=""){
	$sql2p = "select * from $asm_master where `asm_id`='$aid'";
	$res2p = mysqli_query($conn,$sql2p);
	$tot_res2p = mysqli_num_rows($res2p);
	if($tot_res2p>0){
		$row2p = mysqli_fetch_assoc($res2p);
		$asm_name = trim($row2p["asm_name"]);
		$ph_no = trim($row2p["ph_no"]);
		$email = trim($row2p["email"]);
		$branch_code = trim($row2p["branch_code"]);
		$branch = trim($row2p["branch"]);
		
		$asm_data = array("sts"=>"YES","asm_name"=>$asm_name,"ph_no"=>$ph_no,"email"=>$email,"branch_code"=>$branch_code,"branch"=>$branch);
	}
}

return $asm_data;
}

function show_product_data_by_id($conn,$pid){
$product_master = "product_master";
$prod_data = array("prod_name"=>"","point_per_bag"=>0);
$pid = $pid ? trim($pid) : "";
if($pid!=""){
	$sql2p = "select `prod_name`,`point_per_bag` from $product_master where `prod_id`='$pid'";
	$res2p = mysqli_query($conn,$sql2p);
	$tot_res2p = mysqli_num_rows($res2p);
	if($tot_res2p>0){
		$row2p = mysqli_fetch_assoc($res2p);
		$pname = addslashes(trim($row2p["prod_name"]));
		$pper_bag = $row2p["point_per_bag"] ? addslashes(trim($row2p["point_per_bag"])) : 0;
		if($pper_bag==""){
		$pper_bag = 0;	
		}
		$prod_data = array("prod_name"=>$pname,"point_per_bag"=>$pper_bag);
	}
}

return $prod_data;
}

function olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,$page_qury_stn_ky_nm,$filtered_query_string)
{
$pagination = "";
if($page_qury_stn_ky_nm!="page"){
$page_query_string_name = $page_qury_stn_ky_nm;
	if($lastpage > 1)
	{
		$pagination .= "<div class=\"pagination\">";
		//previous button
		if ($page > 1) 
			$pagination.= "<a href=\"$targetpage?$page_query_string_name=$prev$filtered_query_string\">PREV</a>";
		else
			$pagination.= "<span class=\"disabled_pg\">PREV</span>";
		//pages	
		if ($lastpage < 7 + ($adjacents * 2))	//not enough pages to bother breaking it up
		{
			for ($counter = 1; $counter <= $lastpage; $counter++)
			{
				if ($counter == $page)
					$pagination.= "<span class=\"current_pg\">$counter</span>";
				else
					$pagination.= "<a href=\"$targetpage?$page_query_string_name=$counter$filtered_query_string\">$counter</a>";
			}
		}
		elseif($lastpage > 5 + ($adjacents * 2))	//enough pages to hide some
		{
			//close to beginning; only hide later pages
			if($page < 1 + ($adjacents * 2))
			{
				for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current_pg\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?$page_query_string_name=$counter$filtered_query_string\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=$lpm1$filtered_query_string\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=$lastpage$filtered_query_string\">$lastpage</a>";
			}
			//in middle; hide some front and some back
			elseif($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2))
			{
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=1$filtered_query_string\">1</a>";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=2$filtered_query_string\">2</a>";
				$pagination.= "...";
				for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current_pg\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?$page_query_string_name=$counter$filtered_query_string\">$counter</a>";					
				}
				$pagination.= "...";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=$lpm1$filtered_query_string\">$lpm1</a>";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=$lastpage$filtered_query_string\">$lastpage</a>";	
			}
			//close to end; only hide early pages
			else
			{
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=1$filtered_query_string\">1</a>";
				$pagination.= "<a href=\"$targetpage?$page_query_string_name=2$filtered_query_string\">2</a>";
				$pagination.= "...";
				for ($counter = $lastpage - (2 + ($adjacents * 2)); $counter <= $lastpage; $counter++)
				{
					if ($counter == $page)
						$pagination.= "<span class=\"current_pg\">$counter</span>";
					else
						$pagination.= "<a href=\"$targetpage?$page_query_string_name=$counter$filtered_query_string\">$counter</a>";					
				}
			}
		}
		//next button
		if ($page < $counter - 1)
			$pagination.= "<a href=\"$targetpage?$page_query_string_name=$next$filtered_query_string\">NEXT</a>";
		else
			$pagination.= "<span class=\"disabled_pg\">NEXT</span>";
		$pagination.= "</div>\n";
	}
}else{
	$pagination.="Dont use query string key=page";
}
return $pagination;
}
//commend on 27-01-26

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
function send_the_mail($to_email, $subject, $bodyml){
    //echo"<pre>";print_r($bodyml);die;
error_reporting(E_STRICT);
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
require __DIR__.'/phpmailer_new/PHPMailer.php';
require __DIR__.'/phpmailer_new/SMTP.php';
require __DIR__.'/phpmailer_new/Exception.php';
    
    $sts = "FALSE";
    $to_email_arr = array();
    $to_email = $to_email ? trim($to_email) : "";
    $subject = $subject ? trim($subject) : "";
    $bodyml = $bodyml ? trim($bodyml) : "";
    if ($to_email != "" && $subject != "" && $bodyml != "") {
        $to_email_arr = explode(",", $to_email);
        if (count($to_email_arr) > 0) {
$mail = new PHPMailer(true);
try {
    // SMTP configuration
    $mail->isSMTP();
    //$mail->SMTPDebug  = 2;  
    $mail->Host = "cloudmail2.up99plus.com";
    $mail->Port = 25;
    $mail->SMTPAuth = true;
    $mail->Username = "starcement@cloudmail.up99plus.com";
    $mail->Password = "K2TTvLxATyULV2um"; // Insert your actual password here
    $mail->SMTPSecure = false; // Explicit TLS encryption is not used
    $mail->SMTPAutoTLS = false; // Disable automatic TLS upgrade
    // Sender information
    $mail->setFrom('starcement@cloudmail.up99plus.com', 'Starstellar');
    // Recipient
	foreach ($to_email_arr as $to_email_arr_val) {
	if (trim($to_email_arr_val) != "") {
	if (filter_var(trim($to_email_arr_val), FILTER_VALIDATE_EMAIL)) {
	$mail->addAddress(trim($to_email_arr_val), $to_email_arr_val);
	}
	}
	}
			
    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    //$mail->Body = $bodyml;
	$mail->MsgHTML($bodyml);
    $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
    // Send the email
   $mlsts =  $mail->send();    
//    echo"<pre>";print_r($mlsts);die;
    $sts = "TRUE";
} catch (Exception $e) {
    $sts = "FALSE";
}

        }
    }
    return $sts;
}
/*
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
$mail->SMTPDebug  = "1";                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
$mail->Host       = "smtp.gmail.com";       // sets GMAIL as the SMTP server (For gmail "mail.coral.in")
$mail->Port       = 465;                   // set the SMTP port for the GMAIL server (For gmail 465 )
$mail->Username   = "starstellar@starcement.co.in";
$mail->Password   = "Star@2024";
$mail->SetFrom('starstellar@starcement.co.in', 'Starstellar');
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
*/

function send_the_mail_backup($to_email,$subject,$bodyml){

	date_default_timezone_set('America/Toronto');

	require_once('class.phpmailer.php');
	//include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
	
	$mail             = new PHPMailer();
	
	$body             = $bodyml;
	//$body             = eregi_replace("[\]",'',$body);
	
	$mail->IsSMTP(); // telling the class to use SMTP
	//$mail->Host       = "ssl://smtp.gmail.com"; // SMTP server
	$mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
											   // 1 = errors and messages
											   // 2 = messages only
	$mail->SMTPAuth   = true;                  // enable SMTP authentication
	$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
	$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
	$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
	
	$mail->Username   = "starstellar@starcement.co.in";
	$mail->Password   = "Star@2024";
	$mail->SetFrom('starstellar@starcement.co.in', 'Starstellar');
	$mail->Subject    = $subject;
	
	//$mail->AddReplyTo("user2@gmail.com', 'First Last");
	
	$mail->Subject    = "Test email";
	
	//$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
	
	$mail->MsgHTML($body);
	
	
	$mail->AddAddress($to_email, $to_email);
	
	//$mail->AddAttachment("images/phpmailer.gif");      // attachment
	//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
	
	if(!$mail->Send()) {
	  echo "Mailer Error: " . $mail->ErrorInfo;
	} else {
	  echo "Message sent!";
	}
	

}


?>