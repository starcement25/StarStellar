<?php
include "star_connection.php"; 
$order_query = "order_query";

$response = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";

$order_query_id = $_REQUEST["order_query_id"] ? addslashes(trim($_REQUEST["order_query_id"])) : "";
$status_from_app = $_REQUEST["status_from_app"] ? addslashes(trim($_REQUEST["status_from_app"])) :"";
$status_remarks = $_REQUEST["status_remarks"] ? addslashes(trim($_REQUEST["status_remarks"])) : "";



if ($order_query_id == "") {
    $response["process_status"] = "NO";
    $response["process_message"] = "Order query id is missing.";
} else {
    $sqlin = "UPDATE $order_query SET status_from_app='".$status_from_app."', status_remarks='".$status_remarks."' where id='".$order_query_id."'";

    $resin = mysqli_query($conn,$sqlin);
    if (!$resin) {
        $response["process_status"] = "ERROR";
        $response["process_message"] = "Error: " . mysql_error();
    }else{
        $response["process_status"] = "YES";
        $response["process_message"] = "Order query Status Updated.";
    }

    
}

echo json_encode($response);
?>
