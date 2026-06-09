<?php
include "star_connection.php";

$te_master = "te_master";
$engineer_master = "engineer_master";
$gift_master = "gift_master";
$gift_order_master = "gift_order_master";
$ledger_master = "ledger_master";

$gift_data = array();
$e_points = 0;
$upload_dir = "gift_pic/";
$image_url = $server_url."gift_pic/";
$curr_datetime = date("Y-m-d H:i");
$gift_id = $_POST["gift_id"] ? $_POST["gift_id"] : "";
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$set_as_default_profile_address = $_POST["set_as_default_profile_address"] ? $_POST["set_as_default_profile_address"] : "NO";

$e_address = $_POST["e_address"] ? addslashes(trim($_POST["e_address"])) : "";
$e_city = $_POST["e_city"] ? addslashes(trim($_POST["e_city"])) : "";
$e_email = $_POST["email"] ? addslashes(trim($_POST["email"])) : "";
$e_pin = $_POST["e_pin"] ? addslashes(trim($_POST["e_pin"])) : "";
$e_state = $_POST["e_state"] ? addslashes(trim($_POST["e_state"])) : "";
$phone = $_POST["phone"] ? addslashes(trim($_POST["phone"])) : "";

$giftTypeID = 2;

// ----------- Setting: number of ACTIVATION days / start date rule -----------
$acknowledgement_rule_start_date = get_value_by_setting_key($conn, "acknowledgement_rule_start_date");
if ($acknowledgement_rule_start_date == "") {
    $acknowledgement_rule_start_date = '2025-11-01'; // fallback date
}

$current_date = date('Y-m-d');

