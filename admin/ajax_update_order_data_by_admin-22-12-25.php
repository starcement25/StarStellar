<?php
include "star_connection.php";
$gift_order_master = "gift_order_master";
$res_msg = array();
$the_ord_id = $_POST["the_ord_id"] ? trim($_POST["the_ord_id"]) : "";
$admin_sl_ord_sts = $_POST["admin_sl_ord_sts"] ? trim($_POST["admin_sl_ord_sts"]) : "";
$admin_del_dt = $_POST["admin_del_dt"] ? trim($_POST["admin_del_dt"]) : "";
$admin_amz_ord_id = $_POST["admin_amz_ord_id"] ? addslashes(trim($_POST["admin_amz_ord_id"])) : "";
$admin_amz_ord_link = $_POST["admin_amz_ord_link"] ? addslashes(trim($_POST["admin_amz_ord_link"])) : "";

if($the_ord_id!=''){
	$sql5 = "update $gift_order_master set `status`='$admin_sl_ord_sts',`delivery_date`='$admin_del_dt',`amazon_order_id`='$admin_amz_ord_id',`amazon_order_link`='$admin_amz_ord_link' where `g_order_id`='$the_ord_id'";
	$res5 = mysqli_query($conn,$sql5);
$res_msg = array("process_sts"=>"YES","process_msg"=>"Success");
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");
}
echo json_encode($res_msg);
mysqli_close($conn);
?>