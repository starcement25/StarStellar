<?php
ini_set('memory_limit', '9999M');
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include "star_connection.php";
$res_msg = array();
$engineer_master = "engineer_master";
$tf_te_eng_data = "";
$totresfm = 0;
$tf_te_code = $_POST["tf_te_code"] ? addslashes(trim($_POST["tf_te_code"])) : "";
$search_text = $_POST["search_text"] ? addslashes(trim($_POST["search_text"])) : "";
if($tf_te_code!=""){
	if($search_text!=""){
		$search_query = " and (`e_name` like '%$search_text%' or `e_mobile` like '%$search_text%' or `e_email` like '%$search_text%')";
	}else{
		$search_query = "";
	}
		$sqlfm = "select `eid`,`e_name`,`e_mobile`,`e_email` from $engineer_master where `te_code`='$tf_te_code' $search_query order by `e_name` asc";
$resfm = mysqli_query($conn,$sqlfm);
$totresfm = mysqli_num_rows($resfm);
if($totresfm>0){
while($rowfm=mysqli_fetch_assoc($resfm)){
$prod_sid = $rowfm["eid"];
$prod_name = $rowfm["e_name"];		
$prod_mobile_no = $rowfm["e_mobile"];
$prod_email = $rowfm["e_email"];
$tf_te_eng_data .= '<div class="media_menu row_small" id="tf_stk_'.$prod_sid.'" style="background-color:#ffffff; padding-left:7px;padding-bottom: 7px;padding-top: 7px; border-radius:3px;">
				<div class="media-body">
				<h4 class="media-heading">'.$prod_name.'</h4>
				<h4 class="media-heading">Mobile: '.$prod_mobile_no.'</h4>
				<h4 class="media-heading">Email: '.$prod_email.'</h4>
				<div class="checkbox"><label><input type="checkbox" name="tf_stk_ids[]" value="'.$prod_sid.'" class="tf_ck_id"></label></div>
			   </div></div>';

}
}
$res_msg = array("process_sts"=>"YES","process_msg"=>"Success.","tf_te_eng_data"=>$tf_te_eng_data,"tot_tf_eng_count"=>$totresfm);
}else{
	$res_msg = array("process_sts"=>"NO","process_msg"=>"All fields are mandatory.","tf_gdn_stock_data"=>$tf_gdn_stock_data,"tot_tf_stock_count"=>$totresfm);
}
echo json_encode($res_msg);
mysqli_close($conn);
?>