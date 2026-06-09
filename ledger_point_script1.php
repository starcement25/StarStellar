<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
date_default_timezone_set('Asia/Kolkata');

include "star_connection.php";

$ledger_master   = "ledger_master";
$engineer_master = "engineer_master";

echo "<h3>Ledger Remaining Balance Update Report</h3>";
echo "<table border='1' cellpadding='6' cellspacing='0'>
<tr>
    <th>#</th>
    <th>User ID</th>
    <th>Ledger ID</th>
    <th>Earned</th>
    <th>Redeemed</th>
    <th>Updated Remaining Balance</th>
</tr>";

$i = 1;

/* Step 1: Get distinct users with empty remaining_balance */
$sql1 = "
    SELECT DISTINCT user_id
    FROM $ledger_master
    WHERE (remaining_balance IS NULL OR remaining_balance = '' ) 
";
$userRes = mysqli_query($conn, $sql1);

while ($u = mysqli_fetch_assoc($userRes)) {

    $user_id = $u['user_id'];

    /* Step 2: Get current balance from engineer_master */
    $sql2 = "
        SELECT e_points
        FROM $engineer_master
        WHERE eid = '$user_id'
    ";
    $eng = mysqli_fetch_assoc(mysqli_query($conn, $sql2));
    if (!$eng) continue;

    $running_balance = (int)$eng['e_points'];

    /* Step 3: Fetch ledger entries in DESC order */
    $sql3 = "
        SELECT ldgr_id, point_earned, point_redeem, remaining_balance
        FROM $ledger_master
        WHERE user_id = '$user_id'
        ORDER BY  ldgr_id DESC limit 1
    ";
    //echo"<pre>";print_r($sql3);

    $ledRes = mysqli_query($conn, $sql3);

    $is_first = true;

    while ($ld = mysqli_fetch_assoc($ledRes)) {

        $ledger_id = $ld['ldgr_id'];
        $earned    = (int)$ld['point_earned'];
        $redeemed  = (int)$ld['point_redeem'];
         $remaining_balance  = (int)$ld['remaining_balance'];

        /* Update only missing remaining_balance */

        if ($ld['remaining_balance'] === '' || is_null($ld['remaining_balance']) || $ld['remaining_balance']=='0') {
      $sql6="UPDATE $ledger_master SET remaining_balance = '$running_balance' WHERE ldgr_id = '$ledger_id'";
            mysqli_query($conn, $sql6);

            /* REPORT ROW */
            echo "<tr>
                <td>".$i++."</td>
                <td>$user_id</td>
                <td>$ledger_id</td>
                <td>$earned</td>
                <td>$redeemed</td>
                <td><b>$running_balance</b></td>
            </tr>";
        }

        /* First row already set */
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
echo "<br><b>Ledger remaining_balance recalculated successfully</b>";
?>
