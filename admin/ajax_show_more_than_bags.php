<?php
include "star_connection.php";
$product_master = "product_master";
$res = array();
$sel_prod_id1 = isset($_POST["sel_prod_id1"]) ? addslashes(trim($_POST["sel_prod_id1"])) : "";
$more_than_bags = 0;

if ($sel_prod_id1 != "") {
    $sqlckrds = "SELECT `prod_id`, `more_than_bags` FROM $product_master WHERE `prod_id`='$sel_prod_id1'";
    $resckrds = mysqli_query($conn, $sqlckrds);
    $totresckrds = mysqli_num_rows($resckrds);

    if ($totresckrds > 0) {
        $rowckrds = mysqli_fetch_assoc($resckrds);
        $more_than_bags = $rowckrds["more_than_bags"] !== null ? trim($rowckrds["more_than_bags"]) : 0;

        $res = array("process_sts" => "YES", "process_message" => "Success", "more_than_bags" => $more_than_bags);
    } else {
        $res = array("process_sts" => "NO", "process_message" => "Product not found.", "more_than_bags" => $more_than_bags);
    }
} else {
    $res = array("process_sts" => "NO", "process_message" => "Something went wrong.", "more_than_bags" => $more_than_bags);
}

mysqli_close($conn);
echo json_encode($res);
?>
