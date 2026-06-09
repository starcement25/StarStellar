<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('memory_limit', '99999M');
set_time_limit(0);
date_default_timezone_set("Asia/Kolkata");

include "star_connection.php";

$engineer_master = "engineer_master";
$setting_master  = "setting_master";
$ledger_master   = "ledger_master";
$log_table       = "ledger_process_log";
/* ================= SETTINGS ================= */

$app_setting_key_arr = array("birthday_point"=>0,"anniversary_point"=>0);

$sql_settings = "SELECT the_key_name,the_value FROM $setting_master";
$res_settings = mysqli_query($conn,$sql_settings);

while($row=mysqli_fetch_assoc($res_settings)){
    $app_setting_key_arr[$row["the_key_name"]] = intval($row["the_value"]);
}

/* ================= DATE LOOP ================= */

$start_date = "2025-09-04";
$end_date   = date("Y-m-d");

$startObj = new DateTime($start_date);
$endObj   = new DateTime($end_date);
$endObj->modify('+1 day'); // include today

$interval = new DateInterval('P1D');
$dateRange = new DatePeriod($startObj, $interval, $endObj);

/* ================= MAIN LOOP ================= */

foreach ($dateRange as $dateObj) {

    $loop_date = $dateObj->format("Y-m-d");
    $loop_md   = $dateObj->format("m-d");
    $r_submission_date = $loop_date." ".date("H:i:s");

    $sql = "SELECT eid,e_name,e_points,registration_id,device_type,
            DATE_FORMAT(e_dob,'%m-%d') as e_dob_md,
            DATE_FORMAT(e_dom,'%m-%d') as e_dom_md
            FROM $engineer_master
            WHERE (DATE_FORMAT(e_dob,'%m-%d')='$loop_md'
               OR DATE_FORMAT(e_dom,'%m-%d')='$loop_md')";

    $res = mysqli_query($conn,$sql);

    while($row=mysqli_fetch_assoc($res)){

        $eid = $row["eid"];
        $e_name = $row["e_name"];
        $e_points = intval($row["e_points"]);
        $registration_id = trim($row["registration_id"]);
        $device_type = trim($row["device_type"]);

        /* ============= BIRTHDAY ============= */

        if($row["e_dob_md"]==$loop_md){

            $check = mysqli_query($conn,
            "SELECT 1 FROM $ledger_master
             WHERE user_id='$eid'
             AND ldgr_type='BIRTHDAY_POINT'
             AND DATE(ldgr_datetime)='$loop_date'
             LIMIT 1");

            if(mysqli_num_rows($check)==0){

                $add_point = $app_setting_key_arr["birthday_point"];

                mysqli_query($conn,"UPDATE $engineer_master
                                    SET e_points = e_points + $add_point
                                    WHERE eid='$eid'");

                mysqli_query($conn,"INSERT INTO $ledger_master
                                    (user_id,description,point_earned,ldgr_type,ldgr_datetime)
                                    VALUES('$eid',
                                    'Birthday Point - $loop_date',
                                    '$add_point',
                                    'BIRTHDAY_POINT',
                                    '$r_submission_date')");
									
				$ledger_id = mysqli_insert_id($conn);

                // INSERT LOG
                mysqli_query($conn,"INSERT INTO $log_table
                                    (ledger_id,user_id,loop_date,log_datetime)
                                    VALUES('$ledger_id',
                                           '$eid',
                                           '$loop_date',
                                           NOW())");
            }
        }

        /* ============= ANNIVERSARY ============= */

        if($row["e_dom_md"]==$loop_md){

            $check = mysqli_query($conn,
            "SELECT 1 FROM $ledger_master
             WHERE user_id='$eid'
             AND ldgr_type='ANNIVERSARY_POINT'
             AND DATE(ldgr_datetime)='$loop_date'
             LIMIT 1");

            if(mysqli_num_rows($check)==0){

                $add_point = $app_setting_key_arr["anniversary_point"];

                mysqli_query($conn,"UPDATE $engineer_master
                                    SET e_points = e_points + $add_point
                                    WHERE eid='$eid'");

                mysqli_query($conn,"INSERT INTO $ledger_master
                                    (user_id,description,point_earned,ldgr_type,ldgr_datetime)
                                    VALUES('$eid',
                                    'Anniversary Point - $loop_date',
                                    '$add_point',
                                    'ANNIVERSARY_POINT',
                                    '$r_submission_date')");
				$ledger_id = mysqli_insert_id($conn);
									 // INSERT LOG
                mysqli_query($conn,"INSERT INTO $log_table
                                    (ledger_id,user_id,loop_date,log_datetime)
                                    VALUES('$ledger_id',
                                           '$eid',
                                           '$loop_date',
                                           NOW())");
            }
        }

    }
}

mysqli_close($conn);
echo "Done";
?>