<?php
include "star_connection.php";
$engineer_master = "engineer_master";
$res_msg = array();
$eng_id = $_POST["eng_id"] ? trim($_POST["eng_id"]) : "";
$eng_sts = $_POST["eng_sts"] ? trim($_POST["eng_sts"]) : "";
$date=gmdate('d',strtotime('+330 minute'));
$month=gmdate('m',strtotime('+330 minute'));
$year=gmdate('Y',strtotime('+330 minute'));
			
$hour=gmdate('H',strtotime('+330 minute'));
$minute=gmdate('i',strtotime('+330 minute'));
$second=gmdate('s',strtotime('+330 minute'));
			//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
$datetime =$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
if($eng_id!='' && $eng_sts!=''){
	$sql5 = "update $engineer_master set `status`='$eng_sts',status_update_datetime='$datetime' where `eid`='$eng_id'";
	$res5 = mysqli_query($conn,$sql5);
$res_msg = array("process_sts"=>"YES","process_msg"=>"Success");
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");
}
echo json_encode($res_msg);
mysqli_close($conn);
?>