<?php
include "web_check.php";
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$recommended_site_master = "recommended_site_master";
$te_master = "te_master";
$engineer_master = "engineer_master";
$branch_master = "branch_master";
$support_master = "support_master";
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


function show_total_linked_engineer_by_te($conn,$tecode,$sl_day_wise,$from_dt,$to_dt){
$engineer_master = "engineer_master";
$thecnt = 0;
$the_sl_day_wise = $sl_day_wise ? trim($sl_day_wise) : "";
$the_from_dt = $from_dt ? trim($from_dt) : "";
$the_to_dt = $to_dt ? trim($to_dt) : "";

$nuQry = "";
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
if($the_sl_day_wise=="Date_Range"){
	if($the_from_dt!="" && $the_to_dt!=""){
	   $nuQry = " and `reg_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
	}else if($the_from_dt!="" && $the_to_dt==""){
		$nuQry = " and `reg_date` >= '".$the_from_dt." ".$frm_hrs."' ";
	}else if($the_from_dt=="" && $the_to_dt!=""){
		$nuQry = " and `reg_date` <= '".$the_to_dt." ".$to_hrs."' ";
	}
}else{
	if($the_sl_day_wise=="Today"){
		$nuQry = " and `reg_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
	}else if($the_sl_day_wise=="Yesterday"){
		$nuQry = " and `reg_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
	}
}


$tecode = $tecode ? addslashes(trim($tecode)) : "";
if($tecode!=""){
$pgsql2 = "select count(`eid`) as `total_engineer_count` from $engineer_master where `te_code`='$tecode' $nuQry";
$pgres2 = mysqli_query($conn,$pgsql2);
$total_pgres2 = mysqli_num_rows($pgres2);
if($total_pgres2>0){
$row12=mysqli_fetch_assoc($pgres2);
$thecnt = $row12["total_engineer_count"];
}
}
return $thecnt;
}

