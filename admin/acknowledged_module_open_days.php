<?php
include "star_connection.php";
$setting_master = "setting_master";
$res = array();
$acknowledged_module_open_days = $_POST["acknowledged_module_open_days"] ? addslashes(trim($_POST["acknowledged_module_open_days"])) : "";
$last_updated_datetime = date('Y-m-d H:i:s');
$sqlckrds = "select `the_value` from $setting_master where `the_key_name`='acknowledged_module_open_days'";
$resckrds = mysqli_query($conn,$sqlckrds);
$totresckrds = mysqli_num_rows($resckrds);
if($totresckrds>0){
	$sqlupd = "update $setting_master set `the_value`='$acknowledged_module_open_days',`last_updated_datetime`='$last_updated_datetime' where `the_key_name`='acknowledged_module_open_days'";
	$resupd = mysqli_query($conn,$sqlupd);
}else{
	$sqlin = "insert into $setting_master (`the_key_name`,`the_value`,`last_updated_datetime`) values ('acknowledged_module_open_days','$acknowledged_module_open_days','$last_updated_datetime')";
	$resin = mysqli_query($conn,$sqlin);
	if(!$resin){
		echo mysqli_error();
	}
}
$res = array("process_sts"=>"YES","process_message"=>"Success");
mysqli_close($conn);
echo json_encode($res);
?>