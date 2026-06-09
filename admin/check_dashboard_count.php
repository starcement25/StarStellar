<?php
include "web_check.php";
include "star_connection.php";
$recommended_site_master = "recommended_site_master";
$te_master = "te_master";
$engineer_master = "engineer_master";
$branch_master = "branch_master";
$support_master = "support_master";
$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];

function show_total_active_engineer_by_te($conn,$tecode){
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
	echo $sqlActive = "select count($engineer_master.`eid`) as `tot_active_engineer` from $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where $engineer_master.`te_code`='$tecode' and `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`>='$date_before_twelve_month' $nuQry";
	$resActive = mysqli_query($conn,$sqlActive);
	$totresActive = mysqli_num_rows($resActive);
	if($totresActive>0){
	$rowActive=mysqli_fetch_assoc($resActive);
	$thecnt = $rowActive["tot_active_engineer"];
	}
}
return $thecnt;
}
function show_total_semi_active_engineer_by_te($conn,$tecode){
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
		$sqlSemiActive = "select count($engineer_master.`eid`) as `tot_semi_active_engineer` from $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where $engineer_master.`te_code`='$tecode' and `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_three_month' and `latest_recommended_site_master`.`r_submission_date`>='$date_before_six_month' $nuQry";
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
	echo $sqlInActive = "select count($engineer_master.`eid`) as `tot_inactive_engineer` from $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where $engineer_master.`te_code`='$tecode' and `latest_recommended_site_master`.`r_submission_date` is null or (`latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_twelve_month') $nuQry";
	$resInActive = mysqli_query($conn,$sqlInActive);
	$totresInActive = mysqli_num_rows($resInActive);
	if($totresInActive>0){
	$rowInActive=mysqli_fetch_assoc($resInActive);
	$thecnt = $rowInActive["tot_inactive_engineer"];
	}
}
return $thecnt;
}
$the_te_code='E1166';
$total_active_engineer = show_total_active_engineer_by_te($conn,$the_te_code);
$total_inactive_engineer = show_total_inactive_engineer_by_te($conn,$the_te_code);
?>
