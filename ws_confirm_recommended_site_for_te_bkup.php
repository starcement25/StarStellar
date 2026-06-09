<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include "star_connection.php";
include 'admin/APNSBase.php';
include 'admin/APNotification.php';
include 'admin/APFeedback.php';

function send_push_notification_in_android($registration_ids, $message)
{
	$apikey = "AAAAFS9z2u8:APA91bEy8andvCzKLsvpKm5SNvJmdOgkICMh3lLcVXDrPyNYXy84AUZdV5xfilvrrI8QbTub69I3Qub6mDZmXLNana0CnqaF8Y2hSxlg5Eaa6uK5ehiTLzyYGyJbkOsS1PYGSLzJp-OF";

	$url = 'https://fcm.googleapis.com/fcm/send';
	$headers = array(
		'Authorization: key=' . $apikey,
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
	$result_str = json_decode($result, true);
	if ($result_str["success"] == 1) {
		return "TRUE";
	} else {
		return "FALSE";
	}
}

function pushNotificationInIOS_custom_development($deviceToken, $noty_title, $noty_message, $noty_image)
{
	$today_date = date("Y_m_d");
	$notification2 = new APNotification('development');
	$notification2->setDeviceToken($deviceToken);
	$notification2->setTitle($noty_title);
	$notification2->setBadge(1);
	$notification2->setMessage($noty_message);
	if ($noty_image != "") {
		$notification2->setImageUrl($noty_image);
	}
	$notification2->setSound("default");
	$notification2->setPrivateKey('admin/starstellar_dev.pem');
	$notification2->setPrivateKeyPassphrase('C0rali0s');
	$res = $notification2->send();
	$notification2 = NULL;
	if ($res) {
		return "TRUE";
	} else {
		return "FALSE";
	}
}

function pushNotificationInIOS_custom_production($deviceToken, $noty_title, $noty_message, $noty_image)
{
	$today_date = date("Y_m_d");
	$notification2 = new APNotification('production');
	$notification2->setDeviceToken($deviceToken);
	$notification2->setTitle($noty_title);
	$notification2->setBadge(1);
	$notification2->setMessage($noty_message);
	if ($noty_image != "") {
		$notification2->setImageUrl($noty_image);
	}
	$notification2->setSound("default");
	$notification2->setPrivateKey('admin/starstellar_dis.pem');
	$notification2->setPrivateKeyPassphrase('C0rali0s');
	$res = $notification2->send();
	$notification2 = NULL;
	if ($res) {
		return "TRUE";
	} else {
		return "FALSE";
	}
}

$te_master = "te_master";
$engineer_master = "engineer_master";
$ledger_master = "ledger_master";
$recommended_site_master = "recommended_site_master";
$setting_master = "setting_master";
$product_master = "product_master";


$actual_bag_cons_approve_limit = get_value_by_setting_key($conn, "bags_verification_limit_for_te");
if ($actual_bag_cons_approve_limit == "") {
	$actual_bag_cons_approve_limit = 0;
}

$each_verified_site = get_value_by_setting_key($conn, "each_verified_site");
if ($each_verified_site == "") {
	$each_verified_site = 0;
}

$is_show_approval_btn = "NO";
$approval_btn_text = "";

$te_code = $_POST["te_code"] ? addslashes(trim($_POST["te_code"])) : "";
$r_site_id = $_POST["r_site_id"] ? addslashes(trim($_POST["r_site_id"])) : "";
$comments = $_POST["comments"] ? addslashes(trim($_POST["comments"])) : "";


$actual_product_id = $_POST["actual_product_id"] ? addslashes(trim($_POST["actual_product_id"])) : "";
$actual_consumption = $_POST["actual_consumption"] ? addslashes(trim($_POST["actual_consumption"])) : 0;

$purchased_from = $_POST["purchased_from"] ? addslashes(trim($_POST["purchased_from"])) : "";
$purchased_from_name = $_POST["purchased_from_name"] ? addslashes(trim($_POST["purchased_from_name"])) : "";
$purchased_from_area = $_POST["purchased_from_area"] ? addslashes(trim($_POST["purchased_from_area"])) : "";
$purchased_from_contact_no = $_POST["purchased_from_contact_no"] ? addslashes(trim($_POST["purchased_from_contact_no"])) : "";



$upload_dir = "approved_recomend_site_pic/";
$supported_mime_type = array("image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp");

$recomended_site_image_name = $_FILES["verified_site_image"]["name"];
$recomended_site_image_type = $_FILES["verified_site_image"]["type"];
$the_site_image_name = "";
if ($recomended_site_image_name != "" && !in_array($recomended_site_image_type, $supported_mime_type)) {
} else {
	$recomended_site_image_tmp_name = $_FILES["verified_site_image"]["tmp_name"];
	$recomended_site_image_name = str_replace(" ", "_", $recomended_site_image_name);
	$recomended_site_image_name = str_replace("  ", "_", $recomended_site_image_name);
	$recomended_site_image_name = str_replace("'", "_", $recomended_site_image_name);
	$recomended_site_image_name = str_replace('"', "_", $recomended_site_image_name);
	$recomended_site_image_name = str_replace('-', "_", $recomended_site_image_name);
	$unid = uniqid();
	$the_site_image_name = "veri_img_" . $unid . "_" . $recomended_site_image_name;
	$target_file = $upload_dir . $the_site_image_name;
	$upload_the_ad_file = move_uploaded_file($recomended_site_image_tmp_name, $target_file);
	if ($upload_the_ad_file) {
	} else {
		$the_site_image_name = "";
	}
}


if ($te_code != "" && $r_site_id != '') {

	$sql2 = "select te_code from $te_master where te_code='$te_code'";
	$res2 = mysqli_query($conn, $sql2);
	$tot_res2 = mysqli_num_rows($res2);
	if ($tot_res2 > 0) {
		$sql3 = "select r_site_id, r_site_name, r_engineer_id,r_status,is_mail_sent_to_asm,existing_id from $recommended_site_master where r_site_id='$r_site_id' and r_te_code='$te_code'";
// 		$sql3 = "SELECT rsm.r_site_id, rsm.r_site_name, rsm.r_engineer_id, rsm.r_status, rsm.is_mail_sent_to_asm, rsm.existing_id, tm.te_mobile_no
// FROM recommended_site_master rsm
// JOIN te_master tm ON rsm.r_te_code = tm.te_code
// WHERE rsm.r_site_id='$r_site_id' AND rsm.r_te_code='$te_code'";


//echo $sql3;

		$res3 = mysqli_query($conn, $sql3);
		$tot_res3 = mysqli_num_rows($res3);
		if ($tot_res3 > 0) {
			$row3 = mysqli_fetch_assoc($res3);
			$r_engineer_id = $row3["r_engineer_id"] ? trim($row3["r_engineer_id"]) : "";
			$r_site_name = $row3["r_site_name"] ? trim($row3["r_site_name"]) : "";

//$te_mobile_no = $row3["te_mobile_no"] ? trim($row3["te_mobile_no"]) : "";
			$curr_r_status = $row3["r_status"] ? trim($row3["r_status"]) : "";
			$existing_id = $row3["existing_id"] ? trim($row3["existing_id"]) : "";
			$is_mail_sent_to_asm = $row3["is_mail_sent_to_asm"] ? trim($row3["is_mail_sent_to_asm"]) : "NO";
			if ($curr_r_status == "APPROVED") {
				$res_data = array("process_status" => "NO", "process_message" => "This site already confirmed.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($is_mail_sent_to_asm == "YES") {
				$res_data = array("process_status" => "NO", "process_message" => "This site's approval mail already sent to ASM.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($actual_product_id == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please select an actual product.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($purchased_from == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please select purchased from.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($purchased_from_name == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please select purchased from name.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($purchased_from_area == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please select purchased from area.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($purchased_from_contact_no == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please select purchased from contact number.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($actual_consumption >= $actual_bag_cons_approve_limit) {



				$r_submission_date = date("Y-m-d H:i:s");
				$r_te_interaction_date = date("Y-m-d");
				$product_data_arr = show_product_data_by_id($conn, $actual_product_id);
				$actual_product_name = $product_data_arr["prod_name"];
				$sql4 = "update $recommended_site_master set r_site_verification_image='$the_site_image_name',r_te_interaction_comment='$comments',r_te_interaction_date='$r_te_interaction_date',r_last_updated_datetime='$r_submission_date',actual_product_id='$actual_product_id',actual_product_name='$actual_product_name',actual_consumption='$actual_consumption',purchased_from='$purchased_from',purchased_from_name='$purchased_from_name',purchased_from_area='$purchased_from_area',purchased_from_contact_no='$purchased_from_contact_no' where r_site_id='$r_site_id' and r_te_code='$te_code'";
				$res4 = mysqli_query($conn, $sql4);




				$is_show_approval_btn = "YES";
				$approval_btn_text = "SEND MAIL TO ASM FOR APPROVAL";
				$res_data = array("process_status" => "NO", "process_message" => "Since this site is greater than $actual_bag_cons_approve_limit bags, additional approval of ASM is required.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else {
				$r_submission_date = date("Y-m-d H:i:s");
				$r_te_interaction_date = date("Y-m-d");
				$product_data_arr = show_product_data_by_id($conn, $actual_product_id);
				$actual_product_name = $product_data_arr["prod_name"];
				$confirm_recomendation_point = $product_data_arr["point_per_bag"];
				$confirm_more_than_bags = (int) $product_data_arr["more_than_bags"];
				$confirm_bonus_points = (int) $product_data_arr["bonus_points"];
				$each_verified_site = isset($each_verified_site) ? $each_verified_site : 0;

				if ($existing_id != "") {
					$each_verified_site = 0;
				}
				$actual_consumption = (int) $actual_consumption;

				// $actual_recomendation_point = ($confirm_recomendation_point * $actual_consumption) + $each_verified_site;

				$actual_recomendation_point = ($confirm_recomendation_point * $actual_consumption);
				if ($confirm_more_than_bags > 0 && $actual_consumption >= $confirm_more_than_bags) {
					$actual_recomendation_point_total = $actual_recomendation_point + $confirm_bonus_points+$each_verified_site;
				} else{
					$actual_recomendation_point_total = $actual_recomendation_point;
				}

				$sql4 = "update $recommended_site_master set r_status='APPROVED',r_site_verification_image='$the_site_image_name',r_te_interaction_comment='$comments',r_te_interaction_date='$r_te_interaction_date',r_point_earned_by_engineer='$actual_recomendation_point_total',r_last_updated_datetime='$r_submission_date',actual_product_id='$actual_product_id',actual_product_name='$actual_product_name',actual_consumption='$actual_consumption',purchased_from='$purchased_from',purchased_from_name='$purchased_from_name',purchased_from_area='$purchased_from_area',purchased_from_contact_no='$purchased_from_contact_no' where r_site_id='$r_site_id' and r_te_code='$te_code'";
				$res4 = mysqli_query($conn, $sql4);
				if ($r_engineer_id != "") {

					// $sql2e = "select eid,e_points,registration_id,device_type,e_mobile from $engineer_master where eid='$r_engineer_id'";

					$sql2e = "SELECT em.eid, em.e_points, em.registration_id, em.device_type, em.e_mobile, tm.te_code, tm.te_mobile_no
					FROM engineer_master em
					JOIN te_master tm ON em.te_code = tm.te_code
					WHERE em.eid='$r_engineer_id'";

					// echo $sql2e;
					
					$res2e = mysqli_query($conn, $sql2e);
					$tot_res2e = mysqli_num_rows($res2e);
					if ($tot_res2e > 0) {
						$row2e = mysqli_fetch_assoc($res2e);
						$device_type = $row2e["device_type"] ? trim($row2e["device_type"]) : "";
						$e_mobile = $row2e["e_mobile"] ? trim($row2e["e_mobile"]) : "";
						// $te_code = $row2e["te_code"] ? trim($row2e["te_code"]) : "";
						$te_mobile_no = $row2e["te_mobile_no"] ? trim($row2e["te_mobile_no"]) : "";
						$registration_id = $row2e["registration_id"] ? trim($row2e["registration_id"]) : "";
						$e_points = $row2e["e_points"] ? trim($row2e["e_points"]) : 0;
						if ($e_points == "") {
							$e_points = 0;
						}
						$e_points = intval($e_points);
// echo $te_mobile_no;
// echo $e_mobile;
		// 				$congratulatory_message_eng = "Site Name: " . $r_site_name . " successfully Approved/Rejected: Approved. - Star Stellar";
		// sendSMSNotification($e_mobile, $congratulatory_message_eng);

		// $congratulatory_message_te = "New Site: " . $r_site_name . " Successfully Registered. - Star Stellar";
		// sendSMSNotification($te_mobile_no, $congratulatory_message_te);

						// $new_e_points = ($e_points + $actual_recomendation_point+$each_verified_site);
						
						$new_e_points = ($e_points + $actual_recomendation_point_total);

						$sqlinup = "update $engineer_master set e_points='$new_e_points' where eid='$r_engineer_id'";
						$resinup = mysqli_query($conn, $sqlinup);
						$sqlldgrin = "insert into $ledger_master (user_id,description,point_earned,ldgr_type,related_id,ldgr_datetime) values ('$r_engineer_id','Site Recomendation','$actual_recomendation_point','SITE_RECOMENDATION','$r_site_id','$r_submission_date')";
						$resldgrin = mysqli_query($conn, $sqlldgrin);

						if ($confirm_more_than_bags > 0 && $actual_consumption >= $confirm_more_than_bags) {

							// Separate entry for bonus points in the ledger
							$sqlldgrin_bonus = "insert into $ledger_master (user_id,description,point_earned,ldgr_type,related_id,ldgr_datetime) values ('$r_engineer_id','Bonus Points','$confirm_bonus_points','BONUS','$r_site_id','$r_submission_date')";
							$resldgrin_bonus = mysqli_query($conn, $sqlldgrin_bonus);
						}

						if ($existing_id != "") {
						} else {
							$sqlldgrin = "insert into $ledger_master (user_id,description,point_earned,ldgr_type,related_id,ldgr_datetime) values ('$r_engineer_id','Verified Recomendation Site','$each_verified_site','SITE_RECOMENDATION','$r_site_id','$r_submission_date')";
							$resldgrin = mysqli_query($conn, $sqlldgrin);

							
						}


						if ($registration_id != "") {

							$curr_timestamp = date('Y-m-d H:i:s');
							$curr_date_time = date("Y-m-d H:i:s");
							$the_title = "Star Stellar";
							$the_message = "Your recomended site has been confirmed.";
							$payload = array();
							$payload['team'] = 'India';
							$payload['score'] = '5.6';
							$app_noty_image = "";
							$android_message['data'] = array("title" => $the_title, "is_background" => FALSE, "message" => $the_message, "image" => $app_noty_image, "payload" => $payload, "timestamp" => $curr_timestamp);
							$body['aps'] = array("alert" => array('body' => $noty_msg), "sound" => "default");

							if ($device_type == "IOS") {
								//$sent_sts_prod = pushNotificationInIOS_custom_production($registration_id,$the_title,$the_message,$app_noty_image);
								//$sent_sts_dev = pushNotificationInIOS_custom_development($registration_id,$the_title,$the_message,$app_noty_image);

							} else if ($device_type == "ANDROID") {
								$sent_sts = send_push_notification_in_android($registration_id, $android_message);

								$congratulatory_message_eng = "Site Name: " . $r_site_name . " successfully Approved/Rejected: Approved. - Star Stellar";
								sendSMSNotification($e_mobile, $congratulatory_message_eng);
								//echo $e_mobile;
						
								$congratulatory_message_te = "New Site: " . $r_site_name . " Successfully Registered. - Star Stellar";
								sendSMSNotification($te_mobile_no, $congratulatory_message_te);
								//echo $te_mobile_no;

							}
						}
					}
				}

				$res_data = array("process_status" => "YES", "process_message" => "The site successfully confirmed.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			}
		} else {
			$res_data = array("process_status" => "NO", "process_message" => "You have no permission to confirm this site.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
		}
	} else {
		$res_data = array("process_status" => "NO", "process_message" => "The TE Code doesn't exist.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
	}
} else {
	$res_data = array("process_status" => "NO", "process_message" => "Something went wrong. Please try later.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
}
echo json_encode($res_data);
mysqli_close($conn);