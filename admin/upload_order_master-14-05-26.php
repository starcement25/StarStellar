<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include "star_connection.php";
// -------------------- GOOGLE AUTH (PHP 5.6 COMPATIBLE) ------------------------

    function google_auth() {

        $serviceAccountFile = "../js/star-saathi-firebase-adminsdk-rlaes-ca3f762b02.json";
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




$admin_id = $_SESSION['start_stellar_admin'];
$now = date('Y-m-d H:i:s');

/* ================= UPLOAD DIR ================= */
$uploadDir = __DIR__ . '/tmp';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

/* ================= ERROR HANDLER ================= */
function fail($msg) {
    $_SESSION['errors'] = $msg;
    header("Location: upload_order_status.php");
    exit;
}

/* ================= FILE CHECK ================= */
if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] != 0) {
    fail("CSV file required");
}

$ext = strtolower(pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION));
if ($ext != 'csv') {
    fail("Only CSV allowed");
}

$file = $uploadDir . '/order_status_' . time() . '.csv';
move_uploaded_file($_FILES['csv_file']['tmp_name'], $file);

/* ================= STATUS RULES ================= */
$statusRules = array(
    'PENDING' => array(
        'ORDER PLACED' => array('order_tracking_url','tracking_ord_id'),
        'DELIVERED' => array('delivery_date'),
        'REJECT'       => array('remarks')
    ),
    'ORDER PLACED' => array(
        'ORDER PLACED'    => array('order_tracking_url','tracking_ord_id'),
        'DELIVERED' => array('delivery_date'),
        'UNDELIVERED' => array('remarks'),
        'REJECT'    => array('remarks')
    ),
    'DELIVERED' => array(
        'DELIVERED'    => array('delivery_date'),
        'UNDELIVERED' => array('remarks'),
        'REJECT'      => array('remarks')
    ),
     'COMPLAINT/FEEDBACK' => array(
        'UNDELIVERED' => array('remarks'),
        'REJECT'      => array('remarks')
     ),
      'ACKNOWLEDGEMENT OF DELIVERY' => array(
        'UNDELIVERED' => array('remarks'),
        'REJECT'      => array('remarks')
    )
);

/* ================= COUNTERS ================= */
$total = 0;
$success = 0;
$failed = 0;
$errors = array();

/* ================= STATUS MAP ================= */
$statusMap = array(
    1 => 'ORDER PLACED',
    2 => 'DELIVERED',
    3 => 'UNDELIVERED',
    4 => 'REJECT'
);

/* ================= READ CSV ================= */
$handle = fopen($file, "r");
fgetcsv($handle); // skip header
mysqli_autocommit($conn, false);

