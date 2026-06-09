<?php
include "star_connection.php"; // your DB connection
$acknowledged_module_open_days = get_value_by_setting_key($conn, "acknowledged_module_open_days");
if ($acknowledged_module_open_days == "") {
	$acknowledged_module_open_days = 0;
}

$the_engineer_id = $_POST['engineer_id']; // or however you get the user ID

// Fetch the last delivered order within 3 days
  $sql2 = "SELECT * FROM gift_order_master 
         WHERE user_id = '$the_engineer_id' 
           AND delivery_date >= DATE_SUB(NOW(), INTERVAL '$acknowledged_module_open_days' DAY) AND status='DELIVERED'
         ORDER BY delivery_date DESC
         LIMIT 1";

$result = mysqli_query($conn, $sql2);

if(mysqli_num_rows($result) > 0){
    $order = mysqli_fetch_assoc($result);
    $order_id = $order['order_id'];
    $delivery_date = date('d-M-Y', strtotime($order['delivery_date']));

    // Instruction text for Acknowledgement
    $ack_text = "Your order ($order_id) has been delivered on $delivery_date. Please acknowledge receipt to confirm delivery.";

    echo json_encode([
        "status" => "success",
        "message" => $ack_text,
        "order" => $order
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No delivered orders found in the last 3 days."
    ]);
}
?>