function show_total_site_recomended_data_by_te($conn,$tecode,$sl_day_wise,$from_dt,$to_dt){
$recommended_site_count_arr = array("ALL"=>0,"PENDING"=>0,"APPROVED"=>0,"REJECTED"=>0);
$recommended_site_master = "recommended_site_master";
$thecnt = 0;
$the_sl_day_wise = $sl_day_wise ? trim($sl_day_wise) : "";
$the_from_dt = $from_dt ? trim($from_dt) : "";
$the_to_dt = $to_dt ? trim($to_dt) : "";
$nuQry = "";
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
if($the_sl_day_wise=="Date_Range"){
	if($the_from_dt!="" && $the_to_dt!=""){
	   $nuQry = " and $recommended_site_master.`r_submission_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
	}else if($the_from_dt!="" && $the_to_dt==""){
		$nuQry = " and $recommended_site_master.`r_submission_date` >= '".$the_from_dt." ".$frm_hrs."' ";
	}else if($the_from_dt=="" && $the_to_dt!=""){
		$nuQry = " and $recommended_site_master.`r_submission_date` <= '".$the_to_dt." ".$to_hrs."' ";
	}
}else{
	if($the_sl_day_wise=="Today"){
		$nuQry = " and $recommended_site_master.`r_submission_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
	}else if($the_sl_day_wise=="Yesterday"){
		$nuQry = " and $recommended_site_master.`r_submission_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
	}
}


$tecode = $tecode ? addslashes(trim($tecode)) : "";
if($tecode!=""){
$pgsqlrecom1 = "SELECT `r_status`,count(`r_status`) as `tot_r_status` FROM $recommended_site_master where `r_te_code`='$tecode' $nuQry group by `r_status`";
$pgresrecom1 = mysqli_query($conn,$pgsqlrecom1);
$total_pgresrecom1 = mysqli_num_rows($pgresrecom1);
if($total_pgresrecom1>0){
	while($rowrecom1=mysqli_fetch_assoc($pgresrecom1)){
	$recomsts = $rowrecom1["r_status"] ? trim($rowrecom1["r_status"]) : "";
	$recomtot_r_status = $rowrecom1["tot_r_status"] ? trim($rowrecom1["tot_r_status"]) : 0;
	if($recomsts!=""){
		$recommended_site_count_arr[$recomsts] = $recomtot_r_status;
	}
	}
}
$recommended_site_count_arr["ALL"] = $recommended_site_count_arr["PENDING"] + $recommended_site_count_arr["APPROVED"] + $recommended_site_count_arr["REJECTED"];
}
return $recommended_site_count_arr;
}
function show_total_gift_data_by_te($conn,$tecode,$sl_day_wise,$from_dt,$to_dt){
$gift_order_count_arr = array("ALL"=>0,"PENDING"=>0,"DELIVERED"=>0);
$gift_order_master = "gift_order_master";
$engineer_master = "engineer_master";
$thecnt = 0;

$the_sl_day_wise = $sl_day_wise ? trim($sl_day_wise) : "";
$the_from_dt = $from_dt ? trim($from_dt) : "";
$the_to_dt = $to_dt ? trim($to_dt) : "";
$nuQry = "";
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
if($the_sl_day_wise=="Date_Range"){
	if($the_from_dt!="" && $the_to_dt!=""){
	   $nuQry = " and $gift_order_master.`datetime` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
	}else if($the_from_dt!="" && $the_to_dt==""){
		$nuQry = " and $gift_order_master.`datetime` >= '".$the_from_dt." ".$frm_hrs."' ";
	}else if($the_from_dt=="" && $the_to_dt!=""){
		$nuQry = " and $gift_order_master.`datetime` <= '".$the_to_dt." ".$to_hrs."' ";
	}
}else{
	if($the_sl_day_wise=="Today"){
		$nuQry = " and $gift_order_master.`datetime` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
	}else if($the_sl_day_wise=="Yesterday"){
		$nuQry = " and $gift_order_master.`datetime` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
	}
}


$tecode = $tecode ? addslashes(trim($tecode)) : "";
if($tecode!=""){
	$pgsqlgords = "SELECT $gift_order_master.`status`,count($gift_order_master.`status`) as `ord_status_cnt` FROM $gift_order_master left join $engineer_master on $gift_order_master.`user_id`=$engineer_master.`eid` where $engineer_master.`te_code`='$tecode'  $nuQry group by $gift_order_master.`status`";
$pgresgords = mysqli_query($conn,$pgsqlgords);
$total_pgresgords = mysqli_num_rows($pgresgords);
if($total_pgresgords>0){
	while($rowgords=mysqli_fetch_assoc($pgresgords)){
	$gords_status = $rowgords["status"] ? trim($rowgords["status"]) : "";
	$gords_status_cnt = $rowgords["ord_status_cnt"] ? trim($rowgords["ord_status_cnt"]) : 0;
	if($gords_status!=""){
		$gift_order_count_arr[$gords_status] = $gords_status_cnt;
	}
	}
}

$gift_order_count_arr["ALL"] = $gift_order_count_arr["PENDING"] + $gift_order_count_arr["DELIVERED"];
}
return $gift_order_count_arr;
}



function show_total_active_engineer_by_te($conn,$tecode,$sl_day_wise,$from_dt,$to_dt){
$date_before_twelve_month = date('Y-m-d H:i:s',strtotime("-12 month"));
$date_before_six_month = date('Y-m-d H:i:s',strtotime("-6 month"));
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$thecnt = 0;
$tecode = $tecode ? addslashes(trim($tecode)) : "";
$the_sl_day_wise = $sl_day_wise ? trim($sl_day_wise) : "";
$the_from_dt = $from_dt ? trim($from_dt) : "";
$the_to_dt = $to_dt ? trim($to_dt) : "";

$nuQry = "";
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
if($the_sl_day_wise=="Date_Range"){
	if($the_from_dt!="" && $the_to_dt!=""){
	   $nuQry = " and $engineer_master.`reg_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
	}else if($the_from_dt!="" && $the_to_dt==""){
		$nuQry = " and $engineer_master.`reg_date` >= '".$the_from_dt." ".$frm_hrs."' ";
	}else if($the_from_dt=="" && $the_to_dt!=""){
		$nuQry = " and $engineer_master.`reg_date` <= '".$the_to_dt." ".$to_hrs."' ";
	}
}else{
	if($the_sl_day_wise=="Today"){
		$nuQry = " and $engineer_master.`reg_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
	}else if($the_sl_day_wise=="Yesterday"){
		$nuQry = " and $engineer_master.`reg_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
	}
}

if($tecode!=""){
	$sqlActive = "select count($engineer_master.`eid`) as `tot_active_engineer` from $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master where `r_te_code`='$tecode' GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where $engineer_master.`te_code`='$tecode' and `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`>='$date_before_twelve_month' and $engineer_master.`status_by_te`='APPROVED' $nuQry";
	$resActive = mysqli_query($conn,$sqlActive);
	$totresActive = mysqli_num_rows($resActive);
	if($totresActive>0){
	$rowActive=mysqli_fetch_assoc($resActive);
	$thecnt = $rowActive["tot_active_engineer"];
	}
}
return $thecnt;
}

