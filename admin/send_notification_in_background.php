<?php
ini_set('memory_limit', '9999M');
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include "star_connection.php";
$notification_message = "notification_message";
$img_dir = "noty_images/";
$image_link_url = $server_url."admin/noty_images/";


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

function sentNotificationToAll($conn,$pno,$the_title,$the_message,$user_type,$selected_branch_user_id,$the_m_image_link,$noty_id,$total_sending_count){
$notification_message = "notification_message";
$te_master = "te_master";
$engineer_master = "engineer_master";
$te_code_arr = array();
if($pno!=''){
$limit = 50;
$start_from = (($pno-1)*$limit);
if($user_type=="ENGINEER"){
	$sql1 = "select `registration_id`,`device_type` from $engineer_master where `registration_id`!='' and `device_type`!='' order by `eid` asc limit $start_from,$limit";
}else if($user_type=="TE"){
	$sql1 = "select `registration_id`,`device_type` from $te_master where `registration_id`!='' and `device_type`!='' order by `te_id` asc limit $start_from,$limit";
}else if($user_type=="SINGLE_TE"){
	$sql1 = "select `registration_id`,`device_type` from $te_master where `te_code`='$selected_branch_user_id' and `registration_id`!='' and `device_type`!='' order by `te_id` asc limit $start_from,$limit";
}else if($user_type=="SINGLE_ENGINEER"){
	$sql1 = "select `registration_id`,`device_type` from $engineer_master where `eid`='$selected_branch_user_id' and `registration_id`!='' and `device_type`!='' order by `eid` asc limit $start_from,$limit";
}else if($user_type=="BRANCH_WISE_TE"){
	$sql1 = "select `registration_id`,`device_type` from $te_master where FIND_IN_SET('$selected_branch_user_id', `branch_code`) and `registration_id`!='' and `device_type`!='' order by `te_id` asc limit $start_from,$limit";
}else if($user_type=="BRANCH_WISE_ENGINEER"){
	$sql_get_te = "select `te_code` from $te_master where FIND_IN_SET('$selected_branch_user_id', `branch_code`)";
	$res_get_te = mysqli_query($conn,$sql_get_te);
	$totres_get_te = mysqli_num_rows($res_get_te);
	if($totres_get_te>0){
		while($row_get_te=mysqli_fetch_assoc($res_get_te)){
			$the_each_te = $row_get_te["te_code"] ? trim($row_get_te["te_code"]) : "";
			if($the_each_te!=""){
				$te_code_arr[] = $the_each_te; 
			}
		}
		if(count($te_code_arr)>0){
			$te_code_arr_str = implode("','",$te_code_arr);
		}
	}else{
		$te_code_arr_str = "";
	}
	
	$sql1 = "select `registration_id`,`device_type` from $engineer_master where `te_code` in('".$te_code_arr_str."') and `registration_id`!='' and `device_type`!='' order by `eid` asc limit $start_from,$limit";
}else{
	$sql1 = "(SELECT `device_id`,`registration_id`,`device_type` FROM $engineer_master where `registration_id`!='' and `device_type`!='') UNION (SELECT `device_id`,`registration_id`,`device_type` FROM $te_master where `registration_id`!='' and `device_type`!='') order by `device_id` asc limit $start_from,$limit";
}
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
$sent_done = array();
if($totres1>0){
$android_message = array();
$curr_timestamp = date('Y-m-d H:i:s');
$curr_date_time = date("Y-m-d H:i:s");
$nt_title = $the_title;
$payload = array();
$payload['team'] = 'India';
$payload['score'] = '5.6';
$app_noty_image = $the_m_image_link;	
$android_message['data'] = array("title"=>$nt_title,"is_background"=>FALSE,"message"=>$the_message,"image"=>$app_noty_image,"payload"=>$payload,"timestamp"=>$curr_timestamp);			
$body['aps'] = array("alert"=> array('body'=>$noty_msg),"sound"=>"default");

	while($row1=mysqli_fetch_assoc($res1)){
		$registration_id = $row1["registration_id"];
		$device_type = $row1["device_type"];
		if($device_type=="IOS"){
			$sent_sts_prod = pushNotificationInIOS("",$the_message,$the_title,$registration_id,$the_m_image_link);
			//$sent_sts_dev = pushNotificationInIOS("development",$the_message,$the_title,$registration_id,$the_m_image_link);
			if($sent_sts_prod=="TRUE"){
				$sent_done[]="TRUE";
			}
		}else if($device_type=="ANDROID"){	
			$sent_sts = send_push_notification_in_android($registration_id,$android_message);
			if($sent_sts=="TRUE"){
			$sent_done[]=$sent_sts;
			}			
		}
	}
$total_sent = count($sent_done);
$total_sending_count = ($total_sending_count + $total_sent);
if($noty_id!=''){
 $sql_noty_updt = "update $notification_message set `sending_count`='$total_sending_count' where `id`='$noty_id'";
$res_noty_updt = mysqli_query($conn,$sql_noty_updt);
}
$new_pno = ($pno+1);
sentNotificationToAll($conn,$new_pno,$the_title,$the_message,$user_type,$selected_branch_user_id,$the_m_image_link,$noty_id,$total_sending_count);	
}else{
	if($noty_id!=''){
	$sql_noty_updt = "update $notification_message set `status`='END' where `id`='$noty_id'";
	$res_noty_updt = mysqli_query($conn,$sql_noty_updt);
	}
}
	}

}	

$new_gen_noty_id_enc = $argv[1];
$noty_id_decrspt = base64_decode($new_gen_noty_id_enc);
//$noty_id_decrspt='5';
if($noty_id_decrspt!=""){
$sql1 = "select * from $notification_message where `id`='$noty_id_decrspt'";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	$row1=mysqli_fetch_assoc($res1);
	$title = $row1["title"];
	$message = $row1["message"];
	$file_type = $row1["file_type"] ? trim($row1["file_type"]) : "NONE";
	$image_name = $row1["image_name"] ? trim($row1["image_name"]) : "";
	if($file_type=="NONE" || $file_type=="PDF"){
		$m_image_link ="";
	}else{
	if($image_name!=""){
	if(file_exists($img_dir.$image_name)){
	$m_image_link = $image_link_url.$image_name;
	}else{
	$m_image_link ="";
	}
	}else{
	$m_image_link ="";
	}
	}
	echo $user_type = $row1["user_type"] ? strtoupper(trim($row1["user_type"])) : "ALL";
	$the_selected_branch_code = $row1["branch_code"] ? trim($row1["branch_code"]) : "";
	$the_single_user_id = $row1["single_user_id"] ? trim($row1["single_user_id"]) : "";
	if($user_type==""){
		$user_type = "ALL";
	}
	if($user_type=="BRANCH_WISE_TE" || $user_type=="BRANCH_WISE_ENGINEER"){
		$selected_branch_user_id = $the_selected_branch_code;
	}else if($user_type=="SINGLE_TE"){
		$selected_branch_user_id = $the_single_user_id;
	}else if($user_type=="SINGLE_ENGINEER"){
		$selected_branch_user_id = $the_single_user_id;
	}else{
		$selected_branch_user_id = "";
	}
	sentNotificationToAll($conn,1,$title,$message,$user_type,$selected_branch_user_id,$m_image_link,$noty_id_decrspt,0);
}

}
?>