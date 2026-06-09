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

function show_total_linked_engineer_by_te($conn,$tecode){
$engineer_master = "engineer_master";
$thecnt = 0;
$tecode = $tecode ? addslashes(trim($tecode)) : "";
if($tecode!=""){
$pgsql2 = "select count(`eid`) as `total_engineer_count` from $engineer_master where `te_code`='$tecode'";
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

function show_total_active_engineer_by_te($conn,$tecode){
$date_before_three_month = date('Y-m-d H:i:s',strtotime("-3 month"));
$date_before_six_month = date('Y-m-d H:i:s',strtotime("-6 month"));
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$thecnt = 0;
$tecode = $tecode ? addslashes(trim($tecode)) : "";
if($tecode!=""){
	$sqlActive = "select count($engineer_master.`eid`) as `tot_active_engineer` from $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where $engineer_master.`te_code`='$tecode' and `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`>='$date_before_three_month'";
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
if($tecode!=""){
		$sqlSemiActive = "select count($engineer_master.`eid`) as `tot_semi_active_engineer` from $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where $engineer_master.`te_code`='$tecode' and `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_three_month' and `latest_recommended_site_master`.`r_submission_date`>='$date_before_six_month'";
		$resSemiActive = mysqli_query($conn,$sqlSemiActive);
		$totresSemiActive = mysqli_num_rows($resSemiActive);
		if($totresSemiActive>0){
		$rowSemiActive=mysqli_fetch_assoc($resSemiActive);
		$thecnt = $rowSemiActive["tot_semi_active_engineer"];
		}
}
return $thecnt;
}
function show_total_inactive_engineer_by_te($conn,$tecode){
$date_before_three_month = date('Y-m-d H:i:s',strtotime("-3 month"));
$date_before_six_month = date('Y-m-d H:i:s',strtotime("-6 month"));
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$thecnt = 0;
$tecode = $tecode ? addslashes(trim($tecode)) : "";
if($tecode!=""){
	$sqlInActive = "select count($engineer_master.`eid`) as `tot_inactive_engineer` from $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where $engineer_master.`te_code`='$tecode' and `latest_recommended_site_master`.`r_submission_date` is null or (`latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_six_month')";
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



$order_status_arr = array("PENDING","APPROVED","REJECTED");
$supported_mime_type = array("image/jpeg","image/png","image/jpg");
$img_dir = "../recomend_site_pic/";
$approve_img_dir = "../approved_recomend_site_pic/";
$default_image_name = "profile.png";
$image_url_prefix = $server_url."recomend_site_pic/";
$approve_image_url_prefix = $server_url."approved_recomend_site_pic/";
$new_qry_string_filtered = "";
$msg_txt = "";
$add_page_name = "dashboard_with_respect_to_te.php";
$page_name = "dashboard_with_respect_to_te.php";
if(isset($_GET["msg_txt"]) && @$_GET["msg_txt"]!=""){
	$msg_txt = $_GET["msg_txt"];
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
$whr_str = "";
$export_filtered_str = "";
$search_array = array("srch_eng_te_site_dtls"=>$srch_eng_te_site_dtls,"data_show_type"=>$data_show_type,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
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
	$new_whr_str = " where ".$whr_str;
}else{
	$new_whr_str ="";
}

//?sl_branch_code=B0056&sl_ord_sts=Pending&sl_day_wise=Date_Range&from_dt=2018-07-01&to_dt=2018-07-31
if($trn_te_code!=""){
	$sql5dcode = "select `eid`,`e_name` from $engineer_master where `te_code`='$trn_te_code' order by `e_name` asc ";
	$res5dcode = mysqli_query($conn,$sql5dcode);
	$tot_res5dcode = mysqli_num_rows($res5dcode);
}

$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select `te_id` from $te_master $new_whr_str ";
$pgres = mysqli_query($conn,$pgsql);
$total_pgres = mysqli_num_rows($pgres);
$start_from = (($page-1)*$limit);
$prev = $page - 1;							//previous page is page - 1
$next = $page + 1;							//next page is page + 1
$lastpage = ceil($total_pgres/$limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;

/*---------PAGINATION RELATED CODE START----------*/


include "web_header.php";
?>
<style>
.wrapper_scrl{
border: none;
overflow-x: scroll;
overflow-y:hidden;
height: 20px;
}
.wrapper_scrl_div{
height: 20px;	
}
.table-bordered thead tr th{
font-size: 11px;
padding: 5px;
}
.prfl_img{
	width:100px;
	height:80px;
}
.selected_prfl_img{
width:80px;
height:50px;
margin-bottom:10px;
}
.blbl{
	width:100%;
	text-align:left;
}
.engineerEachField{
	display:block;
	width:150px;
	word-wrap: break-word;
}
.teEachField{
	display:block;
	width:150px;
	word-wrap: break-word;
}
.siteEachField{
	display:block;
	width:200px;
	word-wrap: break-word;
}
.contactPersonEachField{
	display:block;
	width:150px;
	word-wrap: break-word;
}
.eachField{
	display:block;
	width:200px;
	word-wrap: break-word;
}
.recomSiteEachField{
	display:block;
	width:150px;
	word-wrap: break-word;
}
.branchCodeEachField{
	display:block;
	width:200px;
	word-wrap: break-word;
	font-size:11px;
	cursor:pointer;
}

.table-scroll {
	position:relative;
	max-width:100%;
	margin:auto;
	overflow:hidden;
	border:1px solid #e2e2e2;
}
.table-wrap {
	width:100%;
	overflow:auto;
}
.table-scroll table {
	width:100%;
	margin:auto;
	border-collapse:separate;
	border-spacing:0;
}
.table-scroll th, .table-scroll td {
	padding:10px 10px;
	border:1px solid #e2e2e2;
	background:#fff;
	white-space:nowrap;
	vertical-align:top;
}
.table-scroll thead, .table-scroll tfoot {
	background:#f9f9f9;
}
.clone {
	position:absolute;
	top:0;
	left:0;
	pointer-events:none;
}
.clone th, .clone td {
	visibility:hidden
}
.clone td, .clone th {
	border-color:transparent
}
.clone tbody th {
	visibility:visible;
	color:#ffffff;
}
.clone .fixed-side {
	border:1px solid #e2e2e2;
	background:#073874;
	visibility:visible;
}
.clone thead, .clone tfoot{background:transparent;}
.fixed-side{
	color:#ffffff;
	background-color: #DB4141 !important;
}
.branch_cls{
	
}
.table-scroll th span.ttckl{
	cursor:pointer;
}
</style>
<script>
jQuery(document).ready(function() {
   jQuery(".main-table").clone(true).appendTo('#table-scroll').addClass('clone');
   jQuery(".ttckl").click(function(){
   		jQuery(this).tooltip('toggle');
    }); 
 });
</script>
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header" style="padding-bottom: 0px;">
<h2>Dashboard with respect to TE (<?php echo $total_pgres;?>)&nbsp;
&nbsp;&nbsp;<a href="export_dashboard_with_respect_to_te_data.php?srch_eng_te_site_dtls=<?php echo $srch_eng_te_site_dtls;?>&sl_day_wise=<?php echo $sl_day_wise;?>&from_dt=<?php echo $from_dt;?>&to_dt=<?php echo $to_dt;?>" class="btn bg-red waves-effe">Export&nbsp;TE&nbsp;Dashboard&nbsp;Data</a>
&nbsp;&nbsp;<span class="rpt_loader"></span> <span class="shomsg"><?php if($msg_txt!=""){ echo $msg_txt;}?></span>
</h2>
    <div class="row clearfix">
    
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_eng_te_site_dtls" value="<?php echo $srch_eng_te_site_dtls;?>" placeholder="Search By TE Details">

    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_day_wise" >
<option value="">Select Day-Wise</option>
<option value="Today" <?php if($sl_day_wise=="Today"){?> selected="selected" <?php } ?>>Today</option>
<option value="Yesterday" <?php if($sl_day_wise=="Yesterday"){?> selected="selected" <?php } ?>>Yesterday</option>
<option value="Date_Range" <?php if($sl_day_wise=="Date_Range"){?> selected="selected" <?php } ?>>Date Range</option>
</select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <input type="text" class="datepicker form-control" id="from_dt" <?php if($sl_day_wise!="Date_Range"){?> style="display:none;" <?php } ?> value="<?php echo $from_dt;?>" placeholder="Choose from date">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <input type="text" class="datepicker form-control" id="to_dt" <?php if($sl_day_wise!="Date_Range"){?> style="display:none;" <?php } ?> value="<?php echo $to_dt;?>" placeholder="Choose to date">
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" >Reset</button>
    </div>
    </div>
                            <span style="clear:both;display:block;"></span>
                        </div>
                        <div class="body" style="padding-top: 0px;padding-left: 4px;">
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>

<div class="wrapper_scrl">
    <div class="wrapper_scrl_div">
    </div>
</div>

<div id="table-scroll" class="table-scroll">
  <div class="table-wrap tr_for_scroll">
<table class="main-table table_for_scroll">
<thead>
<tr>
<th class="fixed-side" scope="col"><b style="color:#ffffff">TE&nbsp;CODE</b></th>
<th class="fixed-side" scope="col"><b style="color:#ffffff">ZONE</b></th>
<th class="fixed-side" scope="col"><b style="color:#ffffff">TE&nbsp;NAME</b></th>
<th scope="col" style="width:200px !important;" class="branch_cls">BRANCH</th>
<th scope="col">LINKED</BR>ENGINEER</th>
<th scope="col">SITES</BR>RECOMMENDED </th>
<th scope="col">SITES</BR>APPROVED</th>
<th scope="col">SITES</BR>PENDING</th>
<th scope="col">SITES</BR>REJECTED</th>
<th scope="col">GIFT</BR>REDEEMED</th>
<th scope="col">GIFT</BR>DELIVERED</th>
<th scope="col">QUERY</BR>RAISED</th>
<th scope="col">QUERY</BR>SOLVED</th>
<th scope="col">ACTIVE LINKED</BR>ENGINEER</th>
<th scope="col">SEMIACTIVE LINKED</BR>ENGINEER</th>
<th scope="col">INACTIVE LINKED</BR>ENGINEER</th>
</tr>
</thead>
<tbody>
<?php
$sql1 = "select * from $te_master $new_whr_str order by `zone` asc limit $start_from,$limit";
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
$total_linked_engineer = show_total_linked_engineer_by_te($conn,$the_te_code);
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
$total_active_engineer = show_total_active_engineer_by_te($conn,$the_te_code);
$total_semi_active_engineer = show_total_semi_active_engineer_by_te($conn,$the_te_code);
$total_inactive_engineer = show_total_inactive_engineer_by_te($conn,$the_te_code);
$query_data_arr = show_query_data_by_te($conn,$the_te_code,$sl_day_wise,$from_dt,$to_dt);
$query_raised = $query_data_arr["ALL"];
$query_solved = $query_data_arr["RESOLVE"];
?>
<tr>
<td class="fixed-side"> <?php echo $the_te_code;?> </td>
<td class="fixed-side"> <?php echo $the_te_zone;?> </td>
<td class="fixed-side"> <?php echo $the_te_name;?> </td>
<td class="branch_cls" style="width:200px !important;">
<div style="width:200px !important;">
<span class="branchCodeEachField ttckl" title="<?php echo $the_branch_name_selected;?>"><?php echo $the_branch_name_selected_short;?>
</span>
</div>
</td>
<td style="text-align:center;"><a href="engineer_master.php?srch_eng_dtls=<?php echo $the_te_code;?>" target="_blank"><?php echo $total_linked_engineer;?></a></td>
<td style="text-align:center;"><a href="recomended_site_master.php?trn_te_code=<?php echo $the_te_code;?>" target="_blank"><?php echo $total_site_recomended;?></a></td>
<td style="text-align:center;"><a href="recomended_site_master.php?trn_te_code=<?php echo $the_te_code;?>&sl_ord_sts=APPROVED" target="_blank"><?php echo $total_site_approved;?></a></td>
<td style="text-align:center;"><a href="recomended_site_master.php?trn_te_code=<?php echo $the_te_code;?>&sl_ord_sts=PENDING" target="_blank"><?php echo $total_site_pending;?></a></td>
<td style="text-align:center;"><a href="recomended_site_master.php?trn_te_code=<?php echo $the_te_code;?>&sl_ord_sts=REJECTED" target="_blank"><?php echo $total_site_rejected;?></a></td>
<td style="text-align:center;"><a href="order_master.php?trn_te_code=<?php echo $the_te_code;?>" target="_blank"><?php echo $gift_redemed;?></a></td>
<td style="text-align:center;"><a href="order_master.php?trn_te_code=<?php echo $the_te_code;?>&sl_ord_sts=DELIVERED" target="_blank"><?php echo $gift_delevered;?></a></td>
<td style="text-align:center;"><a href="support_master.php?srch_eng_dtls=<?php echo $the_te_code;?>" target="_blank"><?php echo $query_raised;?></a></td>
<td style="text-align:center;"><a href="support_master.php?srch_eng_dtls=<?php echo $the_te_code;?>&sl_supp_status=RESOLVE" target="_blank"><?php echo $query_solved;?></a></td>
<td style="text-align:center;"><a href="engineer_master.php?srch_eng_dtls=<?php echo $the_te_code;?>&sl_activity_status=ACTIVE" target="_blank"><?php echo $total_active_engineer;?></a></td>
<td style="text-align:center;"><a href="engineer_master.php?srch_eng_dtls=<?php echo $the_te_code;?>&sl_activity_status=SEMI_ACTIVE" target="_blank"><?php echo $total_semi_active_engineer;?></a></td>
<td style="text-align:center;"><a href="engineer_master.php?srch_eng_dtls=<?php echo $the_te_code;?>&sl_activity_status=INACTIVE" target="_blank"><?php echo $total_inactive_engineer;?></a></td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="16">No data found.</td>
</tr>
<?php
}
?>
</tbody>
</table>
 </div>
 </div>
 
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
                        </div>
                   
                    </div>
                </div>
            </div>
            <!-- #END# Basic Examples -->
            <!-- Exportable Table -->
            
            <!-- #END# Exportable Table -->
        </div>
    </section>
<script type="text/javascript">
jQuery(function(){
	var imgs = '<img src="images/ajax-loader.gif"/>';
	var done_img = '<img src="images/success_tick.png"/>';

setTimeout(function(){
	jQuery(".shomsg").html("");
},8000);

var tr_for_scroll = jQuery(".tr_for_scroll").width();
var table_for_scroll = jQuery(".table_for_scroll").width();
jQuery(".wrapper_scrl").css("width",tr_for_scroll+"px");
jQuery(".wrapper_scrl_div").css("width",table_for_scroll+"px");

jQuery(".wrapper_scrl").scroll(function(){
jQuery(".tr_for_scroll")
.scrollLeft(jQuery(".wrapper_scrl").scrollLeft());
});
jQuery(".tr_for_scroll").scroll(function(){
jQuery(".wrapper_scrl")
.scrollLeft(jQuery(".tr_for_scroll").scrollLeft());
});	

jQuery('#from_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
jQuery('#to_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });

jQuery('#trn_te_code').change(function(){
		var trn_te_code = jQuery(this).val();
		if(trn_te_code!=''){
		var img = '<img src="images/ajax-loader.gif">';
				jQuery(".rpt_loader").html(img);
				jQuery.ajax({
				url: 'ajax_show_engineer_by_te_code.php',
				type: 'post',
				dataType: 'json',
				data: "trn_te_code="+trn_te_code,
				success: function(response){				
				if(response.process_sts=="YES"){					
					jQuery("#sl_en_id").html(response.en_options);
					jQuery(".rpt_loader").html("");		
				}else{
					jQuery("#sl_en_id").html('<option value="">Select Engineer</option>');
					jQuery(".rpt_loader").html(response.process_msg);
					setTimeout(function(){
					jQuery(".rpt_loader").html("");
					},3000);				
				}						
				}
				});
		}else{
			jQuery("#sl_en_id").html('<option value="">Select Engineer</option>');
			
		}
	
	});

jQuery("#sl_day_wise").change(function(){
		var sl_day_wise = jQuery(this).val();
		if(sl_day_wise==""){
			jQuery('#from_dt').hide();
			jQuery('#to_dt').hide();
			jQuery('#from_dt').val("");
			jQuery('#to_dt').val("");
		}else{
			if(sl_day_wise=="Date_Range"){
				jQuery('#from_dt').show();
				jQuery('#to_dt').show();
				jQuery('#from_dt').val("");
				jQuery('#to_dt').val("");
			}else{
				jQuery('#from_dt').hide();
				jQuery('#to_dt').hide();
				jQuery('#from_dt').val("");
				jQuery('#to_dt').val("");
			}
		}
	});

jQuery(".srch_btn").click(function(){
		var srch_eng_te_site_dtls = jQuery("#srch_eng_te_site_dtls").val();
		var sl_day_wise = jQuery("#sl_day_wise").val();
		var from_dt = jQuery("#from_dt").val();
		var to_dt = jQuery("#to_dt").val();
		var qstring ="";
		var dtstring ="";
		var amp = "";
		if(sl_day_wise!="" || srch_eng_te_site_dtls!=""){
		
		if(srch_eng_te_site_dtls!=""){
		if(qstring==""){
		qstring = qstring+"srch_eng_te_site_dtls="+srch_eng_te_site_dtls;
		}else{
		qstring = qstring+"&srch_eng_te_site_dtls="+srch_eng_te_site_dtls;  
		}
		}
		
		if(sl_day_wise!=""){
			if(sl_day_wise=="Date_Range"){
				if(from_dt!="" && to_dt!=""){
					dtstring ="&from_dt="+from_dt+"&to_dt="+to_dt;
				}else if(from_dt!="" && to_dt==""){
					dtstring ="&from_dt="+from_dt;
				}else if(from_dt=="" && to_dt!=""){
					dtstring ="&to_dt="+to_dt;
				}else{
					dtstring ="";
				}
			}else{
				dtstring ="";
			}
			
			if(qstring!=""){
				qstring = qstring+"&sl_day_wise="+sl_day_wise+dtstring;
			}else{
				qstring = qstring+"sl_day_wise="+sl_day_wise+dtstring;
			}
		}
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "<?php echo $page_name;?>"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "<?php echo $page_name;?>";
	});
	
});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>