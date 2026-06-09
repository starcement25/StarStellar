<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$get_fetch_type = $_GET["get_type"] ? trim($_GET["get_type"]) : "";
if($get_fetch_type!=""){
	if($get_fetch_type=="notloggedin" || $get_fetch_type=="loggedin"){
		startCreatDealerCsvfile($conn,$get_fetch_type);
	}else{
		exit;
	}
}else{
	exit;
}

function startCreatDealerCsvfile($conn,$get_fetch_type){
$employee_master = "employee_master";
$get_fetch_type = $get_fetch_type ? strtolower(trim($get_fetch_type)) : "";
$curr_date = date("jS_M_Y_h_m_s_A");
if($get_fetch_type=="notloggedin"){
	$the_file_name = "notloggedin_dealer_".$curr_date.".csv";
	$where_qry = " where `sms_otp`='' and `acedns`='Y' ";
}else if($get_fetch_type=="loggedin"){
	$the_file_name = "loggedin_dealer_".$curr_date.".csv";
	$where_qry = " where `sms_otp`!='' and `acedns`='Y' ";
}else{
	$the_file_name = "all_dealer_".$curr_date.".csv";
	$where_qry = "";
}
$output = "";
$qry = "select `dns_emp_code`,`emp_name`,`acedns`,`phone_no` from $employee_master $where_qry order by `emp_code` asc";
$sql = mysqli_query($conn,$qry);
$columns_total = mysqli_num_fields($sql);

// Get The Field Name

for ($i = 0; $i < $columns_total; $i++) {
$heading = mysqli_field_name($sql, $i);
$output .= '"'.$heading.'",';
}
$output .="\n";
// Get Records from the table

while ($row = mysqli_fetch_array($sql)) {
for ($i = 0; $i < $columns_total; $i++) {
$output .='"'.$row["$i"].'",';
}
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