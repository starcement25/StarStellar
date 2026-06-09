<?php
header("Content-Type: application/json");
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include "star_connection.php";

$eid       = isset($_GET['eid']) ? trim($_GET['eid']) : '';
$user_type = isset($_GET['user_type']) ? trim($_GET['user_type']) : '';

//20-02-2026 birthday features disable
echo json_encode([
        "status" => false,
        "message" => "Birthday disable"
    ]);
    exit;

if ($eid == '' || $user_type == '') {
    echo json_encode([
        "status" => false,
        "message" => "Engineer/TE ID and user_type is required"
    ]);
    exit;
}

/* ===============================
   GET USER DATA BASED ON TYPE
=================================*/

if ($user_type == 'ENGINEER') {

    $sql = "SELECT eid, e_dob, e_name 
            FROM engineer_master 
            WHERE eid = '$eid' 
            LIMIT 1";

    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        echo json_encode([
            "status" => false,
            "message" => "Engineer not found"
        ]);
        exit;
    }

    $row      = mysqli_fetch_assoc($result);
    $dob_raw  = $row['e_dob'];
    $name     = $row['e_name'];

} elseif ($user_type == 'TE') {

    $sql = "SELECT te_id, te_dob, te_name 
            FROM te_master 
            WHERE te_id = '$eid' 
            LIMIT 1";

    $result = mysqli_query($conn, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        echo json_encode([
            "status" => false,
            "message" => "TE not found"
        ]);
        exit;
    }

    $row      = mysqli_fetch_assoc($result);
    $dob_raw  = $row['te_dob'];
    $name     = $row['te_name'];

} else {

    echo json_encode([
        "status" => false,
        "message" => "Invalid user_type"
    ]);
    exit;
}

/* ===============================
   VALIDATE DOB
=================================*/
if ($dob_raw == '' || $dob_raw == '00000000' || strlen($dob_raw) != 10) {
    echo json_encode([
        "status" => false,
        "message" => "Birthday Not Found"
    ]);
    exit;
}

/* ===============================
   CHECK TODAY BIRTHDAY
=================================*/
$today_md     = date("md");
$engineer_md  = date("md", strtotime($dob_raw));

if ($today_md != $engineer_md) {
    echo json_encode([
        "status" => false,
        "message" => "Today is not birthday"
    ]);
    exit;
}

/* ===============================
   CHECK SEEN STATUS
=================================*/
$current_year = date("Y");

$seen_check = "
    SELECT id 
    FROM birthday_wish_seen_log 
    WHERE customer_id = '$eid'
    AND user_type = '$user_type'
    AND seen_year = '$current_year'
";

$seen_res = mysqli_query($conn, $seen_check);

if ($seen_res && mysqli_num_rows($seen_res) > 0) {
    echo json_encode([
        "status" => false,
        "message" => "Birthday already shown"
    ]);
    exit;
}

/* ===============================
   GET BIRTHDAY CONTENT
=================================*/
$content_sql = "
    SELECT type, title, message, img 
    FROM birthday_master 
    WHERE status = 1 
    ORDER BY id DESC 
    LIMIT 1
";

$content_res = mysqli_query($conn, $content_sql);
$content     = mysqli_fetch_assoc($content_res);

/* ===============================
   FORMAT DOB
=================================*/
$dob_formatted = date("d-m-Y", strtotime($dob_raw));

$message = str_replace(
    "{{customer_name}}",
    $name,
    $content['message']
);

echo json_encode([
    "status" => true,
    "eid" => $eid,
    "user_type" => $user_type,
    "engineer_name" => $name,
    "type" => $content['type'],
    "title" => $content['title'],
    "message" => $message,
    "img" => $content['img'],
    "dob" => $dob_formatted
]);
