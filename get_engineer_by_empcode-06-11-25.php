<?php
header('Content-Type: text/plain');
include "star_connection.php"; // your DB connection file

if (!isset($_REQUEST['emp_code']) || trim($_REQUEST['emp_code']) == '') {
    echo "0¥0\nInvalid emp_code parameter";
    exit;
}

$emp_code = mysqli_real_escape_string($conn, trim($_REQUEST['emp_code']));

// Check if emp_code exists in te_master
$sql1 = "SELECT te_code FROM te_master WHERE te_code = '$emp_code'";
$res1 = mysqli_query($conn, $sql1);
$tot_res1 = mysqli_num_rows($res1);

if ($tot_res1 == 0) {
    echo "0¥0\nNo matching record found in te_master for emp_code: $emp_code";
    exit;
}

// Get engineer details
$sql2 = "SELECT eid, e_name, e_mobile FROM engineer_master WHERE te_code = '$emp_code'";
$res2 = mysqli_query($conn, $sql2);
$tot_res2 = mysqli_num_rows($res2);

if ($tot_res2 > 0) {
    echo   $tot_res2 ."¥3". "\n";
    while ($row = mysqli_fetch_assoc($res2)) {
        echo $row['eid'] . "^" . $row['e_name'] . "^" . $row['e_mobile'] . "\n";
    }
} else {
    echo "0¥0\nNo engineer records found for emp_code: $emp_code";
}
?>
