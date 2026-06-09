<?php
include "star_connection.php";
$product_master = "product_master";
$res = array();

$sel_prod_id1 = $_POST["sel_prod_id1"] ? addslashes(trim($_POST["sel_prod_id1"])) : "";
$more_than_bags = $_POST["more_than_bags"] ? addslashes(trim($_POST["more_than_bags"])) : "";

if ($sel_prod_id1 != "") {
    // Fetch bags quantity for the selected product
    $sqlFetchBagsQuantity = "SELECT `bags_quantity` FROM $product_master WHERE `prod_id`='$sel_prod_id1'";
    $resultBagsQuantity = mysqli_query($conn, $sqlFetchBagsQuantity);
    $rowBagsQuantity = mysqli_fetch_assoc($resultBagsQuantity);
    $bagsQuantity = $rowBagsQuantity['bags_quantity'];

    // Define more than bags based on bags quantity
    $moreThanBags = ($bagsQuantity > 100) ? 1 : 0;

    $sqlUpdate = "UPDATE $product_master SET `more_than_bags`='$moreThanBags' WHERE `prod_id`='$sel_prod_id1'";
    $resUpdate = mysqli_query($conn, $sqlUpdate);

    $res = array("process_sts" => "YES", "process_message" => "Success");
} else {
    $res = array("process_sts" => "NO", "process_message" => "Something went wrong.");
}

mysqli_close($conn);
echo json_encode($res);
?>
