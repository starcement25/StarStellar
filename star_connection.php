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

function show_product_data_by_id($conn, $pid)
{
	$product_master = "product_master";
	$prod_data = array("prod_name" => "", "point_per_bag" => 0);
	$pid = $pid ? trim($pid) : "";
	if ($pid != "") {
		$sql2p = "select * from $product_master where `prod_id`='$pid'";
		$res2p = mysqli_query($conn, $sql2p);
		$tot_res2p = mysqli_num_rows($res2p);
		if ($tot_res2p > 0) {
			$row2p = mysqli_fetch_assoc($res2p);
			$pname = addslashes(trim($row2p["prod_name"]));
			$pper_bag = $row2p["point_per_bag"] ? addslashes(trim($row2p["point_per_bag"])) : 0;
			$more_than_bags = $row2p["more_than_bags"] ? addslashes(trim($row2p["more_than_bags"])) : 0;
			$bonus_points = $row2p["bonus_points"] ? addslashes(trim($row2p["bonus_points"])) : 0;
			if ($pper_bag == "") {
				$pper_bag = 0;
			}
			$prod_data = array("prod_name" => $pname, "point_per_bag" => $pper_bag, "more_than_bags" => $more_than_bags, "bonus_points" => $bonus_points);
		}
	}

	return $prod_data;
}

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
//$to_email=$to_email.','.'anupshaw@starcement.co.in,manishranjan@starcement.co.in,abhishekd@forcepower.in';
$to_email=$to_email.','.'anupshaw@starcement.co.in';	
$to_email_arr = explode(",",$to_email);
if(count($to_email_arr)>0){
$mail             = new PHPMailer();
$bodyml             = $bodyml;
//$bodyml             = eregi_replace("[\]",'',$bodyml);
$mail->IsSMTP(); // telling the class to use SMTP
$mail->SMTPDebug  = "";                     // enables SMTP debug information (for testing)
                                           // 1 = errors and messages
                                           // 2 = messages only
$mail->SMTPAuth   = true;                  // enable SMTP authentication
// $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
$mail->SMTPSecure = false; // Explicit TLS encryption is not used
                $mail->SMTPAutoTLS = false; // Disable automatic TLS upgrade
// $mail->Host       = "smtp.gmail.com";
$mail->Host       = "cloudmail2.up99plus.com";
// $mail->Port       = 465;
$mail->Port       = 25;
// $mail->Username   = "starstellar@starcement.co.in";
$mail->Username   = "starcement@cloudmail.up99plus.com";
//$mail->Password   = "Starstellar@2023";	
// $mail->Password   = "Star@2024";	
$mail->Password   = "K2TTvLxATyULV2um";	
$mail->SetFrom('starcement@cloudmail.up99plus.com', 'Starstellar');
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

function sendSMSNotification($mobile, $message)
{
    $url = 'https://http.myvfirst.com/smpp/sendsms';
    $fields = array(
        'username' => 'starhttpdealers',
        'password' => 'star1109',
        'to' => $mobile,
        'from' => 'STARCM',
        'text' => $message,
        'dlr-mask' => '19',
        'dlr' => 'url'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    if ($result === FALSE) {
        return false;
    }
    curl_close($ch);
    return true;
}

?>