function show_total_semi_active_engineer_by_te($conn,$tecode,$sl_day_wise,$from_dt,$to_dt){
$date_before_three_month = date('Y-m-d H:i:s',strtotime("-3 month"));
$date_before_six_month = date('Y-m-d H:i:s',strtotime("-6 month"));
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$thecnt = 0;
$tecode = $tecode ? addslashes(trim($tecode)) : "";
$nuQry = "";
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
if($the_sl_day_wise=="Date_Range"){
	if($the_from_dt!="" && $the_to_dt!=""){
	   $nuQry = " and $engineer_master.`reg_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
	}else if($the_from_dt!="" && $the_to_dt==""){
		$nuQry = " and $engineer_master.`reg_date` >= '".$the_from_dt." ".$frm_hrs."' ";
	}else if($the_from_dt=="" && $the_to_dt!=""){
		$nuQry = " and $engineer_master.`reg_date` <= '".$the_to_dt." ".$to_hrs."' ";
	}
}else{
	if($the_sl_day_wise=="Today"){
		$nuQry = " and $engineer_master.`reg_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
	}else if($the_sl_day_wise=="Yesterday"){
		$nuQry = " and $engineer_master.`reg_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
	}
}

if($tecode!=""){
		$sqlSemiActive = "select count($engineer_master.`eid`) as `tot_semi_active_engineer` from $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master where `r_te_code`='$tecode' GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where $engineer_master.`te_code`='$tecode' and `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_three_month' and `latest_recommended_site_master`.`r_submission_date`>='$date_before_six_month' $nuQry";
		$resSemiActive = mysqli_query($conn,$sqlSemiActive);
		$totresSemiActive = mysqli_num_rows($resSemiActive);
		if($totresSemiActive>0){
		$rowSemiActive=mysqli_fetch_assoc($resSemiActive);
		$thecnt = $rowSemiActive["tot_semi_active_engineer"];
		}
}
return $thecnt;
}
function show_total_inactive_engineer_by_te($conn,$tecode,$sl_day_wise,$from_dt,$to_dt){
$date_before_twelve_month = date('Y-m-d H:i:s',strtotime("-12 month"));
$date_before_six_month = date('Y-m-d H:i:s',strtotime("-6 month"));
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$thecnt = 0;
$tecode = $tecode ? addslashes(trim($tecode)) : "";
$nuQry = "";
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
if($the_sl_day_wise=="Date_Range"){
	if($the_from_dt!="" && $the_to_dt!=""){
	   $nuQry = " and $engineer_master.`reg_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
	}else if($the_from_dt!="" && $the_to_dt==""){
		$nuQry = " and $engineer_master.`reg_date` >= '".$the_from_dt." ".$frm_hrs."' ";
	}else if($the_from_dt=="" && $the_to_dt!=""){
		$nuQry = " and $engineer_master.`reg_date` <= '".$the_to_dt." ".$to_hrs."' ";
	}
}else{
	if($the_sl_day_wise=="Today"){
		$nuQry = " and $engineer_master.`reg_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
	}else if($the_sl_day_wise=="Yesterday"){
		$nuQry = " and $engineer_master.`reg_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
	}
}
if($tecode!=""){
	$sqlInActive = "select count($engineer_master.`eid`) as `tot_inactive_engineer` from $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master  where `r_te_code`='$tecode' GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where $engineer_master.`te_code`='$tecode' and $engineer_master.`status_by_te`='APPROVED' and `latest_recommended_site_master`.`r_submission_date` is null or (`latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_twelve_month') $nuQry";
	$resInActive = mysqli_query($conn,$sqlInActive);
	$totresInActive = mysqli_num_rows($resInActive);
	if($totresInActive>0){
	$rowInActive=mysqli_fetch_assoc($resInActive);
	$thecnt = $rowInActive["tot_inactive_engineer"];
	}
}
return $thecnt;
}

