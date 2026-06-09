<?php
ini_set('memory_limit', '9999M');
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include "star_connection.php";
$res_msg = array();
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$recommended_site_asm_activity_log = "recommended_site_asm_activity_log";
$engineer_transfer_log = "engineer_transfer_log";

$tf_te_code = $_POST["tf_te_code"] ? addslashes(trim($_POST["tf_te_code"])) : "";
$tt_te_code = $_POST["tt_te_code"] ? addslashes(trim($_POST["tt_te_code"])) : "";
$quick_tf_ids = $_POST["quick_tf_ids"] ? addslashes(trim($_POST["quick_tf_ids"])) : "";
$quick_tf_ids_arr = array();
if($quick_tf_ids!=""){
	$quick_tf_ids_arr = explode(";",$quick_tf_ids);
}
$the_wishlist_data = "";
if($tf_te_code==""){
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Please select Transfer From TE.");
}else if(count($quick_tf_ids_arr)==0){
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Please check atleast one engineer of Transfer From TE.");
}else if($tt_te_code==""){
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Please select Transfer TO TE.");
}else{
	$curr_date_time = date("Y-m-d H:i:s");
	$quick_tf_ids_arr_str_save_log = implode(",",$quick_tf_ids_arr);
	
	$quick_tf_ids_arr_str = implode("','",$quick_tf_ids_arr);
	$the_te_branch_code = get_te_branchcode_by_tecode($conn,$tt_te_code);
	
	
$sql44 = "update $engineer_master set `branch_code`='$the_te_branch_code',`te_code`='$tt_te_code',`last_updated_datetime`='$curr_date_time' where `te_code`='$tf_te_code' and `eid` in('".$quick_tf_ids_arr_str."')";
$res44 = mysqli_query($conn,$sql44);

$sql_in_log = "insert into $engineer_transfer_log (`eng_ids`,`from_te_code`,`to_te_code`,`to_te_branch_code`,`datetime`) values ('$quick_tf_ids_arr_str_save_log','$tf_te_code','$tt_te_code','$the_te_branch_code','$curr_date_time')";
$res_in_log = mysqli_query($conn,$sql_in_log);

$sql_sr = "update $recommended_site_master set `r_te_code`='$tt_te_code' where `r_te_code`='$tf_te_code' and `r_engineer_id` in('".$quick_tf_ids_arr_str."')";
$res_sr = mysqli_query($conn,$sql_sr);

$sql_srl = "update $recommended_site_asm_activity_log set `r_te_code`='$tt_te_code' where `r_te_code`='$tf_te_code' and `r_engineer_id` in('".$quick_tf_ids_arr_str."')";
$res_srl = mysqli_query($conn,$sql_srl);




$res_msg = array("process_sts"=>"YES","process_msg"=>"Success.");
}
echo json_encode($res_msg);
mysqli_close($conn);
?>