<?php
set_time_limit(0);
include "star_connection.php";
$res_msg = array();
$branch_schemes_PDF = "branch_schemes_PDF";
$date_time = date("Y-m-d H:i:s");
$the_sl_id = $_POST["the_sl_id"] ? addslashes(trim($_POST["the_sl_id"])) : "";
$dp_start_dt = $_POST["dp_start_dt"] ? addslashes(trim($_POST["dp_start_dt"])) : "";
$dp_end_dt = $_POST["dp_end_dt"] ? addslashes(trim($_POST["dp_end_dt"])) : "";
if($the_sl_id!=""){
$sql14 = "update $branch_schemes_PDF set `start_date`='$dp_start_dt',`end_date`='$dp_end_dt',`download_time`='$date_time' where `sl_no`='$the_sl_id'";
$res14 = mysqli_query($conn,$sql14);
$res_msg = array("process_sts"=>"YES","process_msg"=>"Success.");
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");	
}
echo json_encode($res_msg);
mysqli_close($conn);
?>