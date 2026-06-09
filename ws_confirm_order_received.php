<?php
include "star_connection.php";
$engineer_master = "engineer_master";
$gift_order_master ="gift_order_master";
$curr_datetime = date("Y-m-d H:i");
$order_id = $_POST["order_id"] ? $_POST["order_id"] : "";
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$is_order_received = $_POST["is_order_received"] ? $_POST["is_order_received"] : "NO";
if($order_id!="" && $the_engineer_id!=""){

$sql2e = "select `eid` from $engineer_master where `eid`='$the_engineer_id'";
$res2e = mysqli_query($conn,$sql2e);
$tot_res2e = mysqli_num_rows($res2e);
if($tot_res2e>0){
$sql2 = "select `g_order_id`,`user_id` from $gift_order_master where `g_order_id`='$order_id' and `user_id`='$the_engineer_id'";
$res2 = mysqli_query($conn,$sql2);
$tot_res2 = mysqli_num_rows($res2);
if($tot_res2>0){
$sqlspup = "update $gift_order_master set `is_order_received`='$is_order_received' where `g_order_id`='$order_id' and `user_id`='$the_engineer_id'";
$resspup = mysqli_query($conn,$sqlspup);
$res_data = array("process_status"=>"YES","process_message"=>"Successfully submitted.");
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