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
startCreatTEMasterCsvfile($conn);

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
function startCreatTEMasterCsvfile($conn){
$te_master = "te_master";
$branch_master = "branch_master";
$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "te_master_".$curr_date.".csv";
$output = "";
/*if($data_show_type=='NE'){
$qry = "select * from $te_master where (`zone` like '%A%' or `zone` like '%B%' or `zone` like '%C%' ) order by `te_name` asc";
}else if($data_show_type=='OSNE'){
$qry = "select * from $te_master where (`zone` like '%D%' or `zone` like '%E%' ) order by `te_name` asc";
}else{
$qry = "select * from $te_master order by `te_name` asc";
}*/

if($data_show_type=="ALL"){
$qry = "select * from $te_master order by `te_name` asc";
}else{
	$search_array_val_parts=explode(",",$data_show_type);
				$condition_one=" AND (";
				$condition_two='';
				foreach($search_array_val_parts as $search_array_val_parts_val)
				{
					$condition_two.=" FIND_IN_SET( '".$search_array_val_parts_val."',zone) OR";
				}
				$condition_two=substr($condition_two,0,-2);
				$condition_one.=$condition_two.")";
//$qry = "select * from $te_master where `zone` like '%".$data_show_type."%' order by `te_name` asc";
	$qry = "select * from $te_master where 1  $condition_one order by `te_name` asc";


}


$sql = mysqli_query($conn,$qry);

$output .= '"TE Code/Employee Code","TE Name/Employee Name","Branch Name","Reporting To","Mobile","Email","Designation","HQ","State","Zone","Acedns","Device type","App Version"';

$output .="\n";
// Get Records from the table

while ($row1 = mysqli_fetch_assoc($sql)) {
$the_te_name = $row1["te_name"] ? str_replace('"', '""', trim($row1["te_name"])) : "";
$the_te_mobile_no = $row1["te_mobile_no"];
$the_te_code = $row1["te_code"];
$the_te_email = $row1["te_email"];
$the_branch_name_selected = "";
$the_branch_code_selected = $row1["branch_code"] ? trim($row1["branch_code"]) : "";
if($the_branch_code_selected!=""){
$the_branch_name_selected = get_branch_names_by_ids($conn,$the_branch_code_selected);
}else{
$the_branch_name_selected = "";	
}

$the_te_reporting_to = $row1["reporting_to"] ? str_replace('"', '""', trim($row1["reporting_to"])) : "";
$the_te_designation = $row1["designation"] ? str_replace('"', '""', trim($row1["designation"])) : "";
$the_te_hq = $row1["hq"] ? str_replace('"', '""', trim($row1["hq"])) : "";
$the_te_state = $row1["state"] ? str_replace('"', '""', trim($row1["state"])) : "";
$the_te_zone = $row1["zone"] ? str_replace('"', '""', trim($row1["zone"])) : "";
$the_te_acedns = $row1["acedns"] ? $row1["acedns"] : "N";
$the_device_type = $row1["device_type"] ? trim($row1["device_type"]) : "";
$the_app_version = $row1["app_version"] ? trim($row1["app_version"]) : "";

$output .= '"'.$the_te_code.'","'.$the_te_name.'","'.$the_branch_name_selected.'","'.$the_te_reporting_to.'","'.$the_te_mobile_no.'","'.$the_te_email.'","'.$the_te_designation.'","'.$the_te_hq.'","'.$the_te_state.'","'.$the_te_zone.'","'.$the_te_acedns.'","'.$the_device_type.'","'.$the_app_version.'"';
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