<?php
include "star_connection.php";
$product_master = "product_master";
$res = array();

// Define an array with product IDs and their fixed bonus points
$productFixedBonusPoints = array(
    "STAR PPC" => 1000,
    "STAR ANTI RUST CEMENT" => 1250,
    "STAR WEATHER SHIELD" => 1250
);

foreach ($productFixedBonusPoints as $productName => $fixedBonusPoints) {
    // Use prepared statement to avoid SQL injection
    $sqlUpdate = "UPDATE $product_master SET `bonus_points` = ? WHERE `prod_name` = ?";
    $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);

    // Bind parameters for the update statement
    mysqli_stmt_bind_param($stmtUpdate, "is", $fixedBonusPoints, $productName);

    // Execute the update statement
    mysqli_stmt_execute($stmtUpdate);

    // Close the statement
    mysqli_stmt_close($stmtUpdate);
}

$res = array("process_sts" => "YES", "process_message" => "Success");

// Close the connection
mysqli_close($conn);
echo json_encode($res);
?>
