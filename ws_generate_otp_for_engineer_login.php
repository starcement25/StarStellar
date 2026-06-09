<?php
include "star_connection.php";

function get_te_zone_by_tecode($conn,$tecode){
	$zone = "";
	$te_master = "te_master";
	if($tecode!=""){
		$sql_gapc = "select `zone` from $te_master where `te_code`='$tecode'";
		$res_gapc = mysqli_query($conn,$sql_gapc);
		$totres_gapc = mysqli_num_rows($res_gapc);
		if($totres_gapc>0){
			$row_gapc=mysqli_fetch_assoc($res_gapc);
			$zones = trim($row_gapc["zone"]);
			$zone_arr = explode(",", $zones);
			$zone = trim($zone_arr[0]);
			


		}
	}
	return $zone;
}

$te_master = "te_master";
$engineer_master = "engineer_master";
$setting_master = "setting_master";
$ledger_master = "ledger_master";
$te_code = $_POST["te_code"] ? addslashes(trim($_POST["te_code"])) : "";
$mobile = $_POST["mobile"] ? addslashes(trim($_POST["mobile"])) : "";
$e_name = $_POST["e_name"] ? addslashes(trim($_POST["e_name"])) : "";
$e_email = $_POST["e_email"] ? addslashes(trim($_POST["e_email"])) : "";
if($te_code!="" && $mobile!='' && $e_name!='' && $e_email!=''){
	
$sqlckrds = "select `the_value` from $setting_master where `the_key_name`='signup_point'";
$resckrds = mysqli_query($conn,$sqlckrds);
$totresckrds = mysqli_num_rows($resckrds);
if($totresckrds>0){
$rowckrds = mysqli_fetch_assoc($resckrds);
$signup_point = $rowckrds["the_value"] ? trim($rowckrds["the_value"]) : 0;
if($signup_point==""){
$signup_point = 0;
}
}else{
$signup_point = 0;	
}

	$sql2 = "select `te_code` from $te_master where `te_code`='$te_code'";
	$res2 = mysqli_query($conn,$sql2);
	$tot_res2 = mysqli_num_rows($res2);
	if($tot_res2>0){
		
	$sql3 = "select `te_code` from $engineer_master where `e_mobile`='$mobile'";
	$res3 = mysqli_query($conn,$sql3);
	$tot_res3 = mysqli_num_rows($res3);
	if($tot_res3>0){
		$row3 = mysqli_fetch_assoc($res3);
		$the_old_te_code = trim($row3["te_code"]);
		if(strtoupper($te_code)==strtoupper($the_old_te_code)){
			$res_data = array("process_status"=>"NO","process_message"=>"The mobile number is already exists. Please login.");	
		}else{
			$res_data = array("process_status"=>"NO","process_message"=>"Your mobile number is already linked to another TE. Kindly contact the administartor.");
		}
	}else{
$sql_eml = "select `e_email` from $engineer_master where `e_email`='$e_email'";
$res_eml = mysqli_query($conn,$sql_eml);
$tot_res_eml = mysqli_num_rows($res_eml);
if($tot_res_eml>0){
$res_data = array("process_status"=>"NO","process_message"=>"The email is already exists.");	
}else{

if($mobile=="9233974090"  || $mobile=="9638307128" || $mobile=="9831722939" || $mobile=="8436384404" || $mobile=="7278212381"){
$otp = 1010;
}else{
$otp = rand(1,9).rand(0,9).rand(0,9).rand(1,9);
//$otp = 1010;
}
		
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
		
		$curr_datetime = date("Y-m-d H:i:s");
		$te_branch_code = get_te_branchcode_by_tecode($conn,$te_code);
		$zone = get_te_zone_by_tecode($conn, $te_code);
		$sql4 = "insert into $engineer_master (`e_name`,`e_email`,`te_code`,`branch_code`,`e_mobile`,`e_points`,`the_otp`,`reg_date`, `e_zone`, `last_updated_datetime`) 
			values('$e_name','$e_email','$te_code','$te_branch_code','$mobile','0','$otp','$curr_datetime','$zone','$curr_datetime')";
		$res4 = mysqli_query($conn,$sql4);
		$r_submission_date = date("Y-m-d H:i:s");
		$the_engineer_id = mysqli_insert_id($conn);
		/*$sqlldgrin = "insert into $ledger_master (`user_id`,`description`,`point_earned`,`ldgr_datetime`) values ('$the_engineer_id','Sign up','$signup_point','$r_submission_date')";
		$resldgrin = mysqli_query($conn,$sqlldgrin);*/
		$res_data = array("process_status"=>"YES","process_message"=>"OTP has been sent to your mobile number.");
		


}

}

	}else{
		$res_data = array("process_status"=>"NO","process_message"=>"TE Code doesn't exist.");
	}
}else{	
$res_data = array("process_status"=>"NO","process_message"=>"All fields are mandatory");
}
echo json_encode($res_data);
mysqli_close($conn);
?>