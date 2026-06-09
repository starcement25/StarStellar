<?php
include "star_connection.php";
$app_version = "app_version";
$res = array();
$ios_app_version = $_POST["ios_app_version"] ? addslashes(trim($_POST["ios_app_version"])) : "1.0";
$sqlupd = "update $app_version set `app_version`='$ios_app_version' where `device_type`='IOS'";
$resupd = mysqli_query($conn,$sqlupd);
$res = array("process_sts"=>"YES","process_message"=>"Success");
mysqli_close($conn);
echo json_encode($res);
?>