if ($the_engineer_id != "") {

    // ---------- Apply rule only if current date >= rule start date ----------
    if (strtotime($current_date) >= strtotime($acknowledgement_rule_start_date)) {

        // ---------- Check last order placed on or after start date ----------
        $sql_last_order = "
            SELECT order_id, status, datetime 
            FROM $gift_order_master 
            WHERE user_id = '$the_engineer_id' 
              AND DATE(datetime) >= '$acknowledgement_rule_start_date' AND status='DELIVERED'
            ORDER BY g_order_id DESC 
            LIMIT 1";
        // echo "<pre>$sql_last_order"; die;

        $res_last_order = mysqli_query($conn, $sql_last_order);

        if ($res_last_order && mysqli_num_rows($res_last_order) > 0) {
            $row_last = mysqli_fetch_assoc($res_last_order);
            $last_status = strtoupper(trim($row_last['status']));
            $last_order_date = $row_last['datetime'];

            // ---------- Block if last order not yet acknowledged ----------
            // Allowed to continue only if status contains 'ACKNOWLEDGEMENT' or 'COMPLAINT/FEEDBACK'
            if (
                //$last_status != 'ACKNOWLEDGEMENT OF DELIVERY' &&
               // $last_status != 'COMPLAINT/FEEDBACK'
			   //$last_status == 'DELIVERED' || $last_status == 'ORDER PLACED' || $last_status == 'PENDING'
			   $last_status == 'DELIVERED' 
            ) {
                $res_data = array(
                    "process_status" => "NO",
                    "process_message" => "You cannot place a new order until your previous order has been acknowledged."
                );
                echo json_encode($res_data);
                mysqli_close($conn);
                exit;
            }
        }
    }

    // ---------- Continue with Engineer validation and order process ----------

    // ---------- Engineer validation ----------
    $sql2e = "SELECT `eid`, `e_points` FROM $engineer_master WHERE `eid`='$the_engineer_id'";
    $res2e = mysqli_query($conn, $sql2e);
    if (mysqli_num_rows($res2e) > 0) {
        $row2e = mysqli_fetch_assoc($res2e);
        $e_points = intval($row2e["e_points"]);

        $sql2 = "SELECT `id`, `gift_type_id`, `gift_title`, `point_require` 
                 FROM $gift_master WHERE `id`='$gift_id'";
        $res2 = mysqli_query($conn, $sql2);

        if (mysqli_num_rows($res2) > 0) {
            $row2 = mysqli_fetch_assoc($res2);
            $gift_title = trim($row2["gift_title"]);
            $gift_point = intval($row2["point_require"]);

            if ($row2["gift_type_id"] == $giftTypeID && ($e_email == "" || !filter_var($e_email, FILTER_VALIDATE_EMAIL))) {
                $res_data = array("process_status" => "NO", "process_message" => "Your email is required.");
            } else {

                $TDS = get_value_by_setting_key($conn, "TDS");
                        if ($TDS == "") {
                            $TDS = '20'; // fallback date
                        }
                     $tds_point=($gift_point*$TDS)/100;
                    $total_point=$gift_point+$tds_point;

                if ($total_point > $e_points) {
                    $res_data = array("process_status" => "NO", "process_message" => "You are not eligible to make order $gift_title.");
                } else {
                    
                    $rest_points_for_engineer = intval($e_points - $total_point);
                    $datetime = date("Y-m-d H:i:s");
                    $delivery_date = "";

                    // -------- Generate next g_order_id (SS00000001, SS00000002, etc.) --------
                    $sqlGetLast = "SELECT g_order_id FROM $gift_order_master 
                                   WHERE g_order_id IS NOT NULL AND g_order_id <> '' 
                                   ORDER BY g_order_id DESC LIMIT 1";
                    $resGetLast = mysqli_query($conn, $sqlGetLast);
                    $rowLast = mysqli_fetch_assoc($resGetLast);

                    if ($rowLast && !empty($rowLast['g_order_id'])) {
                        $lastId = $rowLast['g_order_id'];
                        $num = intval($lastId);
                        $nextNum = $num + 1;
                        $new_g_order_id = 'SS' . str_pad($nextNum, 8, '0', STR_PAD_LEFT);
                    } else {
                        $new_g_order_id = 'SS00000001';
                    }

                    // Fetch Gift Details again
                    $sql21 = "SELECT `id`, `gift_type_id`, `gift_title`, `description`, `point_require` 
                              FROM $gift_master WHERE `id` = '$gift_id'";
                    $res21 = mysqli_query($conn, $sql21);
                    if (mysqli_num_rows($res21) > 0) {
                        $row21 = mysqli_fetch_assoc($res21);
                        $gift_title = addslashes(trim($row21["gift_title"]));
                        $gift_description = addslashes(trim($row21["description"]));
                        $gift_point = intval($row21["point_require"]);
                    }
                    
                    // -------- Insert new order --------
                    $sqlmkord = "INSERT INTO $gift_order_master 
                                 (`order_id`, `user_id`, `gift_id`, `city`, `user_email`, `pin`, `state`, `address`, `point_taken`, `tds`,`product_point`,`datetime`, `phone`, `gift_title`, `gift_description`) 
                                 VALUES 
                                 ('$new_g_order_id', '$the_engineer_id', '$gift_id', '$e_city', '$e_email', '$e_pin', '$e_state', '$e_address', '$total_point','$tds_point','$gift_point', '$datetime', '$phone', '$gift_title', '$gift_description')";
                    $resmkord = mysqli_query($conn, $sqlmkord);

                    if ($resmkord) {
                        $the_order_id = mysqli_insert_id($conn);

                        if ($set_as_default_profile_address == "YES") {
                            $sqlupdusr = "UPDATE $engineer_master 
                                          SET `e_points`='$rest_points_for_engineer',
                                              `e_address`='$e_address',
                                              `e_pin`='$e_pin',
                                              `e_state`='$e_state',
                                              `e_city_town`='$e_city'
                                          WHERE `eid`='$the_engineer_id'";
                        } else {
                            $sqlupdusr = "UPDATE $engineer_master 
                                          SET `e_points`='$rest_points_for_engineer'
                                          WHERE `eid`='$the_engineer_id'";
                        }
                        mysqli_query($conn, $sqlupdusr);

                        $gift_title = addslashes($gift_title);
                        $sqlldgrin = "INSERT INTO $ledger_master 
                                      (`user_id`, `description`, `point_redeem`, `ldgr_type`, `related_id`, `ldgr_datetime`, `remaining_balance`) 
                                      VALUES 
                                      ('$the_engineer_id', '$gift_title', '$total_point', 'GIFT_REDEEM', '$gift_id', '$datetime', '$rest_points_for_engineer')";
                        mysqli_query($conn, $sqlldgrin);

                        $rest_points_for_engineer_msg = "Stellar Points : ".$rest_points_for_engineer;
                        $res_data = array(
                            "process_status" => "YES",
                            "process_message" => "Order successfully placed.",
                            "the_order_id" => $the_order_id,
                            "e_points_msg" => $rest_points_for_engineer_msg,
                            "the_point" => $rest_points_for_engineer
                        );
                    } else {
                        $res_data = array("process_status" => "NO", "process_message" => "Something went wrong.");
                    }
                }
            }
        } else {
            $res_data = array("process_status" => "NO", "process_message" => "No gift found.");
        }
    } else {
        $res_data = array("process_status" => "NO", "process_message" => "The engineer details don't exist.");
    }
} else {
    $res_data = array("process_status" => "NO", "process_message" => "Something went wrong.");
}

echo json_encode($res_data);
mysqli_close($conn);
?>
