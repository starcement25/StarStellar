<?php
include "star_connection.php";
$selected_menu_for_user = "selected_menu_for_user";
$res_msg = array();
$the_user_id = $_POST["the_user_id"] ? trim($_POST["the_user_id"]) : "";
$the_status = $_POST["the_status"] ? trim($_POST["the_status"]) : "";
$menu_id = $_POST["menu_id"] ? trim($_POST["menu_id"]) : "";

//$the_user_id = '28';
//$the_status ='INACTIVE';
//$menu_id ='3';

if($the_user_id!='' && $the_status!='' && $menu_id!=''){
	$sql5 = "update $selected_menu_for_user set `edit_access`='$the_status' where `user_id`='$the_user_id' AND `menu_id`='$menu_id'";

	$res5 = mysqli_query($conn,$sql5);
$res_msg = array("process_sts"=>"YES","process_msg"=>"Success");
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");
}
echo json_encode($res_msg);
mysqli_close($conn);
?>