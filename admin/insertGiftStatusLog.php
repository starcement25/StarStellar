<?php
session_start();

function insertGiftStatusLog($gift_id, $status_type, $old_value, $new_value)
{
    global $conn;

    date_default_timezone_set('Asia/Kolkata');
    $cur_time = date('Y-m-d H:i:s');

    $admin_id = isset($_SESSION['start_stellar_admin']) 
                ? $_SESSION['start_stellar_admin'] 
                : 0;

    $sql = "INSERT INTO gift_master_status_log
            (gift_id, status_type, old_value, new_value, action_by, action_time)
            VALUES (?,?,?,?,?,?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "isssis",
        $gift_id,
        $status_type,
        $old_value,
        $new_value,
        $admin_id,
        $cur_time
    );

    mysqli_stmt_execute($stmt);
}
?>
