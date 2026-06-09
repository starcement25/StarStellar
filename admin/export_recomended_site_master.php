<?php
include "web_check.php";
ini_set('memory_limit', '9999M');
set_time_limit(0);
include "star_connection.php";
$branch_master = "branch_master";
$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}
function get_branch_names_by_ids($conn,$bids){
$pnms = "Not set yet";
$nmsarr = array();
$selected_pids_arr = array();
$branch_master = "branch_master";
$bids = $bids ? trim($bids) : "" ;
	if($bids!=""){
		$selected_pids_arr = explode(",",$bids);
		$selected_pids_str_for_qry = implode("','",$selected_pids_arr);
	$sql1 = "select `branch_name` from $branch_master where `branch_code` in ('".$selected_pids_str_for_qry."') ";
	$res1 = mysqli_query($conn,$sql1);
	$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
	$the_prod_name_sho = trim($row1["branch_name"]);
	$nmsarr[] = $the_prod_name_sho;
	}
	$qpnms = implode(",",$nmsarr);
	}	 
	}
return $qpnms;
}

$trn_te_code = $_GET["trn_te_code"] ? addslashes(trim($_GET["trn_te_code"])) : "";
$sl_en_id = $_GET["sl_en_id"] ? addslashes(trim($_GET["sl_en_id"])) : "";
$srch_eng_te_site_dtls = $_GET["srch_eng_te_site_dtls"] ? addslashes(trim($_GET["srch_eng_te_site_dtls"])) : "";
$sl_ord_sts = $_GET["sl_ord_sts"] ? addslashes(trim($_GET["sl_ord_sts"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";





startCreatRecomendedSiteMasterCsvfile($conn,$trn_te_code,$sl_en_id,$srch_eng_te_site_dtls,$sl_ord_sts,$sl_day_wise,$from_dt,$to_dt);

function startCreatRecomendedSiteMasterCsvfile($conn,$trn_te_code,$sl_en_id,$srch_eng_te_site_dtls,$sl_ord_sts,$sl_day_wise,$from_dt,$to_dt){
$server_url = "https://" . $_SERVER['SERVER_NAME']."/";
$recommended_site_master = "recommended_site_master";
$te_master = "te_master";
$engineer_master = "engineer_master";
$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}
$img_dir = "../recomend_site_pic/";
$approve_img_dir = "../approved_recomend_site_pic/";
$image_url_prefix = $server_url."recomend_site_pic/";
$approve_image_url_prefix = $server_url."approved_recomend_site_pic/";


$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$new_qry_string_filtered = "";
$trn_te_code = $trn_te_code ? $trn_te_code : "";
$sl_en_id = $sl_en_id ? $sl_en_id : "";
$srch_eng_te_site_dtls = $srch_eng_te_site_dtls ? $srch_eng_te_site_dtls : "";
$sl_ord_sts = $sl_ord_sts ? $sl_ord_sts : "";
$sl_day_wise = $sl_day_wise ? $sl_day_wise : "";
$from_dt = $from_dt ? $from_dt : "";
$to_dt = $to_dt ? $to_dt : "";
$whr_str = "";
$export_filtered_str = "";
$search_array = array("trn_te_code"=>$trn_te_code,"sl_en_id"=>$sl_en_id,"srch_eng_te_site_dtls"=>$srch_eng_te_site_dtls,"sl_ord_sts"=>$sl_ord_sts,"data_show_type"=>$data_show_type,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="sl_ord_sts"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			
			$whr_str .= "$aand $recommended_site_master.`r_status`='$search_array_val' ";
			
			$new_qry_string_filtered .= "&sl_ord_sts=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_ord_sts=".$search_array_val;
			}else{
				$export_filtered_str .= "&sl_ord_sts=".$search_array_val;
			}	
			
		}		
	}else if($search_array_key=="data_show_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			/*if($search_array_val=='NE'){
$whr_str .= "$aand ($te_master.`zone` like '%A%' or $te_master.`zone` like '%B%' or $te_master.`zone` like '%C%' ) ";
			}else if($search_array_val=='OSNE'){
$whr_str .= "$aand ($te_master.`zone` like '%D%' or $te_master.`zone` like '%E%' ) ";
			}*/
			if($search_array_val!='ALL'){
				$whr_str .= "$aand $te_master.`zone` like '%".$search_array_val."%' ";
			}
			
		}
	}else if($search_array_key=="trn_te_code"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $recommended_site_master.`r_te_code`='$search_array_val' ";	
			$new_qry_string_filtered .= "&trn_te_code=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&trn_te_code=".$search_array_val;
			}else{
				$export_filtered_str .= "&trn_te_code=".$search_array_val;
			}		
		}		
	}else if($search_array_key=="sl_en_id"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $recommended_site_master.`r_engineer_id`='$search_array_val' ";	
			$new_qry_string_filtered .= "&sl_en_id=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_en_id=".$search_array_val;
			}else{
				$export_filtered_str .= "&sl_en_id=".$search_array_val;
			}	
		}		
	}else if($search_array_key=="srch_eng_te_site_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			
			$whr_str .= "$aand ($engineer_master.`e_name` like '%$search_array_val%' or $engineer_master.`te_code` like '%$search_array_val%' or $engineer_master.`e_mobile` like '%$search_array_val%' or $te_master.`te_name` like '%$search_array_val%' or $te_master.`te_mobile_no` like '%$search_array_val%' or $recommended_site_master.`r_site_name` like '%$search_array_val%' or $recommended_site_master.`r_contact_person_name` like '%$search_array_val%' or $recommended_site_master.`r_mobile_no` like '%$search_array_val%' or $recommended_site_master.`r_address` like '%$search_array_val%' or $recommended_site_master.`r_site_potential_in_mt` like '%$search_array_val%' or $recommended_site_master.`r_contact_person_category_name` like '%$search_array_val%' )";
			
			$new_qry_string_filtered .= "&srch_eng_te_site_dtls=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&srch_eng_te_site_dtls=".$search_array_val;
			}else{
				$export_filtered_str .= "&srch_eng_te_site_dtls=".$search_array_val;
			}	
		}		
	}else if($search_array_key=="daywise"){
		$the_sl_day_wise = $search_array_val["sl_day_wise"];
		$the_from_dt = $search_array_val["from_dt"];
		$the_to_dt = $search_array_val["to_dt"];
		if(trim($whr_str)!=""){
		$aand = " and";
		}else{
		$aand = "";
		}
		if($the_sl_day_wise=="Date_Range"){
			$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
			}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
			}
			if($the_from_dt!="" && $the_to_dt!=""){
			   $whr_str .= "$aand $recommended_site_master.`r_submission_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $recommended_site_master.`r_submission_date` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $recommended_site_master.`r_submission_date` <= '".$the_to_dt." ".$to_hrs."' ";
				$new_qry_string_filtered .= "&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&to_dt=".$the_to_dt;
				}
			}
		}else{
			if($the_sl_day_wise=="Today"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $recommended_site_master.`r_submission_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $recommended_site_master.`r_submission_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}


if($whr_str!=""){
	$new_whr_str = " where ".$whr_str;
}else{
	$new_whr_str ="";
}


$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "recomended_site_master_".$curr_date.".csv";
$output = "";

if($data_show_type=="ALL"){

	$qry = "select $recommended_site_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`branch_code` as `eng_branch_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $recommended_site_master left join $engineer_master on $recommended_site_master.`r_engineer_id`=$engineer_master.`eid` left join $te_master on $recommended_site_master.`r_te_code`=$te_master.`te_code` $new_whr_str order by $recommended_site_master.`r_site_id` desc";
	
}else{
	$where_clause = "";
	$zones = explode(",", $data_show_type); // Assuming $data_show_type is a comma-separated string of zones
	
	foreach ($zones as $zone) {
		$zone = trim($zone);
		if ($where_clause != "") {
			$where_clause .= " OR ";
		}
		$where_clause .= "te_master.`zone` LIKE '%$zone%'";
	}
	
	$qry = "select $recommended_site_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`branch_code` as `eng_branch_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $recommended_site_master left join $engineer_master on $recommended_site_master.`r_engineer_id`=$engineer_master.`eid` left join $te_master on $recommended_site_master.`r_te_code`=$te_master.`te_code` where $where_clause order by $recommended_site_master.`r_site_id` desc";
}

$sql = mysqli_query($conn,$qry);

$output .= '"Reccomendation ID","Existing Site ID","Reccomendation date","TE Branch","Engineer name","ENGINEER PH NO","Engineer Branch","Linked TE CODE","Linked TE NAME","Linked TE MOBILE","SITE DETAILS","SITE STATUS","EXPECTED PRODUCT","EXPECTED CONSUMPTION","ACTUAL PRODUCT","ACTUAL CONSUMPTION","PURCHASED FROM","PURCHASED FROM NAME","PURCHASED FROM AREA","PURCHASED FROM CONTACT","APPROVED DATE","REJECTED DATE","POINTS EARNED","Contact Person name","Contact Phn No","Contact person category","SITE IMAGE","TE Interaction Image","mail_sent_to_asm","asm_name","asm_ph_no","asm_email","asm_branch"';

$output .="\n";
// Get Records from the table

while ($row1 = mysqli_fetch_assoc($sql)) {
$the_r_site_id = $row1["r_site_id"];	
$r_existing_id = $row1["existing_id"] ? trim($row1["existing_id"]) : "";
$r_te_code = $row1["r_te_code"] ? trim($row1["r_te_code"]) : "";
$r_engineer_id = $row1["r_engineer_id"] ? trim($row1["r_engineer_id"]) : "";
$r_engineer_name = $row1["e_name"] ? str_replace('"', '""', trim($row1["e_name"])) : "";
$r_engineer_mobile = $row1["e_mobile"] ? trim($row1["e_mobile"]) : "";
$r_te_name = $row1["te_name"] ? str_replace('"', '""', trim($row1["te_name"])) : "";
$r_te_mobile_no = $row1["te_mobile_no"] ? trim($row1["te_mobile_no"]) : "";
$r_site_name = $row1["r_site_name"] ? str_replace('"', '""', trim($row1["r_site_name"])) : "";
$r_contact_person_name = $row1["r_contact_person_name"] ? str_replace('"', '""', trim($row1["r_contact_person_name"])) : "";
$r_mobile_no = $row1["r_mobile_no"] ? trim($row1["r_mobile_no"]) : "";
$r_address = $row1["r_address"] ? str_replace('"', '""', trim($row1["r_address"])) : "";
$r_site_potential_in_mt = $row1["r_site_potential_in_mt"] ? trim($row1["r_site_potential_in_mt"]) : "";
$r_contact_person_category_name = $row1["r_contact_person_category_name"] ? str_replace('"', '""', trim($row1["r_contact_person_category_name"])) : "";
$r_recomended_site_image = $row1["r_recomended_site_image"] ? trim($row1["r_recomended_site_image"]) : "";
$r_status = $row1["r_status"] ? trim($row1["r_status"]) : "";
$r_submission_date = $row1["r_submission_date"] ? trim($row1["r_submission_date"]) : "";
$r_te_interaction_date = $row1["r_te_interaction_date"] ? trim($row1["r_te_interaction_date"]) : "";
$r_site_verification_image = $row1["r_site_verification_image"] ? trim($row1["r_site_verification_image"]) : "";
$r_te_interaction_comment = $row1["r_te_interaction_comment"] ? str_replace('"', '""', trim($row1["r_te_interaction_comment"])) : "";
$r_point_earned_by_engineer = $row1["r_point_earned_by_engineer"] ? trim($row1["r_point_earned_by_engineer"]) : "0";

$expected_product_id = $row1["expected_product_id"] ? trim($row1["expected_product_id"]) : "";
$expected_product_name = $row1["expected_product_name"] ? str_replace('"', '""', trim($row1["expected_product_name"])) : "";
$expected_consumption = $row1["expected_consumption"] ? trim($row1["expected_consumption"]) : "0";

$actual_product_id = $row1["actual_product_id"] ? trim($row1["actual_product_id"]) : "";
$actual_product_name = $row1["actual_product_name"] ? str_replace('"', '""', trim($row1["actual_product_name"])) : "";
$actual_consumption = $row1["actual_consumption"] ? trim($row1["actual_consumption"]) : "0";
$purchased_from = $row1["purchased_from"] ? str_replace('"', '""', trim($row1["purchased_from"])) : "";
$purchased_from_name = $row1["purchased_from_name"] ? str_replace('"', '""', trim($row1["purchased_from_name"])) : "";
$purchased_from_area = $row1["purchased_from_area"] ? str_replace('"', '""', trim($row1["purchased_from_area"])) : "";
$purchased_from_contact_no = $row1["purchased_from_contact_no"] ? str_replace('"', '""', trim($row1["purchased_from_contact_no"])) : "";


$is_mail_sent_to_asm = $row1["is_mail_sent_to_asm"] ? trim($row1["is_mail_sent_to_asm"]) : "";

$r_asm_id = $row1["r_asm_id"] ? trim($row1["r_asm_id"]) : "";
$r_asm_name = $row1["r_asm_name"] ? trim($row1["r_asm_name"]) : "";
$r_asm_email = $row1["r_asm_email"] ? trim($row1["r_asm_email"]) : "";
$r_asm_ph_no = $row1["r_asm_ph_no"] ? trim($row1["r_asm_ph_no"]) : "";
$r_asm_branch = $row1["r_asm_branch"] ? trim($row1["r_asm_branch"]) : "";
if($r_asm_id!=""){
$the_app_asm_data = get_asm_data_by_id($conn,$r_asm_id);
if($the_app_asm_data["sts"]=="YES"){
$r_asm_name = $the_app_asm_data["asm_name"];
$r_asm_email = $the_app_asm_data["email"];
$r_asm_ph_no = $the_app_asm_data["ph_no"];
$r_asm_branch = $the_app_asm_data["branch"];	
}
}

$the_eng_branch_name_selected = "";	
$the_eng_branch_code_selected = $row1["eng_branch_code"];
if($the_eng_branch_code_selected!=""){
$the_eng_branch_name_selected = get_branch_names_by_ids($conn,$the_eng_branch_code_selected);
}

$the_branch_code_selected = $row1["branch_code"];
if($the_branch_code_selected!=""){
$the_branch_code_selected = get_branch_names_by_ids($conn,$the_branch_code_selected);
}else{
$the_branch_code_selected = "";	
}

if($r_submission_date!=""){
	$r_submission_date = date("d-m-Y",strtotime($r_submission_date));
}
if($r_te_interaction_date!=""){
	$r_te_interaction_date = date("d-m-Y",strtotime($r_te_interaction_date));
}
if($r_status=="PENDING"){
$approved_date = "";
$rejected_date = "";
}else if($r_status=="APPROVED"){
$approved_date = $r_te_interaction_date;
$rejected_date = "";
}else if($r_status=="REJECTED"){
$approved_date = "";
$rejected_date = $r_te_interaction_date;
}else{
$approved_date = "";
$rejected_date = "";
}

	if($r_recomended_site_image!=""){
	if(file_exists($img_dir.$r_recomended_site_image)){
	$r_recomended_site_image_url = $image_url_prefix.$r_recomended_site_image;
	}else{
	$r_recomended_site_image_url = "";
	}
	}else{
	$r_recomended_site_image_url = "";
	}

	if($r_site_verification_image!=""){
	if(file_exists($approve_img_dir.$r_site_verification_image)){
	$r_site_verification_image_url = $approve_image_url_prefix.$r_site_verification_image;
	}else{
	$r_site_verification_image_url = "";
	}
	}else{
	$r_site_verification_image_url = "";
	}

$site_details = "";
$site_details .= $r_site_name."\n";
$site_details .= "Potential: ".$r_site_potential_in_mt." MT\n";
$site_details .= "Address: ".$r_address."\n";




$output .= '"'.$the_r_site_id.'","'.$r_existing_id.'","'.$r_submission_date.'","'.$the_branch_code_selected.'","'.$r_engineer_name.'","'.$r_engineer_mobile.'","'.$the_eng_branch_name_selected.'","'.$r_te_code.'","'.$r_te_name.'","'.$r_te_mobile_no.'","'.$site_details.'","'.$r_status.'","'.$expected_product_name.'","'.$expected_consumption.'","'.$actual_product_name.'","'.$actual_consumption.'","'.$purchased_from.'","'.$purchased_from_name.'","'.$purchased_from_area.'","'.$purchased_from_contact_no.'","'.$approved_date.'","'.$rejected_date.'","'.$r_point_earned_by_engineer.'","'.$r_contact_person_name.'","'.$r_mobile_no.'","'.$r_contact_person_category_name.'","'.$r_recomended_site_image_url.'","'.$r_site_verification_image_url.'","'.$is_mail_sent_to_asm.'","'.$r_asm_name.'","'.$r_asm_ph_no.'","'.$r_asm_email.'","'.$r_asm_branch.'"';
$output .="\n";
}

// Download the file

$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;
}

mysqli_close($conn);
?>