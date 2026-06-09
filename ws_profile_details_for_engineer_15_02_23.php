<?php
include "star_connection.php";
$te_master = "te_master";
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$gift_order_master = "gift_order_master";
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$the_image_dir = "en_profile_pic/";
$profile_img_prifix = $server_url."en_profile_pic/";
$profile_default_image_link = $server_url."en_profile_pic/profile.png";
$profile_data = array();
function get_te_details_by_te_code($conn,$tcode){
$tearr = array("sts"=>"NO","te_name"=>"","te_mobile"=>"","te_email"=>"","tm_name"=>"","tm_mobile"=>"","tm_email"=>"");


$te_master = "te_master";
$tcode = $tcode ? addslashes(trim($tcode)) : "" ;
	if($tcode!=""){
	$sql1 = "select `te_name`,`te_mobile_no`,`te_email`,`reporting_to` from $te_master where `te_code`='$tcode'";
	$res1 = mysqli_query($conn,$sql1);
	$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
	$row1=mysqli_fetch_assoc($res1);
	$the_te_name = $row1["te_name"] ? trim($row1["te_name"]) : "";
	$the_te_mobile_no = $row1["te_mobile_no"] ? trim($row1["te_mobile_no"]) : "";
	$the_te_email = $row1["te_email"] ? trim($row1["te_email"]) : "";
	$the_te_reporting_to = $row1["reporting_to"] ? trim($row1["reporting_to"]) : "";
	$the_tm_name = "";
	$the_tm_mobile = "";
	$the_tm_email = "";
	if($the_te_reporting_to!=""){
		$sql_tm = "select `te_name`,`te_mobile_no`,`te_email` from $te_master where `te_code`='$the_te_reporting_to'";
		$res_tm = mysqli_query($conn,$sql_tm);
		$totres_tm = mysqli_num_rows($res_tm);
		if($totres_tm>0){
		$row_tm=mysqli_fetch_assoc($res_tm);
		$the_tm_name = $row_tm["te_name"] ? trim($row_tm["te_name"]) : "";
		$the_tm_mobile = $row_tm["te_mobile_no"] ? trim($row_tm["te_mobile_no"]) : "";
		$the_tm_email = $row_tm["te_email"] ? trim($row_tm["te_email"]) : "";
		}
	}
	
	
	$tearr = array("sts"=>"YES","te_name"=>$the_te_name,"te_mobile"=>$the_te_mobile_no,"te_email"=>$the_te_email,"tm_name"=>$the_tm_name,"tm_mobile"=>$the_tm_mobile,"tm_email"=>$the_tm_email);
	}	 
	}