function show_query_data_by_te($conn,$tecode,$sl_day_wise,$from_dt,$to_dt){
$support_count_arr = array("ALL"=>0,"PENDING"=>0,"RESOLVE"=>0);
$support_master = "support_master";
$engineer_master = "engineer_master";
$thecnt = 0;

$the_sl_day_wise = $sl_day_wise ? trim($sl_day_wise) : "";
$the_from_dt = $from_dt ? trim($from_dt) : "";
$the_to_dt = $to_dt ? trim($to_dt) : "";
$nuQry = "";
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
if($the_sl_day_wise=="Date_Range"){
	if($the_from_dt!="" && $the_to_dt!=""){
	   $nuQry = " and $support_master.`submitted_datetime` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
	}else if($the_from_dt!="" && $the_to_dt==""){
		$nuQry = " and $support_master.`submitted_datetime` >= '".$the_from_dt." ".$frm_hrs."' ";
	}else if($the_from_dt=="" && $the_to_dt!=""){
		$nuQry = " and $support_master.`submitted_datetime` <= '".$the_to_dt." ".$to_hrs."' ";
	}
}else{
	if($the_sl_day_wise=="Today"){
		$nuQry = " and $support_master.`submitted_datetime` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
	}else if($the_sl_day_wise=="Yesterday"){
		$nuQry = " and $support_master.`submitted_datetime` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
	}
}


$tecode = $tecode ? addslashes(trim($tecode)) : "";
if($tecode!=""){
	$pgsqlgords = "SELECT $support_master.`status`,count($support_master.`status`) as `supp_status_cnt` FROM $support_master left join $engineer_master on $support_master.`user_id`=$engineer_master.`eid` where $engineer_master.`te_code`='$tecode'  $nuQry group by $support_master.`status`";
$pgresgords = mysqli_query($conn,$pgsqlgords);
$total_pgresgords = mysqli_num_rows($pgresgords);
if($total_pgresgords>0){
	while($rowgords=mysqli_fetch_assoc($pgresgords)){
	$gords_status = $rowgords["status"] ? trim($rowgords["status"]) : "";
	$gords_status_cnt = $rowgords["supp_status_cnt"] ? trim($rowgords["supp_status_cnt"]) : 0;
	if($gords_status!=""){
		$support_count_arr[$gords_status] = $gords_status_cnt;
	}
	}
}

$support_count_arr["ALL"] = $support_count_arr["PENDING"] + $support_count_arr["RESOLVE"];
}
return $support_count_arr;
}

