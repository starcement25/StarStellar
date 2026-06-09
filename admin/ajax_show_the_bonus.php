<?php
include "star_connection.php";
$product_master = "product_master";
$res = array();

$sel_prod_id1 = $_POST["sel_prod_id1"] ? addslashes(trim($_POST["sel_prod_id1"])) : "";

if ($sel_prod_id1 != "") {
    $sqlckrds = "SELECT `prod_id`, `bonus_points` FROM $product_master WHERE `prod_id`='$sel_prod_id1'";
    $resckrds = mysqli_query($conn, $sqlckrds);

    if ($resckrds) {
        $rowckrds = mysqli_fetch_assoc($resckrds);

        $res = array(
            "process_sts" => "YES",
            "process_message" => "Success",
            "bonus_points" => $rowckrds["bonus_points"] ?? 0
        );
    } else {
        $res = array("process_sts" => "NO", "process_message" => "Error fetching product details.");
    }
} else {
    $res = array("process_sts" => "NO", "process_message" => "Invalid product ID.");
}

mysqli_close($conn);
echo json_encode($res);
?>