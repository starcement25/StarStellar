<?php
include "star_connection.php";
$product_master = "product_master";
$res = array();

$sel_prod_id_bonus = isset($_POST["sel_prod_id_bonus"]) ? mysqli_real_escape_string($conn, trim($_POST["sel_prod_id_bonus"])) : "";
$bonus_points_input = isset($_POST["bonus_points_input"]) ? mysqli_real_escape_string($conn, trim($_POST["bonus_points_input"])) : "";

if ($sel_prod_id_bonus != "") {
    $sqlckrds = "SELECT `prod_id` FROM $product_master WHERE `prod_id`='$sel_prod_id_bonus'";
    $resckrds = mysqli_query($conn, $sqlckrds);

    if ($resckrds !== false) {
        $totresckrds = mysqli_num_rows($resckrds);

        if ($totresckrds > 0) {
            $sqlupd = "UPDATE $product_master SET `bonus_points`='$bonus_points_input' WHERE `prod_id`='$sel_prod_id_bonus'";
            $resupd = mysqli_query($conn, $sqlupd);

            if ($resupd !== false) {
                $res = array("process_sts" => "YES", "process_message" => "Success");
            } else {
                $res = array("process_sts" => "NO", "process_message" => "Error updating bonus_points column: " . mysqli_error($conn));
            }
        } else {
            $res = array("process_sts" => "NO", "process_message" => "Product not found.");
        }
    } else {
        $res = array("process_sts" => "NO", "process_message" => "Error checking product details: " . mysqli_error($conn));
    }
} else {
    $res = array("process_sts" => "NO", "process_message" => "Invalid product ID.");
}

mysqli_close($conn);
echo json_encode($res);
?>