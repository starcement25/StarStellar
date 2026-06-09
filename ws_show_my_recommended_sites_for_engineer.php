<?php
include "star_connection.php";
$te_master = "te_master";
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$my_recommended_site_data = array();
$upload_dir = "recomend_site_pic/";
$image_url = $server_url."recomend_site_pic/";
$curr_datetime = date("Y-m-d H:i");
$the_status = "PENDING";
$tot_res2cnt = 0;
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$limit = 10;
if($the_engineer_id!=""){
$sql21 = "select `e_name`,`e_mobile`,`e_email` from $engineer_master where `eid`='$the_engineer_id'";
$res21 = mysqli_query($conn,$sql21);
$tot_res21 = mysqli_num_rows($res21);
if($tot_res21>0){
$row21 = mysqli_fetch_assoc($res21);
$r_e_name = $row21["e_name"] ? trim($row21["e_name"]) : "";
$r_e_mobile = $row21["e_mobile"] ? trim($row21["e_mobile"]) : "";
$r_e_email = $row21["e_email"] ? trim($row21["e_email"]) : "";	

$sql2 = "select * from $recommended_site_master where `r_engineer_id`='$the_engineer_id' and (`existing_id` is null or `existing_id`='') group by `r_site_name` order by `r_submission_date` desc";
$res2 = mysqli_query($conn,$sql2);
$tot_res2 = mysqli_num_rows($res2);
if($tot_res2>0){
while($row2 = mysqli_fetch_assoc($res2)){
$r_site_id = $row2["r_site_id"];
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
$r_expected_product_id = $row2["expected_product_id"] ? trim($row2["expected_product_id"]) : "";
$r_expected_product_name = $row2["expected_product_name"] ? trim($row2["expected_product_name"]) : "";
$r_expected_consumption = $row2["expected_consumption"] ? trim($row2["expected_consumption"]) : "";

$my_recommended_site_data[] = array("r_site_id"=>$r_site_id,"r_site_name"=>$r_site_name,"r_contact_person_name"=>$r_contact_person_name,"r_mobile_no"=>$r_mobile_no,"r_address"=>$r_address,"r_site_potential_in_mt"=>$r_site_potential_in_mt,"r_contact_person_category_name"=>$r_contact_person_category_name,"r_recomended_site_image_url"=>$r_recomended_site_image_url,"r_status"=>$r_status,"r_submission_date"=>$r_submission_date,"r_submission_date_modified"=>$r_submission_date_modified,"expected_product_id"=>$r_expected_product_id,"expected_product_name"=>$r_expected_product_name,"expected_consumption"=>$r_expected_consumption,"r_recomended_by"=>$r_e_name,"r_contact_no"=>$r_e_mobile,"r_email"=>$r_e_email);
}
$res_data = array("process_status"=>"YES","process_message"=>"Success.","my_recommended_site_data"=>$my_recommended_site_data);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"No record found.","my_recommended_site_data"=>$my_recommended_site_data);
}

}else{
$res_data = array("process_status"=>"NO","process_message"=>"Engineer details not found.");	
}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>