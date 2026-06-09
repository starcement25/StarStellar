<?php
// terms_api.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // allow public access (optional)

// Database connection
include 'star_connection.php'; // Make sure this file sets up $conn as your mysqli connection

// Prepare default response
$response = array(
    "status" => "error",
    "link" => "https://starstellar.com/terms_and_Conditions_page.php",
    "content" => ""
);

// Fetch terms_condition from setting_master table
$sql = "SELECT the_value FROM setting_master WHERE the_key_name = 'terms_condition' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['status'] = "success";
    $response['content'] = $row['the_value'];
} else {
    $response['content'] = "Terms & Conditions content not found.";
}

// Send JSON response
echo json_encode($response, JSON_PRETTY_PRINT);
exit;
?>
