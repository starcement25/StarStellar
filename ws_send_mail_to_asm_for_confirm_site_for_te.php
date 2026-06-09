<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
date_default_timezone_set('Asia/Kolkata');
include "star_connection.php";

$te_master = "te_master";
$engineer_master = "engineer_master";
$ledger_master = "ledger_master";
$recommended_site_master = "recommended_site_master";
$setting_master = "setting_master";
$product_master = "product_master";

$asm_master = "asm_master";
$branch_master = "branch_master";

$upload_dir = "recomend_site_pic/";
$image_url = $home_url."recomend_site_pic/";

$approved_upload_dir = "approved_recomend_site_pic/";
$approved_image_url = $home_url."approved_recomend_site_pic/";

$ar_url_prefix = $home_url."crs_by_asm.php";

$te_code = $_POST["te_code"] ? addslashes(trim($_POST["te_code"])) : "";
$r_site_id = $_POST["r_site_id"] ? addslashes(trim($_POST["r_site_id"])) : "";

if($te_code!="" && $r_site_id!=''){

	$sql2 = "select `te_code`,`te_name`,`te_mobile_no`,`te_email`,`branch_code` from $te_master where `te_code`='$te_code'";
	$res2 = mysqli_query($conn,$sql2);
	$tot_res2 = mysqli_num_rows($res2);
	if($tot_res2>0){
		$row2 = mysqli_fetch_assoc($res2);
		$te_branch_code = $row2["branch_code"] ? trim($row2["branch_code"]) : "";
		$te_name = $row2["te_name"] ? trim($row2["te_name"]) : "";
		$te_mobile_no = $row2["te_mobile_no"] ? trim($row2["te_mobile_no"]) : "";
		$te_email = $row2["te_email"] ? trim($row2["te_email"]) : "";

$sql3 = "select * from $recommended_site_master where `r_site_id`='$r_site_id' and `r_te_code`='$te_code'";
$res3 = mysqli_query($conn,$sql3);
$tot_res3 = mysqli_num_rows($res3);
if($tot_res3>0){
$row3 = mysqli_fetch_assoc($res3);
$r_engineer_id = $row3["r_engineer_id"] ? trim($row3["r_engineer_id"]) : "";
$curr_r_status = $row3["r_status"] ? trim($row3["r_status"]) : "";
$curr_is_mail_sent_to_asm = $row3["is_mail_sent_to_asm"] ? trim($row3["is_mail_sent_to_asm"]) : "";
if($curr_r_status=="APPROVED"){
$res_data = array("process_status"=>"NO","process_message"=>"This site already confirmed.");
}else if($curr_is_mail_sent_to_asm=="YES"){
$res_data = array("process_status"=>"NO","process_message"=>"Mail already sent to ASM.");
}else{

$e_name = "";
$e_mobile = "";
$e_email = "";

$sql24 = "select `eid`,`e_name`,`e_mobile`,`e_email`,`branch_code` from $engineer_master where `eid`='$r_engineer_id'";
$res24 = mysqli_query($conn,$sql24);
$tot_res24 = mysqli_num_rows($res24);
if($tot_res24>0){
$row24 = mysqli_fetch_assoc($res24);
$e_name = $row24["e_name"] ? trim($row24["e_name"]) : "";
$e_mobile = $row24["e_mobile"] ? trim($row24["e_mobile"]) : "";
$e_email = $row24["e_email"] ? trim($row24["e_email"]) : "";
$e_branch_code = $row24["branch_code"] ? trim($row24["branch_code"]) : "";

if($e_branch_code==""){
$res_data = array("process_status"=>"NO","process_message"=>"No branch is set in the engineer details.");
}else{

$en_branch_name = "";
$en_asm_email = "";
$en_asm_ph_no = "";
$sql_brnc = "select * from $asm_master where `branch_code`='$e_branch_code'";
$res_brnc = mysqli_query($conn,$sql_brnc);
$tot_res_brnc = mysqli_num_rows($res_brnc);
if($tot_res_brnc>0){
$row_brnc = mysqli_fetch_assoc($res_brnc);
$en_asm_id = $row_brnc["asm_id"] ? trim($row_brnc["asm_id"]) : "";
$en_asm_name = $row_brnc["asm_name"] ? trim($row_brnc["asm_name"]) : "";
$en_branch_name = $row_brnc["branch"] ? trim($row_brnc["branch"]) : "";
$en_asm_email = $row_brnc["email"] ? trim($row_brnc["email"]) : "";
$en_asm_ph_no = $row_brnc["ph_no"] ? trim($row_brnc["ph_no"]) : "";

if($en_asm_email==""){
if($en_branch_name!=""){
	$process_message = $en_branch_name." branch email not set.";
}else{
	$process_message = "Branch email not set.";
}
$res_data = array("process_status"=>"NO","process_message"=>$process_message);	
	
}else{
$r_recomended_site_image_url ="";
$r_site_verification_image_url ="";
$r_site_name = $row3["r_site_name"];
$r_contact_person_name = $row3["r_contact_person_name"];
$r_mobile_no = $row3["r_mobile_no"];
$r_address = $row3["r_address"];
$r_site_potential_in_mt = $row3["r_site_potential_in_mt"];
$r_contact_person_category_name = $row3["r_contact_person_category_name"];
$r_recomended_site_image = $row3["r_recomended_site_image"] ? trim($row3["r_recomended_site_image"]) : "";
if($r_recomended_site_image!=''){
if(file_exists($upload_dir.$r_recomended_site_image)){
$r_recomended_site_image_url = $image_url.$r_recomended_site_image;
}else{
$r_recomended_site_image_url ="";
}
}else{
$r_recomended_site_image_url ="";
}

$r_site_verification_image = $row3["r_site_verification_image"] ? trim($row3["r_site_verification_image"]) : "";

if($r_site_verification_image!=''){
if(file_exists($approved_upload_dir.$r_site_verification_image)){
$r_site_verification_image_url = $approved_image_url.$r_site_verification_image;
}else{
$r_site_verification_image_url ="";
}
}else{
$r_site_verification_image_url ="";
}

$r_submission_date = $row3["r_submission_date"];
$r_submission_date_modified = date("jS M, Y",strtotime($r_submission_date));

$r_expected_product_id = $row3["expected_product_id"] ? trim($row3["expected_product_id"]) : "";
$r_expected_product_name = $row3["expected_product_name"] ? trim($row3["expected_product_name"]) : "";
$r_expected_consumption = $row3["expected_consumption"] ? trim($row3["expected_consumption"]) : "";


$r_actual_product_id = $row3["actual_product_id"] ? trim($row3["actual_product_id"]) : "";
$r_actual_product_name = $row3["actual_product_name"] ? trim($row3["actual_product_name"]) : "";
$r_actual_consumption = $row3["actual_consumption"] ? trim($row3["actual_consumption"]) : "";

$r_purchased_from = $row3["purchased_from"] ? trim($row3["purchased_from"]) : "";
$r_purchased_from_name = $row3["purchased_from_name"] ? trim($row3["purchased_from_name"]) : "";
$r_purchased_from_area = $row3["purchased_from_area"] ? trim($row3["purchased_from_area"]) : "";
$r_purchased_from_contact_no = $row3["purchased_from_contact_no"] ? trim($row3["purchased_from_contact_no"]) : "";




$to_email = $en_asm_email;
$subject = "Site approval request from Star Stellar";
$bodyml = '<br>
<b>Site Name : </b> '.$r_site_name.'<br>
<b>Site Address : </b> '.$r_address.'<br>
<b>Site Potential(MT) : </b> '.$r_site_potential_in_mt.'<br>
<b>Contact Person : </b> '.$r_contact_person_name.'<br>
<b>Category : </b> '.$r_contact_person_category_name.'<br>
<b>Mobile : </b> '.$r_mobile_no.'<br>
<b>Recommended By Engineer : </b> '.$e_name.'<br>
<b>Engineer Mobile : </b> '.$e_mobile.'<br>
<b>Engineer Email : </b> '.$e_email.'<br>
<b>Engineer Branch : </b> '.$en_branch_name.'<br>
<b>Expected Product Name : </b> '.$r_expected_product_name.'<br>
<b>Expected Consumption(No. of bags) : </b> '.$r_expected_consumption.'<br>
<b>TE Details </b><br>
<b>TE Name : </b> '.$te_name.'<br>
<b>Mobile : </b> '.$te_mobile_no.'<br>
<b>Email : </b> '.$te_email.'<br><br>
<b>Actual Product Name : </b> '.$r_actual_product_name.'<br>
<b>Consumption as per Technical Engineer (No. of Bags): </b> '.$r_actual_consumption.'<br>
<b>Purchased From : </b> '.$r_purchased_from.'<br>
<b>Name : </b> '.$r_purchased_from_name.'<br>
<b>Area : </b> '.$r_purchased_from_area.'<br>
<b>Contact : </b> '.$r_purchased_from_contact_no.'<br><br>


To approve quickly, pls click on <a href="'.$ar_url_prefix.'?r_site_id='.$r_site_id.'&te_code='.$te_code.'&asm_status=APPROVE">Quick-Approval</a><br><br>

To reject quickly, pls click on <a href="'.$ar_url_prefix.'?r_site_id='.$r_site_id.'&te_code='.$te_code.'&asm_status=REJECT">Quick-Rejection</a><br><br>';
if($r_recomended_site_image_url!=""){
$bodyml .= '<b>Site Image </b><br>
<img src="'.$r_recomended_site_image_url.'" style="width:350px;"/>
<br><br>';
}
if($r_site_verification_image_url!=""){
$bodyml .= '<b>TE Interaction Image </b><br>
<img src="'.$r_site_verification_image_url.'" style="width:350px;"/>
<br><br>';
}
$bodyml .= '<br><br>';
/*if($to_email!=""){
$to_email = $to_email.",suranjitd@coral.in";	
}*/
$mail_send_sts = send_the_mail($to_email,$subject,$bodyml);			
if($mail_send_sts=="TRUE"){
$sql4 = "update $recommended_site_master set `is_mail_sent_to_asm`='YES',`approval_message`='ASM FOR APPROVAL',`r_asm_id`='$en_asm_id',`r_asm_name`='$en_asm_name',`r_asm_email`='$en_asm_email',`r_asm_ph_no`='$en_asm_ph_no',`r_asm_branch`='$en_branch_name' where `r_site_id`='$r_site_id' and `r_te_code`='$te_code'";
$res4 = mysqli_query($conn,$sql4);

$res_data = array("process_status"=>"YES","process_message"=>"A mail has been sent to ASM.","mail_sts"=>$mail_send_sts);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Mail can't be sent.");	
}



}

}else{
$res_data = array("process_status"=>"NO","process_message"=>"Branch email not set.");	
}

}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"The engineer details not found.");
}


}			

}else{
$res_data = array("process_status"=>"NO","process_message"=>"You have no permission to do so.");
}

	}else{
		$res_data = array("process_status"=>"NO","process_message"=>"The TE Code doesn't exist.");
	}
	
}else{	
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>