<?php
include "star_connection.php";
$te_master = "te_master";
$engineer_master = "engineer_master";
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$e_name = $_POST["e_name"] ? addslashes(trim($_POST["e_name"])) : "";
$e_dob = $_POST["e_dob"] ? addslashes(trim($_POST["e_dob"])) : "";
$e_dom = $_POST["e_dom"] ? addslashes(trim($_POST["e_dom"])) : "";
$e_address = $_POST["e_address"] ? addslashes(trim($_POST["e_address"])) : "";
$e_pin = $_POST["e_pin"] ? addslashes(trim($_POST["e_pin"])) : "";
$e_state = $_POST["e_state"] ? addslashes(trim($_POST["e_state"])) : "";
$e_city_town = $_POST["e_city_town"] ? addslashes(trim($_POST["e_city_town"])) : "";

$e_profile_image_name = $_FILES["e_profile_image"]["name"];
$e_profile_image_type = $_FILES["e_profile_image"]["type"];

$upload_dir = "en_profile_pic/";
$profile_img_prifix = $server_url."en_profile_pic/";
$profile_default_image_link = $server_url."en_profile_pic/profile.png";
$supported_mime_type = array("image/jpeg","image/jpg","image/png","image/gif","image/bmp");
if($the_engineer_id!=''){
	$sql2 = "select `eid`,`e_profile_image` from $engineer_master where `eid`='$the_engineer_id'";
	$res2 = mysqli_query($conn,$sql2);
	$tot_res2 = mysqli_num_rows($res2);
	if($tot_res2>0){
if($e_name==''){
	$res_data = array("process_status"=>"NO","process_message"=>"Please enter name.");
}else if($e_address==''){
	$res_data = array("process_status"=>"NO","process_message"=>"Please enter address.");
}else if($e_pin==''){
	$res_data = array("process_status"=>"NO","process_message"=>"Please enter pin.");
}else if($e_state==''){
	$res_data = array("process_status"=>"NO","process_message"=>"Please enter state.");
}else if($e_city_town==''){
	$res_data = array("process_status"=>"NO","process_message"=>"Please enter city.");
}else{

if($e_profile_image_name!="" && !in_array($e_profile_image_type,$supported_mime_type)){
$res_data = array("process_status"=>"NO","process_message"=>"Please select an image file.");			
}else{

$row2 = mysqli_fetch_assoc($res2);
$old_profile_img_name = $row2["e_profile_image"] ? trim($row2["e_profile_image"]) : "";

if($e_profile_image_name!=""){
$e_profile_image_tmp_name = $_FILES["e_profile_image"]["tmp_name"];
$e_profile_image_name = str_replace(" ","_",$e_profile_image_name);
$e_profile_image_name = str_replace("  ","_",$e_profile_image_name);
$e_profile_image_name = str_replace("'","_",$e_profile_image_name);
$e_profile_image_name = str_replace('"',"_",$e_profile_image_name);
$e_profile_image_name = str_replace('-',"_",$e_profile_image_name);
$unid = uniqid();
$the_profile_image_name = "profile_img_".$unid."_".$e_profile_image_name;
$target_file = $upload_dir.$the_profile_image_name;
$upload_the_ad_file = move_uploaded_file($e_profile_image_tmp_name,$target_file);
if($upload_the_ad_file){
$profile_image_qry = ",e_profile_image='$the_profile_image_name'";
}else{
$profile_image_qry = "";
}
}else{
$profile_image_qry = "";
}


$curr_datetime = date("Y-m-d H:i:s");
$sql_noty = "update $engineer_master set `e_name`='$e_name',`e_address`='$e_address',`e_dob`='$e_dob',`e_dom`='$e_dom',`e_pin`='$e_pin',`e_state`='$e_state',`e_city_town`='$e_city_town',`last_updated_datetime`='$curr_datetime'$profile_image_qry where `eid`='$the_engineer_id'";
$res_noty = mysqli_query($conn,$sql_noty);
if($old_profile_img_name!=""){
	if($profile_image_qry!=""){
		if(file_exists($upload_dir.$old_profile_img_name)){
			unlink($upload_dir.$old_profile_img_name);
		}
	}
}
$res_data = array("process_status"=>"YES","process_message"=>"Successfully updated.");

}

}
	}else{
		$res_data = array("process_status"=>"NO","process_message"=>"The engineer details doesn't exist.");
	}
	
	
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>