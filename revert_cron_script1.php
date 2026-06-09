<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
date_default_timezone_set('Asia/Kolkata');

include "star_connection.php";

$recommended_site_master = "recommended_site_master";
$engineer_master = "engineer_master";
$ledger_master = "ledger_master";
$recommended_site_asm_activity_log = "recommended_site_asm_activity_log";

$now   = date("Y-m-d H:i:s");
$today = date("Y-m-d");
$count=0;
/* Fetch auto-approved records */
$sql = "
SELECT *
FROM $recommended_site_master
WHERE r_status = 'APPROVED'
AND r_te_interaction_comment = 'Auto approved by system (4 days)'
AND approval_message = 'Auto approved by system'
";
//echo"<pre>";print_r($sql);
$res = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($res)) {

    $r_site_id      = $row['r_site_id'];
    $r_engineer_id  = $row['r_engineer_id'];
    $te_code        = $row['r_te_code'];
    $earned_points  = (int)$row['r_point_earned_by_engineer'];

    /* Get current engineer balance */
    $eng = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT e_points FROM $engineer_master WHERE eid = '$r_engineer_id'
    "));

    $old_points = (int)$eng['e_points'];
    $new_points = $old_points - $earned_points;

    if ($new_points < 0) {
        $new_points = 0; // safety
    }

    /* Revert recommended_site_master */
    $sql2=" UPDATE $recommended_site_master SET
            r_status = 'PENDING',
            r_te_interaction_comment = NULL,
            approval_message = 'Point deducted due to system error',
            r_te_interaction_date = NULL,
            r_point_earned_by_engineer = 0,
            r_last_updated_datetime = '$now'
        WHERE r_site_id = '$r_site_id'";
        //echo"<pre>";print_r($sql2);
    mysqli_query($conn, $sql2);

    /* Update engineer points */
    $sql3="UPDATE $engineer_master
        SET e_points = '$new_points'
        WHERE eid = '$r_engineer_id'";
        //echo"<pre>";print_r($sql3);
    mysqli_query($conn, $sql3);

    /* Ledger entry – point deduction */
    $sql4="INSERT INTO $ledger_master
        (user_id, description, product_point, ldgr_type, related_id, ldgr_datetime, remaining_balance)
        VALUES
        (
            '$r_engineer_id',
            'Point deducted due to system error',
            '$earned_points',
            'SITE_RECOMENDATION',
            '$r_site_id',
            '$now',
            '$new_points'
        )";
        //echo"<pre>";print_r($sql4);
    mysqli_query($conn, $sql4);

    /* ASM Activity Log */
    $sql5="INSERT INTO $recommended_site_asm_activity_log
        (
            r_site_id, r_te_code, r_engineer_id, r_site_name,
            r_status, r_submission_date, r_te_interaction_date,
            r_point_earned_by_engineer, r_last_updated_datetime,
            r_te_interaction_comment
        )
        VALUES
        (
            '$r_site_id',
            '$te_code',
            '$r_engineer_id',
            '".$row['r_site_name']."',
            'PENDING',
            '".$row['r_submission_date']."',
            '$today',
            '$earned_points',
            '$now',
            'Point deducted due to system error'
        )";
    mysqli_query($conn, $sql5);
   // echo"<pre>";print_r($sql5);
    //die;
    $count++;
}

mysqli_close($conn);
echo "Auto approval revert cron executed successfully\n".$count;
