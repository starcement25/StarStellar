<?php
include "star_connection.php";
$setting_master = "setting_master";
$res = array();
$anniversary_point = $_POST["anniversary_point"] ? addslashes(trim($_POST["anniversary_point"])) : "";
$last_updated_datetime = date('Y-m-d H:i:s');
$sqlckrds = "select `the_value` from $setting_master where `the_key_name`='anniversary_point'";
$resckrds = mysqli_query($conn,$sqlckrds);
$totresckrds = mysqli_num_rows($resckrds);
if($totresckrds>0){
	$sqlupd = "update $setting_master set `the_value`='$anniversary_point',`last_updated_datetime`='$last_updated_datetime' where `the_key_name`='anniversary_point'";
	$resupd = mysqli_query($conn,$sqlupd);
}else{
	$sqlin = "insert into $setting_master (`the_key_name`,`the_value`,`last_updated_datetime`) values ('anniversary_point','$anniversary_point','$last_updated_datetime')";
	$resin = mysqli_query($conn,$sqlin);
	if(!$resin){
		echo mysqli_error();
	}
}
$res = array("process_sts"=>"YES","process_message"=>"Success");
mysqli_close($conn);
echo json_encode($res);
?>