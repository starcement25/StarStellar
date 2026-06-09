<?php
include "star_connection.php";
$te_master = "te_master";
$engineer_master = "engineer_master";
$recommended_site_master = "recommended_site_master";
$approved_recommendation_data = array();
$upload_dir = "recomend_site_pic/";
$image_url = $server_url . "recomend_site_pic/";
$curr_datetime = date("Y-m-d H:i");
$the_status = "APPROVED"; // Change the status to "APPROVED"
$tot_res2cnt = 0;
$page_no = $_POST["page_no"] ? addslashes(trim($_POST["page_no"])) : 1; // Adjust variable names
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$the_max_date_time = $_POST["the_max_date_time"] ? addslashes(trim($_POST["the_max_date_time"])) : "";
$limit = 10;
$start_from = (($page_no - 1) * $limit);

if ($the_engineer_id != "") {

    if ($the_max_date_time != "") {
        $max_date_qry = " and $recommended_site_master.`r_submission_date`<='$the_max_date_time'";
    } else {
        $max_date_qry = "";
    }

    $sql2cnt = "SELECT $recommended_site_master.`r_site_id` FROM $recommended_site_master 
                LEFT JOIN $te_master ON $recommended_site_master.`r_te_code`=$te_master.`te_code` 
                WHERE $recommended_site_master.`r_engineer_id`='$the_engineer_id' 
                AND $recommended_site_master.`r_status`='$the_status' $max_date_qry";

    $res2cnt = mysqli_query($conn, $sql2cnt);
    $tot_res2cnt = mysqli_num_rows($res2cnt);
    $tot_res2cnt = intval($tot_res2cnt);

    $sql2 = "SELECT *, $te_master.`te_name` FROM $recommended_site_master 
             LEFT JOIN $te_master ON $recommended_site_master.`r_te_code`=$te_master.`te_code` 
             WHERE $recommended_site_master.`r_engineer_id`='$the_engineer_id' 
             AND $recommended_site_master.`r_status`='$the_status' $max_date_qry 
             ORDER BY $recommended_site_master.`r_submission_date` DESC 
             LIMIT $start_from, $limit";


    $res2 = mysqli_query($conn, $sql2);
    $tot_res2 = mysqli_num_rows($res2);

    if ($tot_res2 > 0) {
        while ($row2 = mysqli_fetch_assoc($res2)) {
            $buyer_details_data = array();
            $the_quality_parameter_data = array();
            $r_site_id = $row2["r_site_id"];
            $r_site_name = $row2["r_site_name"];
            $r_te_code = $row2["r_te_code"];
            $r_te_name = $row2["te_name"];
            $r_site_name = $row2["r_site_name"];
            $r_contact_person_name = $row2["r_contact_person_name"];
            $r_mobile_no = $row2["r_mobile_no"];
            $r_address = $row2["r_address"];
            $point_earned = $row2["r_point_earned_by_engineer"];
            $r_site_potential_in_mt = $row2["r_site_potential_in_mt"];
            $r_contact_person_category_name = $row2["r_contact_person_category_name"];
            $r_recomended_site_image = $row2["r_recomended_site_image"] ? trim($row2["r_recomended_site_image"]) : "";
            
            // Additional Fields
            $actual_product_name = $row2["actual_product_name"] ? trim($row2["actual_product_name"]) : "";
            $actual_consumption = $row2["actual_consumption"] ? trim($row2["actual_consumption"]) : "";
            $purchased_from = $row2["purchased_from"] ? trim($row2["purchased_from"]) : "";
            $purchased_from_name = $row2["purchased_from_name"] ? trim($row2["purchased_from_name"]) : "";
            $purchased_from_area = $row2["purchased_from_area"] ? trim($row2["purchased_from_area"]) : "";
            $purchased_from_contact_no = $row2["purchased_from_contact_no"] ? trim($row2["purchased_from_contact_no"]) : "";

            if ($r_recomended_site_image != '') {
                if (file_exists($upload_dir . $r_recomended_site_image)) {
                    $r_recomended_site_image_url = $image_url . $r_recomended_site_image;
                } else {
                    $r_recomended_site_image_url = "";
                }
            } else {
                $r_recomended_site_image_url = "";
            }
            $r_status = $row2["r_status"];
            $r_submission_date = $row2["r_submission_date"];
            $r_submission_date_modified = date("d/m/y", strtotime($r_submission_date));
            $r_expected_product_id = $row2["expected_product_id"] ? trim($row2["expected_product_id"]) : "";
            $r_expected_product_name = $row2["expected_product_name"] ? trim($row2["expected_product_name"]) : "";
            $r_expected_consumption = $row2["expected_consumption"] ? trim($row2["expected_consumption"]) : "";

            $approved_recommendation_data[] = array(
                "r_site_id" => $r_site_id,
                "r_site_name" => $r_site_name,
                "r_te_code" => $r_te_code,
                "r_te_name" => $r_te_name,
                "r_contact_person_name" => $r_contact_person_name,
                "r_mobile_no" => $r_mobile_no,
                "r_address" => $r_address,
                "point_earned" => $point_earned,
                "r_site_potential_in_mt" => $r_site_potential_in_mt,
                "r_contact_person_category_name" => $r_contact_person_category_name,
                "r_recomended_site_image_url" => $r_recomended_site_image_url,
                "r_status" => $r_status,
                "r_submission_date" => $r_submission_date,
                "r_submission_date_modified" => $r_submission_date_modified,
                "expected_product_id" => $r_expected_product_id,
                "expected_product_name" => $r_expected_product_name,
                "expected_consumption" => $r_expected_consumption,
                // Additional Fields
                "actual_product_name" => $actual_product_name,
                "actual_consumption" => $actual_consumption,
                "purchased_from" => $purchased_from,
                "purchased_from_name" => $purchased_from_name,
                "purchased_from_area" => $purchased_from_area,
                "purchased_from_contact_no" => $purchased_from_contact_no
            );
        }
        $res_data = array("process_status" => "YES", "process_message" => "Success.", "tot_count" => $tot_res2cnt, "approved_recommendation_data" => $approved_recommendation_data);
    } else {
        $res_data = array("process_status" => "NO", "process_message" => "No new record found.", "approved_recommendation_data" => $approved_recommendation_data);
    }
} else {
    $res_data = array("process_status" => "NO", "process_message" => "Something went wrong.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>
