<?php
include "star_connection.php";
$te_master = "te_master";
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$product_master = "product_master";
function show_product_name_by_id($conn,$pid){
$product_master = "product_master";
$pname = "";
$pid = $pid ? trim($pid) : "";
if($pid!=""){
	$sql2p = "select `prod_name` from $product_master where `prod_id`='$pid'";
	$res2p = mysqli_query($conn,$sql2p);
	$tot_res2p = mysqli_num_rows($res2p);
	if($tot_res2p>0){
		$row2p = mysqli_fetch_assoc($res2p);
		$pname = addslashes(trim($row2p["prod_name"]));
	}
}

return $pname;
}
//$existing_id = $_POST["existing_id"] ? addslashes(trim($_POST["existing_id"])) : "";
$existing_id = "";
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$site_name = $_POST["site_name"] ? addslashes(trim($_POST["site_name"])) : "";
$contact_person_name = $_POST["contact_person_name"] ? addslashes(trim($_POST["contact_person_name"])) : "";
$mobile_no = $_POST["mobile_no"] ? addslashes(trim($_POST["mobile_no"])) : "";
$address = $_POST["address"] ? addslashes(trim($_POST["address"])) : "";
$site_potential_in_mt = $_POST["site_potential_in_mt"] ? addslashes(trim($_POST["site_potential_in_mt"])) : "";
$contact_person_category_name = $_POST["contact_person_category_name"] ? addslashes(trim($_POST["contact_person_category_name"])) : "";
$recomended_site_image_name = $_FILES["recomended_site_image"]["name"];
$recomended_site_image_type = $_FILES["recomended_site_image"]["type"];

$expected_product_id = $_POST["expected_product_id"] ? addslashes(trim($_POST["expected_product_id"])) : "";
$expected_consumption = $_POST["expected_consumption"] ? addslashes(trim($_POST["expected_consumption"])) : "";

$upload_dir = "recomend_site_pic/";
$supported_mime_type = array("image/jpeg","image/jpg","image/png","image/gif","image/bmp");

if($the_engineer_id!=''){

	

$sql3 = "select `eid`,`te_code` from $engineer_master where `eid`='$the_engineer_id'";
$res3 = mysqli_query($conn,$sql3);
$tot_res3 = mysqli_num_rows($res3);
if($tot_res3>0){
	$row3 = mysqli_fetch_assoc($res3);
	$te_code = $row3["te_code"] ? addslashes(trim($row3["te_code"])) : "";
	
$sql2 = "select `te_code` from $te_master where `te_code`='$te_code'";
$res2 = mysqli_query($conn,$sql2);
$tot_res2 = mysqli_num_rows($res2);
if($tot_res2>0){

if($site_name==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please enter site name.");			
}else if($contact_person_name==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please enter contact person name.");			
}else if($mobile_no==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please enter mobile number.");			
}else if($address==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please enter address.");			
}else if($site_potential_in_mt==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please enter site potential in MT.");			
}else if($contact_person_category_name==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please select contact person category.");			
}else if($expected_product_id==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please select an expected product.");			
}else if($expected_consumption==""){
$res_data = array("process_status"=>"NO","process_message"=>"Please enter expected consumption.");			
}else{
if($recomended_site_image_name!="" && !in_array($recomended_site_image_type,$supported_mime_type)){
$res_data = array("process_status"=>"NO","process_message"=>"Please select an image file.");			
}else{
if($recomended_site_image_name!=""){
$recomended_site_image_tmp_name = $_FILES["recomended_site_image"]["tmp_name"];
$recomended_site_image_name = str_replace(" ","_",$recomended_site_image_name);
$recomended_site_image_name = str_replace("  ","_",$recomended_site_image_name);
$recomended_site_image_name = str_replace("'","_",$recomended_site_image_name);
$recomended_site_image_name = str_replace('"',"_",$recomended_site_image_name);
$recomended_site_image_name = str_replace('-',"_",$recomended_site_image_name);
$unid = uniqid();
$the_site_image_name = "site_img_".$unid."_".$recomended_site_image_name;
$target_file = $upload_dir.$the_site_image_name;
$upload_the_ad_file = move_uploaded_file($recomended_site_image_tmp_name,$target_file);
if($upload_the_ad_file){

}else{
$the_site_image_name = "";
}
}else{
$the_site_image_name = "";
}
$expected_product_name = show_product_name_by_id($conn,$expected_product_id);
$r_submission_date = date("Y-m-d H:i:s");
$sql4 = "insert into $recommended_site_master (`r_te_code`,`r_engineer_id`,`r_site_name`,`existing_id`,`r_contact_person_name`,`r_mobile_no`,`r_address`,`r_site_potential_in_mt`,`r_contact_person_category_name`,`r_recomended_site_image`,`expected_product_id`,`expected_product_name`,`expected_consumption`,`r_submission_date`,`r_last_updated_datetime`) values('$te_code','$the_engineer_id','$site_name','$existing_id','$contact_person_name','$mobile_no','$address','$site_potential_in_mt','$contact_person_category_name','$the_site_image_name','$expected_product_id','$expected_product_name','$expected_consumption','$r_submission_date','$r_submission_date')";
$res4 = mysqli_query($conn,$sql4);
$the_recommended_id = mysqli_insert_id($conn);
$res_data = array("process_status"=>"YES","process_message"=>"The site details are successfully recommended.","the_recommended_id"=>$the_recommended_id);	
}			
}


}else{
	$res_data = array("process_status"=>"NO","process_message"=>"The TE Code doesn't exist.");
}	
		
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Your engineer account doesn't exist.");
}
	
}else{	
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>