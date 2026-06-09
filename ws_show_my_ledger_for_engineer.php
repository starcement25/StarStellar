<?php
include "star_connection.php";
$engineer_master = "engineer_master";
$ledger_master = "ledger_master";
$te_master = "te_master";
$recommended_site_master = "recommended_site_master";
function site_recomendation_related_data($conn,$the_engineer_id,$site_recom_id){
$engineer_master = "engineer_master";
$ledger_master = "ledger_master";
$te_master = "te_master";
$recommended_site_master = "recommended_site_master";
$server_url = "https://" . $_SERVER['SERVER_NAME']."/";
$approved_recommendation_data = array("r_site_id"=>"","r_site_name"=>"","r_te_code"=>"","r_te_name"=>"","r_contact_person_name"=>"","r_mobile_no"=>"","r_address"=>"","r_site_potential_in_mt"=>"","r_contact_person_category_name"=>"","r_recomended_site_image_url"=>"","r_status"=>"","r_submission_date"=>"","r_submission_date_modified"=>"","point_earned"=>"");

$upload_dir = "recomend_site_pic/";
$image_url = $server_url."recomend_site_pic/";	
	$sql4 = "select *,$te_master.`te_name` from $recommended_site_master left join $te_master on $recommended_site_master.`r_te_code`=$te_master.`te_code` where $recommended_site_master.`r_engineer_id`='$the_engineer_id' and $recommended_site_master.`r_site_id`='$site_recom_id' ";
$res4 = mysqli_query($conn,$sql4);
$tot_res4 = mysqli_num_rows($res4);
if($tot_res4>0){
$row4 = mysqli_fetch_assoc($res4);
$r_site_id = $row4["r_site_id"];
$r_site_name = $row4["r_site_name"];
$r_te_code = $row4["r_te_code"];
$r_te_name = $row4["te_name"];
$r_site_name = $row4["r_site_name"];
$r_contact_person_name = $row4["r_contact_person_name"];
$r_mobile_no = $row4["r_mobile_no"];
$r_address = $row4["r_address"];
$r_site_potential_in_mt = $row4["r_site_potential_in_mt"];
$r_contact_person_category_name = $row4["r_contact_person_category_name"];
$r_recomended_site_image = $row4["r_recomended_site_image"] ? trim($row4["r_recomended_site_image"]) : "";
if($r_recomended_site_image!=''){
if(file_exists($upload_dir.$r_recomended_site_image)){
$r_recomended_site_image_url = $image_url.$r_recomended_site_image;
}else{
$r_recomended_site_image_url ="";
}
}else{
$r_recomended_site_image_url ="";
}
$r_status = $row4["r_status"];
$r_submission_date = $row4["r_submission_date"];
$r_submission_date_modified = date("d/m/y",strtotime($r_submission_date));
$r_point_earned_by_engineer = $row4["r_point_earned_by_engineer"] ? trim($row4["r_point_earned_by_engineer"]) : "0";
$approved_recommendation_data = array("r_site_id"=>$r_site_id,"r_site_name"=>$r_site_name,"r_te_code"=>$r_te_code,"r_te_name"=>$r_te_name,"r_contact_person_name"=>$r_contact_person_name,"r_mobile_no"=>$r_mobile_no,"r_address"=>$r_address,"r_site_potential_in_mt"=>$r_site_potential_in_mt,"r_contact_person_category_name"=>$r_contact_person_category_name,"r_recomended_site_image_url"=>$r_recomended_site_image_url,"r_status"=>$r_status,"r_submission_date"=>$r_submission_date_modified,"r_submission_date_modified"=>$r_submission_date_modified,"point_earned"=>$r_point_earned_by_engineer);

}

return $approved_recommendation_data;
	
}
$ledger_data = array();
$page_no = $_POST["page_no"] ? $_POST["page_no"] : 1;
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$limit = 20;
$start_from = (($page_no-1)*$limit);
if($the_engineer_id!=""){
	
	$sql2ep = "select `e_points` from $engineer_master where `eid`='$the_engineer_id'";
	$res2ep = mysqli_query($conn,$sql2ep);
	$tot_res2ep = mysqli_num_rows($res2ep);
	if($tot_res2ep!=""){
	$row2ep = mysqli_fetch_assoc($res2ep);
	$e_pointsep = $row2ep["e_points"] ? trim($row2ep["e_points"]) : "0";
	}else{
	$e_pointsep = "0";
	}
	
$sql2 = "select * from $ledger_master where `user_id`='$the_engineer_id' order by `ldgr_datetime` desc limit $start_from,$limit";
$res2 = mysqli_query($conn,$sql2);
$tot_res2 = mysqli_num_rows($res2);
if($tot_res2>0){
while($row2 = mysqli_fetch_assoc($res2)){
$ldgr_id = $row2["ldgr_id"];
$description = $row2["description"];
$point_earned = $row2["point_earned"] ? trim($row2["point_earned"]) : "";
$point_redeem = $row2["point_redeem"] ? trim($row2["point_redeem"]) : "";
$ldgr_datetime = $row2["ldgr_datetime"];
$ldgr_datetime = date("d/m/y",strtotime($ldgr_datetime));
$ldgr_type = $row2["ldgr_type"] ? trim($row2["ldgr_type"]) : "SIGNUP";
$related_id = $row2["related_id"] ? trim($row2["related_id"]) : "";
$related_data = array();
if($ldgr_type=="SITE_RECOMENDATION"){
$related_data = site_recomendation_related_data($conn,$the_engineer_id,$related_id);
}
$ledger_data[] = array("ldgr_id"=>$ldgr_id,"description"=>$description,"point_earned"=>$point_earned,"point_redeem"=>$point_redeem,"ldgr_datetime"=>$ldgr_datetime,"ldgr_type"=>$ldgr_type,"related_data"=>$related_data);
}
$res_data = array("process_status"=>"YES","process_message"=>"Success.","total_epoint"=>$e_pointsep,"ledger_data"=>$ledger_data);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"No new record found.","total_epoint"=>$e_pointsep,"ledger_data"=>$ledger_data);
}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>