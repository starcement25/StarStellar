<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
startCreatBranchMasterCsvfile($conn);


function startCreatBranchMasterCsvfile($conn){
$branch_master = "branch_master";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "branch_master_".$curr_date.".csv";
$output = "";
$qry = "select `branch_code`,`branch_name`,`zone`,`branch_state` from $branch_master order by `branch_code` asc";
$sql = mysqli_query($conn,$qry);
$columns_total = mysqli_num_fields($sql);

$output .= '"branch_code","branch_name","zone","branch_state"';
$output .="\n";
// Get Records from the table

while ($row = mysqli_fetch_array($sql)) {
$branch_code = $row["branch_code"];
$branch_name = $row["branch_name"];
$zone = $row["zone"];
$branch_state = $row["branch_state"];
$output .='"'.$branch_code.'","'.$branch_name.'","'.$zone.'","'.$branch_state.'"';

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