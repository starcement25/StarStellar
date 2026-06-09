<?php
include "star_connection.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$gift_order_master = "gift_order_master";
$engineer_master = "engineer_master";

$the_engineer_id = isset($_POST["the_engineer_id"]) ? trim($_POST["the_engineer_id"]) : "";

if ($the_engineer_id == "") {
    echo json_encode([
        "status" => "NO",
        "message" => "Engineer ID missing."
    ]);
    exit;
}

// Step 1: Try to get last order details for this engineer
 $sql_last_order = "
    SELECT user_email, phone 
    FROM $gift_order_master 
    WHERE user_id = '$the_engineer_id' 
    ORDER BY g_order_id DESC 
    LIMIT 1
";

$res_last_order = mysqli_query($conn, $sql_last_order);

if ($res_last_order && mysqli_num_rows($res_last_order) > 0) {
    $row = mysqli_fetch_assoc($res_last_order);
    $user_email = $row["user_email"] ? trim($row["user_email"]) : "";
    $phone = $row["phone"] ? trim($row["phone"]) : "";

    echo json_encode([
        "status" => "YES",
        "source" => "gift_order_master",
        "user_email" => $user_email,
        "phone" => $phone
    ]);
} else {
    // Step 2: If no order found, get from engineer_master
    $sql_engineer = "
        SELECT e_email, e_mobile 
        FROM $engineer_master 
        WHERE eid = '$the_engineer_id' 
        LIMIT 1
    ";

    $res_engineer = mysqli_query($conn, $sql_engineer);
    if ($res_engineer && mysqli_num_rows($res_engineer) > 0) {
        $row2 = mysqli_fetch_assoc($res_engineer);
        $user_email = $row2["e_email"] ? trim($row2["e_email"]) : "";
        $phone = $row2["e_mobile"] ? trim($row2["e_mobile"]) : "";

        echo json_encode([
            "status" => "YES",
            "source" => "engineer_master",
            "user_email" => $user_email,
            "phone" => $phone
        ]);
    } else {
        echo json_encode([
            "status" => "NO",
            "message" => "Engineer not found."
        ]);
    }
}

mysqli_close($conn);
?>
