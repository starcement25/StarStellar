<?php
include "star_connection.php";
$engineer_master = "engineer_master";
$gift_order_master ="gift_order_master";
$support_master = "support_master";
$curr_datetime = date("Y-m-d H:i");
$order_id = $_POST["order_id"] ? $_POST["order_id"] : "";
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$support_type = $_POST["support_type"] ? $_POST["support_type"] : "";
$comment = $_POST["comment"] ? addslashes(trim($_POST["comment"])) : "";


if($order_id!="" && $the_engineer_id!="" && $support_type!=""){

$sql2e = "select `eid` from $engineer_master where `eid`='$the_engineer_id'";
$res2e = mysqli_query($conn,$sql2e);
$tot_res2e = mysqli_num_rows($res2e);
if($tot_res2e>0){
$sql2 = "select `g_order_id`,`user_id` from $gift_order_master where `g_order_id`='$order_id' and `user_id`='$the_engineer_id'";
$res2 = mysqli_query($conn,$sql2);
$tot_res2 = mysqli_num_rows($res2);
if($tot_res2>0){
$pros_msg = "";
$sql2sp = "select `sid` from $support_master where `order_id`='$order_id' and `user_id`='$the_engineer_id'";
$res2sp = mysqli_query($conn,$sql2sp);
$tot_res2sp = mysqli_num_rows($res2sp);
if($tot_res2sp>0){
$row2sp = mysqli_fetch_assoc($res2sp);
$the_sid = $row2sp["sid"];
$datetime = date("Y-m-d H:i:s");
$sqlspup = "update $support_master set `s_type`='$support_type',`s_comment`='$comment',`last_updated_datetime`='$datetime' where `sid`='$the_sid'";
$resspup = mysqli_query($conn,$sqlspup);
$pros_msg .= "Successfully updated.";
}else{
	$datetime = date("Y-m-d H:i:s");
	$sqlspin = "insert into $support_master (`order_id`,`user_id`,`s_type`,`s_comment`,`submitted_datetime`,`last_updated_datetime`) values ('$order_id','$the_engineer_id','$support_type','$comment','$datetime','$datetime')";
	$resspin = mysqli_query($conn,$sqlspin);
	$pros_msg .= "Successfully submitted.";
}

$res_data = array("process_status"=>"YES","process_message"=>$pros_msg);

}else{
$res_data = array("process_status"=>"NO","process_message"=>"You have no permission to submit this form.");
}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"The engineer details doesn't exist.");
}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"All fields are mandatory.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>