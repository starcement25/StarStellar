<?php
header("Content-Type: application/json");
include "star_connection.php"; // your DB connection file

// Set timezone to Kolkata
date_default_timezone_set('Asia/Kolkata');

// Helper function for JSON response
function send_response($status, $message) {
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

// Get POST inputs
$user_id   = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
$user_type = isset($_POST['user_type']) ? trim($_POST['user_type']) : '';
$token     = isset($_POST['token']) ? trim($_POST['token']) : '';

// Validate required fields
if ($user_id == '' || $user_type == '') {
    send_response("error", "Missing required fields: user_id, user_type");
}

// Escape values for safety
$user_id   = mysqli_real_escape_string($conn, $user_id);
$user_type = mysqli_real_escape_string($conn, $user_type);
$token     = mysqli_real_escape_string($conn, $token);

// Current timestamp in Kolkata
$created_at = date("Y-m-d H:i:s");

// Check if user_id + user_type already exist
 $check_sql = "SELECT id FROM user_tokens WHERE user_id = '$user_id' AND user_type = '$user_type'";
$result = mysqli_query($conn, $check_sql);
//echo"<pre>";print_r(mysqli_num_rows($result));die;

if (mysqli_num_rows($result) > 0) {
    // Update existing record
    $update_sql = "UPDATE user_tokens 
                   SET token = '$token', created_at = '$created_at'
                   WHERE user_id = '$user_id' AND user_type = '$user_type'";
    if (mysqli_query($conn, $update_sql)) {
        send_response("success", "Token updated successfully");
    } else {
        send_response("error", "Failed to update token: " . mysqli_error($conn));
    }
} else {
    // Insert new record
    $insert_sql = "INSERT INTO user_tokens (user_id, user_type, token, created_at)
                   VALUES ('$user_id', '$user_type', '$token', '$created_at')";
    if (mysqli_query($conn, $insert_sql)) {
        send_response("success", "Token stored successfully");
    } else {
        send_response("error", "Failed to store token: " . mysqli_error($conn));
    }
}
?>
