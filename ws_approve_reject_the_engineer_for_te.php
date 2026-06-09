<?php
	set_time_limit(0);
	error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
	date_default_timezone_set('Asia/Kolkata');
	include "star_connection.php";
	include 'admin/APNSBase.php';
	include 'admin/APNotification.php';
	include 'admin/APFeedback.php';
	function send_push_notification_in_android($registration_ids,$message)
	{
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
		if ($result === FALSE)
		{
			$result = '{"success":0,"failure":1}';
		} 
		// Close connection
		curl_close($ch);
		$result_str = json_decode($result,true);
		if($result_str["success"]==1)
		{
			return "TRUE";
		}
		else
		{
			return "FALSE";
		}
	}

	function pushNotificationInIOS_custom_development($deviceToken,$noty_title,$noty_message,$noty_image)
	{
		$today_date = date("Y_m_d");
		$notification2 = new APNotification('development');
		$notification2->setDeviceToken($deviceToken);
		$notification2->setTitle($noty_title);
		$notification2->setBadge(1);
		$notification2->setMessage($noty_message);
		if($noty_image!="")
		{
			$notification2->setImageUrl($noty_image);
		}
		$notification2->setSound("default");
		$notification2->setPrivateKey('admin/starstellar_dev.pem');
		$notification2->setPrivateKeyPassphrase('C0rali0s');
		$res = $notification2->send();
		$notification2 = NULL;
		if($res)
		{
			return "TRUE";
		}
		else
		{
			return "FALSE";
		}    
	}

	function pushNotificationInIOS_custom_production($deviceToken,$noty_title,$noty_message,$noty_image)
	{
		$today_date = date("Y_m_d");
		$notification2 = new APNotification('production');
		$notification2->setDeviceToken($deviceToken);
		$notification2->setTitle($noty_title);
		$notification2->setBadge(1);
		$notification2->setMessage($noty_message);
		if($noty_image!="")
		{
			$notification2->setImageUrl($noty_image);
		}
		$notification2->setSound("default");
		$notification2->setPrivateKey('admin/starstellar_dis.pem');
		$notification2->setPrivateKeyPassphrase('C0rali0s');
		$res = $notification2->send();
		$notification2 = NULL;
		if($res)
		{
			return "TRUE";
		}
		else
		{
			return "FALSE";
		}    
	}
	$te_master = "te_master";
	$engineer_master = "engineer_master";
	$setting_master = "setting_master";
	$ledger_master = "ledger_master";
	$curr_date_time = date("Y-m-d H:i:s");
	$te_code = $_POST["te_code"] ? addslashes(trim($_POST["te_code"])) : "";
	$eid = $_POST["eid"] ? addslashes(trim($_POST["eid"])) : "";
	$status = $_POST["status"] ? addslashes(trim($_POST["status"])) : ""; // APPROVE or REJECT
	if($te_code!="" && $eid!="" && $status!="")
	{
		$sqlckrds = "select `the_value` from $setting_master where `the_key_name`='signup_point'";
		$resckrds = mysqli_query($conn,$sqlckrds);
		$totresckrds = mysqli_num_rows($resckrds);
		if($totresckrds>0)
		{
			$rowckrds = mysqli_fetch_assoc($resckrds);
			$signup_point = $rowckrds["the_value"] ? trim($rowckrds["the_value"]) : 0;
			if($signup_point=="")
			{
				$signup_point = 0;
			}
		}
		else
		{
			$signup_point = 0;	
		}

		$sql2 = "SELECT `e_points`,`status_by_te`,`registration_id`,`device_type` FROM $engineer_master where `te_code`='$te_code' and `eid`='$eid'";
		$res2 = mysqli_query($conn,$sql2);
		$tot_res2 = mysqli_num_rows($res2);
		if($tot_res2>0)
		{
			$row2 = mysqli_fetch_assoc($res2);
			$e_points = $row2["e_points"];
			if($e_points=="")
			{
				$e_points = 0;	
			}
			$e_points = intval($e_points);
			$status_by_te = $row2["status_by_te"];
			$device_type = $row2["device_type"] ? trim($row2["device_type"]) : "";
			$registration_id = $row2["registration_id"] ? trim($row2["registration_id"]) : "";

			if($status=="APPROVE")
			{
				if($status_by_te=="PENDING")
				{
					$actual_eng_point = ($e_points + $signup_point);
					$sql26 = "update $engineer_master set `status_by_te`='APPROVED',`e_points`='$actual_eng_point' where `te_code`='$te_code' and `eid`='$eid'";
					$res26 = mysqli_query($conn,$sql26);

					$sqlldgrin = "insert into $ledger_master (`user_id`,`description`,`point_earned`,`ldgr_datetime`,`remaining_balance`) values ('$eid','Sign up','$signup_point','$curr_date_time','$actual_eng_point')";
					$resldgrin = mysqli_query($conn,$sqlldgrin);

					if($registration_id!="")
					{
						
						$curr_timestamp = date('Y-m-d H:i:s');
						$curr_date_time = date("Y-m-d H:i:s");
						$the_title = "Star Stellar";
						$the_message = "Congrats! You have got signup point.";
						$payload = array();
						$payload['team'] = 'India';
						$payload['score'] = '5.6';
						$app_noty_image = "";	
						$android_message['data'] = array("title"=>$the_title,"is_background"=>FALSE,"message"=>$the_message,"image"=>$app_noty_image,"payload"=>$payload,"timestamp"=>$curr_timestamp);			
						$body['aps'] = array("alert"=> array('body'=>$noty_msg),"sound"=>"default");
						
						if($device_type=="IOS")
						{
							$sent_sts_prod = pushNotificationInIOS_custom_production($registration_id,$the_title,$the_message,$app_noty_image);
							$sent_sts_dev = pushNotificationInIOS_custom_development($registration_id,$the_title,$the_message,$app_noty_image);
						}
						else if($device_type=="ANDROID")
						{			
							$sent_sts = send_push_notification_in_android($registration_id,$android_message);	
						}
					}

					$res_data = array("process_status"=>"YES","process_message"=>"Successfully approved.");
				}
				else if($status_by_te=="APPROVED")
				{
					$res_data = array("process_status"=>"YES","process_message"=>"The engineer details already approved.");	
				}
				else if($status_by_te=="REJECTED")
				{
					$res_data = array("process_status"=>"YES","process_message"=>"The engineer details already rejected.");	
				}
				else
				{
					$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");	
				}
			}
			else if($status=="REJECT")
			{
				$sql24 = "update $engineer_master set `status_by_te`='REJECTED' where `te_code`='$te_code' and `eid`='$eid'";
				$res24 = mysqli_query($conn,$sql24);
				$res_data = array("process_status"=>"YES","process_message"=>"Successfully rejected.");
			}
			else
			{
				$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");	
			}
		}
		else
		{
			$res_data = array("process_status"=>"NO","process_message"=>"No record found.");
		}
	}
	else
	{
		$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
	}
	echo json_encode($res_data);
	mysqli_close($conn);
?>