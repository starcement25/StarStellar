<?php
include "star_connection.php";
$setting_master = "setting_master";
$res = array();
$the_setting_key = $_POST["the_setting_key"] ? addslashes(trim($_POST["the_setting_key"])) : "";
$the_setting_val = $_POST["the_setting_val"] ? addslashes(trim($_POST["the_setting_val"])) : "";
$last_updated_datetime = date('Y-m-d H:i:s');
if($the_setting_key!=""){
$sqlckrds = "select `the_value` from $setting_master where `the_key_name`='$the_setting_key'";
$resckrds = mysqli_query($conn,$sqlckrds);
$totresckrds = mysqli_num_rows($resckrds);
if($totresckrds>0){
	$sqlupd = "update $setting_master set `the_value`='$the_setting_val',`last_updated_datetime`='$last_updated_datetime' where `the_key_name`='$the_setting_key'";
	$resupd = mysqli_query($conn,$sqlupd);
}else{
	$sqlin = "insert into $setting_master (`the_key_name`,`the_value`,`last_updated_datetime`) values ('$the_setting_key','$the_setting_val','$last_updated_datetime')";
	$resin = mysqli_query($conn,$sqlin);
	if(!$resin){
		echo mysqli_error();
	}
}
$res = array("process_sts"=>"YES","process_message"=>"Success");
}else{
$res = array("process_sts"=>"NO","process_message"=>"Something went wrong.");	
}
mysqli_close($conn);
echo json_encode($res);
?>