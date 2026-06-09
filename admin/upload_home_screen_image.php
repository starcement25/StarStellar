<?php
include "star_connection.php";
$home_screen_text_for_engineer = "home_screen_text_for_engineer";
$target_dir = "../home_screen_pic/";
$home_screen_pic_url_prefix = $server_url."home_screen_pic/";
$res_msg = array();
$supported_mime_type = array("image/jpeg","image/png","image/jpg");
$images_arr = array();
$mx_img_siz_cnt = 0;
$img_ext_cnt = 0;
$no_of_image = 10;
if($_POST['image_form_submit'] == 1){
$hsi_image_name = $_FILES['hsi_image']['name'];
$hsi_image_size = $_FILES['hsi_image']['size'];
$hsi_image_type = $_FILES['hsi_image']['type'];
$hsi_image_tmp_name = $_FILES['hsi_image']['tmp_name'];
if($hsi_image_name!=""){
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Please select a image.");
}if(!in_array($hsi_image_type,$supported_mime_type)){
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Please select a image.");
}else{
		$unid = uniqid();
		$img_file_name = str_replace(" ","_",$hsi_image_name);
		$img_file_name = str_replace("  ","_",$img_file_name);
		$img_file_name = str_replace("'","_",$img_file_name);
		$img_file_name = str_replace('"',"_",$img_file_name);
		$img_file_name = str_replace('-',"_",$img_file_name);
		$ad_new_name = "ad_img_".$unid."_".$img_file_name;
		$target_file = $target_dir.$ad_new_name;
		$upload_the_ad_file = move_uploaded_file($hsi_image_tmp_name,$target_file);
		if($upload_the_ad_file){
			$sqlhst = "select * from $home_screen_text_for_engineer order by `id` asc limit 0,1";
			$reshst = mysqli_query($conn,$sqlhst);
			$totreshst = mysqli_num_rows($reshst);
			if($totreshst>0){
			$rowhst=mysqli_fetch_assoc($reshst);
			$the_image = $rowhst["the_image"] ? trim($rowhst["the_image"]) : "";
			if($the_image!=""){
			if(file_exists($target_dir.$the_image)){
				unlink($target_dir.$the_image);
			}
			}
			}
			
			
			$sqlupd = "update $home_screen_text_for_engineer set `the_image`='$ad_new_name'";
			$resupd = mysqli_query($conn,$sqlupd);
			$the_new_image = '<img src="'.$home_screen_pic_url_prefix.$ad_new_name.'" class="the_hsimg" />';
			$res_msg = array("process_sts"=>"YES","process_msg"=>"Success.","the_new_img"=>$the_new_image);
		}else{
		$res_msg = array("process_sts"=>"NO","process_msg"=>"Uploading failed.");	
		}
}
}else{
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong. Please try later.");
}
mysqli_close($conn);
echo json_encode($res_msg);
exit;
?>