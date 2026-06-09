<?php
ini_set('memory_limit', '9999M');
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include "star_connection.php";
$res_msg = array();
$engineer_master = "engineer_master";
$tt_te_eng_data = "";
$totresfm = 0;
$tt_te_code = $_POST["tt_te_code"] ? addslashes(trim($_POST["tt_te_code"])) : "";
if($tt_te_code!=""){	
		$sqlfm = "select `eid`,`e_name`,`e_mobile`,`e_email` from $engineer_master where `te_code`='$tt_te_code' order by `e_name` asc";
$resfm = mysqli_query($conn,$sqlfm);
$totresfm = mysqli_num_rows($resfm);
if($totresfm>0){
while($rowfm=mysqli_fetch_assoc($resfm)){
$prod_sid = $rowfm["eid"];
$prod_name = $rowfm["e_name"];		
$prod_mobile_no = $rowfm["e_mobile"];
$prod_email = $rowfm["e_email"];
$tt_te_eng_data .= '<div class="media_menu row_small" id="tf_stk_'.$prod_sid.'" style="background-color:#ffffff; padding-left:7px;padding-bottom: 7px;padding-top: 7px; border-radius:3px;">
				<div class="media-body">
				<h4 class="media-heading">'.$prod_name.'</h4>
				<h4 class="media-heading">Mobile: '.$prod_mobile_no.'</h4>
				<h4 class="media-heading">Email: '.$prod_email.'</h4>
			   </div></div>';

}
}
$res_msg = array("process_sts"=>"YES","process_msg"=>"Success.","tt_te_eng_data"=>$tt_te_eng_data,"tot_tt_eng_count"=>$totresfm);
}else{
	$res_msg = array("process_sts"=>"NO","process_msg"=>"All fields are mandatory.","tt_gdn_stock_data"=>$tt_gdn_stock_data,"tot_tt_stock_count"=>$totresfm);
}
echo json_encode($res_msg);
mysqli_close($conn);
?>