while (($data = fgetcsv($handle)) !== false) {

    $total++;

    $order_id   = trim($data[0]);
    // $new_status = strtoupper(trim($data[1]));
    $remarks    = trim($data[2]);
    $track_url  = trim($data[3]);
    $track_id   = trim($data[4]);
    $del_date   = trim($data[5]);

    $statusRaw = trim($data[1]);

        if (is_numeric($statusRaw) && isset($statusMap[$statusRaw])) {
            $new_status = $statusMap[$statusRaw];
        } else {
            $new_status = strtoupper($statusRaw);
        }

    /* BASIC */
    if ($order_id == '' || $new_status == '') {
        $failed++;
        $errors[] = "Row $total : Order ID / Status missing";
        continue;
    }

    /* FETCH ORDER */
    $res = mysqli_query($conn,"SELECT status,g_order_id,point_taken,user_id FROM gift_order_master WHERE order_id='$order_id' LIMIT 1");
    if (!$res || mysqli_num_rows($res) == 0) {
        $failed++;
        $errors[] = "Row $total : Order not found ($order_id)";
        continue;
    }

    $row = mysqli_fetch_assoc($res);
    $current_status = strtoupper(trim($row['status']));
    $g_order_id = strtoupper(trim($row['g_order_id']));
    $gift_point = intval($row['point_taken']);
    $engineer_id = $row['user_id'];
    /* STATUS FLOW VALIDATION */
    if (!isset($statusRules[$current_status][$new_status])) {
        $failed++;
        $errors[] = "Row $total : Invalid status change ($current_status → $new_status)";
        continue;
    }

    /* MANDATORY FIELD CHECK */
    $required = $statusRules[$current_status][$new_status];

    foreach ($required as $field) {

        if ($field == 'order_tracking_url' && $track_url == '') {
            $failed++; $errors[] = "Row $total : order_tracking_url required"; continue 2;
        }
        if ($field == 'tracking_ord_id' && $track_id == '') {
            $failed++; $errors[] = "Row $total : tracking_ord_id required"; continue 2;
        }
        if ($field == 'delivery_date' && $del_date == '') {
            $failed++; $errors[] = "Row $total : delivery_date required"; continue 2;
        }
        if ($field == 'remarks' && $remarks == '') {
            $failed++; $errors[] = "Row $total : remarks required"; continue 2;
        }
    }

    /* BUILD UPDATE QUERY (NO BLANK OVERWRITE) */
    $update = array();
    $update[] = "status='$new_status'";

    if ($remarks != '') {
        $update[] = "remarks='".mysqli_real_escape_string($conn,$remarks)."'";
    }
    if ($track_url != '') {
        $update[] = "amazon_order_link='".mysqli_real_escape_string($conn,$track_url)."'";
    }
    if ($track_id != '') {
        $update[] = "amazon_order_id='".mysqli_real_escape_string($conn,$track_id)."'";
    }
    if ($del_date != '') {
        $update[] = "delivery_date='".date('Y-m-d',strtotime($del_date))."'";
    }

    mysqli_query($conn,
        "UPDATE gift_order_master SET ".implode(",",$update)." WHERE order_id='$order_id'"
    );
    /* ================= POINT REVERT ON UNDELIVERED / REJECT ================= */
    if (($new_status == 'UNDELIVERED' || $new_status == 'REJECT')) {
        //echo"<pre>";print_r($new_status);die;

        

        if ($gift_point > 0 && $engineer_id != '') {

            /* Fetch engineer points */
            $res_eng = mysqli_query($conn,"
                SELECT e_points
                FROM engineer_master
                WHERE eid='$engineer_id'
                LIMIT 1
            ");
            //echo"<pre>";print_r($res_eng);die;

            if ($res_eng && mysqli_num_rows($res_eng) > 0) {

                $eng = mysqli_fetch_assoc($res_eng);
                $current_points = intval($eng['e_points']);
                $new_points = $current_points + $gift_point;

                /* Update engineer points */
                mysqli_query($conn,"
                    UPDATE engineer_master
                    SET e_points='$new_points'
                    WHERE eid='$engineer_id'
                ");

                /* Fetch gift title */
                $gift_title = '';
                $res_gift = mysqli_query($conn,"
                    SELECT gift_title FROM gift_master
                    WHERE id='{$row['gift_id']}' LIMIT 1
                ");
                if ($res_gift && mysqli_num_rows($res_gift) > 0) {
                    $g = mysqli_fetch_assoc($res_gift);
                    $gift_title = $g['gift_title'];
                }

                /* Ledger entry */
                $gift_title_esc = mysqli_real_escape_string($conn, $gift_title);
                $datetime = date('Y-m-d H:i:s');
                $sql4="INSERT INTO ledger_master
                    (user_id, description, point_earned, ldgr_type, related_id, ldgr_datetime, remaining_balance)
                    VALUES
                    ('$engineer_id','$gift_title_esc','$gift_point','GIFT_REFUND','$g_order_id','$datetime','$new_points')";
                mysqli_query($conn,$sql4);
               // echo"<pre>";print_r($sql4);die;

            }
        }
    }

    /* LOG */
    $sql3="INSERT INTO gift_order_master_log
        (g_order_id, old_status, new_status, admin_action_id, comments, log_datetime)
        VALUES
        ('$g_order_id','$current_status','$new_status','$admin_id','$remarks','$now')";
        //echo"<pre>";print_r($sql3);die;

    mysqli_query($conn,$sql3);

    //sendPushNotification($conn, $single_user_id, $title, $message);
      

    switch ($new_status) {
			case "PENDING":
				$title = "Order Pending";
				$message = "Your order for '$gift_title' (Order ID: $order_id) is pending approval.";
				break;

			case "ORDER PLACED":
				$title = "Order Placed Successfully";
				$message = "Your order for '$gift_title' (Order ID: $order_id) has been placed successfully.";
				break;

			case "DELIVERED":
				$title = "Order Delivered";
				$message = "Your order for '$gift_title' (Order ID: $order_id) has been delivered.";
				break;

			case "ACKNOWLEDGEMENT OF DELIVERY":
				$title = "Acknowledgement of Delivery";
				$message = "Please acknowledge the delivery for your order '$gift_title' (Order ID: $order_id).";
				break;

			case "COMPLAINT/FEEDBACK":
				$title = "Feedback / Complaint Request";
				$message = "We value your feedback for '$gift_title' (Order ID: $order_id). Please share your experience.";
				break;

			case "UNDELIVERED":
				$title = "Order Undelivered";
				$message = "Your order for '$gift_title' (Order ID: $order_id) could not be delivered. Please contact support.";
				break;

			case "REJECT":
				$title = "Order Rejected";
				$message = "Your order for '$gift_title' (Order ID: $order_id) has been rejected.";
				break;

			default:
				$title = "Order Update";
				$message = "Your order for '$gift_title' (Order ID: $order_id) has been updated.";
				break;
		}
       // echo"<pre>";print_r($engineer_id);die;

        $insert_sql = "INSERT INTO notification_message 
		(`title`, `message`, `image_name`, `file_type`, `sending_count`, `status`, `user_type`, `branch_code`, `single_user_id`, `selected_user_ids`, `date_time`)
		VALUES 
		('$title', '$message', '', 'NONE', '0', 'STARTED', 'SINGLE_ENGINEER', '', '$engineer_id', '', '$now')";
        //echo"<pre>";print_r($insert_sql);die;

		mysqli_query($conn, $insert_sql);
			$data  = array("data" => "test value");
			// Fetch latest token from user_tokens table
			$sql_token = "SELECT token FROM user_tokens WHERE user_id = '$engineer_id' ORDER BY id DESC LIMIT 1";
			$res_token = mysqli_query($conn, $sql_token);
			if (!$res_token || mysqli_num_rows($res_token) == 0) {
				//return; // No token found, skip notification
               
			}

			$row = mysqli_fetch_assoc($res_token);
			$token = $row['token'];
			$result=send_fcm_notification($token, $title, $message, $data) ;
            //echo"<pre>";print_r($result);die;


    $success++;
}

mysqli_commit($conn);
mysqli_autocommit($conn, true);
fclose($handle);

/* ================= SUMMARY ================= */
$_SESSION['uploaded_response'] = array(
    'total' => $total,
    'success' => $success,
    'failed' => $failed,
    'errors' => $errors
);

header("Location: add_order_master.php");
exit;
?>
