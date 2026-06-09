<?php
include "star_connection.php";
$te_master = "te_master";
$engineer_master = "engineer_master";

$mobile = $_POST["mobile"] ? addslashes(trim($_POST["mobile"])) : "";
$otp = $_POST["otp"] ? addslashes(trim($_POST["otp"])) : "";
$user_type = $_POST["user_type"] ? addslashes(trim($_POST["user_type"])) : ""; // TE or ENGINEER
$device_id = $_POST["device_id"] ? addslashes(trim($_POST["device_id"])) : "";
$registration_id = $_POST["registration_id"] ? addslashes(trim($_POST["registration_id"])) : "";
$device_type = $_POST["device_type"] ? addslashes(trim($_POST["device_type"])) : "";

$the_image_dir_for_te = "te_profile_pic/";
$the_image_dir_for_en = "en_profile_pic/";
$profile_img_prifix_for_en = $server_url."en_profile_pic/";
$profile_img_prifix_for_te = $server_url."te_profile_pic/";
$profile_default_image_link_for_en = $server_url."en_profile_pic/profile.png";
$profile_default_image_link_for_te = $server_url."te_profile_pic/profile.png";

if($user_type!='' && $mobile!='' && $otp!=''){

if($user_type=="TE"){
	$sql2 = "select * from $te_master where `te_mobile_no`='$mobile' and `the_otp`='$otp'";
	$res2 = mysqli_query($conn,$sql2);
	$tot_res2 = mysqli_num_rows($res2);
	if($tot_res2>0){
		$row2 = mysqli_fetch_assoc($res2);
		$the_te_id = $row2["te_id"];
		$the_acedns = $row2["acedns"] ? trim($row2["acedns"]) : "N";
		if($the_acedns=="N"){
				$res_data = array("process_status"=>"NO","process_message"=>"Mobile number that you entered, is currently inactive. Please contact administrator.");
			}else{
		$the_te_name = $row2["te_name"];
		$the_te_code = $row2["te_code"];
		$the_te_mobile_no = $row2["te_mobile_no"];
		$the_te_email = $row2["te_email"];
		$the_te_profile_image = $row2["te_profile_image"] ? trim($row2["te_profile_image"]) : "";
		if($the_te_profile_image!=""){
		if(file_exists($the_image_dir_for_te.$the_te_profile_image)){
		$profile_image_link = $profile_img_prifix_for_te.$the_te_profile_image;
		}else{
		$profile_image_link = $profile_default_image_link_for_te;
		}		
		}else{
		$profile_image_link = $profile_default_image_link_for_te;
		}
		if($device_id!='' && $registration_id!='' && $device_type!=''){
			$curr_datetime = date("Y-m-d H:i:s");
	$sql_noty = "update $te_master set `registration_id`='$registration_id',`device_type`='$device_type',`device_id`='$device_id',`last_updated_datetime`='$curr_datetime' where `te_id`='$the_te_id'";
	$res_noty = mysqli_query($conn,$sql_noty);
		}
		$res_data = array("process_status"=>"YES","process_message"=>"Success.","user_type"=>"TE","the_te_id"=>$the_te_id,"the_te_name"=>$the_te_name,"the_te_code"=>$the_te_code,"the_te_mobile_no"=>$the_te_mobile_no,"the_te_email"=>$the_te_email,"te_profile_image"=>$profile_image_link);
		
			}
		
	}else{
		$res_data = array("process_status"=>"NO","process_message"=>"Wrong Credentials.");
	}	
}else if($user_type=="ENGINEER"){
	$sql2 = "select * from $engineer_master where `e_mobile`='$mobile' and `the_otp`='$otp'";
	$res2 = mysqli_query($conn,$sql2);
	$tot_res2 = mysqli_num_rows($res2);
	if($tot_res2>0){
		$row2 = mysqli_fetch_assoc($res2);
		$the_engineer_id = $row2["eid"];
		$the_status = $row2["status"] ? trim($row2["status"]) : "INACTIVE";
		if($the_status=="INACTIVE"){
				$res_data = array("process_status"=>"NO","process_message"=>"Mobile number that you entered, is currently inactive. Please contact administrator.");
			}else{
		$e_name = $row2["e_name"] ? trim($row2["e_name"]) : "";
		$e_mobile = $row2["e_mobile"] ? trim($row2["e_mobile"]) : "";
		$te_code = $row2["te_code"] ? trim($row2["te_code"]) : "";
		//$te_branch_code = get_te_branchcode_by_tecode($conn,$te_code);
		$e_email = $row2["e_email"] ? trim($row2["e_email"]) : "";
		$e_dob = $row2["e_dob"] ? trim($row2["e_dob"]) : "";
		$e_dom = $row2["e_dom"] ? trim($row2["e_dom"]) : "";
		$e_address = $row2["e_address"] ? trim($row2["e_address"]) : "";
		$e_pin = $row2["e_pin"] ? trim($row2["e_pin"]) : "";
		$e_state = $row2["e_state"] ? trim($row2["e_state"]) : "";
		$e_city_town = $row2["e_city_town"] ? trim($row2["e_city_town"]) : "";
		$e_profile_image = $row2["e_profile_image"] ? trim($row2["e_profile_image"]) : "";
		if($e_profile_image!=""){
		if(file_exists($the_image_dir_for_en.$e_profile_image)){
		$profile_image_link = $profile_img_prifix_for_en.$e_profile_image;
		}else{
		$profile_image_link = $profile_default_image_link_for_en;
		}		
		}else{
		$profile_image_link = $profile_default_image_link_for_en;
		}
		if($device_id!='' && $registration_id!='' && $device_type!=''){
			$curr_datetime = date("Y-m-d H:i:s");
	$sql_noty = "update $engineer_master set `registration_id`='$registration_id',`device_type`='$device_type',`device_id`='$device_id',`last_updated_datetime`='$curr_datetime' where `eid`='$the_engineer_id'";
	$res_noty = mysqli_query($conn,$sql_noty);
		}
		
$res_data = array("process_status"=>"YES","process_message"=>"Success.","user_type"=>"ENGINEER","the_engineer_id"=>$the_engineer_id,"e_name"=>$e_name,"e_mobile"=>$e_mobile,"te_code"=>$te_code,"e_email"=>$e_email,"e_dob"=>$e_dob,"e_dom"=>$e_dom,"e_address"=>$e_address,"e_pin"=>$e_pin,"e_state"=>$e_state,"e_city_town"=>$e_city_town,"e_profile_image"=>$profile_image_link);

			}

	}else{
		$res_data = array("process_status"=>"NO","process_message"=>"Wrong Credentials.");
	}
	
}else{
	$res_data = array("process_status"=>"NO","process_message"=>"Wrong user type.");
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"All fields are mandatory.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>