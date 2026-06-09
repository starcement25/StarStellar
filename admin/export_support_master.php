<?php
include "web_check.php";
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}
startCreatSupportMasterCsvfile($conn);

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
function startCreatSupportMasterCsvfile($conn){
$support_master = "support_master";
$engineer_master = "engineer_master";
$te_master = "te_master";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "support_master_".$curr_date.".csv";
$output = "";
$sql1 = "select $support_master.*,$support_master.`status` as `s_status`,$engineer_master.* from $support_master left join $engineer_master on $support_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` $new_whr_str order by $support_master.`submitted_datetime` desc";
$res1 = mysqli_query($conn,$sql1);

$output .= '"Order ID","Engineer Code","Engineer Name","Engineer Mobile","Engineer Email","Type","Comment","Status","Date Time"';

$output .="\n";
// Get Records from the table
$totres1 = mysqli_num_rows($res1);
	while($row1=mysqli_fetch_assoc($res1)){
		$the_sid = $row1["sid"];
		$the_order_id = $row1["order_id"];
		$the_e_name = $row1["e_name"];
		$the_e_mobile = $row1["e_mobile"];
		$the_te_code = $row1["te_code"];
		$the_branch_code_selected = $row1["branch_code"];
		$the_e_email = $row1["e_email"];
		$the_s_type = $row1["s_type"];
		$the_s_comment = $row1["s_comment"];
		$the_s_status = $row1["s_status"] ? trim($row1["s_status"]) : "";
		$the_s_submitted_datetime = $row1["submitted_datetime"] ? trim($row1["submitted_datetime"]) : "";
		if($the_s_submitted_datetime!=""){
		$the_s_submitted_datetime = date("dS M, Y",strtotime($the_s_submitted_datetime));
		}

	$output .= '"'.$the_order_id.'","'.$the_te_code.'","'.$the_e_name.'","'.$the_e_mobile.'","'.$the_e_email.'","'.$the_s_type.'","'.$the_s_comment.'","'.$the_s_status.'","'.$the_s_submitted_datetime.'"';
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