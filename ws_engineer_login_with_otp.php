<?php
include "star_connection.php";
$te_master = "te_master";
$engineer_master = "engineer_master";

$te_code = $_POST["te_code"] ? addslashes(trim($_POST["te_code"])) : "";
$mobile = $_POST["mobile"] ? addslashes(trim($_POST["mobile"])) : "";
$otp = $_POST["otp"] ? addslashes(trim($_POST["otp"])) : "";
$device_id = $_POST["device_id"] ? addslashes(trim($_POST["device_id"])) : "";
$registration_id = $_POST["registration_id"] ? addslashes(trim($_POST["registration_id"])) : "";
$device_type = $_POST["device_type"] ? addslashes(trim($_POST["device_type"])) : "";
$the_image_dir = "en_profile_pic/";
$profile_img_prifix = $server_url."en_profile_pic/";
$profile_default_image_link = $server_url."en_profile_pic/profile.png";

if ($te_code != '' && $mobile != '' && $otp != '') {
    $sql23 = "select `te_code` from $te_master where `te_code`='$te_code'";
    $res23 = mysqli_query($conn, $sql23);
    $tot_res23 = mysqli_num_rows($res23);
    if ($tot_res23 > 0) {
        $sql2 = "select * from $engineer_master where `te_code`='$te_code' and `e_mobile`='$mobile' and `the_otp`='$otp'";
        $res2 = mysqli_query($conn, $sql2);
        $tot_res2 = mysqli_num_rows($res2);
        if ($tot_res2 > 0) {
            $row2 = mysqli_fetch_assoc($res2);
            $the_engineer_id = $row2["eid"];
            $e_name = $row2["e_name"] ? trim($row2["e_name"]) : "";
            $e_mobile = $row2["e_mobile"] ? trim($row2["e_mobile"]) : "";
            $te_code = $row2["te_code"] ? trim($row2["te_code"]) : "";
            $e_email = $row2["e_email"] ? trim($row2["e_email"]) : "";
            $e_dob = $row2["e_dob"] ? trim($row2["e_dob"]) : "";
            $e_dom = $row2["e_dom"] ? trim($row2["e_dom"]) : "";
            $e_status_by_te = $row2["status_by_te"] ? trim($row2["status_by_te"]) : "PENDING";

            $e_address = $row2["e_address"] ? trim($row2["e_address"]) : "";
            $e_pin = $row2["e_pin"] ? trim($row2["e_pin"]) : "";
            $e_state = $row2["e_state"] ? trim($row2["e_state"]) : "";
            $e_city_town = $row2["e_city_town"] ? trim($row2["e_city_town"]) : "";
            $e_profile_image = $row2["e_profile_image"] ? trim($row2["e_profile_image"]) : "";
            if ($e_profile_image != "") {
                if (file_exists($the_image_dir . $e_profile_image)) {
                    $profile_image_link = $profile_img_prifix . $e_profile_image;
                } else {
                    $profile_image_link = $profile_default_image_link;
                }
            } else {
                $profile_image_link = $profile_default_image_link;
            }
            if ($device_id != '' && $registration_id != '' && $device_type != '') {
                $curr_datetime = date("Y-m-d H:i:s");
                $te_branch_code = get_te_branchcode_by_tecode($conn, $te_code);
                $sql_noty = "update $engineer_master set `branch_code`='$te_branch_code',`registration_id`='$registration_id',`device_type`='$device_type',`device_id`='$device_id',`last_updated_datetime`='$curr_datetime' where `eid`='$the_engineer_id'";
                $res_noty = mysqli_query($conn, $sql_noty);
            }

            // Check if the message has already been sent
            $sql_check_message_sent = "SELECT congratulatory_message_sent FROM $engineer_master WHERE e_mobile = '$mobile'";
            $res_check_message_sent = mysqli_query($conn, $sql_check_message_sent);
            $row_check_message_sent = mysqli_fetch_assoc($res_check_message_sent);
            $message_sent = $row_check_message_sent['congratulatory_message_sent'];

            if (!$message_sent) {
                // Send congratulatory SMS
                $congratulatory_message = "Congratulation! You have been successfully registered on Star Stellar - STAR CEMENT";
                sendSMSNotification($mobile, $congratulatory_message);

                // Update the flag to indicate that the message has been sent
                $sql_update_flag = "UPDATE $engineer_master SET congratulatory_message_sent = true WHERE e_mobile = '$mobile'";
                mysqli_query($conn, $sql_update_flag);
            }

            // Prepare response data
            $res_data = array(
                "process_status" => "YES",
                "process_message" => "Thank you for signing up with Star Stellar.",
                "the_engineer_id" => $the_engineer_id,
                "e_name" => $e_name,
                "e_mobile" => $e_mobile,
                "te_code" => $te_code,
                "e_email" => $e_email,
                "e_dob" => $e_dob,
                "e_dom" => $e_dom,
                "e_address" => $e_address,
                "e_pin" => $e_pin,
                "e_state" => $e_state,
                "e_city_town" => $e_city_town,
                "e_profile_image" => $profile_image_link
            );
        } else {
            $res_data = array("process_status" => "NO", "process_message" => "Wrong Credentials.");
        }
    } else {
        $res_data = array("process_status" => "NO", "process_message" => "TE Code doesn't exist.");
    }
} else {
    $res_data = array("process_status" => "NO", "process_message" => "All fields are mandatory.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>