<?php
    include "star_connection.php";

    $te_master = "te_master";
    $engineer_master = "engineer_master";
    $gift_master = "gift_master";
    $gift_data = array();
    $e_points = 0;
    $e_points_msg = "Stellar Points: ";
    $upload_dir = "gift_pic/";
    $image_url = $server_url . "gift_pic/";
    $curr_datetime = date("Y-m-d H:i");
    $page_no = $_POST["page_no"] ? $_POST["page_no"] : 1;
    $the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
    $limit = 20;
    $start_from = (($page_no - 1) * $limit);

    $giftTypeID = 2;
    // ----------- Setting: number of ACTIVATION days / start date rule -----------
        $TDS = get_value_by_setting_key($conn, "TDS");
        if ($TDS == "") {
            $TDS = '20'; // fallback date
        }
    if ($the_engineer_id != "") {
        $sql2e = "SELECT `eid`,`e_points` FROM $engineer_master WHERE `eid`='$the_engineer_id'";
        $res2e = mysqli_query($conn, $sql2e);
        $tot_res2e = mysqli_num_rows($res2e);

        if ($tot_res2e > 0) {
            $row2e = mysqli_fetch_assoc($res2e);
            $e_points = $row2e["e_points"] ? trim($row2e["e_points"]) : 0;

            if ($e_points == "") {
                $e_points = 0;
            }

            $e_points = intval($e_points);
            $sql2 = "SELECT * FROM $gift_master WHERE `status`='ACTIVE' ORDER BY ABS(`point_require`) ASC LIMIT $start_from,$limit";
            $res2 = mysqli_query($conn, $sql2);
            $tot_res2 = mysqli_num_rows($res2);

            if ($tot_res2 > 0) {
                while ($row2 = mysqli_fetch_assoc($res2)) {
                    $gift_id = $row2["id"];
                    $gift_title = $row2["gift_title"];
                    $gift_description = $row2["description"];
                    $gift_image = $row2["gift_image"] ? trim($row2["gift_image"]) : "";

                    if ($gift_image != '' && file_exists($upload_dir . $gift_image)) {
                        $gift_image_url = $image_url . $gift_image;
                    } else {
                        $gift_image_url = "";
                    }

                    $point_require = $row2["point_require"] ? trim($row2["point_require"]) : 0;
                    $point_require_text = "Redeem " . $point_require . " pts";
                    $redeem_status = $row2["redeem"];
                    $is_email_required = false;
                    if($row2["gift_type_id"] == $giftTypeID)
                    {
                        $is_email_required = true;
                    }

                    if ($e_points >= $point_require && strtoupper($redeem_status) === 'YES') {
                        $button_status = "ENABLE";
                    } else {
                        $button_status = "DISABLE";
                    }
                    $tds_point=($point_require*$TDS)/100;
                    $total_point=$point_require+$tds_point;
                    $gift_data[] = array(
                        "gift_id" => $gift_id,
                        "gift_title" => $gift_title,
                        "gift_description" => $gift_description,
                        "gift_image_url" => $gift_image_url,
                        "point_require" => $point_require,
                        "point_require_text" => $point_require_text,
                        "tds_point" => $tds_point,
                        "total_point" => $total_point,
                        "button_status" => $button_status,
                        "is_email_required" => $is_email_required,
                    );
                }

                $e_points_msg = $e_points_msg . $e_points;
                $res_data = array(
                    "process_status" => "YES",
                    "process_message" => "Success.",
                    "e_points" => $e_points,
                    "e_points_msg" => $e_points_msg,
                    "gift_data" => $gift_data
                );
            } else {
                $e_points_msg = $e_points_msg . $e_points;
                $res_data = array(
                    "process_status" => "NO",
                    "process_message" => "No record found.",
                    "e_points" => $e_points,
                    "e_points_msg" => $e_points_msg,
                    "gift_data" => $gift_data
                );
            }
        } else {
            $e_points_msg = $e_points_msg . $e_points;
            $res_data = array(
                "process_status" => "NO",
                "process_message" => "Something went wrong.",
                "e_points" => $e_points,
                "e_points_msg" => $e_points_msg,
                "gift_data" => $gift_data
            );
        }
    } else {
        $res_data = array("process_status" => "NO", "process_message" => "Something went wrong.");
    }

    echo json_encode($res_data);
    mysqli_close($conn);
?>
