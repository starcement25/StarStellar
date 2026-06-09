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
$pnms = "";
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
startCreatEngineerMasterCsvfile($conn);


function startCreatEngineerMasterCsvfile($conn){
$server_url = "https://" . $_SERVER['SERVER_NAME']."/";
$img_dir = "../en_profile_pic/";
$default_image_name = "profile.png";
$image_url_prefix = $server_url."en_profile_pic/";
$date_before_three_month = date('Y-m-d H:i:s',strtotime("-3 month"));
$date_before_six_month = date('Y-m-d H:i:s',strtotime("-6 month"));
$date_before_three_month_stamp = strtotime($date_before_three_month);
$date_before_six_month_stamp = strtotime($date_before_six_month);
$recommended_site_master = "recommended_site_master";
$engineer_master = "engineer_master";
$te_master = "te_master";
$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "engineer_master_".$curr_date.".csv";
$output = "";
/*if($data_show_type=="NE"){
$qry = "select $engineer_master.*,$te_master.`zone`,$te_master.`te_name` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where ($te_master.`zone` like '%A%' or $te_master.`zone` like '%B%' or $te_master.`zone` like '%C%' )";
}else if($data_show_type=="OSNE"){
$qry = "select $engineer_master.*,$te_master.`zone`,$te_master.`te_name` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where ($te_master.`zone` like '%D%' or $te_master.`zone` like '%E%' )";
}else{
$qry = "select *,$te_master.`te_name` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` order by `e_name` asc";
}*/

if($data_show_type=="ALL"){
$qry = "select *,$te_master.`te_name` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` order by `e_name` asc";
}else{
$qry = "select $engineer_master.*,$te_master.`zone`,$te_master.`te_name` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where $te_master.`zone` like '%".$data_show_type."%' ";
}


$sql = mysqli_query($conn,$qry);

$output .= '"Name","Mobile","Email","Point","Branch","Birthday","Anniversary","LINKED TE Code","LINKED TE Name","Address","Pin","State","City","App Access Status","Status By TE","Reg Datetime"';

$output .="\n";
// Get Records from the table

while ($row1 = mysqli_fetch_assoc($sql)) {
$the_eid = $row1["eid"];
$the_e_name = $row1["e_name"] ? str_replace('"', '""', trim($row1["e_name"])) : "";
$the_e_mobile = $row1["e_mobile"];
$the_te_code = $row1["te_code"];
$the_te_name = $row1["te_name"] ? str_replace('"', '""', trim($row1["te_name"])) : "";
$the_e_email = $row1["e_email"];
$the_e_dob = $row1["e_dob"] ? trim($row1["e_dob"]) : "";
if($the_e_dob!=""){
$the_e_dob = date("Y-m-d",strtotime($the_e_dob));	
}
$the_e_dom = $row1["e_dom"] ? trim($row1["e_dom"]) : "";
if($the_e_dom!=""){
$the_e_dom = date("Y-m-d",strtotime($the_e_dom));	
}
$the_e_address = $row1["e_address"] ? str_replace('"', '""', trim($row1["e_address"])) : "";
$the_e_pin = $row1["e_pin"];
$the_e_state = $row1["e_state"];
$the_e_city_town = $row1["e_city_town"];

$the_e_status = $row1["status"] ? trim($row1["status"]) : "";
$the_status_by_te = $row1["status_by_te"] ? trim($row1["status_by_te"]) : "";

$the_e_points = $row1["e_points"] ? trim($row1["e_points"]) : "";
$the_branch_code_selected = $row1["branch_code"] ? trim($row1["branch_code"]) : "";
if($the_branch_code_selected!=""){
$the_branch_code_selected = get_branch_names_by_ids($conn,$the_branch_code_selected);
}else{
$the_branch_code_selected = "";	
}
$the_reg_date_time = $row1["reg_date"] ? trim($row1["reg_date"]) : "";

$output .= '"'.$the_e_name.'","'.$the_e_mobile.'","'.$the_e_email.'","'.$the_e_points.'","'.$the_branch_code_selected.'","'.$the_e_dob.'","'.$the_e_dom.'","'.$the_te_code.'","'.$the_te_name.'","'.$the_e_address.'","'.$the_e_pin.'","'.$the_e_state.'","'.$the_e_city_town.'","'.$the_e_status.'","'.$the_status_by_te.'","'.$the_reg_date_time.'"';
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