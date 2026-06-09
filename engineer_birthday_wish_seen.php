<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include "star_connection.php";

$eid       = isset($_POST['eid']) ? trim($_POST['eid']) : '';
$user_type = isset($_POST['user_type']) ? trim($_POST['user_type']) : '';

if ($eid == '' || $user_type == '') {
    echo json_encode([
        "status" => false,
        "message" => "eid and user_type required"
    ]);
    exit;
}

$current_year = date("Y");

/* ===============================
   CHECK ALREADY EXISTS
=================================*/

$check_sql = "
    SELECT id 
    FROM birthday_wish_seen_log 
    WHERE customer_id = '$eid'
    AND user_type = '$user_type'
    AND seen_year = '$current_year'
";

$check_res = mysqli_query($conn, $check_sql);

if ($check_res && mysqli_num_rows($check_res) > 0) {

    echo json_encode([
        "status" => true,
        "message" => "Already marked as seen"
    ]);
    exit;
}

/* ===============================
   INSERT LOG
=================================*/
$date=date('Y-m-d');
 $insert_sql = "
    INSERT INTO birthday_wish_seen_log 
    (customer_id, user_type, seen_year, seen_date, created_at)
    VALUES
    ('$eid', '$user_type', '$current_year', '$date', NOW())
";

$insert_res = mysqli_query($conn, $insert_sql);

if ($insert_res) {

    echo json_encode([
        "status" => true,
        "message" => "Marked as seen successfully"
    ]);

} else {

    echo json_encode([
        "status" => false,
        "message" => "Insert failed"
    ]);
}
