<?php
include "star_connection.php";
$support_master = "support_master";
$res_msg = array();
$s_id = $_POST["s_id"] ? trim($_POST["s_id"]) : "";
$supp_sts = $_POST["supp_sts"] ? trim($_POST["supp_sts"]) : "";
if($s_id!='' && $supp_sts!=''){
	$sql5 = "update $support_master set `status`='$supp_sts' where `sid`='$s_id'";
	$res5 = mysqli_query($conn,$sql5);
$res_msg = array("process_sts"=>"YES","process_msg"=>"Success");
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");
}
echo json_encode($res_msg);
mysqli_close($conn);
?>