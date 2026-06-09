<?php
include "star_connection.php";
$setting_master = "setting_master";
$res = array();
$signup_point = $_POST["signup_point"] ? addslashes(trim($_POST["signup_point"])) : "";
$last_updated_datetime = date('Y-m-d H:i:s');
$sqlckrds = "select `the_value` from $setting_master where `the_key_name`='signup_point'";
$resckrds = mysqli_query($conn,$sqlckrds);
$totresckrds = mysqli_num_rows($resckrds);
if($totresckrds>0){
	$sqlupd = "update $setting_master set `the_value`='$signup_point',`last_updated_datetime`='$last_updated_datetime' where `the_key_name`='signup_point'";
	$resupd = mysqli_query($conn,$sqlupd);
}else{
	$sqlin = "insert into $setting_master (`the_key_name`,`the_value`,`last_updated_datetime`) values ('signup_point','$signup_point','$last_updated_datetime')";
	$resin = mysqli_query($conn,$sqlin);
	if(!$resin){
		echo mysqli_error();
	}
}
$res = array("process_sts"=>"YES","process_message"=>"Success");
mysqli_close($conn);
echo json_encode($res);
?>