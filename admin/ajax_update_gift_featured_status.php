<?php
include "star_connection.php";
include "insertGiftStatusLog.php";

$gift_master = "gift_master";
$res_msg = array();
$eng_id = $_POST["eng_id"] ? trim($_POST["eng_id"]) : "";
$eng_sts = $_POST["eng_sts"] ? trim($_POST["eng_sts"]) : "";
if($eng_id!='' && $eng_sts!=''){

	$oldRes = mysqli_query($conn, "SELECT featured FROM $gift_master WHERE id='$eng_id'");
	$oldRow = mysqli_fetch_assoc($oldRes);

	$sql5 = "update $gift_master set `featured`='$eng_sts' where `id`='$eng_id'";
	$res5 = mysqli_query($conn,$sql5);
	insertGiftStatusLog($eng_id, 'FEATURED', $oldRow['featured'], $eng_sts);
$res_msg = array("process_sts"=>"YES","process_msg"=>"Success");
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");
}
echo json_encode($res_msg);
mysqli_close($conn);
?>