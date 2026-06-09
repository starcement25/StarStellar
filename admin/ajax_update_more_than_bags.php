<?php
include "star_connection.php";
$product_master = "product_master";
$res = array();
$sel_prod_id1 = isset($_POST["sel_prod_id1"]) ? addslashes(trim($_POST["sel_prod_id1"])) : "";
$more_than_bags_input = isset($_POST["more_than_bags_input"]) ? addslashes(trim($_POST["more_than_bags_input"])) : "";

if ($sel_prod_id1 != "") {
    $sqlckrds = "SELECT `prod_id` FROM $product_master WHERE `prod_id`='$sel_prod_id1'";
    $resckrds = mysqli_query($conn, $sqlckrds);
    $totresckrds = mysqli_num_rows($resckrds);

    if ($totresckrds > 0) {
        $sqlupd = "UPDATE $product_master SET `more_than_bags`='$more_than_bags_input' WHERE `prod_id`='$sel_prod_id1'";
        $resupd = mysqli_query($conn, $sqlupd);

        if ($resupd) {
            $res = array("process_sts" => "YES", "process_message" => "Success");
        } else {
            $res = array("process_sts" => "NO", "process_message" => "Error updating more_than_bags column: " . mysqli_error($conn));
        }
    } else {
        $res = array("process_sts" => "NO", "process_message" => "Product not found.");
    }
} else {
    $res = array("process_sts" => "NO", "process_message" => "Something went wrong.");
}

mysqli_close($conn);
echo json_encode($res);
?>
