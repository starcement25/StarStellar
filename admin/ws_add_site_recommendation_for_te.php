<?php
include "star_connection.php";
$te_master = "te_master";
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$product_master = "product_master";
$ledger_master = "ledger_master";
$is_show_approval_btn = "NO";
$approval_btn_text = "";
function show_product_name_by_id($conn, $pid)
{
	$product_master = "product_master";
	$pname = "";
	$pid = $pid ? trim($pid) : "";
	if ($pid != "") {
		$sql2p = "select `prod_name` from $product_master where `prod_id`='$pid'";
		// $sql2p = "SELECT `prod_name` FROM $product_master WHERE `prod_id` = '$pid' AND `product_status` = 'active'";
		$res2p = mysqli_query($conn, $sql2p);
		$tot_res2p = mysqli_num_rows($res2p);
		if ($tot_res2p > 0) {
			$row2p = mysqli_fetch_assoc($res2p);
			$pname = addslashes(trim($row2p["prod_name"]));
		}
	}

	return $pname;
}

$actual_bag_cons_approve_limit = get_value_by_setting_key($conn, "bags_verification_limit_for_te");
if ($actual_bag_cons_approve_limit == "") {
	$actual_bag_cons_approve_limit = 0;
}

$each_verified_site = get_value_by_setting_key($conn, "each_verified_site");
if ($each_verified_site == "") {
	$each_verified_site = 0;
}

$existing_id = $_POST["existing_id"] ? addslashes(trim($_POST["existing_id"])) : "";
$te_code = $_POST["te_code"] ? addslashes(trim($_POST["te_code"])) : "";
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$site_name = $_POST["site_name"] ? addslashes(trim($_POST["site_name"])) : "";
$contact_person_name = $_POST["contact_person_name"] ? addslashes(trim($_POST["contact_person_name"])) : "";
$mobile_no = $_POST["mobile_no"] ? addslashes(trim($_POST["mobile_no"])) : "";
$address = $_POST["address"] ? addslashes(trim($_POST["address"])) : "";
$site_potential_in_mt = $_POST["site_potential_in_mt"] ? addslashes(trim($_POST["site_potential_in_mt"])) : "";
$contact_person_category_name = $_POST["contact_person_category_name"] ? addslashes(trim($_POST["contact_person_category_name"])) : "";
/*$recomended_site_image_name = $_FILES["recomended_site_image"]["name"];
$recomended_site_image_type = $_FILES["recomended_site_image"]["type"];*/



$comments = $_POST["comments"] ? addslashes(trim($_POST["comments"])) : "";


$actual_product_id = $_POST["actual_product_id"] ? addslashes(trim($_POST["actual_product_id"])) : "";
$actual_consumption = $_POST["actual_consumption"] ? addslashes(trim($_POST["actual_consumption"])) : 0;

$expected_product_id = $actual_product_id;
$expected_consumption = $actual_consumption;

$purchased_from = $_POST["purchased_from"] ? addslashes(trim($_POST["purchased_from"])) : "";
$purchased_from_name = $_POST["purchased_from_name"] ? addslashes(trim($_POST["purchased_from_name"])) : "";
$purchased_from_area = $_POST["purchased_from_area"] ? addslashes(trim($_POST["purchased_from_area"])) : "";
$purchased_from_contact_no = $_POST["purchased_from_contact_no"] ? addslashes(trim($_POST["purchased_from_contact_no"])) : "";



$approv_upload_dir = "approved_recomend_site_pic/";
$supported_mime_type = array("image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp");

$reco_upload_dir = "recomend_site_pic/";
$supported_mime_type = array("image/jpeg", "image/jpg", "image/png", "image/gif", "image/bmp");

