<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
date_default_timezone_set('Asia/Kolkata');

include "star_connection.php";

$ledger_master   = "ledger_master";
$engineer_master = "engineer_master";

echo "<h3>Ledger & Engineer Balance Recalculation Report</h3>";
echo "<table border='1' cellpadding='6' cellspacing='0'>
<tr>
    <th>#</th>
    <th>Engineer ID</th>
    <th>Ledger ID</th>
    <th>Earned</th>
    <th>Redeemed</th>
    <th>Updated Remaining Balance</th>
</tr>";

$i = 1;

/* STEP 1: Get users having missing remaining_balance */
$sqlUsers = "
    SELECT DISTINCT user_id 
    FROM $ledger_master 
    WHERE (remaining_balance IS NULL OR remaining_balance = '' ) AND `user_id`='1004'
";
//echo"<pre>";print_r($sqlUsers);die;

$userRes = mysqli_query($conn, $sqlUsers);

while ($u = mysqli_fetch_assoc($userRes)) {

    $user_id = $u['user_id'];

    /* STEP 2: Calculate correct balance from ledger */
    $sqlSum = "
        SELECT 
            IFNULL(SUM(point_earned),0) AS total_earned,
            IFNULL(SUM(point_redeem),0)  AS total_redeemed
        FROM $ledger_master
        WHERE user_id = '$user_id'
          AND ldgr_datetime BETWEEN '2024-03-19 00:00:00' AND NOW()
    ";
    //echo"<pre>";print_r($sqlSum);
    $sum = mysqli_fetch_assoc(mysqli_query($conn, $sqlSum));

    $total_earned   = (int)$sum['total_earned'];
    $total_redeemed = (int)$sum['total_redeemed'];

    $final_balance = $total_earned - $total_redeemed;
    if ($final_balance < 0) $final_balance = 0;

    /* STEP 3: Update engineer_master.e_points */
    mysqli_query($conn, "
        UPDATE $engineer_master
        SET e_points = '$final_balance'
        WHERE eid = '$user_id'
    ");

    /* STEP 4: Ledger entries in DESC order for back calculation */
    $sqlLedger = "
        SELECT ldgr_id, product_point, point_redeem, remaining_balance
        FROM $ledger_master
        WHERE user_id = '$user_id'
          AND ldgr_datetime BETWEEN '2024-03-19 00:00:00' AND NOW()
        ORDER BY ldgr_datetime DESC, ldgr_id DESC
    ";
    $ledRes = mysqli_query($conn, $sqlLedger);

    $running_balance = $final_balance;
    $is_first = true;

    while ($ld = mysqli_fetch_assoc($ledRes)) {

        $ledger_id = $ld['ldgr_id'];
        $earned    = (int)$ld['product_point'];
        $redeemed  = (int)$ld['point_redeem'];

        /* Update missing remaining_balance */
        if ($ld['remaining_balance'] === '' || is_null($ld['remaining_balance'])) {

            mysqli_query($conn, "
                UPDATE $ledger_master
                SET remaining_balance = '$running_balance'
                WHERE ldgr_id = '$ledger_id'
            ");

            echo "<tr>
                <td>".$i++."</td>
                <td>$user_id</td>
                <td>$ledger_id</td>
                <td>$earned</td>
                <td>$redeemed</td>
                <td><b>$running_balance</b></td>
            </tr>";
        }

        /* Skip reverse calc for first row */
        if ($is_first) {
            $is_first = false;
            continue;
        }

        /* Reverse calculation */
        if ($earned > 0) {
            $running_balance -= $earned;
        }

        if ($redeemed > 0) {
            $running_balance += $redeemed;
        }

        if ($running_balance < 0) {
            $running_balance = 0;
        }
    }
}

echo "</table>";
mysqli_close($conn);

echo "<br><b>Ledger & Engineer balances recalculated successfully.</b>";
?>
