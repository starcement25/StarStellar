<?php
include "star_connection.php";
$gift_master = "gift_master";
$res_msg = array();
$gft_id = $_POST["gft_id"] ? trim($_POST["gft_id"]) : "";
$redeem_sts = $_POST["redeem_sts"] ? trim($_POST["redeem_sts"]) : "";
if($gft_id!='' && $redeem_sts!=''){ // Change $eng_id to $gft_id
    $sql5 = "update $gift_master set `redeem`='$redeem_sts' where `id`='$gft_id'";
    $res5 = mysqli_query($conn, $sql5);
    $res_msg = array("process_sts" => "YES", "process_msg" => "Success");
} else {
    $res_msg = array("process_sts" => "NO", "process_msg" => "Something went wrong.");
}
echo json_encode($res_msg);
mysqli_close($conn);
?>
