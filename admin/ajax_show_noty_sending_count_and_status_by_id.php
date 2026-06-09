<?php
include "star_connection.php";
$server_url = "https://" . $_SERVER['SERVER_NAME']."/";
$notification_message = "notification_message";
$the_n_id = $_REQUEST["the_n_id"] ? addslashes(trim($_REQUEST["the_n_id"])) : "";
if($the_n_id!=""){
$sqlall = "select `sending_count`,`status` from $notification_message where `id`='$the_n_id'";
$resall = mysqli_query($conn,$sqlall);
$totall = mysqli_num_rows($resall);
if($totall>0){
$row11=mysqli_fetch_assoc($resall);
$n_cr_cnt = $row11["sending_count"];
$n_cr_sts = $row11["status"];
$res_data = array("process_sts"=>"YES","process_msg"=>"Success.","n_cr_cnt"=>$n_cr_cnt,"n_cr_sts"=>$n_cr_sts);
}else{
	$res_data = array("process_sts"=>"NO","process_msg"=>"No record found.");
}
}else{	
	$res_data = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");
}	
echo json_encode($res_data);
mysqli_close($conn);
?>