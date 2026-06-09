<?php
include "web_check.php";
ini_set('memory_limit', '999M');
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

if($data_show_type=="ALL"){
	
	$new_whr_str = " where $ledger_master.`ldgr_datetime` >= '$date_of_correction_in_point_calculation_as_per_new_SOP' ";
$qry = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` $new_whr_str order by $ledger_master.`ldgr_id` asc";

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
$qry = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where $where_clause order by $ledger_master.`ldgr_id` asc";

// $qry = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where $te_master.`zone` like '%".$data_show_type."%' order by $ledger_master.`ldgr_id` asc";
}


$sql = mysqli_query($conn,$qry);

$output .= '"Engineer Name","Engineer Phn No","Branch","TE CODE","TE Name","TE Mobile","Description","Point Earned","Point Redeem","Date"';

$output .="\n";
// Get Records from the table

while ($row1 = mysqli_fetch_assoc($sql)) {
$r_te_code = $row1["te_code"] ? trim($row1["te_code"]) : "";
$r_engineer_id = $row1["user_id"] ? trim($row1["user_id"]) : "";
$r_engineer_name = $row1["e_name"] ? trim($row1["e_name"]) : "";
$r_engineer_mobile = $row1["e_mobile"] ? trim($row1["e_mobile"]) : "";
$r_te_name = $row1["te_name"] ? trim($row1["te_name"]) : "";
$r_te_mobile_no = $row1["te_mobile_no"] ? trim($row1["te_mobile_no"]) : "";
$r_description = $row1["description"] ? str_replace('"', '""', trim($row1["description"])) : "";
$r_point_earned = $row1["point_earned"] ? trim($row1["point_earned"]) : "";
$r_point_redeem = $row1["point_redeem"] ? trim($row1["point_redeem"]) : "";
$the_branch_code_selected = $row1["branch_code"];
if($the_branch_code_selected!=""){
$the_branch_code_selected = get_branch_names_by_ids($conn,$the_branch_code_selected);
}else{
$the_branch_code_selected = "";	
}
$ldgr_datetime = $row1["ldgr_datetime"] ? trim($row1["ldgr_datetime"]) : "";

if($ldgr_datetime!=""){
	$ldgr_datetime = date("d-m-Y H:i:s",strtotime($ldgr_datetime));
}

$output .= '"'.$r_engineer_name.'","'.$r_engineer_mobile.'","'.$the_branch_code_selected.'","'.$r_te_code.'","'.$r_te_name.'","'.$r_te_mobile_no.'","'.$r_description.'","'.$r_point_earned.'","'.$r_point_redeem.'","'.$ldgr_datetime.'"';
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