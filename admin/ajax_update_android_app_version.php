<?php
include "star_connection.php";
$app_version = "app_version";
$res = array();
$android_app_version = $_POST["android_app_version"] ? addslashes(trim($_POST["android_app_version"])) : "1.0";
$sqlupd = "update $app_version set `app_version`='$android_app_version' where `device_type`='ANDROID'";
$resupd = mysqli_query($conn,$sqlupd);
$res = array("process_sts"=>"YES","process_message"=>"Success");
mysqli_close($conn);
echo json_encode($res);
?>