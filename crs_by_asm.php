<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include "star_connection.php";
include 'admin/APNSBase.php';
include 'admin/APNotification.php';
include 'admin/APFeedback.php';

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

$te_master = "te_master";
$engineer_master = "engineer_master";
$ledger_master = "ledger_master";
$recommended_site_master = "recommended_site_master";
$recommended_site_asm_activity_log = "recommended_site_asm_activity_log";
$setting_master = "setting_master";
$product_master = "product_master";

$show_the_message = "";

$each_verified_site = get_value_by_setting_key($conn,"each_verified_site");
if($each_verified_site==""){
$each_verified_site = 0;
}


$pending_recommendation_data = array();
$upload_dir = "recomend_site_pic/";
$image_url = $home_url."recomend_site_pic/";
$tot_res2 = 0;
$r_site_id = $_GET["r_site_id"] ? addslashes(trim($_GET["r_site_id"])) : "";
$te_code = $_GET["te_code"] ? addslashes(trim($_GET["te_code"])) : "";
$asm_status = $_GET["asm_status"] ? addslashes(trim($_GET["asm_status"])) : ""; /*--APPROVE/REJECT--*/

if($r_site_id!="" && $te_code!="" && $asm_status!=""){
$sql2 = "select * from $recommended_site_master where `r_te_code`='$te_code' and `r_site_id`='$r_site_id'";
$res2 = mysqli_query($conn,$sql2);
$tot_res2 = mysqli_num_rows($res2);
if($tot_res2>0){
$row2 = mysqli_fetch_assoc($res2);
$r_status = $row2["r_status"];
$existing_id2 = $row2["existing_id"] ? trim($row2["existing_id"]) : "";

if($r_status=="PENDING"){

$r_engineer_id = $row2["r_engineer_id"] ? trim($row2["r_engineer_id"]) : "";
$actual_product_id = $row2["actual_product_id"] ? trim($row2["actual_product_id"]) : "";
$actual_consumption = $row2["actual_consumption"] ? trim($row2["actual_consumption"]) : "";
	
$r_submission_date = date("Y-m-d H:i:s");
$r_te_interaction_date = date("Y-m-d");
if($asm_status=="APPROVE"){
$product_data_arr = show_product_data_by_id($conn,$actual_product_id);
$actual_product_name = $product_data_arr["prod_name"];
$confirm_recomendation_point = $product_data_arr["point_per_bag"];
if($existing_id2!=""){
$each_verified_site = 0;	
}
//$actual_recomendation_point = ($confirm_recomendation_point * $actual_consumption) + $each_verified_site;
$actual_recomendation_point = ($confirm_recomendation_point * $actual_consumption);
$sql4 = "update $recommended_site_master set `r_status`='APPROVED',`r_te_interaction_comment`='Approved by ASM',`approval_message`='Approved by ASM',`r_te_interaction_date`='$r_te_interaction_date',`r_point_earned_by_engineer`='$actual_recomendation_point',`r_last_updated_datetime`='$r_submission_date' where `r_te_code`='$te_code' and `r_site_id`='$r_site_id'";
$res4 = mysqli_query($conn,$sql4);

$sql_rs = "select * from $recommended_site_master where `r_te_code`='$te_code' and `r_site_id`='$r_site_id'";
$res_rs = mysqli_query($conn,$sql_rs);
$row_rs = mysqli_fetch_assoc($res_rs);
$r_site_name = $row_rs["r_site_name"] ? trim($row_rs["r_site_name"]) : "";
$existing_id = $row_rs["existing_id"] ? trim($row_rs["existing_id"]) : "";
$r_contact_person_name = $row_rs["r_contact_person_name"] ? trim($row_rs["r_contact_person_name"]) : "";
$r_mobile_no = $row_rs["r_mobile_no"] ? trim($row_rs["r_mobile_no"]) : "";
$r_address = $row_rs["r_address"] ? trim($row_rs["r_address"]) : "";
$r_site_potential_in_mt = $row_rs["r_site_potential_in_mt"] ? trim($row_rs["r_site_potential_in_mt"]) : "";
$r_contact_person_category_name = $row_rs["r_contact_person_category_name"] ? trim($row_rs["r_contact_person_category_name"]) : "";
$r_recomended_site_image = $row_rs["r_recomended_site_image"] ? trim($row_rs["r_recomended_site_image"]) : "";
$expected_product_id = $row_rs["expected_product_id"] ? trim($row_rs["expected_product_id"]) : "";
$expected_product_name = $row_rs["expected_product_name"] ? trim($row_rs["expected_product_name"]) : "";
$expected_consumption = $row_rs["expected_consumption"] ? trim($row_rs["expected_consumption"]) : "";
$r_status = $row_rs["r_status"] ? trim($row_rs["r_status"]) : "";
$r_submission_date = $row_rs["r_submission_date"] ? trim($row_rs["r_submission_date"]) : "";
$r_te_interaction_date = $row_rs["r_te_interaction_date"] ? trim($row_rs["r_te_interaction_date"]) : "";
$actual_product_id = $row_rs["actual_product_id"] ? trim($row_rs["actual_product_id"]) : "";
$actual_product_name = $row_rs["actual_product_name"] ? trim($row_rs["actual_product_name"]) : "";
$actual_consumption = $row_rs["actual_consumption"] ? trim($row_rs["actual_consumption"]) : "";

$r_site_verification_image = $row_rs["r_site_verification_image"] ? trim($row_rs["r_site_verification_image"]) : "";


$is_mail_sent_to_asm = $row_rs["is_mail_sent_to_asm"] ? trim($row_rs["is_mail_sent_to_asm"]) : "";
$approval_message = $row_rs["approval_message"] ? trim($row_rs["approval_message"]) : "";
$purchased_from = $row_rs["purchased_from"] ? trim($row_rs["purchased_from"]) : "";
$purchased_from_name = $row_rs["purchased_from_name"] ? trim($row_rs["purchased_from_name"]) : "";
$purchased_from_area = $row_rs["purchased_from_area"] ? trim($row_rs["purchased_from_area"]) : "";
$purchased_from_contact_no = $row_rs["purchased_from_contact_no"] ? trim($row_rs["purchased_from_contact_no"]) : "";
$r_te_interaction_comment = $row_rs["r_te_interaction_comment"] ? trim($row_rs["r_te_interaction_comment"]) : "";
$r_point_earned_by_engineer = $row_rs["r_point_earned_by_engineer"] ? trim($row_rs["r_point_earned_by_engineer"]) : "";
$r_last_updated_datetime = $row_rs["r_last_updated_datetime"] ? trim($row_rs["r_last_updated_datetime"]) : "";

$r_asm_id = $row_rs["r_asm_id"] ? trim($row_rs["r_asm_id"]) : "";
$r_asm_name = $row_rs["r_asm_name"] ? trim($row_rs["r_asm_name"]) : "";
$r_asm_email = $row_rs["r_asm_email"] ? trim($row_rs["r_asm_email"]) : "";
$r_asm_ph_no = $row_rs["r_asm_ph_no"] ? trim($row_rs["r_asm_ph_no"]) : "";
$r_asm_branch = $row_rs["r_asm_branch"] ? trim($row_rs["r_asm_branch"]) : "";




$sql_rsl_in = "insert into $recommended_site_asm_activity_log (`r_site_id`,`r_te_code`,`r_engineer_id`,`r_site_name`,`existing_id`,`r_contact_person_name`,`r_mobile_no`,`r_address`,`r_site_potential_in_mt`,`r_contact_person_category_name`,`r_recomended_site_image`,`expected_product_id`,`expected_product_name`,`expected_consumption`,`r_status`,`r_submission_date`,`r_te_interaction_date`,`actual_product_id`,`actual_product_name`,`actual_consumption`,`r_site_verification_image`,`is_mail_sent_to_asm`,`approval_message`,`purchased_from`,`purchased_from_name`,`purchased_from_area`,`purchased_from_contact_no`,`r_te_interaction_comment`,`r_point_earned_by_engineer`,`r_last_updated_datetime`,`r_asm_id`,`r_asm_name`,`r_asm_email`,`r_asm_ph_no`,`r_asm_branch`) values ('$r_site_id','$te_code','$r_engineer_id','$r_site_name','$existing_id','$r_contact_person_name','$r_mobile_no','$r_address','$r_site_potential_in_mt','$r_contact_person_category_name','$r_recomended_site_image','$expected_product_id','$expected_product_name','$expected_consumption','$r_status','$r_submission_date','$r_te_interaction_date','$actual_product_id','$actual_product_name','$actual_consumption','$r_site_verification_image','$is_mail_sent_to_asm','$approval_message','$purchased_from','$purchased_from_name','$purchased_from_area','$purchased_from_contact_no','$r_te_interaction_comment','$r_point_earned_by_engineer','$r_last_updated_datetime','$r_asm_id','$r_asm_name','$r_asm_email','$r_asm_ph_no','$r_asm_branch')";
$res_rsl_in = mysqli_query($conn,$sql_rsl_in);


if($r_engineer_id!=""){

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

if($existing_id!=""){
$each_verified_site = 0;	
}

$new_e_points = ($e_points + $actual_recomendation_point + $each_verified_site);
$sqlinup = "update $engineer_master set `e_points`='$new_e_points' where `eid`='$r_engineer_id'";
$resinup = mysqli_query($conn,$sqlinup);
$sqlldgrin = "insert into $ledger_master (`user_id`,`description`,`point_earned`,`ldgr_type`,`related_id`,`ldgr_datetime`) values ('$r_engineer_id','Site Recomendation(Approved by ASM)','$actual_recomendation_point','SITE_RECOMENDATION','$r_site_id','$r_submission_date')";
$resldgrin = mysqli_query($conn,$sqlldgrin);

if($existing_id!=""){
	
}else{
$sqlldgrin = "insert into $ledger_master (`user_id`,`description`,`point_earned`,`ldgr_type`,`related_id`,`ldgr_datetime`) values ('$r_engineer_id','Verified Recomendation Site(Approved by ASM)','$each_verified_site','SITE_RECOMENDATION','$r_site_id','$r_submission_date')";
$resldgrin = mysqli_query($conn,$sqlldgrin);
}

if($registration_id!=""){
	
$curr_timestamp = date('Y-m-d H:i:s');
$curr_date_time = date("Y-m-d H:i:s");
$the_title = "Star Stellar";
$the_message = "Your recomended site has been confirmed.";
$payload = array();
$payload['team'] = 'India';
$payload['score'] = '5.6';
$app_noty_image = "";	
$android_message['data'] = array("title"=>$the_title,"is_background"=>FALSE,"message"=>$the_message,"image"=>$app_noty_image,"payload"=>$payload,"timestamp"=>$curr_timestamp);			
$body['aps'] = array("alert"=> array('body'=>$noty_msg),"sound"=>"default");
	
	if($device_type=="IOS"){
	//$sent_sts_prod = pushNotificationInIOS_custom_production($registration_id,$the_title,$the_message,$app_noty_image);
	//$sent_sts_dev = pushNotificationInIOS_custom_development($registration_id,$the_title,$the_message,$app_noty_image);
	
	}else if($device_type=="ANDROID"){			
	$sent_sts = send_push_notification_in_android($registration_id,$android_message);
				
	}
}

}

}	
$show_the_message = "This site is successfully updated.";
}else if($asm_status=="REJECT"){
$sql4 = "update $recommended_site_master set `r_status`='REJECTED',`approval_message`='Rejected by ASM',`r_te_interaction_comment`='Rejected by ASM',`r_te_interaction_date`='$r_te_interaction_date',`r_last_updated_datetime`='$r_submission_date' where `r_te_code`='$te_code' and `r_site_id`='$r_site_id'";
$res4 = mysqli_query($conn,$sql4);
$show_the_message = "This site is successfully rejected.";
}else{
$show_the_message = "Something went wrong.";
}


}else{
$show_the_message = "This site's current status is ".$r_status;	
}

}else{
$show_the_message = "No site record found.";		
}
	
	
}else{
$show_the_message = "Something went wrong.";	
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CONFIRM RECOMMENDER SITE BY ASM</title>

<link rel="stylesheet" href="css/bootstrap.min.css">
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<style>

</style>
</head>

<body>

<div class="container-fluid">
<h3 style="margin-top:40px;text-align:center;">
<?php
if($show_the_message!=""){
echo $show_the_message;	
}
?>
</h3>
</div>

</body>
</html>
<?php
mysqli_close($conn);
?>
