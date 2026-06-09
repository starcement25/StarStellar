<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include "star_connection.php";

$eid       = isset($_POST['eid']) ? trim($_POST['eid']) : '';
$user_type = isset($_POST['user_type']) ? trim($_POST['user_type']) : '';
$dob       = isset($_POST['dob']) ? trim($_POST['dob']) : '';

if ($eid == '' || $user_type == '' || $dob == '') {
    echo json_encode([
        "status" => false,
        "message" => "eid, user_type and dob are required"
    ]);
    exit;
}

/* ===============================
   UPDATE BASED ON USER TYPE
=================================*/

if ($user_type == 'ENGINEER') {

    $update_sql = "
        UPDATE engineer_master 
        SET e_dob = '$dob' 
        WHERE eid = '$eid'
    ";

} elseif ($user_type == 'TE') {

    $update_sql = "
        UPDATE te_master 
        SET te_dob = '$dob' 
        WHERE te_id = '$eid'
    ";

} else {

    echo json_encode([
        "status" => false,
        "message" => "Invalid user_type"
    ]);
    exit;
}

$result = mysqli_query($conn, $update_sql);

if ($result) {
    echo json_encode([
        "status" => true,
        "message" => "DOB updated successfully",
        "user_type" => $user_type
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Update failed"
    ]);
}