$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$new_qry_string_filtered = "";
$srch_eng_te_site_dtls = $_GET["srch_eng_te_site_dtls"] ? addslashes(trim($_GET["srch_eng_te_site_dtls"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$trn_zone = $_GET["trn_zone"] ? addslashes(trim($_GET["trn_zone"])) : "";
$trn_branch = $_GET["trn_branch"] ? addslashes(trim($_GET["trn_branch"])) : "";
$whr_str = "";
$export_filtered_str = "";
$search_array = array("srch_eng_te_site_dtls"=>$srch_eng_te_site_dtls,"trn_branch"=>$trn_branch,"trn_zone"=>$trn_zone,"data_show_type"=>$data_show_type,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_eng_te_site_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			
			$whr_str .= "$aand ($te_master.`te_name` like '%$search_array_val%' or $te_master.`te_code` like '%$search_array_val%' or $te_master.`te_mobile_no` like '%$search_array_val%' or $te_master.`state` like '%$search_array_val%' )";
			
			$new_qry_string_filtered .= "&srch_eng_te_site_dtls=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&srch_eng_te_site_dtls=".$search_array_val;
			}else{
				$export_filtered_str .= "&srch_eng_te_site_dtls=".$search_array_val;
			}	
		}		
	}
	else if($search_array_key=="trn_zone"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			
			$whr_str .= "$aand (FIND_IN_SET('".$search_array_val."',$te_master.zone))";
			
			$new_qry_string_filtered .= "&trn_zone=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&trn_zone=".$search_array_val;
			}else{
				$export_filtered_str .= "&trn_zone=".$search_array_val;
			}	
		}		
	}
	else if($search_array_key=="trn_branch"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			
			$whr_str .= "$aand (FIND_IN_SET('".$search_array_val."',$te_master.branch_code))";			
			$new_qry_string_filtered .= "&trn_branch=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&trn_branch=".$search_array_val;
			}else{
				$export_filtered_str .= "&trn_branch=".$search_array_val;
			}	
		}		
	}
	else if($search_array_key=="data_show_type"){
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
			   //$whr_str .= "$aand $recommended_site_master.`r_submission_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				//$whr_str .= "$aand $recommended_site_master.`r_submission_date` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				//$whr_str .= "$aand $recommended_site_master.`r_submission_date` <= '".$the_to_dt." ".$to_hrs."' ";
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
				//$whr_str .= "$aand $recommended_site_master.`r_submission_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				//$whr_str .= "$aand $recommended_site_master.`r_submission_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}


if($whr_str!=""){
	$new_whr_str = " and ".$whr_str;
}else{
	$new_whr_str ="";
}

$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "te_dashboard_".$curr_date.".csv";
$output = "";

$output .= '"TE CODE","ZONE","TE NAME","BRANCH","LINKED ENGINEER","SITES RECOMMENDED","SITES APPROVED","SITES PENDING","SITES REJECTED","GIFT REDEEMED","GIFT DELIVERED","QUERY RAISED","QUERY SOLVED","ACTIVE LINKED ENGINEER","INACTIVE LINKED ENGINEER"';

$output .="\n";


$sql1 = "select * from $te_master where `acedns`='Y' $new_whr_str order by `zone` asc";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
$the_te_id = $row1["te_id"];
$the_te_name = $row1["te_name"];
$the_te_mobile_no = $row1["te_mobile_no"];
$the_te_code = $row1["te_code"];
$the_te_email = $row1["te_email"];
$the_te_zone = $row1["zone"];
$the_branch_name_selected = "";
$the_branch_name_selected_short = "";
$the_branch_code_selected = $row1["branch_code"] ? trim($row1["branch_code"]) : "";
if($the_branch_code_selected!=""){
$the_branch_name_selected = get_branch_names_by_ids($conn,$the_branch_code_selected);
if(strlen($the_branch_name_selected)>28){
$the_branch_name_selected_short = substr($the_branch_name_selected,0,28)."...";
}else{
$the_branch_name_selected_short = 	$the_branch_name_selected;
}
}else{
$the_branch_name_selected = "";	
}
$total_linked_engineer = show_total_linked_engineer_by_te($conn,$the_te_code,$sl_day_wise,$from_dt,$to_dt);
$site_recomended_data_arr = array();
$site_recomended_data_arr = show_total_site_recomended_data_by_te($conn,$the_te_code,$sl_day_wise,$from_dt,$to_dt);
$total_site_recomended = $site_recomended_data_arr["ALL"];
$total_site_pending = $site_recomended_data_arr["PENDING"];
$total_site_approved = $site_recomended_data_arr["APPROVED"];
$total_site_rejected = $site_recomended_data_arr["REJECTED"];
$gift_data_arr = array();
$gift_data_arr = show_total_gift_data_by_te($conn,$the_te_code,$sl_day_wise,$from_dt,$to_dt);
$gift_redemed = $gift_data_arr["ALL"];
$gift_delevered = $gift_data_arr["DELIVERED"];
$total_active_engineer = show_total_active_engineer_by_te($conn,$the_te_code,$sl_day_wise,$from_dt,$to_dt);
$total_inactive_engineer = show_total_inactive_engineer_by_te($conn,$the_te_code,$sl_day_wise,$from_dt,$to_dt);
$query_data_arr = show_query_data_by_te($conn,$the_te_code,$sl_day_wise,$from_dt,$to_dt);
$query_raised = $query_data_arr["ALL"];
$query_solved = $query_data_arr["RESOLVE"];


$output .= '"'.$the_te_code.'","'.$the_te_zone.'","'.$the_te_name.'","'.$the_branch_name_selected_short.'","'.$total_linked_engineer.'","'.$total_site_recomended.'","'.$total_site_approved.'","'.$total_site_pending.'","'.$total_site_rejected.'","'.$gift_redemed.'","'.$gift_delevered.'","'.$query_raised.'","'.$query_solved.'","'.$total_active_engineer.'","'.$total_inactive_engineer.'"';
$output .="\n";

}
}
mysqli_close($conn);
$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;

?>