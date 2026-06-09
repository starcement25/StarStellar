<?php
include "star_connection.php";

$engineer_master = "engineer_master";
$gift_master = "gift_master";
$gift_order_master = "gift_order_master";
$support_master = "support_master"; // Added support_master table
$order_data = array();
$upload_dir = "gift_pic/";
$image_url = $server_url . "gift_pic/";
$curr_datetime = date("Y-m-d H:i");
$page_no = $_POST["page_no"] ? $_POST["page_no"] : 1;
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$status_show = "DELIVERED";
$limit = 20;
$start_from = (($page_no - 1) * $limit);

if ($the_engineer_id != "") {
    $sql2 = "SELECT go.*, gm.gift_title, gm.description, gm.gift_image, 
                    sm.s_type, sm.s_comment
             FROM $gift_order_master go
             LEFT JOIN $gift_master gm ON go.gift_id = gm.id
             LEFT JOIN $support_master sm ON go.g_order_id = sm.order_id
             WHERE go.user_id = '$the_engineer_id' 
                   AND go.status = '$status_show' 
             ORDER BY go.g_order_id DESC 
             LIMIT $start_from, $limit";

    $res2 = mysqli_query($conn, $sql2);
    $tot_res2 = mysqli_num_rows($res2);

    if ($tot_res2 > 0) {
        while ($row2 = mysqli_fetch_assoc($res2)) {
            // Extracting fields as before
            $order_id = $row2["order_id"];
            $gift_id = $row2["gift_id"];
            $gift_title = $row2["gift_title"];
            $gift_description = $row2["description"];
            $gift_image_url = $row2["gift_image"];
            $point_taken = $row2["point_taken"];
            $point_taken_text = $row2["point_taken_text"];
            $status = $row2["status"];
            $city = $row2["city"];
            $state = $row2["state"];
            $address = $row2["address"];
            $pin = $row2["pin"];
            $datetime_text = $row2["datetime"];
            $delivery_date_text = $row2["delivery_date"];
            $is_order_received = $row2["is_order_received"];

            // Additional fields from support_master
            $s_type = $row2["s_type"] ? trim($row2["s_type"]) : "";
            $s_comment = $row2["s_comment"] ? trim($row2["s_comment"]) : "";

            $order_data[] = array(
                "order_id" => $order_id,
                "gift_id" => $gift_id,
                "gift_title" => $gift_title,
                "gift_description" => $gift_description,
                "gift_image_url" => $gift_image_url,
                "point_taken" => $point_taken,
                "point_taken_text" => $point_taken_text,
                "status" => $status,
                "city" => $city,
                "state" => $state,
                "address" => $address,
                "pin" => $pin,
                "datetime" => $datetime_text,
                "delivery_date" => $delivery_date_text,
                "is_order_received" => $is_order_received,
                "s_type" => $s_type, // New fields
                "s_comment" => $s_comment, 
            );
        }

        $res_data = array("process_status" => "YES", "process_message" => "Success.", "order_data" => $order_data);
    } else {
        $res_data = array("process_status" => "NO", "process_message" => "No record found.", "order_data" => $order_data);
    }
} else {
    $res_data = array("process_status" => "NO", "process_message" => "Something went wrong.");
}

echo json_encode($res_data);
mysqli_close($conn);
?>