return $tearr;
}
$number_of_sites = 0;
$number_of_points = 0;
$number_of_gifts = 0;
if($the_engineer_id!=''){
$te_data = array();
	$sql2 = "select * from $engineer_master where `eid`='$the_engineer_id'";
	$res2 = mysqli_query($conn,$sql2);
	$tot_res2 = mysqli_num_rows($res2);
	if($tot_res2>0){
		
$sql_site = "select `r_site_id` from $recommended_site_master where `r_engineer_id`='$the_engineer_id'";
$res2_site = mysqli_query($conn,$sql_site);
$tot_res2_site = mysqli_num_rows($res2_site);
$number_of_sites = intval($tot_res2_site);

$sql_gift = "select `g_order_id` from $gift_order_master where `user_id`='$the_engineer_id'";
$res2_gift = mysqli_query($conn,$sql_gift);
$tot_res2_gift = mysqli_num_rows($res2_gift);
$number_of_gifts = intval($tot_res2_gift);
		
		$row2 = mysqli_fetch_assoc($res2);
		$the_engineer_id = $row2["eid"];
		$e_name = $row2["e_name"] ? trim($row2["e_name"]) : "";
		$e_mobile = $row2["e_mobile"] ? trim($row2["e_mobile"]) : "";
		$te_code = $row2["te_code"] ? trim($row2["te_code"]) : "";
		$e_points = $row2["e_points"] ? trim($row2["e_points"]) : "0";
		$number_of_points = intval($e_points);
		$te_data = get_te_details_by_te_code($conn,$te_code);
		if($te_data["sts"]=="YES"){
			$te_name = $te_data["te_name"];
			$te_mobile = $te_data["te_mobile"];
			$te_email = $te_data["te_email"];
			$tm_name = $te_data["tm_name"];
			$tm_mobile = $te_data["tm_mobile"];
			$tm_email = $te_data["tm_email"];
		}else{
			$te_name = "";
			$te_mobile = "";
			$te_email = "";
			$tm_name = "";
			$tm_mobile = "";
			$tm_email = "";
		}
		
		$e_email = $row2["e_email"] ? trim($row2["e_email"]) : "";
		$e_dob = $row2["e_dob"] ? trim($row2["e_dob"]) : "";
		if($e_dob!=""){
		$e_dob = date("d/m/y",strtotime($e_dob));
		}
		$e_dom = $row2["e_dom"] ? trim($row2["e_dom"]) : "";
		if($e_dom!=""){
		$e_dom = date("d/m/y",strtotime($e_dom));
		}
		$e_address = $row2["e_address"] ? trim($row2["e_address"]) : "";
		$e_pin = $row2["e_pin"] ? trim($row2["e_pin"]) : "";
		$e_state = $row2["e_state"] ? trim($row2["e_state"]) : "";
		$e_city_town = $row2["e_city_town"] ? trim($row2["e_city_town"]) : "";
		$e_profile_image = $row2["e_profile_image"] ? trim($row2["e_profile_image"]) : "";
		if($e_profile_image!=""){
		if(file_exists($the_image_dir.$e_profile_image)){
		$profile_image_link = $profile_img_prifix.$e_profile_image;
		}else{
		$profile_image_link = $profile_default_image_link;
		}		
		}else{
		$profile_image_link = $profile_default_image_link;
		}


		$profile_data[] = array("label"=>"Address","value"=>$e_address);
		$profile_data[] = array("label"=>"City/Town","value"=>$e_city_town);
		$profile_data[] = array("label"=>"Pin","value"=>$e_pin);
		$profile_data[] = array("label"=>"State","value"=>$e_state);
		$profile_data[] = array("label"=>"Birthday","value"=>$e_dob);
		$profile_data[] = array("label"=>"Anniversary","value"=>$e_dom);
		$profile_data[] = array("label"=>"TE Name","value"=>$te_name);
		$profile_data[] = array("label"=>"TE Mobile","value"=>$te_mobile);
		$profile_data[] = array("label"=>"TE Email","value"=>$te_email);
		$profile_data[] = array("label"=>"TM Name","value"=>$tm_name);
		$profile_data[] = array("label"=>"TM Mobile","value"=>$tm_mobile);
		$profile_data[] = array("label"=>"TM Email","value"=>$tm_email);
		
$res_data = array("process_status"=>"YES","process_message"=>"Success.","number_of_sites"=>$number_of_sites,"number_of_points"=>$number_of_points,"number_of_gifts"=>$number_of_gifts,"the_engineer_id"=>$the_engineer_id,"e_name"=>$e_name,"e_mobile"=>$e_mobile,"e_email"=>$e_email,"e_dob"=>$e_dob,"e_dom"=>$e_dom,"e_address"=>$e_address,"e_pin"=>$e_pin,"e_state"=>$e_state,"e_city_town"=>$e_city_town,"e_profile_image"=>$profile_image_link,"te_code"=>$te_code,"te_name"=>$te_name,"te_mobile"=>$te_mobile,"te_email"=>$te_email,"tm_name"=>$tm_name,"tm_mobile"=>$tm_mobile,"tm_email"=>$tm_email,"profile_data"=>$profile_data);
	}else{
		$res_data = array("process_status"=>"NO","process_message"=>"The engineer details doesn't exist.");
	}
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>