<?php
include "star_connection.php";
$table_name = "employee_master";
$branch_schemes_pdf = "branch_schemes_PDF";
$branch_master = "branch_master";
$target_dir = "../schemes/";
$res_msg = array();
$supported_mime_type = array("application/pdf");
$uploaded_pdf_file_names_arr = array();
$max_img_size_limt = 10;
$mx_img_siz_cnt = 0;
$img_ext_cnt = 0;
$no_of_image = 10;
$img_data = "";
$prs_msg = "";
$branch_code_arr = array();
$branch_code_arr = $_POST['astn_branch_code'];
$start_dt = $_POST['start_dt'] ? addslashes(trim($_POST['start_dt'])) : "";
$end_dt = $_POST['end_dt'] ? addslashes(trim($_POST['end_dt'])) : "";
if(count($branch_code_arr)>0){
if($start_dt==""){
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Please select start date.");
}else if($end_dt==""){
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Please select end date.");
}else{

if($_POST['pdf_form_submit'] == 1){
	if(count($_FILES['npdf_file']['name'])>0){
		if(count($_FILES['npdf_file']['name'])>$no_of_image){
			$res_msg = array("process_sts"=>"NO","process_msg"=>"Maximum pdf file upload limit is $no_of_image.");
		}else{
			$count=0;
	foreach($_FILES['npdf_file']['name'] as $key=>$val){
		$image_name = $_FILES['npdf_file']['name'][$key];
		$tmp_name 	= $_FILES['npdf_file']['tmp_name'][$key];
		$size 		= $_FILES['npdf_file']['size'][$key];
		$size_in_mb = ($size/(1024*1024));
		$type 		= $_FILES['npdf_file']['type'][$key];
		$error 		= $_FILES['npdf_file']['error'][$key];
		if(!in_array($type,$supported_mime_type)){
			$img_ext_cnt = ($img_ext_cnt+1);
		}else if($size_in_mb>$max_img_size_limt){
			$mx_img_siz_cnt = ($mx_img_siz_cnt+1);
		}else{
			$ad_new_name = str_replace(" ","_",$_FILES['npdf_file']['name'][$key]);
			$ad_new_name = str_replace("-","_",$ad_new_name);		
			$ad_new_name = "pdf_".time()."_".$ad_new_name;
			$target_file = $target_dir.$ad_new_name;
			if(move_uploaded_file($_FILES['npdf_file']['tmp_name'][$key],$target_file)){
				$uploaded_pdf_file_names_arr[] = $ad_new_name;
			}
		}
}
	if(count($_FILES['npdf_file']['name'])>0){
		if($img_ext_cnt>0){
			$prs_msg .= " $img_ext_cnt files are not uploaded due to invalid extention( Only supports PDF).";
		}
		if($mx_img_siz_cnt>0){
			$prs_msg .= " $mx_img_siz_cnt files are not uploaded due to size limit of $max_img_size_limt MB.";
		}
		if(count($uploaded_pdf_file_names_arr>0)){
			$curr_date_time = date("Y-m-d H:i:s");
			foreach($branch_code_arr as $branch_code_arr_val){
				$the_branch_code = $branch_code_arr_val;
				foreach($uploaded_pdf_file_names_arr as $uploaded_pdf_file_names_vals){
				$sql_in = "insert into $branch_schemes_pdf (`branch_code`,`PDF_file_name`,`start_date`,`end_date`,`download_time`) values('$the_branch_code','$uploaded_pdf_file_names_vals','$start_dt','$end_dt','$curr_date_time')";
				$res_in = mysqli_query($conn,$sql_in);
				}
			}
			$res_msg = array("process_sts"=>"YES","process_msg"=>"Scheme PDF successfully added. ".$prs_msg);			
						
		}else{
			$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong. Please try later.".$prs_msg);
		}
		
	}else{
		$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong. Please try later.");
	}
	}
	}else{
		$res_msg = array("process_sts"=>"NO","process_msg"=>"Please select atleast one pdf file.");
	}
}else{
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong. Please try later.");
}

}

}else{
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Please select atleast one branch.");
}
mysqli_close($conn);
echo json_encode($res_msg);
exit;
?>