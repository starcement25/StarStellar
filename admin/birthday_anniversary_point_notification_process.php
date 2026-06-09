<?php
error_reporting(E_STRICT);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");
include "star_connection.php";
function send_push_notification_in_android($registration_ids,$message) {
$apikey = "AAAAFS9z2u8:APA91bEy8andvCzKLsvpKm5SNvJmdOgkICMh3lLcVXDrPyNYXy84AUZdV5xfilvrrI8QbTub69I3Qub6mDZmXLNana0CnqaF8Y2hSxlg5Eaa6uK5ehiTLzyYGyJbkOsS1PYGSLzJp-OF";	
       
$url = 'https://fcm.googleapis.com/fcm/send';
$headers = array(
'Authorization: key='.$apikey,
'Content-Type: application/json'
);
$fields = array(
'to' => $registration_ids,
'data' => $message
);
	// Open connection
	$ch = curl_init(); 
	// Set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_URL, $url); 
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	// Disabling SSL Certificate support temporarly
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields)); 
	// Execute post
	$result = curl_exec($ch);	
	//print_r($result);
	if ($result === FALSE) {
	$result = '{"success":0,"failure":1}';
	} 
	// Close connection
	curl_close($ch);
	$result_str = json_decode($result,true);
	if($result_str["success"]==1){
	return "TRUE";
	}else{
	return "FALSE";
	}
    }

function base64($data){
return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
}
function pushNotificationInIOS($noti_mode,$message_body,$noty_title,$deviceToken,$noty_image){
$pnres = "FALSE";
$keyfile = 'AuthKey_A4PUG4TL3P.p8';               # <- Your AuthKey file
$keyid = 'A4PUG4TL3P';                            # <- Your Key ID
$teamid = 'LTCHGYC6BS';                           # <- Your Team ID (see Developer Portal)
$bundleid = 'com.forcepower.StarStellar';         # <- Your Bundle ID
if($noti_mode=="development"){
$url = 'https://api.development.push.apple.com';  # <- development url, or use http://api.push.apple.com for production environment
}else{
$url = 'https://api.push.apple.com';	
}
$arr_msg = array();
$arr_msg["aps"]["alert"] = array("title"=>$noty_title,"body"=>$message_body);
$arr_msg["aps"]["sound"] = "default";
$arr_msg["aps"]["badge"] = 1;
if($noty_image!=""){
$arr_msg['mediaUrl'] = $noty_image;
}
$message = json_encode($arr_msg);

$key = openssl_pkey_get_private('file://'.$keyfile);

$header["alg"] = "ES256";
$header["kid"] = $keyid;

$claims["iss"] = $teamid;
$claims["iat"] = time();

$header_encoded = base64($header);
$claims_encoded = base64($claims);

$signature = '';
openssl_sign($header_encoded . '.' . $claims_encoded, $signature, $key, 'sha256');
$jwt = $header_encoded . '.' . $claims_encoded . '.' . base64_encode($signature);

// only needed for PHP prior to 5.5.24
if (!defined('CURL_HTTP_VERSION_2_0')) {
define('CURL_HTTP_VERSION_2_0', 3);
}

$http2ch = curl_init();
curl_setopt_array($http2ch, array(
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
CURLOPT_URL => "$url/3/device/$deviceToken",
CURLOPT_PORT => 443,
CURLOPT_HTTPHEADER => array(
"apns-topic: {$bundleid}",
"authorization: bearer $jwt"
),
CURLOPT_POST => TRUE,
CURLOPT_POSTFIELDS => $message,
CURLOPT_RETURNTRANSFER => TRUE,
CURLOPT_TIMEOUT => 30,
CURLOPT_HEADER => 1
));

$result = curl_exec($http2ch);
if ($result === FALSE) {
//throw new Exception("Curl failed: ".curl_error($http2ch));
}else{
$pn_status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
if($pn_status==200){
$pnres = "TRUE";	
}
}


return $pnres;
  
}

$engineer_master = "engineer_master";
$setting_master = "setting_master";
$ledger_master = "ledger_master";
$res = array();

$app_setting_key_arr = array("birthday_point"=>0,"anniversary_point"=>0);
$sql_ckredius = "select `the_key_name`,`the_value` from $setting_master";
$res_ckredius = mysqli_query($conn,$sql_ckredius);
$totres_ckredius = mysqli_num_rows($res_ckredius);
if($totres_ckredius>0){
	while($row_ckredius=mysqli_fetch_assoc($res_ckredius)){
		$the_key_name = $row_ckredius["the_key_name"];
		$the_key_value = $row_ckredius["the_value"] ? trim($row_ckredius["the_value"]) : "";
		if($the_key_name!=""){
			$app_setting_key_arr[$the_key_name] = $the_key_value;
		}
	}
}

//$todays_date = date("Y-m-d");
//$todays_date = "2019-09-23";
$todays_month_day = date("m-d");
$r_submission_date = date("Y-m-d H:i:s");

