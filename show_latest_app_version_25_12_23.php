<?php
include "star_connection.php";
$res_data = array();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$app_version = "app_version";
$te_master = "te_master";
$engineer_master = "engineer_master";
$app_version_data = array("ANDROID"=>"1.1","IOS"=>"1.1");
$engineer_force_logout = "NO";
$te_force_logout = "NO";
$force_logout_message = "";
$engineer_id = isset($_POST["the_engineer_id"]) ? addslashes(trim($_POST["the_engineer_id"])) : "";
$en_device_id = isset($_POST["en_device_id"]) ? addslashes(trim($_POST["en_device_id"])) : "";
$en_registration_id = isset($_POST["en_registration_id"]) ? addslashes(trim($_POST["en_registration_id"])) : "";
$en_device_type = isset($_POST["en_device_type"]) ? addslashes(trim($_POST["en_device_type"])) : "";

function handleForceLogout($userType, $userId, $conn) {
    $sql_logout = "UPDATE " . ($userType === 'engineer' ? "engineer_master" : "te_master") . " SET `registration_id`='', `device_type`='', `device_id`='', `app_version`='' WHERE `eid`='$userId'";
    $res_logout = mysqli_query($conn, $sql_logout);
}

$the_app_version_val = isset($_POST["app_version"]) ? addslashes(trim($_POST["app_version"])) : "";

$te_code = isset($_POST["te_code"]) ? addslashes(trim($_POST["te_code"])) : "";
$te_device_id = isset($_POST["te_device_id"]) ? addslashes(trim($_POST["te_device_id"])) : "";
$te_registration_id = isset($_POST["te_registration_id"]) ? addslashes(trim($_POST["te_registration_id"])) : "";
$te_device_type = isset($_POST["te_device_type"]) ? addslashes(trim($_POST["te_device_type"])) : "";

if ($engineer_id != '' && $en_device_id != '' && $en_registration_id != '' && $en_device_type != '') {
    session_start();
    if (isset($_SESSION['engineer_id']) && $_SESSION['engineer_id'] != $engineer_id) {
        $engineer_force_logout = "YES";
        $force_logout_message = "Another device has logged in. Your session is terminated.";

        handleForceLogout('engineer', $_SESSION['engineer_id'], $conn);
    } else {
        $_SESSION['engineer_id'] = $engineer_id;
        $en_sql_noty = "UPDATE $engineer_master SET `registration_id`='$en_registration_id',`device_type`='$en_device_type',`device_id`='$en_device_id',`app_version`='$the_app_version_val' WHERE `eid`='$engineer_id'";
        $en_res_noty = mysqli_query($conn, $en_sql_noty);
    }
    session_write_close();
}

if ($engineer_id != '') {
    $pgfl = "SELECT `status`,`status_by_te` FROM $engineer_master WHERE `eid`='$engineer_id'";
    $pgresfl = mysqli_query($conn, $pgfl);
    $totalresfl = mysqli_num_rows($pgresfl);
    if ($totalresfl > 0) {
        $pgrowfl = mysqli_fetch_assoc($pgresfl);
        $the_status = $pgrowfl["status"] ? trim($pgrowfl["status"]) : "";
        $status_by_te = $pgrowfl["status_by_te"] ? trim($pgrowfl["status_by_te"]) : "";
        if ($the_status == "INACTIVE") {
            $engineer_force_logout = "YES";
            $force_logout_message = "Your details are inactive. Please contact admin.";
        } else {
            if ($status_by_te == "REJECTED") {
                $engineer_force_logout = "YES";
                $force_logout_message = "Your details are rejected. Please contact admin.";
            }
        }
    } else {
        $engineer_force_logout = "YES";
        $force_logout_message = "Your details not found. Please contact admin.";
    }
}

// Technician login

if ($te_code != '' && $te_device_id != '' && $te_registration_id != '' && $te_device_type != '') {
    session_start();
    if (isset($_SESSION['te_code']) && $_SESSION['te_code'] != $te_code) {
        $te_force_logout = "YES";
        $force_logout_message = "Another device has logged in. Your session is terminated.";

        // Handle force logout for the previous device
        handleForceLogout('technician', $_SESSION['te_code'], $conn);
    } else {
        $_SESSION['te_code'] = $te_code;
        $te_sql_noty = "UPDATE $te_master SET `registration_id`='$te_registration_id',`device_type`='$te_device_type',`device_id`='$te_device_id',`app_version`='$the_app_version_val' WHERE `te_code`='$te_code'";
        $te_res_noty = mysqli_query($conn, $te_sql_noty);
    }
    session_write_close();
}

if ($te_code != '') {
    $pgfl = "SELECT `acedns` FROM $te_master WHERE `te_code`='$te_code'";
    $pgresfl = mysqli_query($conn, $pgfl);
    $totalresfl = mysqli_num_rows($pgresfl);
    if ($totalresfl > 0) {
        $pgrowfl = mysqli_fetch_assoc($pgresfl);
        $the_acedns = $pgrowfl["acedns"] ? trim($pgrowfl["acedns"]) : "N";
        if ($the_acedns == "N") {
            $te_force_logout = "YES";
            $force_logout_message = "Your details are inactive. Please contact admin.";
        }
    } else {
        $te_force_logout = "YES";
        $force_logout_message = "Your details not found. Please contact admin.";
    }
}

$sqlall = "SELECT * FROM $app_version";
$resall = mysqli_query($conn, $sqlall);
$totall = mysqli_num_rows($resall);
if ($totall > 0) {
    while ($rowall = mysqli_fetch_assoc($resall)) {
        $the_device_type = $rowall["device_type"] ? trim($rowall["device_type"]) : "";
        $the_app_version = $rowall["app_version"] ? trim($rowall["app_version"]) : "";
        if ($the_device_type != "" && $the_app_version != "") {
            $the_device_type = strtoupper(strtolower($the_device_type));
            if (array_key_exists($the_device_type, $app_version_data)) {
                $app_version_data[$the_device_type] = $the_app_version;
            }
        }
    }
    $res_data = array("process_status"=>"YES", "process_message"=>"New version available.", "android_app_version"=>$app_version_data["ANDROID"], "ios_app_version"=>$app_version_data["IOS"], "engineer_force_logout"=>$engineer_force_logout, "te_force_logout"=>$te_force_logout, "force_logout_message"=>$force_logout_message);
} else {
    $res_data = array("process_status"=>"YES", "process_message"=>"Success.", "android_app_version"=>$app_version_data["ANDROID"], "ios_app_version"=>$app_version_data["IOS"], "engineer_force_logout"=>$engineer_force_logout, "te_force_logout"=>$te_force_logout, "force_logout_message"=>$force_logout_message);
}

echo json_encode($res_data);
mysqli_close($conn);
?>
