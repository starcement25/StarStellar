<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set("Asia/Kolkata");

include "star_connection.php";

$ledger_master   = "ledger_master";
$engineer_master = "engineer_master";

$start_date = "2025-09-04";
$end_date   = date("Y-m-d");

echo "<pre>";

/* ================= FIND DUPLICATES ================= */

$sql = "
SELECT user_id,
       ldgr_type,
       DATE(ldgr_datetime) as ld_date,
       GROUP_CONCAT(ldgr_id ORDER BY ldgr_id ASC) as ids,
       COUNT(*) as total
FROM $ledger_master
WHERE ldgr_type IN ('BIRTHDAY_POINT','ANNIVERSARY_POINT')
AND DATE(ldgr_datetime) >= '$start_date'
AND DATE(ldgr_datetime) <= '$end_date'
GROUP BY user_id, ldgr_type, DATE(ldgr_datetime)
HAVING total > 1
";

$res = mysqli_query($conn,$sql);

if(mysqli_num_rows($res) == 0){
    echo "No duplicate records found.";
    exit;
}

echo "Duplicate groups found: ".mysqli_num_rows($res)."\n\n";

/* ================= PROCESS DUPLICATES ================= */

while($row = mysqli_fetch_assoc($res)){

    $user_id   = $row['user_id'];
    $ldgr_type = $row['ldgr_type'];
    $ld_date   = $row['ld_date'];

    $ids = explode(",", $row['ids']);

    // Keep first record
    $keep_id = array_shift($ids);
    $delete_ids = $ids;

    echo "Processing User: $user_id | Type: $ldgr_type | Date: $ld_date\n";
    echo "Keeping ldgr_id: $keep_id\n";
    echo "Deleting ldgr_id: ".implode(",", $delete_ids)."\n\n";

    foreach($delete_ids as $del_id){

        // 1️⃣ Get duplicate point value
        $getPoint = mysqli_query($conn,"
            SELECT point_earned 
            FROM $ledger_master 
            WHERE ldgr_id='$del_id'
            LIMIT 1
        ");

        if(mysqli_num_rows($getPoint)==0){
            echo "Ledger $del_id already deleted or not found.\n";
            continue;
        }

        $point_row = mysqli_fetch_assoc($getPoint);
        $point_earned = intval($point_row['point_earned']);

        // 2️⃣ Delete duplicate ledger entry
        $delete = mysqli_query($conn,"
            DELETE FROM $ledger_master
            WHERE ldgr_id='$del_id'
            LIMIT 1
        ");

        if(!$delete){
            echo "Failed to delete $del_id\n";
            continue;
        }

        // 3️⃣ Reduce engineer points safely
        mysqli_query($conn,"
            UPDATE $engineer_master
            SET e_points = IF(e_points >= $point_earned,
                              e_points - $point_earned,
                              0)
            WHERE eid='$user_id'
        ");

        echo "Deleted $del_id and reduced $point_earned points.\n";
    }

    echo "------------------------------------------\n";
}

echo "Cleanup completed safely.";
echo "</pre>";
?>