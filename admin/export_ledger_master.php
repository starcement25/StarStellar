<?php
//echo"<pre>";print_r($_GET);die;
include "web_check.php";
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$branch_master = "branch_master";
$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
//set manualy all 23-12-25
$the_data_show_type='ALL';
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
startCreatLedgerMasterCsvfile($conn);

function startCreatLedgerMasterCsvfile($conn){
$ledger_master = "ledger_master";
$date_of_correction_in_point_calculation_as_per_new_SOP = "2024-03-19 00:00:00";
$te_master = "te_master";
$engineer_master = "engineer_master";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "ledger_master_".$curr_date.".csv";
$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
//set manualy all 23-12-25
$the_data_show_type='ALL';
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}
$output = "";
/*if($data_show_type=='NE'){
$qry = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where ($te_master.`zone` like '%A%' or $te_master.`zone` like '%B%' or $te_master.`zone` like '%C%' ) order by $ledger_master.`ldgr_id` asc";
}else if($data_show_type=='OSNE'){
$qry = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where ($te_master.`zone` like '%D%' or $te_master.`zone` like '%E%' ) order by $ledger_master.`ldgr_id` asc";
}else{
$qry = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` $new_whr_str order by $ledger_master.`ldgr_id` asc";
}*/


$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$new_qry_string_filtered = "";
$trn_te_code = $_GET["trn_te_code"] ? addslashes(trim($_GET["trn_te_code"])) : "";
$sl_en_id = $_GET["sl_en_id"] ? addslashes(trim($_GET["sl_en_id"])) : "";
$srch_eng_te_site_dtls = $_GET["srch_eng_te_site_dtls"] ? addslashes(trim($_GET["srch_eng_te_site_dtls"])) : "";
$sl_ord_sts = $_GET["sl_ord_sts"] ? addslashes(trim($_GET["sl_ord_sts"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
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
			if($search_array_val=="POINT_EARNED"){ // points_earned
				$whr_str .= "$aand ($ledger_master.`point_earned` is not null or $ledger_master.`point_earned`!='') and ($ledger_master.`point_redeem` is null or  $ledger_master.`point_redeem`='')";
			}else if($search_array_val=="POINT_REDEEM"){
				$whr_str .= "$aand ($ledger_master.`point_redeem` is not null or $ledger_master.`point_redeem`!='') and ($ledger_master.`point_earned` is null or  $ledger_master.`point_earned`='')";
			}			
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
			$whr_str .= "$aand $engineer_master.`te_code`='$search_array_val' ";	
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
			$whr_str .= "$aand $ledger_master.`user_id`='$search_array_val' ";	
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
			
			$whr_str .= "$aand ($engineer_master.`e_name` like '%$search_array_val%' or $engineer_master.`te_code` like '%$search_array_val%' or $engineer_master.`e_mobile` like '%$search_array_val%' or $te_master.`te_name` like '%$search_array_val%' or $te_master.`te_mobile_no` like '%$search_array_val%' or $ledger_master.`description` like '%$search_array_val%' )";
			
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
			   $whr_str .= "$aand $ledger_master.`ldgr_datetime` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $ledger_master.`ldgr_datetime` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $ledger_master.`ldgr_datetime` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $ledger_master.`ldgr_datetime` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $ledger_master.`ldgr_datetime` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}
/*
if($whr_str!=""){
	$new_whr_str = " where $ledger_master.`ldgr_datetime` >= '$date_of_correction_in_point_calculation_as_per_new_SOP' AND ".$whr_str;
}else{
	$new_whr_str =" where $ledger_master.`ldgr_datetime` >= '$date_of_correction_in_point_calculation_as_per_new_SOP' ";
}*/
if($whr_str!=""){
	$new_whr_str = " where  ".$whr_str;
}else{
	$new_whr_str ="  ";
}
//echo"<pre>";print_r($new_whr_str);die;

if($data_show_type=="ALL"){
	//echo"<pre>";print_r($new_whr_str);die;
	//$new_whr_str = " where $ledger_master.`ldgr_datetime` >= '$date_of_correction_in_point_calculation_as_per_new_SOP' ";
$qry = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` $new_whr_str order by $ledger_master.`ldgr_datetime` asc";

}else{
$where_clause = "$ledger_master.`ldgr_datetime` >= '$date_of_correction_in_point_calculation_as_per_new_SOP'";
$zones = explode(",", $data_show_type); // Assuming $data_show_type is a comma-separated string of zones

foreach ($zones as $index => $zone) {
	$zone = trim($zone);
	if ($index != 0) {
		$where_clause .= " OR ";
	}
	else
	{
		$where_clause .= " AND ";
	}
	$where_clause .= "te_master.`zone` LIKE '%$zone%'";
}

$qry = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where $new_whr_str order by $ledger_master.`ldgr_id` asc";

// $qry = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where $te_master.`zone` like '%".$data_show_type."%' order by $ledger_master.`ldgr_id` asc";
}

//echo"<pre>";print_r($qry);die;

$sql = mysqli_query($conn,$qry);

$output .= '"TE CODE","Order No","OLD Order No","Gift ID","Gift Description","Gift Point","Engineer Name","Engineer Phn No","Branch","TE Name","TE Mobile","Shipping Address","Shipping Pin","Point Added","Point Reduced","TDS","Available Points","Creation Date"';

$output .="\n";
// Get Records from the table max ldgr_id
$user_last_balance = array();

$sql_last = "
    SELECT lm.user_id, lm.ldgr_id
    FROM ledger_master lm
    INNER JOIN (
        SELECT user_id, MAX(ldgr_id) AS max_ldgr_id
        FROM ledger_master
        GROUP BY user_id
    ) t ON t.user_id = lm.user_id
       AND t.max_ldgr_id = lm.ldgr_id
";

$res_last = mysqli_query($conn, $sql_last);
while ($row = mysqli_fetch_assoc($res_last)) {
    $uid = $row['user_id'];
    $user_last_balance[$uid] = array(
        'ldgr_id' => $row['ldgr_id']
    );
}

while ($row1 = mysqli_fetch_assoc($sql)) {
$ldgr_id = $row1["ldgr_id"] ? trim($row1["ldgr_id"]) : "";
$r_te_code = $row1["te_code"] ? trim($row1["te_code"]) : "";
$r_engineer_id = $row1["user_id"] ? trim($row1["user_id"]) : "";
$r_engineer_name = $row1["e_name"] ? trim($row1["e_name"]) : "";
$r_engineer_mobile = $row1["e_mobile"] ? trim($row1["e_mobile"]) : "";
$r_te_name = $row1["te_name"] ? trim($row1["te_name"]) : "";
$r_te_mobile_no = $row1["te_mobile_no"] ? trim($row1["te_mobile_no"]) : "";
$r_description = $row1["description"] ? str_replace('"', '""', trim($row1["description"])) : "";
$r_point_earned = $row1["point_earned"] ? trim($row1["point_earned"]) : "";
$r_point_redeem = $row1["point_redeem"] ? trim($row1["point_redeem"]) : "";
$tds = $row1["tds"] ? trim($row1["tds"]) : "";
$product_point = $row1["product_point"] ? trim($row1["product_point"]) : "";
$remaining_balance = $row1["remaining_balance"] ? trim($row1["remaining_balance"]) : "";
$the_branch_code_selected = $row1["branch_code"];

if($the_branch_code_selected!=""){
$the_branch_code_selected = get_branch_names_by_ids($conn,$the_branch_code_selected);
}else{
$the_branch_code_selected = "";	
}
$ldgr_datetime = $row1["ldgr_datetime"] ? trim($row1["ldgr_datetime"]) : "";
$onlyDate = date('Y-m-d H:i:s', strtotime($ldgr_datetime));

if($ldgr_datetime!=""){
	$ldgr_datetime = date("d-m-Y H:i:s",strtotime($ldgr_datetime));
}
//sk add line start 10-11-12
$g_order_id='';
$old_order_id='';
$address='';
$pin='';
$gift_id='';
$product_point='';
 if($r_point_redeem!=""){
			//$sqlgift_id="SELECT GOM.order_id,tds,address,pin,gift_id,GOM.g_order_id FROM `gift_master` GF,gift_order_master GOM WHERE GOM.gift_id=GF.id and GF.`gift_title`='".$r_description."' AND GOM.user_id='".$r_engineer_id."'";
			$sqlgift_id = "
				SELECT 
					GOM.order_id,
					GOM.tds,
					GOM.product_point,
					GOM.address,
					GOM.pin,
					GOM.product_point,
					GOM.g_order_id,
					GOM.gift_id
				FROM gift_master GF
				JOIN gift_order_master GOM ON GOM.gift_id = GF.id
				WHERE GF.gift_title = '".$r_description."'
				AND GOM.user_id = '".$r_engineer_id."'
				AND GOM.point_taken = '".$r_point_redeem."'
				AND GOM.datetime = '".$onlyDate."'
				";
			$resgift_id = mysqli_query($conn,$sqlgift_id);
			$rowgift_id=mysqli_fetch_assoc($resgift_id);
			$g_order_id_new = $rowgift_id["order_id"];
			$the_g_order_id = $rowgift_id["g_order_id"];
			$tds = $rowgift_id["tds"];
			$address = $rowgift_id["address"];
			$pin = $rowgift_id["pin"];
			$gift_id = $rowgift_id["gift_id"];
			$product_point=$rowgift_id["product_point"];
			$acknowledgement_rule_start_date = get_value_by_setting_key($conn, "acknowledgement_rule_start_date");
			if ($acknowledgement_rule_start_date == "") {
				$acknowledgement_rule_start_date = '2025-11-01'; // fallback date
			}
			
			$current_date = date('Y-m-d');
			$g_order_id='';
			$old_order_id='';
			//show all order change on 09-03-26
			$g_order_id=$g_order_id_new;
				 if (strtotime($ldgr_datetime) <= strtotime($acknowledgement_rule_start_date)) {
					$old_order_id=$the_g_order_id;
				 }else{
					$g_order_id=$g_order_id_new;

				 }
	
 }

//sk add end line start 10-02-26
if (
    isset($user_last_balance[$r_engineer_id]) &&
    (int)$user_last_balance[$r_engineer_id]['ldgr_id'] === (int)$ldgr_id
) {
    $show_remaining_balance = $remaining_balance;
} else {
    $show_remaining_balance = '';
}

$output .= '"'.$r_te_code.'","'.$g_order_id.'","'.$old_order_id.'","'.$gift_id.'","'.$r_description.'","'.$product_point.'","'.$r_engineer_name.'","'.$r_engineer_mobile.'","'.$the_branch_code_selected.'","'.$r_te_name.'","'.$r_te_mobile_no.'","'.$address.'","'.$pin.'","'.$r_point_earned.'","'.$r_point_redeem.'","'.$tds.'","'.$show_remaining_balance.'","'.$ldgr_datetime.'"';
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