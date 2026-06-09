<?php

die;
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
date_default_timezone_set('Asia/Kolkata');

include "star_connection.php";

$recommended_site_master = "recommended_site_master";
$engineer_master = "engineer_master";
$ledger_master = "ledger_master";
$recommended_site_asm_activity_log = "recommended_site_asm_activity_log";

/* Each verified site bonus */
$each_verified_site = get_value_by_setting_key($conn, "each_verified_site");
if ($each_verified_site == "") {
    $each_verified_site = 0;
}

/* Fetch PENDING sites older than 4 days */
$sql = "
SELECT *
FROM $recommended_site_master
WHERE r_status = 'PENDING'
AND r_submission_date <= DATE_SUB(NOW(), INTERVAL 4 DAY)
";

$res = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($res)) {

    $r_site_id = $row['r_site_id'];
    $te_code   = $row['r_te_code'];
    $r_engineer_id = $row['r_engineer_id'];
    $actual_product_id = $row['actual_product_id'];
    $actual_consumption = (int)$row['actual_consumption'];
    $existing_id = trim($row['existing_id']);

    /* Get product points */
    $product = show_product_data_by_id($conn, $actual_product_id);
    $point_per_bag = (int)$product['point_per_bag'];

    if ($existing_id != "") {
        $each_verified_site = 0;
    }

    $earned_points = ($point_per_bag * $actual_consumption);
    $now = date("Y-m-d H:i:s");
    $today = date("Y-m-d");

    /* Approve site */
    mysqli_query($conn, "
        UPDATE $recommended_site_master SET
            r_status = 'APPROVED',
            r_te_interaction_comment = 'Auto approved by system (4 days)',
            approval_message = 'Auto approved by system',
            r_te_interaction_date = '$today',
            r_point_earned_by_engineer = '$earned_points',
            r_last_updated_datetime = '$now'
        WHERE r_site_id = '$r_site_id'
    ");

    /* Engineer points */
    $eng = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT e_points FROM $engineer_master WHERE eid = '$r_engineer_id'
    "));

    $old_points = (int)$eng['e_points'];
    $new_points = $old_points + $earned_points + $each_verified_site;

    mysqli_query($conn, "
        UPDATE $engineer_master
        SET e_points = '$new_points'
        WHERE eid = '$r_engineer_id'
    ");

    /* Ledger entry */
    mysqli_query($conn, "
        INSERT INTO $ledger_master
        (user_id, description, point_earned, ldgr_type, related_id, ldgr_datetime,remaining_balance)
        VALUES
        ('$r_engineer_id', 'Site Recommendation (Auto Approved)',
         '$earned_points', 'SITE_RECOMENDATION', '$r_site_id', '$now','$new_points')
    ");

    if ($each_verified_site > 0) {
        mysqli_query($conn, "
            INSERT INTO $ledger_master
            (user_id, description, point_earned, ldgr_type, related_id, ldgr_datetime)
            VALUES
            ('$r_engineer_id', 'Verified Recommendation Site (Auto)',
             '$each_verified_site', 'SITE_RECOMENDATION', '$r_site_id', '$now')
        ");
    }

    /* ASM Activity Log */
    mysqli_query($conn, "
        INSERT INTO $recommended_site_asm_activity_log
        (r_site_id, r_te_code, r_engineer_id, r_site_name, r_status,
         r_submission_date, r_te_interaction_date, r_point_earned_by_engineer,
         r_last_updated_datetime)
        VALUES
        ('$r_site_id', '$te_code', '$r_engineer_id',
         '".$row['r_site_name']."', 'APPROVED',
         '".$row['r_submission_date']."', '$today',
         '$earned_points', '$now')
    ");
}

mysqli_close($conn);
echo "Auto approval cron executed successfully\n";
