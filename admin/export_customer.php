<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$get_fetch_type = $_GET["get_type"] ? trim($_GET["get_type"]) : "all";
if($get_fetch_type!=""){
	if($get_fetch_type=="dealer" || $get_fetch_type=="subdealer" || $get_fetch_type=="all"){
		startCreatCustomerCsvfile($conn,$get_fetch_type);
	}else{
		exit;
	}
}else{
	exit;
}

function startCreatCustomerCsvfile($conn,$get_fetch_type){
$customer_master = "customer_master";
$get_fetch_type = $get_fetch_type ? strtolower(trim($get_fetch_type)) : "";
$curr_date = date("jS_M_Y_h_m_s_A");
if($get_fetch_type=="dealer"){
	$the_file_name = "dealer_customer_".$curr_date.".csv";
	$where_qry = " where `cust_type`='Dealer' and `acedns`='Y' ";
}else if($get_fetch_type=="subdealer"){
	$the_file_name = "subdealer_customer_".$curr_date.".csv";
	$where_qry = " where `cust_type`='Sub Dealer' and `acedns`='Y' ";
}else{
	$the_file_name = "all_customer_".$curr_date.".csv";
	$where_qry = " where (`cust_type`='Sub Dealer' or `cust_type`='Dealer') and `acedns`='Y' ";
}
$output = "";
$qry = "select `customer_code`,`dns_customer_code`,`customer_name`,`address`,`phone_no`,`route_code`,`acedns`,`black_list`,`cust_type`,`rds_tag` from $customer_master $where_qry order by `customer_code` asc";
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