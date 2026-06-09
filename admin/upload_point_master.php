<?php
include "web_check.php";
include "star_connection.php";
session_start();
/*error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);*/

date_default_timezone_set('Asia/Kolkata');
//$now = '2024-03-19 15:00:00';
$now = date('Y-m-d H:i:s');
$admin_id = $_SESSION['start_stellar_admin'];
$engineer_master="engineer_master";
$ledger_master="ledger_master";
$log_table       = "engineer_point_action_log";
/* ================= SECURITY ================= */
if ($_SERVER['REQUEST_METHOD'] != 'POST' ||
    !isset($_POST['csrf_token']) ||
    $_POST['csrf_token'] != $_SESSION['csrf_token']) {
    die("Bad Request");
}

/* ================= CONFIG ================= */
$uploadDir = __DIR__ . '/tmp';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

/* ================= ERROR HANDLER ================= */
function errorAndExit($msg) {
    $_SESSION['errors'] = "<p style='color:red;'>".$msg."</p>";
    header("Location: add_point_master.php");
    exit;
}

/* ================= FILE CHECK ================= */
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] != 0) {
    errorAndExit("Please upload a valid CSV file.");
}

$ext = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
if ($ext != 'csv') {
    errorAndExit("Only CSV files allowed.");
}

/* ================= SAVE FILE ================= */
$fileName = 'point_upload_' . time() . '_' . rand(1000,9999) . '.csv';
$filePath = $uploadDir . '/' . $fileName;

if (!move_uploaded_file($_FILES['csv_file']['tmp_name'], $filePath)) {
    errorAndExit("File upload failed.");
}

/* ================= COUNTERS ================= */
$total = 0;
$success = 0;
$failed = 0;
$failedRows = array();

/* ================= PROCESS CSV ================= */
$handle = fopen($filePath, "r");
if ($handle === false) {
    errorAndExit("CSV read failed.");
}

$csv=fgetcsv($handle); // skip header
mysqli_autocommit($conn, false);


while (($data = fgetcsv($handle)) !== false) {

    $total++;

    $mobile  = isset($data[0]) ? trim($data[0]) : '';
    $add     = isset($data[1]) ? (int)$data[1] : 0;
    $deduct  = isset($data[2]) ? (int)$data[2] : 0;
    $remarks = isset($data[3]) ? trim($data[3]) : '';
    $remarks = mysqli_real_escape_string($conn, $remarks);
    /* ===== BASIC VALIDATION ===== */
    if ($mobile == '') {
        $failed++;
        $failedRows[] = "Row $total : Mobile missing";
        continue;
    }

    /* ===== ZERO CHECK ===== */
    if ($add == 0 && $deduct == 0) {
        $failed++;
        $failedRows[] = "Row $total : Both points zero";
        continue;
    }

    /* ===== BOTH FILLED INVALID ===== */
    if ($add > 0 && $deduct > 0) {
        $failed++;
        $failedRows[] = "Row $total : Both earned & reduced filled";
        continue;
    }

    /* ===== GET ENGINEER ===== */
    $sql="SELECT eid, e_points FROM $engineer_master WHERE e_mobile='$mobile'";
    $res = mysqli_query($conn,$sql);
    //echo"<pre>";print_r($sql);
    if (!$res || mysqli_num_rows($res) == 0) {
        $failed++;
        $failedRows[] = "Row $total : Engineer not found ($mobile)";
        continue;
    }

    $row = mysqli_fetch_assoc($res);
    $eid = $row['eid'];
    $oldPoint = (int)$row['e_points'];

    /* ===== ACTION DECISION ===== */
    if ($add > 0) {
        $action     = 'ADD';
        $ldgr_type  = 'ADDED_BY_ADMIN';
        $change     = $add;
        $ledger_col = 'point_earned';
        $newPoint   =  $add;
    } else {
        $action     = 'DEDUCT';
        $ldgr_type  = 'DEDUCTED_BY_ADMIN';
        $change     = $deduct;
        $ledger_col = 'point_redeem';
        $newPoint   = $deduct;
    }

    /* ===== UPDATE ENGINEER ===== */
   // $sql1="UPDATE $engineer_master SET e_points='$newPoint' WHERE eid='$eid'";
//echo"<pre>";print_r($sql1);die;
    //echo"<pre> sql1";print_r($sql1);
    
    /*
    if (!mysqli_query($conn,$sql1)) {
        // mysqli_rollback($conn);
        // errorAndExit("DB update failed.");
    }
*/
    /* ===== LEDGER ENTRY ===== */
    $sql2= "INSERT INTO $ledger_master
        (user_id, ldgr_type, description, $ledger_col, ldgr_datetime, remaining_balance)
        VALUES
        ('$eid','$ldgr_type','$remarks','$change','$now','')";
    //echo"<pre> sql2";print_r($sql2);

    mysqli_query($conn,$sql2);
    
           // echo"<pre>";print_r($sql2);die;

   // echo"<pre>";print_r('ss');die;

    /* ===== ACTION LOG ===== */
    $sql3="INSERT INTO $log_table
        (engineer_id, action_type, old_point, change_point, new_point, action_by, action_at)
        VALUES
        ('$eid','$action','$oldPoint','$change','$newPoint','$admin_id','2026-02-26 28:26:00')";
    //echo"<pre> sql3";print_r($sql3);

    mysqli_query($conn,$sql3);

    $success++;
}

mysqli_commit($conn);
mysqli_autocommit($conn, true);
fclose($handle);

/* ================= SUMMARY ================= */
$_SESSION['uploaded_response'] = array(
    'total'   => $total,
    'success' => $success,
    'failed'  => $failed,
    'errors'  => $failedRows
);
//echo"<pre>";print_r($_SESSION);

header("Location: add_point_master.php");
exit;
?>
