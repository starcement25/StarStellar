<?php
include "star_connection.php";

$app_version = "app_version";
$res = array();

// Validate and sanitize input
$status = isset($_POST["status"]) ? trim($_POST["status"]) : "";
$device_type = isset($_POST["device_type"]) ? trim($_POST["device_type"]) : "";

if ($status !== "" && $device_type !== "") {
    $status = mysqli_real_escape_string($conn, $status);
    $device_type = mysqli_real_escape_string($conn, $device_type);

    $sql_check_status = "SELECT `status` FROM $app_version WHERE `device_type`='$device_type'";
    $result_check_status = mysqli_query($conn, $sql_check_status);

    if ($result_check_status) {
        $total_rows = mysqli_num_rows($result_check_status);
        $last_updated_datetime = date('Y-m-d H:i:s');

        if ($total_rows > 0) {
            $sql_update_status = "UPDATE $app_version SET `status`='$status', `last_updated_datetime`='$last_updated_datetime' WHERE `device_type`='$device_type'";
            $result_update_status = mysqli_query($conn, $sql_update_status);
        } else {
            $sql_insert_status = "INSERT INTO $app_version (`device_type`, `status`, `last_updated_datetime`) VALUES ('$device_type', '$status', '$last_updated_datetime')";
            $result_insert_status = mysqli_query($conn, $sql_insert_status);
        }

        $res = array("process_sts" => "YES", "process_message" => "Success");
    } else {
        $res = array("process_sts" => "NO", "process_message" => mysqli_error($conn));
    }
} else {
    $res = array("process_sts" => "NO", "process_message" => "Invalid input");
}

mysqli_close($conn);
echo json_encode($res);
?>
