<?php
include "star_connection.php";
$product_master = "product_master";
$res = array();
$sel_prod_id_bonus = isset($_POST["sel_prod_id_bonus"]) ? addslashes(trim($_POST["sel_prod_id_bonus"])) : "";
$bonus_points = 0;

if ($sel_prod_id_bonus != "") {
    $sqlckrds = "SELECT `prod_id`, `bonus_points` FROM $product_master WHERE `prod_id`='$sel_prod_id_bonus'";
    $resckrds = mysqli_query($conn, $sqlckrds);
    $totresckrds = mysqli_num_rows($resckrds);

    if ($totresckrds > 0) {
        $rowckrds = mysqli_fetch_assoc($resckrds);
        $bonus_points = $rowckrds["bonus_points"] !== null ? trim($rowckrds["bonus_points"]) : 0;

        $res = array("process_sts" => "YES", "process_message" => "Success", "bonus_points" => $bonus_points);
    } else {
        $res = array("process_sts" => "NO", "process_message" => "Product not found.", "bonus_points" => $bonus_points);
    }
} else {
    $res = array("process_sts" => "NO", "process_message" => "Something went wrong.", "bonus_points" => $bonus_points);
}

mysqli_close($conn);
echo json_encode($res);
?>
