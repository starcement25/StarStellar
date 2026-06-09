<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "star_connection.php";

$app_version = "app_version";

$query = isset($_GET["query"]) ? $_GET["query"] : "";
$value = isset($_GET["value"]) ? $_GET["value"] : "";

$res_data = array("process_sts" => "NO");

if ($query != '' && $value != '') {
    $update_sql = "UPDATE `" . $app_version . "` SET `" . $query . "` = '" . $value ."'";
    $res_update_sql = mysqli_query($conn, $update_sql);
    
    if ($res_update_sql) {
        $res_data["process_sts"] = "YES";
    }
    mysqli_close($conn);
} else {
    $res_data["process_sts"] = "NO";
}

echo json_encode($res_data);
