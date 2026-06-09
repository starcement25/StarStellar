<?php
	include "star_connection.php";
	$te_master = "te_master";
	$engineer_master = "engineer_master";
	$mobile = $_POST["mobile"] ? addslashes(trim($_POST["mobile"])) : "";
	$otp = rand(1,9).rand(0,9).rand(0,9).rand(1,9);
	$OTP_TYPE_DYNAMIC = 1;
	$OTP_TYPE_STATIC = 0;
	//$otp = 1010;
	$static_otp = 1010;
	if($mobile!="")
	{
		$sql2 = "select `te_id`,`te_mobile_no`,`acedns`,`otp_type` from $te_master where `te_mobile_no`='$mobile'";
		$res2 = mysqli_query($conn,$sql2);
		$tot_res2 = mysqli_num_rows($res2);
		if($tot_res2>0)
		{
			$row2 = mysqli_fetch_assoc($res2);
			$the_te_id = $row2["te_id"];
			$the_acedns = $row2["acedns"] ? trim($row2["acedns"]) : "N";
			if($the_acedns=="N")
			{
				$res_data = array("process_status"=>"NO","process_message"=>"Mobile number that you entered, is currently inactive. Please contact administrator.");
			}
			else
			{
				if($row2["otp_type"] == $OTP_TYPE_DYNAMIC)
				{
					// if($mobile=="9233974090" || $mobile=="9831722939" || $mobile=="9638307128" || $mobile=="7278212381" || $mobile=="9874450813")
					// {
					// 	$otp = 1010;
					// }
					$otp_text = $otp." is your OTP for Star Stellar login. Regards, Star Cement";		
					$lipl_uri = "https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to=".$mobile."&from=STARCM&text=".urlencode($otp_text)."&tempid=1707164069304597602&dlr-mask=19&dlr-url";
					$lipl_ch = curl_init();
					curl_setopt($lipl_ch, CURLOPT_URL, $lipl_uri);
					curl_setopt($lipl_ch, CURLOPT_TIMEOUT, 20);
					curl_setopt($lipl_ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt($lipl_ch, CURLOPT_HEADER,0);
					curl_setopt($lipl_ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($lipl_ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
					$lipl_return_val = curl_exec($lipl_ch);
					curl_close($lipl_ch);
				
					$sql_noty = "update $te_master set `the_otp`='$otp' where `te_id`='$the_te_id'";
					$res_noty = mysqli_query($conn,$sql_noty);
				}
				else
				{
					$sql_noty = "update $te_master set `the_otp`='$static_otp' where `te_id`='$the_te_id'";
					$res_noty = mysqli_query($conn,$sql_noty);
				}
				$res_data = array("process_status"=>"YES","process_message"=>"OTP has been sent to your mobile number.","user_type"=>"TE");
			}
		}
		else
		{
			$sql2 = "select `eid`,`e_mobile`,`status`,`otp_type` from $engineer_master where `e_mobile`='$mobile'";
			$res2 = mysqli_query($conn,$sql2);
			$tot_res2 = mysqli_num_rows($res2);

			$congratulatory_message = "Congratulation! You have been successfully registered on Star Stellar - STAR CEMENT";
			sendSMSNotification($mobile, $congratulatory_message);

			if($tot_res2>0)
			{
				$row2 = mysqli_fetch_assoc($res2);
				$the_eid = $row2["eid"];
				$the_status = $row2["status"] ? trim($row2["status"]) : "INACTIVE";
				if($the_status=="INACTIVE")
				{
					$res_data = array("process_status"=>"NO","process_message"=>"Mobile number that you entered, is currently inactive. Please contact administrator.");
				}
				else
				{
					if($row2["otp_type"] == $OTP_TYPE_DYNAMIC)
					{
						// if($mobile=="9874412349" || $mobile=="8697182232" || $mobile=="8961499496" || $mobile=="9874450813" || $mobile=="7278212381")
						// {
						// 	$otp = 1010;
						// }
						$otp_text = $otp." is your OTP for Star Stellar login. Regards, Star Cement";		
						$lipl_uri = "https://http.myvfirst.com/smpp/sendsms?username=starhttpdealers&password=star1109&to=".$mobile."&from=STARCM&text=".urlencode($otp_text)."&tempid=1707164069304597602&dlr-mask=19&dlr-url";
						$lipl_ch = curl_init();
						curl_setopt($lipl_ch, CURLOPT_URL, $lipl_uri);
						curl_setopt($lipl_ch, CURLOPT_TIMEOUT, 20);
						curl_setopt($lipl_ch, CURLOPT_FOLLOWLOCATION, 1);
						curl_setopt($lipl_ch, CURLOPT_HEADER,0);
						curl_setopt($lipl_ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($lipl_ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
						$lipl_return_val = curl_exec($lipl_ch);
						curl_close($lipl_ch);
						
						
						$sql_noty = "update $engineer_master set `the_otp`='$otp' where `eid`='$the_eid'";
						$res_noty = mysqli_query($conn,$sql_noty);
					}
					else
					{
						$sql_noty = "update $engineer_master set `the_otp`='$static_otp' where `eid`='$the_eid'";
						$res_noty = mysqli_query($conn,$sql_noty);
					}
					$res_data = array("process_status"=>"YES","process_message"=>"OTP has been sent to your mobile number.","user_type"=>"ENGINEER");
				}
			}
			else
			{
				$res_data = array("process_status"=>"NO","process_message"=>"Mobile number doesn't exist.");
			}

			
		}	
	}
	else
	{	
		$res_data = array("process_status"=>"NO","process_message"=>"All fields are mandatory");
	}
	echo json_encode($res_data);
	mysqli_close($conn);
?>