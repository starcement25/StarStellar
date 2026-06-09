<?php
include "star_connection.php";

// Function to send SMS notification
function sendSMSNotification($mobileNumber, $message) {
    // API endpoint and parameters
    $url = 'https://http.myvfirst.com/smpp/sendsms';
    $username = 'starhttpdealers';
    $password = 'star1109';
    $from = 'STARCM';
    $dlrMask = '19';

    // Build the query string
    $params = http_build_query(array(
        'username' => $username,
        'password' => $password,
        'to' => $mobileNumber,
        'from' => $from,
        'text' => $message,
        'dlr-mask' => $dlrMask
    ));

    // Initialize cURL session
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the cURL request
    $response = curl_exec($ch);

    // Check for errors
    if($response === false) {
        echo 'cURL error: ' . curl_error($ch);
    } else {
        echo 'SMS sent successfully.';
    }

    // Close cURL session
    curl_close($ch);
}

// Function to approve/reject transaction and send SMS notification
function approveRejectTransaction($engineerId, $status, $siteName) {
    global $conn;

    // Perform update query to update status in the engineer master table
    $sql = "UPDATE engineer_master SET status = '$status' WHERE eid = $engineerId";
    $result = $conn->query($sql);

    if ($result === TRUE) {
        // Send SMS notification
        $mobileNumber = 'e_mobile'; // Get mobile number from engineer master table based on $engineerId
        $message = "Site Name: $siteName successfully $status. - Star Stellar"; // Construct SMS message
        sendSMSNotification($mobileNumber, $message);
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Example usage of approveRejectTransaction function
$engineerId = 'eid'; // Replace with actual engineer ID
$status = 'Approved'; // or 'Rejected'
$siteName = 'branch_code'; // Replace with actual site name
approveRejectTransaction($engineerId, $status, $siteName);

// Close database connection
$conn->close();

?>