if ($the_engineer_id != '' && $existing_id != '') {




	$sql3 = "select `eid`,`te_code` from $engineer_master where `eid`='$the_engineer_id'";
	$res3 = mysqli_query($conn, $sql3);
	$tot_res3 = mysqli_num_rows($res3);
	if ($tot_res3 > 0) {
		$row3 = mysqli_fetch_assoc($res3);
		$te_code = $row3["te_code"] ? addslashes(trim($row3["te_code"])) : "";

		$sql2 = "select `te_code` from $te_master where `te_code`='$te_code'";
		$res2 = mysqli_query($conn, $sql2);
		$tot_res2 = mysqli_num_rows($res2);
		if ($tot_res2 > 0) {

			if ($site_name == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please enter site name.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($contact_person_name == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please enter contact person name.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($mobile_no == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please enter mobile number.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($address == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please enter address.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($site_potential_in_mt == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please enter site potential in MT.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($contact_person_category_name == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please select contact person category.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($actual_product_id == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please select an actual product.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($actual_consumption == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please enter actual consumption.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($purchased_from == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please select purchased from.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($purchased_from_name == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please select purchased from name.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($purchased_from_area == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please select purchased from area.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else if ($purchased_from_contact_no == "") {
				$res_data = array("process_status" => "NO", "process_message" => "Please select purchased from contact number.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
			} else {

				$the_old_recco_site_image_name = "";
				$sql_ftc = "select `r_recomended_site_image` from $recommended_site_master where `r_site_id`='$existing_id'";
				$res_ftc = mysqli_query($conn, $sql_ftc);
				$row_ftc = mysqli_fetch_assoc($res_ftc);
				$the_old_recco_site_image_name = $row_ftc["r_recomended_site_image"] ? trim($row_ftc["r_recomended_site_image"]) : "";


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
					$target_file = $approv_upload_dir . $the_site_image_name;
					$upload_the_ad_file = move_uploaded_file($recomended_site_image_tmp_name, $target_file);
					if ($upload_the_ad_file) {

					} else {
						$the_site_image_name = "";
					}
				}

				$r_submission_date = date("Y-m-d H:i:s");
				$r_te_interaction_date = date("Y-m-d");
				$product_data_arr = show_product_data_by_id($conn, $actual_product_id);
				$actual_product_name = $product_data_arr["prod_name"];
				$confirm_recomendation_point = $product_data_arr["point_per_bag"];
				// added new below
				$confirm_more_than_bags = (int) $product_data_arr["more_than_bags"];
				$confirm_bonus_points = (int) $product_data_arr["bonus_points"];
				if ($existing_id != "") {
					$each_verified_site = 0;
				}
				$actual_consumption = (int) $actual_consumption;

				$actual_recomendation_point = ($confirm_recomendation_point * $actual_consumption);

				if ($confirm_more_than_bags > 0 && $actual_consumption >= $confirm_more_than_bags) {
					$actual_recomendation_point_total = $actual_recomendation_point + $confirm_bonus_points;
				 } 
				 else {
					$actual_recomendation_point_total = $actual_recomendation_point;
				}

				if ($actual_consumption >= $actual_bag_cons_approve_limit) {

					/* IF ACTUAL CONSUMPTION IS GREATER THAN LIMIT THEN THIS CODE WILL BE EXECUTED */

					$sql4 = "insert into $recommended_site_master (`r_te_code`,`r_engineer_id`,`r_site_name`,`existing_id`,`r_contact_person_name`,`r_mobile_no`,`r_address`,`r_site_potential_in_mt`,`r_contact_person_category_name`,`r_recomended_site_image`,`expected_product_id`,`expected_product_name`,`expected_consumption`,`r_submission_date`,`r_last_updated_datetime`,`r_site_verification_image`,`r_te_interaction_comment`,`r_te_interaction_date`,`actual_product_id`,`actual_product_name`,`actual_consumption`,`purchased_from`,`purchased_from_name`,`purchased_from_area`,`purchased_from_contact_no`) values('$te_code','$the_engineer_id','$site_name','$existing_id','$contact_person_name','$mobile_no','$address','$site_potential_in_mt','$contact_person_category_name','$the_old_recco_site_image_name','$actual_product_id','$actual_product_name','$expected_consumption','$r_submission_date','$r_submission_date','$the_site_image_name','$comments','$r_te_interaction_date','$actual_product_id','$actual_product_name','$actual_consumption','$purchased_from','$purchased_from_name','$purchased_from_area','$purchased_from_contact_no')";

					$res4 = mysqli_query($conn, $sql4);
					$the_recommended_id = mysqli_insert_id($conn);


					$is_show_approval_btn = "YES";
					$approval_btn_text = "SEND MAIL TO ASM FOR APPROVAL";
					$res_data = array("process_status" => "NO", "process_message" => "Since this site is greater than $actual_bag_cons_approve_limit bags, additional approval of ASM is required.", "the_recommended_id" => $the_recommended_id, "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);

				} else {

					/* IF ACTUAL CONSUMPTION IS LESS THAN LIMIT THEN THIS CODE WILL BE EXECUTED */

					$sql4 = "insert into $recommended_site_master (`r_te_code`,`r_engineer_id`,`r_site_name`,`existing_id`,`r_contact_person_name`,`r_mobile_no`,`r_address`,`r_site_potential_in_mt`,`r_contact_person_category_name`,`r_recomended_site_image`,`expected_product_id`,`expected_product_name`,`expected_consumption`,`r_submission_date`,`r_last_updated_datetime`,`r_site_verification_image`,`r_te_interaction_comment`,`r_te_interaction_date`,`actual_product_id`,`actual_product_name`,`actual_consumption`,`purchased_from`,`purchased_from_name`,`purchased_from_area`,`purchased_from_contact_no`,`r_status`,`r_point_earned_by_engineer`) values('$te_code','$the_engineer_id','$site_name','$existing_id','$contact_person_name','$mobile_no','$address','$site_potential_in_mt','$contact_person_category_name','$the_old_recco_site_image_name','$actual_product_id','$actual_product_name','$expected_consumption','$r_submission_date','$r_submission_date','$the_site_image_name','$comments','$r_te_interaction_date','$actual_product_id','$actual_product_name','$actual_consumption','$purchased_from','$purchased_from_name','$purchased_from_area','$purchased_from_contact_no','APPROVED','$actual_recomendation_point_total')";

					$res4 = mysqli_query($conn, $sql4);
					$the_recommended_id = mysqli_insert_id($conn);


					if ($the_engineer_id != "") {

						$sql2e = "select `eid`,`e_points`,`registration_id`,`device_type` from $engineer_master where `eid`='$the_engineer_id'";
						$res2e = mysqli_query($conn, $sql2e);
						$tot_res2e = mysqli_num_rows($res2e);
						if ($tot_res2e > 0) {
							$row2e = mysqli_fetch_assoc($res2e);
							$device_type = $row2e["device_type"] ? trim($row2e["device_type"]) : "";
							$registration_id = $row2e["registration_id"] ? trim($row2e["registration_id"]) : "";
							$e_points = $row2e["e_points"] ? trim($row2e["e_points"]) : 0;
							if ($e_points == "") {
								$e_points = 0;
							}
							$e_points = intval($e_points);

							if ($existing_id != "") {
								$each_verified_site = 0;
							}

							$new_e_points = ($e_points + $actual_recomendation_point_total);

							$sqlinup = "update $engineer_master set `e_points`='$new_e_points' where `eid`='$the_engineer_id'";
							$resinup = mysqli_query($conn, $sqlinup);
							$sqlldgrin = "insert into $ledger_master (`user_id`,`description`,`point_earned`,`ldgr_type`,`related_id`,`ldgr_datetime`) values ('$the_engineer_id','Site Recomendation','$actual_recomendation_point','SITE_RECOMENDATION','$the_recommended_id','$r_submission_date')";
							$resldgrin = mysqli_query($conn, $sqlldgrin);

							if ($confirm_more_than_bags > 0 && $actual_consumption >= $confirm_more_than_bags) {
								// Separate entry for bonus points in the ledger
								$sqlinup1 = "update $engineer_master set `e_points`='$new_e_points' where `eid`='$the_engineer_id'";
							$resinup1 = mysqli_query($conn, $sqlinup1);
							$sqlldgrin1 = "insert into $ledger_master (`user_id`,`description`,`point_earned`,`ldgr_type`,`related_id`,`ldgr_datetime`) values ('$the_engineer_id','Bonus Point','$confirm_bonus_points','BONUS','$the_recommended_id','$r_submission_date')";
							$resldgrin1 = mysqli_query($conn, $sqlldgrin1);
							}

							/*if($existing_id!=""){

							}else{
							$sqlldgrin = "insert into $ledger_master (`user_id`,`description`,`point_earned`,`ldgr_type`,`related_id`,`ldgr_datetime`) values ('$the_engineer_id','Verified Recomendation Site','$each_verified_site','SITE_RECOMENDATION','$the_recommended_id','$r_submission_date')";
							$resldgrin = mysqli_query($conn,$sqlldgrin);
							}*/

							/*if($registration_id!=""){

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
							}*/

						}

					}


					$res_data = array("process_status" => "YES", "process_message" => "The site details are successfully saved.", "the_recommended_id" => $the_recommended_id, "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
				}


			}



		} else {
			$res_data = array("process_status" => "NO", "process_message" => "The TE Code doesn't exist.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
		}


	} else {
		$res_data = array("process_status" => "NO", "process_message" => "Your engineer account doesn't exist.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
	}

} else {
	$res_data = array("process_status" => "NO", "process_message" => "Something went wrong. Please try later.", "is_show_approval_btn" => $is_show_approval_btn, "approval_btn_text" => $approval_btn_text);
}
echo json_encode($res_data);
mysqli_close($conn);
?>