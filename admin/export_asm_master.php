<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
startCreatAsmMasterCsvfile($conn);


function startCreatAsmMasterCsvfile($conn){
$asm_master = "asm_master";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "asm_master_".$curr_date.".csv";
$output = "";
$qry = "select `branch_code`,`branch`,`asm_name`,`ph_no`,`email` from $asm_master order by `branch_code` asc";
$sql = mysqli_query($conn,$qry);
$columns_total = mysqli_num_fields($sql);

// Get The Field Name

$output .= '"branch_code","branch","asm_name","ph_no","email"';
$output .="\n";
// Get Records from the table

while ($row = mysqli_fetch_array($sql)) {
$branch_code = $row["branch_code"];
$branch = $row["branch"];
$asm_name = $row["asm_name"];
$ph_no = $row["ph_no"];
$email = $row["email"];
$output .='"'.$branch_code.'","'.$branch.'","'.$asm_name.'","'.$ph_no.'","'.$email.'"';

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