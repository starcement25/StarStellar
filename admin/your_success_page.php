<?php
include "star_connection.php";

$giftMasterTable = "gift_master";
$responseMessage = array();

$giftId = isset($_POST["gft_id"]) ? trim($_POST["gft_id"]) : "";
$redeemStatus = isset($_POST["redeemStatus"]) ? trim($_POST["redeemStatus"]) : "";

if (!empty($giftId) && !empty($redeemStatus)) {
    $updateQuery = "UPDATE $giftMasterTable SET `redeem`=? WHERE `id`=?";
    $statement = mysqli_prepare($conn, $updateQuery);

    if ($statement) {
        mysqli_stmt_bind_param($statement, "ss", $redeemStatus, $giftId);
        $success = mysqli_stmt_execute($statement);

        if ($success) {
            $responseMessage = array("process_sts" => "YES", "process_msg" => "Success");
        } else {
            $responseMessage = array("process_sts" => "NO", "process_msg" => "Failed to update the redeem status.");
        }

        mysqli_stmt_close($statement);
    } else {
        $responseMessage = array("process_sts" => "NO", "process_msg" => "Failed to prepare SQL statement.");
    }
} else {
    $responseMessage = array("process_sts" => "NO", "process_msg" => "Invalid or missing parameters.");
}

echo json_encode($responseMessage);
mysqli_close($conn);
?>
