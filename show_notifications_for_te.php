<?php
include "star_connection.php";
$notification_message = "notification_message";
$te_master = "te_master";
$the_branch_code_str = "";
$notification_data = array();
$curr_date_time  = date("Y-m-d H:i:s");
$img_dir = "admin/noty_images/";
$img_url = $server_url."admin/noty_images/";
$page_no = $_POST["page_no"] ? $_POST["page_no"] : 1;
$limit = 10;
$start_from = (($page_no-1)*$limit);
$the_te_code = $_REQUEST["te_code"] ? addslashes(trim($_REQUEST["te_code"])) : "";
$last_update_datetime = $_REQUEST["last_update_datetime"] ? addslashes(trim($_REQUEST["last_update_datetime"])) : "";
if($the_te_code!=""){


$sql_get_te = "select `branch_code` from $te_master where `te_code`='$the_te_code'";
$res_get_te = mysqli_query($conn,$sql_get_te);
$totres_get_te = mysqli_num_rows($res_get_te);
if($totres_get_te>0){
	$row_get_te = mysqli_fetch_assoc($res_get_te);
	$the_branch_code = $row_get_te["branch_code"] ? trim($row_get_te["branch_code"]) : "";
	if($the_branch_code!=""){
		$the_branch_code_arr = explode(",",$the_branch_code);
		$the_branch_code_arr=array_map('trim',$the_branch_code_arr);
		if($the_branch_code_arr!=""){
		$the_branch_code_str = implode(",",$the_branch_code_arr);
		}
	}
}



if($last_update_datetime!=""){
	$where_qry = " and `date_time`>'$last_update_datetime' ";
}else{
	$where_qry = "";
}

$sqlall = "select * from $notification_message where (IF(`user_type` = 'SINGLE_TE', IF(`single_user_id` = '$the_te_code', 1, 0), 0) = 1) or (IF(`user_type` = 'BRANCH_WISE_TE', IF(FIND_IN_SET(`branch_code`, '".$the_branch_code_str."'), 2, 0), 0) = 2) or (`user_type`='ALL' or `user_type`='TE')  order by `date_time` desc limit $start_from,$limit";

$resall = mysqli_query($conn,$sqlall);
$totall = mysqli_num_rows($resall);
if($totall>0){
	while($row11=mysqli_fetch_assoc($resall)){
		$nid = $row11["id"];
		$m_title = $row11["title"];
		$m_message = $row11["message"];
		$m_file_type = $row11["file_type"];
		$m_image_name = $row11["image_name"] ? trim($row11["image_name"]) : "";
		if($m_image_name!=""){
			if(file_exists($img_dir.$m_image_name)){
				$m_image_link = $img_url.$m_image_name;
			}else{
			$m_image_link ="";
		}
		}else{
			$m_image_link ="";
		}
		$n_date_time = $row11["date_time"];
		$notification_data[] = array("nid"=>$nid,"m_title"=>$m_title,"m_message"=>$m_message,"m_file_type"=>$m_file_type,"m_image_link"=>$m_image_link,"n_date_time"=>$n_date_time);
	}	

$res_data = array("process_status"=>"YES","process_message"=>"Success.","curr_date_time"=>$curr_date_time,"notification_data"=>$notification_data);
}else{
	$res_data = array("process_status"=>"NO","process_message"=>"No new record found.","curr_date_time"=>$curr_date_time);
}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"All fields are mandatory.","curr_date_time"=>$curr_date_time);
}	
echo json_encode($res_data);
mysqli_close($conn);
?>