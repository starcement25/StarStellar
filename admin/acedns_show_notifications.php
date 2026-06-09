<?php
include "star_connection.php";
$server_url = "http://" . $_SERVER['SERVER_NAME']."/";
$notification_message = "notification_message";
$notification_data = array();
$curr_date_time  = date("Y-m-d H:i:s");
$img_dir = "admin/noty_images/";
$img_url = $server_url."admin/noty_images/";
$the_branch_code = $_REQUEST["the_branch_code"] ? addslashes(trim($_REQUEST["the_branch_code"])) : "";
$last_update_datetime = $_REQUEST["last_update_datetime"] ? addslashes(trim($_REQUEST["last_update_datetime"])) : "";
if($the_branch_code!=""){

if($last_update_datetime!=""){
	$where_qry = " and `date_time`>'$last_update_datetime' ";
}else{
	$where_qry = "";
}

$sqlall = "select * from $notification_message where (FIND_IN_SET('$the_branch_code',`branch_code`) or `branch_code`='ALL') $where_qry order by `date_time` desc";
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