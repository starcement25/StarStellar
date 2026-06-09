<?php
include "star_connection.php";
$te_master = "te_master";
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$rejected_recommendation_data = array();
$upload_dir = "recomend_site_pic/";
$image_url = $server_url."recomend_site_pic/";
$curr_datetime = date("Y-m-d H:i");
$the_status = "REJECTED";
$tot_res2cnt = 0;
$page_no = $_POST["page_no"] ? $_POST["page_no"] : 1;
$te_code = $_POST["te_code"] ? addslashes(trim($_POST["te_code"])) : "";
$the_max_date_time = $_POST["the_max_date_time"] ? addslashes(trim($_POST["the_max_date_time"])) : "";
$limit = 10;
$start_from = (($page_no-1)*$limit);
if($te_code!=""){

if($the_max_date_time!=""){
$max_date_qry = " and $recommended_site_master.`r_submission_date`<='$the_max_date_time'";	
}else{
$max_date_qry = "";
}

$sql2cnt = "select $recommended_site_master.`r_site_id` from $recommended_site_master left join $engineer_master on $recommended_site_master.`r_engineer_id`=$engineer_master.`eid` where $recommended_site_master.`r_te_code`='$te_code' and $recommended_site_master.`r_status`='$the_status' $max_date_qry";
$res2cnt = mysqli_query($conn,$sql2cnt);
$tot_res2cnt = mysqli_num_rows($res2cnt);
$tot_res2cnt = intval($tot_res2cnt);

$sql2 = "select *,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`e_email` from $recommended_site_master left join $engineer_master on $recommended_site_master.`r_engineer_id`=$engineer_master.`eid` where $recommended_site_master.`r_te_code`='$te_code' and $recommended_site_master.`r_status`='$the_status' $max_date_qry order by $recommended_site_master.`r_submission_date` desc limit $start_from,$limit";
$res2 = mysqli_query($conn,$sql2);
$tot_res2 = mysqli_num_rows($res2);
if($tot_res2>0){
while($row2 = mysqli_fetch_assoc($res2)){
	$buyer_details_data = array();
	$the_quality_parameter_data = array();
$r_site_id = $row2["r_site_id"];
$r_site_name = $row2["r_site_name"];
$r_te_code = $row2["r_te_code"];
$r_e_name = $row2["e_name"];
$r_e_mobile = $row2["e_mobile"];
$r_e_email = $row2["e_email"];
$r_site_name = $row2["r_site_name"];
$r_contact_person_name = $row2["r_contact_person_name"];
$r_mobile_no = $row2["r_mobile_no"];
$r_address = $row2["r_address"];
$r_site_potential_in_mt = $row2["r_site_potential_in_mt"];
$r_contact_person_category_name = $row2["r_contact_person_category_name"];
$r_recomended_site_image = $row2["r_recomended_site_image"] ? trim($row2["r_recomended_site_image"]) : "";
if($r_recomended_site_image!=''){
if(file_exists($upload_dir.$r_recomended_site_image)){
$r_recomended_site_image_url = $image_url.$r_recomended_site_image;
}else{
$r_recomended_site_image_url ="";
}
}else{
$r_recomended_site_image_url ="";
}
$r_status = $row2["r_status"];
$r_submission_date = $row2["r_submission_date"];
$r_submission_date_modified = date("d/m/y",strtotime($r_submission_date));

$r_te_interaction_date = $row2["r_te_interaction_date"];
if($r_te_interaction_date!=""){
	$r_te_interaction_date = date("d/m/y",strtotime($r_te_interaction_date));
}
$r_te_interaction_comment = $row2["r_te_interaction_comment"];

$r_expected_product_id = $row2["expected_product_id"] ? trim($row2["expected_product_id"]) : "";
$r_expected_product_name = $row2["expected_product_name"] ? trim($row2["expected_product_name"]) : "";
$r_expected_consumption = $row2["expected_consumption"] ? trim($row2["expected_consumption"]) : "";

$rejected_recommendation_data[] = array("r_site_id"=>$r_site_id,"r_site_name"=>$r_site_name,"r_te_code"=>$r_te_code,"r_recomended_by"=>$r_e_name,"r_contact_no"=>$r_e_mobile,"r_email"=>$r_e_email,"r_contact_person_name"=>$r_contact_person_name,"r_mobile_no"=>$r_mobile_no,"r_address"=>$r_address,"r_site_potential_in_mt"=>$r_site_potential_in_mt,"r_contact_person_category_name"=>$r_contact_person_category_name,"r_recomended_site_image_url"=>$r_recomended_site_image_url,"r_status"=>$r_status,"r_submission_date"=>$r_submission_date,"r_submission_date_modified"=>$r_submission_date_modified,"rejected_date"=>$r_te_interaction_date,"comments"=>$r_te_interaction_comment,"expected_product_id"=>$r_expected_product_id,"expected_product_name"=>$r_expected_product_name,"expected_consumption"=>$r_expected_consumption);
}
$res_data = array("process_status"=>"YES","process_message"=>"Success.","tot_count"=>$tot_res2cnt,"rejected_recommendation_data"=>$rejected_recommendation_data);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"No new record found.","rejected_recommendation_data"=>$rejected_recommendation_data);
}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>