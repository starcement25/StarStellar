<?php
include "star_connection.php";
$gift_master = "gift_master";
$res_msg = array();
$gft_id = $_POST["gft_id"] ? trim($_POST["gft_id"]) : "";
$redeem_sts = $_POST["redeem_sts"] ? trim($_POST["redeem_sts"]) : "";

if ($gft_id != '' && $redeem_sts != '') {
    $sql5 = "UPDATE $gift_master SET `redeem`=? WHERE `id`=?";
    $stmt = mysqli_prepare($conn, $sql5);
    mysqli_stmt_bind_param($stmt, "ss", $redeem_sts, $gft_id);
    mysqli_stmt_execute($stmt);

    $res_msg = array("process_sts" => "YES", "process_msg" => "Success");
} else {
    $res_msg = array("process_sts" => "NO", "process_msg" => "Something went wrong.");
}

echo json_encode($res_msg);
mysqli_close($conn);
?>
