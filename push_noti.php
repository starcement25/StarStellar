<?php

$tokens = array(
    'feg6QjcmSNeCuyPwST0Hmy:APA91bF7pF2mEv479XNgHn2FyNHIKYf2iPc_wpILCFMQizLf_acMZyvm31K5RT7vwx2iQIakqyqoUC1X8tjgfih4-p66VYrrTYwdfI3J6Nu-0t_5oRwop0E'
);

$title = 'test';
$body  = 'test';
$data  = array("data" => "test value");

$result = send_fcm_notification($tokens, $title, $body, $data);

echo "<pre>";
print_r($result);
echo "</pre>";


// -------------------- GOOGLE AUTH (PHP 5.6 COMPATIBLE) ------------------------

function google_auth() {

    $serviceAccountFile = __DIR__ . "/js/star-saathi-firebase-adminsdk-rlaes-ca3f762b02.json";
    $scope = "https://www.googleapis.com/auth/firebase.messaging";

    $jsonKey = json_decode(file_get_contents($serviceAccountFile), true);

    if (!$jsonKey) {
        return array("error" => "Invalid service account JSON file");
    }

    $header = base64_encode(json_encode(array(
        "alg" => "RS256",
        "typ" => "JWT"
    )));

    $now = time();

    $jwt_claim = base64_encode(json_encode(array(
        "iss" => $jsonKey["client_email"],
        "scope" => $scope,
        "aud" => "https://oauth2.googleapis.com/token",
        "iat" => $now,
        "exp" => $now + 3600
    )));

    $data = $header . '.' . $jwt_claim;

    openssl_sign($data, $signature, $jsonKey["private_key"], "sha256WithRSAEncryption");
    $jwt = $data . '.' . base64_encode($signature);

    $postFields = http_build_query(array(
        "grant_type" => "urn:ietf:params:oauth:grant-type:jwt-bearer",
        "assertion"  => $jwt
    ));

    $ch = curl_init("https://oauth2.googleapis.com/token");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (!isset($result["access_token"])) {
        return array("error" => "Failed to fetch access token", "response" => $response);
    }

    return $result["access_token"];
}


// -------------------- SEND FCM NOTIFICATION (PHP 5.6 COMPATIBLE) ------------------------

function send_fcm_notification($tokens, $title, $body, $data = null) {

    if (!is_array($tokens)) {
        $tokens = array($tokens);
    }

    $access_token = google_auth();

    if (is_array($access_token)) {
        return array("auth_error" => $access_token);
    }

    $project_id = "star-saathi";
    $url = "https://fcm.googleapis.com/v1/projects/" . $project_id . "/messages:send";

    $headers = array(
        "Authorization: Bearer " . $access_token,
        "Content-Type: application/json"
    );

    $results = array();

    foreach ($tokens as $token) {

        $payload = array(
            "message" => array(
                "token" => $token,
                "notification" => array(
                    "title" => $title,
                    "body"  => $body
                ),
                "data" => $data ? $data : new stdClass()
            )
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlerr  = curl_error($ch);

        curl_close($ch);

        $decoded = json_decode($response, true);

        $results[] = array(
            "token" => $token,
            "http_code" => $httpcode,
            "curl_error" => $curlerr ? $curlerr : "none",
            "response" => $decoded ? $decoded : $response
        );
    }

    return $results;
}

?>
