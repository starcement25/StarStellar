<?php
include "star_connection.php";
date_default_timezone_set('Asia/Kolkata');
$te_master = "te_master";
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$engineer_data = array();
$upload_dir = "en_profile_pic/";
$image_url = $server_url."en_profile_pic/";
$curr_datetime = date("Y-m-d H:i");
$tot_res2cnt = 0;
$date_before_twelve_month = date('Y-m-d H:i:s',strtotime("-12 month"));
$date_before_three_month = date('Y-m-d H:i:s',strtotime("-3 month"));
$date_before_six_month = date('Y-m-d H:i:s',strtotime("-6 month"));
$page_no = $_POST["page_no"] ? $_POST["page_no"] : 1;
$te_code = $_POST["te_code"] ? addslashes(trim($_POST["te_code"])) : "";
$search_term = $_POST["search_term"] ? addslashes(trim($_POST["search_term"])) : "";
$status = $_POST["status"] ? addslashes(trim($_POST["status"])) : "";
$limit = 10;
$start_from = (($page_no-1)*$limit);
if($te_code!=""){

if($search_term!=""){
$search_term_qry = " and ($engineer_master.`e_name` like '%$search_term%' or $engineer_master.`e_mobile` like '%$search_term%')";	
}else{
$search_term_qry = "";
}

if($status!=""){
	if($status=="ACTIVE"){
		$status_qry = " and `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`>='$date_before_twelve_month' ";	
	}else if($status=="SEMI_ACTIVE"){
		/*$status_qry = " and `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_three_month' and `latest_recommended_site_master`.`r_submission_date`>='$date_before_six_month' ";*/
		$status_qry = " and `latest_recommended_site_master`.`r_submission_date` is null or (`latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_twelve_month') ";
		
	}else if($status=="INACTIVE"){
		$status_qry = " and `latest_recommended_site_master`.`r_submission_date` is null or (`latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_twelve_month') ";
	}else{
$status_qry = "";
}

}else{
$status_qry = "";
}


$sql2 = "SELECT $engineer_master.`eid`,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`e_city_town`,$engineer_master.`e_profile_image`,`latest_recommended_site_master`.`r_submission_date` FROM $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where $engineer_master.`te_code`='$te_code' and $engineer_master.`status`='ACTIVE' $search_term_qry $status_qry order by `latest_recommended_site_master`.`r_submission_date` desc limit $start_from,$limit";


//$sql2 = "select * from $engineer_master where `te_code`='$te_code' $search_term_qry order by `e_name` asc limit $start_from,$limit";
$res2 = mysqli_query($conn,$sql2);
$tot_res2 = mysqli_num_rows($res2);
if($tot_res2>0){
while($row2 = mysqli_fetch_assoc($res2)){
$eid = $row2["eid"];
$e_name = $row2["e_name"];
$e_mobile = $row2["e_mobile"] ? trim($row2["e_mobile"]) : "";
$e_city_town = $row2["e_city_town"] ? trim($row2["e_city_town"]) : "";
$e_profile_image = $row2["e_profile_image"] ? trim($row2["e_profile_image"]) : "";
if($e_profile_image!=''){
if(file_exists($upload_dir.$e_profile_image)){
$e_profile_image_url = $image_url.$e_profile_image;
}else{
$e_profile_image_url ="";
}
}else{
$e_profile_image_url ="";
}
$r_submission_date = $row2["r_submission_date"] ? trim($row2["r_submission_date"]) : "";
if($r_submission_date!=""){
$the_date_time_stamp = strtotime($r_submission_date);
$date_before_twelve_month_stamp = strtotime($date_before_twelve_month);
$date_before_three_month_stamp = strtotime($date_before_three_month);

$date_before_six_month_stamp = strtotime($date_before_six_month);

if($the_date_time_stamp>=$date_before_twelve_month_stamp){
	$e_status = "ACTIVE";
$e_status_show = "Active";
}else{
	$e_status = "INACTIVE";
$e_status_show = "Inactive";
}
}else{
$e_status = "INACTIVE";
$e_status_show = "Inactive";	
}

$engineer_data[] = array("eid"=>$eid,"e_name"=>$e_name,"e_mobile"=>$e_mobile,"e_city_town"=>$e_city_town,"e_profile_image_url"=>$e_profile_image_url,"e_status"=>$e_status,"e_status_show"=>$e_status_show,"r_submission_date"=>$r_submission_date);
}
$res_data = array("process_status"=>"YES","process_message"=>"Success.","engineer_data"=>$engineer_data);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"No new record found.","engineer_data"=>$engineer_data);
}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>