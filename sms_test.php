<?php
// API endpoint
$url = 'https://http.myvfirst.com/smpp/sendsms';

// API credentials
$username = 'starhttpdealers';
$password = 'star1109';

// SMS details
$mobileNumber = '8961499496'; // Replace with recipient's mobile number
$message = 'Congratulation ! You have been successfully registered on Star Stellar - STAR CEMENT'; // Replace with your message
$senderID = 'STARCM';
$dlrMask = '19'; // Delivery receipt mask
$dlrUrl = 'url'; // Delivery receipt URL

// Construct cURL request data
$data = array(
    'username' => $username,
    'password' => $password,
    'to' => $mobileNumber,
    'from' => $senderID,
    'text' => $message,
    'dlr-mask' => $dlrMask,
    'dlr-url' => $dlrUrl
);

// Initialize cURL session
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL request
$response = curl_exec($ch);

// Check for errors
if($response === false) {
    echo 'cURL error: ' . curl_error($ch);
} else {
    echo 'SMS sent successfully.';
}

// Close cURL session
curl_close($ch);
?>
