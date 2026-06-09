<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include "star_connection.php";
include 'APNSBase.php';
include 'APNotification.php';
include 'APFeedback.php';

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

function pushNotificationInIOS_custom_development($deviceToken,$noty_title,$noty_message,$noty_image){
	$today_date = date("Y_m_d");
	$notification2 = new APNotification('development');
	$notification2->setDeviceToken($deviceToken);
	$notification2->setTitle($noty_title);
	$notification2->setBadge(1);
	$notification2->setMessage($noty_message);
	if($noty_image!=""){
		$notification2->setImageUrl($noty_image);
	}
	$notification2->setSound("default");
	$notification2->setPrivateKey('admin/starstellar_dev.pem');
	$notification2->setPrivateKeyPassphrase('C0rali0s');
	$res = $notification2->send();
	$notification2 = NULL;
    if($res){
		return "TRUE";
	}else{
		return "FALSE";
	}    
}

function pushNotificationInIOS_custom_production($deviceToken,$noty_title,$noty_message,$noty_image){
	$today_date = date("Y_m_d");
	$notification2 = new APNotification('production');
	$notification2->setDeviceToken($deviceToken);
	$notification2->setTitle($noty_title);
	$notification2->setBadge(1);
	$notification2->setMessage($noty_message);
	if($noty_image!=""){
		$notification2->setImageUrl($noty_image);
	}
	$notification2->setSound("default");
	$notification2->setPrivateKey('admin/starstellar_dis.pem');
	$notification2->setPrivateKeyPassphrase('C0rali0s');
	$res = $notification2->send();
	$notification2 = NULL;
    if($res){
		return "TRUE";
	}else{
		return "FALSE";
	}    
}

$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$setting_master = "setting_master";
$ledger_master = "ledger_master";
$res_msg = array();
$rtaid = $_POST["rtaid"] ? trim($_POST["rtaid"]) : "";
if($rtaid!=''){
	$sql5 = "select `r_engineer_id` from $recommended_site_master where `r_site_id`='$rtaid'";
	$res5 = mysqli_query($conn,$sql5);
	$tot_res5 = mysqli_num_rows($res5);
	if($tot_res5>0){
	$row5 = mysqli_fetch_assoc($res5);
	$r_engineer_id=$row5["r_engineer_id"] ? trim($row5["r_engineer_id"]) : "";
	if($r_engineer_id!=""){
		
	$sqlckrds = "select `the_value` from $setting_master where `the_key_name`='site_approved_point'";
	$resckrds = mysqli_query($conn,$sqlckrds);
	$totresckrds = mysqli_num_rows($resckrds);
	if($totresckrds>0){
	$rowckrds = mysqli_fetch_assoc($resckrds);
	$confirm_recomendation_point = $rowckrds["the_value"] ? trim($rowckrds["the_value"]) : 0;
	if($confirm_recomendation_point==""){
	$confirm_recomendation_point = 0;
	}
	}else{
	$confirm_recomendation_point = 0;	
	}
	
	$r_submission_date = date("Y-m-d H:i:s");
	$r_te_interaction_date = date("Y-m-d");
	$sql4 = "update $recommended_site_master set `r_status`='APPROVED',`r_te_interaction_comment`='Approved by admin',`r_te_interaction_date`='$r_te_interaction_date',`r_point_earned_by_engineer`='$confirm_recomendation_point',`r_last_updated_datetime`='$r_submission_date' where `r_site_id`='$rtaid'";
	$res4 = mysqli_query($conn,$sql4);
	
	$sql2e = "select `eid`,`e_points`,`registration_id`,`device_type` from $engineer_master where `eid`='$r_engineer_id'";
	$res2e = mysqli_query($conn,$sql2e);
	$tot_res2e = mysqli_num_rows($res2e);
	if($tot_res2e>0){
	$row2e = mysqli_fetch_assoc($res2e);
	$device_type = $row2e["device_type"] ? trim($row2e["device_type"]) : "";
	$registration_id = $row2e["registration_id"] ? trim($row2e["registration_id"]) : "";
	$e_points = $row2e["e_points"] ? trim($row2e["e_points"]) : 0;
	if($e_points==""){
	$e_points = 0;	
	}
	$e_points = intval($e_points);
	
	$new_e_points = $e_points + $confirm_recomendation_point;
	$sqlinup = "update $engineer_master set `e_points`='$new_e_points' where `eid`='$r_engineer_id'";
	$resinup = mysqli_query($conn,$sqlinup);
	$sqlldgrin = "insert into $ledger_master (`user_id`,`description`,`point_earned`,`ldgr_type`,`related_id`,`ldgr_datetime`) values ('$r_engineer_id','Site Recomendation','$confirm_recomendation_point','SITE_RECOMENDATION','$rtaid','$r_submission_date')";
	$resldgrin = mysqli_query($conn,$sqlldgrin);
	
	
	if($registration_id!=""){
	
$curr_timestamp = date('Y-m-d H:i:s');
$curr_date_time = date("Y-m-d H:i:s");
$the_title = "Star Stellar";
$the_message = "Your recommended site has been confirmed.";
$payload = array();
$payload['team'] = 'India';
$payload['score'] = '5.6';
$app_noty_image = "";	
$android_message['data'] = array("title"=>$the_title,"is_background"=>FALSE,"message"=>$the_message,"image"=>$app_noty_image,"payload"=>$payload,"timestamp"=>$curr_timestamp);			
$body['aps'] = array("alert"=> array('body'=>$noty_msg),"sound"=>"default");
	
	if($device_type=="IOS"){
	$sent_sts_prod = pushNotificationInIOS_custom_production($registration_id,$the_title,$the_message,$app_noty_image);
	$sent_sts_dev = pushNotificationInIOS_custom_development($registration_id,$the_title,$the_message,$app_noty_image);
	
	}else if($device_type=="ANDROID"){			
	$sent_sts = send_push_notification_in_android($registration_id,$android_message);
				
	}
}
	
	
	}
	
	$res_msg = array("process_sts"=>"YES","process_msg"=>"Success","curr_sts"=>"APPROVED","curr_point"=>$confirm_recomendation_point." PT");
	}else{
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Engineer details not exist in this recomended site.");
	}
	}else{
	$res_msg = array("process_sts"=>"NO","process_msg"=>"The site details not exist.");
	}
	
}else{
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");
}
echo json_encode($res_msg);
mysqli_close($conn);
?>