$sqlckrds = "SELECT `eid`,`e_name`,`e_mobile`,`e_email`,`e_dob`,`e_dom`,`e_points`,`registration_id`,`device_type`,DATE_FORMAT(str_to_date(`e_dob`, '%Y-%m-%d' ), '%m-%d') as `e_dob_md`,DATE_FORMAT(str_to_date(`e_dom`, '%Y-%m-%d' ), '%m-%d') as `e_dom_md` FROM $engineer_master where ((`e_dob` is not null and `e_dob`!='') or (`e_dom` is not null and `e_dom`!='')) having (`e_dob_md`='".$todays_month_day."' or `e_dom_md`='".$todays_month_day."') and ((`e_dob_md` is not null and `e_dob_md`!='') or (`e_dom_md` is not null and `e_dom_md`!='')) order by `eid` asc";

$resckrds = mysqli_query($conn,$sqlckrds);
$totresckrds = mysqli_num_rows($resckrds);
if($totresckrds>0){
while($rowckrds=mysqli_fetch_assoc($resckrds)){
$eid=$rowckrds["eid"];
$e_name=$rowckrds["e_name"];
$e_mobile=$rowckrds["e_mobile"] ? trim($rowckrds["e_mobile"]) : "";
$e_email=$rowckrds["e_email"] ? trim($rowckrds["e_email"]) : "";
$e_dob=$rowckrds["e_dob"] ? trim($rowckrds["e_dob"]) : "";
$e_dom=$rowckrds["e_dom"] ? trim($rowckrds["e_dom"]) : "";


$e_dob_md=$rowckrds["e_dob_md"] ? trim($rowckrds["e_dob_md"]) : "";
$e_dom_md=$rowckrds["e_dom_md"] ? trim($rowckrds["e_dom_md"]) : "";

$e_points=$rowckrds["e_points"] ? trim($rowckrds["e_points"]) : 0;
if($e_points==""){
$e_points = 0;	
}
$e_points = intval($e_points);
$registration_id=$rowckrds["registration_id"] ? trim($rowckrds["registration_id"]) : "";
$device_type=$rowckrds["device_type"] ? trim($rowckrds["device_type"]) : "";
if($e_dob_md==$todays_month_day){
$add_this_point = intval($app_setting_key_arr["birthday_point"]);
$new_e_points = $e_points + $add_this_point;
$sqlinup = "update $engineer_master set `e_points`='$new_e_points' where `eid`='$eid'";
$resinup = mysqli_query($conn,$sqlinup);
$sqlldgrin = "insert into $ledger_master (`user_id`,`description`,`point_earned`,`ldgr_type`,`ldgr_datetime`) values ('$eid','Birthday Point','$add_this_point','BIRTHDAY_POINT','$r_submission_date')";
$resldgrin = mysqli_query($conn,$sqlldgrin);
if($registration_id!=""){
	
$curr_timestamp = date('Y-m-d H:i:s');
$curr_date_time = date("Y-m-d H:i:s");
$the_title = "Star Stellar";
$the_message = "Happy Birthday ".$e_name.".";
$payload = array();
$payload['team'] = 'India';
$payload['score'] = '5.6';
$app_noty_image = "";	
$android_message['data'] = array("title"=>$the_title,"is_background"=>FALSE,"message"=>$the_message,"image"=>$app_noty_image,"payload"=>$payload,"timestamp"=>$curr_timestamp);			
$body['aps'] = array("alert"=> array('body'=>$noty_msg),"sound"=>"default");
	
	if($device_type=="IOS"){
	$sent_sts_prod = pushNotificationInIOS("",$the_message,$the_title,$registration_id,$app_noty_image);
	//$sent_sts_dev = pushNotificationInIOS("development",$the_message,$the_title,$registration_id,$app_noty_image);
	
	}else if($device_type=="ANDROID"){
	$sent_sts = send_push_notification_in_android($registration_id,$android_message);
				
	}
}


}
if($e_dom_md==$todays_month_day){
$add_this_point = intval($app_setting_key_arr["anniversary_point"]);
$new_e_points = $e_points + $add_this_point;
$sqlinup = "update $engineer_master set `e_points`='$new_e_points' where `eid`='$eid'";
$resinup = mysqli_query($conn,$sqlinup);
$sqlldgrin = "insert into $ledger_master (`user_id`,`description`,`point_earned`,`ldgr_type`,`ldgr_datetime`) values ('$eid','Anniversary Point','$add_this_point','ANNIVERSARY_POINT','$r_submission_date')";
$resldgrin = mysqli_query($conn,$sqlldgrin);
if($registration_id!=""){
	
$curr_timestamp = date('Y-m-d H:i:s');
$curr_date_time = date("Y-m-d H:i:s");
$the_title = "Star Stellar";
$the_message = "Happy Marriage Anniversary ".$e_name.".";
$payload = array();
$payload['team'] = 'India';
$payload['score'] = '5.6';
$app_noty_image = "";	
$android_message['data'] = array("title"=>$the_title,"is_background"=>FALSE,"message"=>$the_message,"image"=>$app_noty_image,"payload"=>$payload,"timestamp"=>$curr_timestamp);			
$body['aps'] = array("alert"=> array('body'=>$noty_msg),"sound"=>"default");
	
	if($device_type=="IOS"){
	
	$sent_sts_prod = pushNotificationInIOS("",$the_message,$the_title,$registration_id,$app_noty_image);
	//$sent_sts_dev = pushNotificationInIOS("development",$the_message,$the_title,$registration_id,$app_noty_image);
	
	
	}else if($device_type=="ANDROID"){
		$sent_sts = send_push_notification_in_android($registration_id,$android_message);
				
	}
}

}



}
	
}
mysqli_close($conn);
echo "Done";
?>