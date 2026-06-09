<?php
header('Content-type: text/html; charset=utf-8');
include "star_connection.php";
mysqli_set_charset("UTF8");
$order_header = "order_header";
$res_data = array();
$the_id = $_POST["curr_ordid"] ? addslashes(trim($_POST["curr_ordid"])) : "";
$stsval = $_POST["stsval"] ? addslashes(trim($_POST["stsval"])) : "";
if($the_id!="" && $stsval!=""){
	$sqlup = "update $order_header set `status`='$stsval' where `order_no`='$the_id'";
	$resup = mysqli_query($conn,$sqlup);
	$res_data = array("process_status"=>"YES","process_message"=>"Success.");
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}	
echo json_encode($res_data);
mysqli_close($conn);
?>