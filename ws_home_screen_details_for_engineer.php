<?php
include "star_connection.php";
$te_master = "te_master";
$engineer_master = "engineer_master";
$gift_master = "gift_master";
$home_screen_text_for_engineer = "home_screen_text_for_engineer";
$home_screen_slider_for_engineer = "home_screen_slider_for_engineer";
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$home_screen_pic_dir = "home_screen_pic/";
$home_screen_pic_prifix = $server_url."home_screen_pic/";

$e_slider_dir = "e_slider/";
$e_slider_prifix = $server_url."e_slider/";

$featured_slider_dir = "gift_pic/";
$featured_slider_prifix = $server_url."gift_pic/";

$profile_default_image_link = $server_url."en_profile_pic/profile.png";
$offer_slider_data = array();
$featured_slider_data = array();

$number_of_points = 0;
if($the_engineer_id!=''){
$te_data = array();
	$sql2 = "select `e_points`,`eid`,`e_name` from $engineer_master where `eid`='$the_engineer_id'";
	$res2 = mysqli_query($conn,$sql2);
	$tot_res2 = mysqli_num_rows($res2);
	if($tot_res2>0){		
		$row2 = mysqli_fetch_assoc($res2);
		$e_name = $row2["e_name"] ? trim($row2["e_name"]) : "";
		$e_points = $row2["e_points"] ? trim($row2["e_points"]) : "0";
		$number_of_points = "Stellar Points : ".$e_points;
		
$sql_hst = "select `top_section_header_text`,`top_section_description_text`,`the_image` from $home_screen_text_for_engineer order by `id` asc limit 0,1";
$res_hst = mysqli_query($conn,$sql_hst);
$tot_res_hst = mysqli_num_rows($res_hst);
if($tot_res_hst>0){
$row_hst = mysqli_fetch_assoc($res_hst);
$top_section_header_text = $row_hst["top_section_header_text"] ? trim($row_hst["top_section_header_text"]) : "";
$top_section_description_text = $row_hst["top_section_description_text"] ? trim($row_hst["top_section_description_text"]) : "";
$the_image = $row_hst["the_image"] ? trim($row_hst["the_image"]) : "";
if($the_image!=""){
	if(file_exists($home_screen_pic_dir.$the_image)){
		$top_section_image_link = $home_screen_pic_prifix.$the_image;
	}else{
		$top_section_image_link = "";
	}
}else{
	$top_section_image_link = "";	
}
}else{
$top_section_header_text = "";
$top_section_description_text = "";
$top_section_image_link = "";
}

$sql_slider = "select `slider_header_text`,`slider_description_text`,`slider_image`,`slider_category` from $home_screen_slider_for_engineer order by `slider_visible_order` asc";
$res_slider = mysqli_query($conn,$sql_slider);
$tot_res_slider = mysqli_num_rows($res_slider);
if($tot_res_slider>0){		
while($row_slider = mysqli_fetch_assoc($res_slider)){
$slider_header_text = $row_slider["slider_header_text"] ? trim($row_slider["slider_header_text"]) : "";
$slider_description_text = $row_slider["slider_description_text"] ? trim($row_slider["slider_description_text"]) : "";
$slider_category = $row_slider["slider_category"] ? trim($row_slider["slider_category"]) : "";
$slider_image = $row_slider["slider_image"] ? trim($row_slider["slider_image"]) : "";
if($slider_image!=""){
	if(file_exists($e_slider_dir.$slider_image)){
		$slider_image_link = $e_slider_prifix.$slider_image;
	}else{
		$slider_image_link = "";
	}
}else{
	$slider_image_link = "";	
}
$offer_slider_data[] = array("slider_header_text"=>$slider_header_text,"slider_description_text"=>$slider_description_text,"slider_image_link"=>$slider_image_link,"slider_category"=>$slider_category);
}
}


$sql_featured_slider = "select `id`,`gift_title`,`gift_image` from $gift_master where `status`='ACTIVE' and `featured`='YES' order by `gift_title` asc limit 0,3";
$res_featured_slider = mysqli_query($conn,$sql_featured_slider);
$tot_res_featured_slider = mysqli_num_rows($res_featured_slider);
if($tot_res_featured_slider>0){		
while($row_featured_slider = mysqli_fetch_assoc($res_featured_slider)){
$featured_gift_id = $row_featured_slider["id"] ? trim($row_featured_slider["id"]) : "";
$featured_gift_title = $row_featured_slider["gift_title"] ? trim($row_featured_slider["gift_title"]) : "";
$featured_gift_image = $row_featured_slider["gift_image"] ? trim($row_featured_slider["gift_image"]) : "";
if($featured_gift_image!=""){
	if(file_exists($featured_slider_dir.$featured_gift_image)){
		$featured_gift_image_link = $featured_slider_prifix.$featured_gift_image;
	}else{
		$featured_gift_image_link = "";
	}
}else{
	$featured_gift_image_link = "";	
}
$featured_slider_data[] = array("featured_gift_id"=>$featured_gift_id,"featured_gift_title"=>$featured_gift_title,"featured_gift_image_link"=>$featured_gift_image_link);
}
}

$res_data = array("process_status"=>"YES","process_message"=>"Success.","e_name"=>$e_name,"number_of_points"=>$number_of_points,"top_section_header_text"=>$top_section_header_text,"top_section_description_text"=>$top_section_description_text,"top_section_image_link"=>$top_section_image_link,"offer_slider_data"=>$offer_slider_data,"featured_slider_data"=>$featured_slider_data);
	}else{
		$res_data = array("process_status"=>"NO","process_message"=>"The engineer details doesn't exist.");
	}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>