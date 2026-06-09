<?php
	include "star_connection.php";
	$te_master = "te_master";
	$engineer_master = "engineer_master";
	$gift_master = "gift_master";
	$gift_order_master ="gift_order_master";
	$ledger_master = "ledger_master";
	$gift_data = array();
	$e_points = 0;
	$upload_dir = "gift_pic/";
	$image_url = $server_url."gift_pic/";
	$curr_datetime = date("Y-m-d H:i");
	$gift_id = $_POST["gift_id"] ? $_POST["gift_id"] : "";
	$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
	$set_as_default_profile_address = $_POST["set_as_default_profile_address"] ? $_POST["set_as_default_profile_address"] : "NO";

	$e_address = $_POST["e_address"] ? addslashes(trim($_POST["e_address"])) : "";
	$e_city = $_POST["e_city"] ? addslashes(trim($_POST["e_city"])) : "";
	$e_email = $_POST["email"] ? addslashes(trim($_POST["email"])) : "";
	$e_pin = $_POST["e_pin"] ? addslashes(trim($_POST["e_pin"])) : "";
	$e_state = $_POST["e_state"] ? addslashes(trim($_POST["e_state"])) : "";

	$giftTypeID = 2;

	if($the_engineer_id!=""){

		$sql2e = "select `eid`,`e_points` from $engineer_master where `eid`='$the_engineer_id'";
		$res2e = mysqli_query($conn,$sql2e);
		$tot_res2e = mysqli_num_rows($res2e);
		if($tot_res2e>0){
			$row2e = mysqli_fetch_assoc($res2e);
			$e_points = $row2e["e_points"] ? trim($row2e["e_points"]) : 0;
			if($e_points=="")
			{
				$e_points = 0;	
			}
			$e_points = intval($e_points);
			$sql2 = "select `id`, `gift_type_id`, `gift_title`,`point_require` from $gift_master where `id`='$gift_id'";
			$res2 = mysqli_query($conn,$sql2);
			$tot_res2 = mysqli_num_rows($res2);
			if($tot_res2>0)
			{
				$row2 = mysqli_fetch_assoc($res2);
				$gift_title = $row2["gift_title"] ? trim($row2["gift_title"]) : "";
				$gift_point = $row2["point_require"] ? trim($row2["point_require"]) : 0;
				if($row2["gift_type_id"] == $giftTypeID && ($e_email == "" || !filter_var($e_email, FILTER_VALIDATE_EMAIL)))
				{
					$res_data = array("process_status"=>"NO","process_message"=>"Your email is required.");
				}
				else
				{
					if($gift_point=="")
					{
						$gift_point = 0;	
					}
					$gift_point = intval($gift_point);
					if($gift_point>$e_points)
					{
						$res_data = array("process_status"=>"NO","process_message"=>"You are not eligible to make order $gift_title.");
					}
					else
					{
						$rest_points_for_engineer = intval($e_points-$gift_point);
						
						$datetime = date("Y-m-d H:i:s");
						/*$delivery_date = date('Y-m-d',strtotime("+5 day"));*/
						$delivery_date = "";
						if($row2["gift_type_id"] == $giftTypeID)
						{
							$sqlmkord = "insert into $gift_order_master (`user_id`,`gift_id`,`city`,`user_email`,`pin`,`state`,`address`,`point_taken`,`datetime`) values ('$the_engineer_id','$gift_id','$e_city','$e_email','$e_pin','$e_state','$e_address','$gift_point','$datetime')";
						}
						else
						{
							$sqlmkord = "insert into $gift_order_master (`user_id`,`gift_id`,`city`,`pin`,`state`,`address`,`point_taken`,`datetime`) values ('$the_engineer_id','$gift_id','$e_city','$e_pin','$e_state','$e_address','$gift_point','$datetime')";
						}
						$resmkord = mysqli_query($conn,$sqlmkord);
						if($resmkord)
						{
							$the_order_id = mysqli_insert_id($conn);
							if($set_as_default_profile_address=="YES")
							{
								$sqlupdusr = "update $engineer_master set `e_points`='$rest_points_for_engineer',`e_address`='$e_address',`e_pin`='$e_pin',`e_state`='$e_state',`e_city_town`='$e_city' where `eid`='$the_engineer_id'";
								$resupdusr = mysqli_query($conn,$sqlupdusr);
							}
							else
							{
								$sqlupdusr = "update $engineer_master set `e_points`='$rest_points_for_engineer' where `eid`='$the_engineer_id'";
								$resupdusr = mysqli_query($conn,$sqlupdusr);	
							}
							$gift_title = addslashes($gift_title);
							$sqlldgrin = "insert into $ledger_master (`user_id`,`description`,`point_redeem`,`ldgr_type`,`related_id`,`ldgr_datetime`) values ('$the_engineer_id','$gift_title','$gift_point','GIFT_REDEEM','$gift_id','$datetime')";
							$resldgrin = mysqli_query($conn,$sqlldgrin);
							$rest_points_for_engineer_msg = "Stellar Points : ".$rest_points_for_engineer;
							$res_data = array("process_status"=>"YES","process_message"=>"Order successfully placed.","the_order_id"=>$the_order_id,"e_points_msg"=>$rest_points_for_engineer_msg,"the_point"=>$rest_points_for_engineer);
						}
						else
						{
							$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
						}	
					}
				}
			}
			else
			{
				$res_data = array("process_status"=>"NO","process_message"=>"No gift found.");
			}
		}
		else
		{
			$res_data = array("process_status"=>"NO","process_message"=>"The engineer details doesn't exist.");
		}
	}
	else
	{
		$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
	}
	echo json_encode($res_data);
	mysqli_close($conn);
?>