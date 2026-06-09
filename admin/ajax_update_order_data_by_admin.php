<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
session_start();
date_default_timezone_set('Asia/Kolkata');
include "star_connection.php";
$gift_order_master = "gift_order_master";
$res_msg = array();
$the_ord_id = $_POST["the_ord_id"] ? trim($_POST["the_ord_id"]) : "";
$admin_sl_ord_sts = $_POST["admin_sl_ord_sts"] ? trim($_POST["admin_sl_ord_sts"]) : "";
$admin_del_dt = $_POST["admin_del_dt"] ? trim($_POST["admin_del_dt"]) : "";
$admin_amz_ord_id = $_POST["admin_amz_ord_id"] ? addslashes(trim($_POST["admin_amz_ord_id"])) : "";
$admin_amz_ord_link = $_POST["admin_amz_ord_link"] ? addslashes(trim($_POST["admin_amz_ord_link"])) : "";
$old_status = $_POST["admin_old_status"] ? addslashes(trim($_POST["admin_old_status"])) : "";
$admin_remarks = $_POST["admin_remarks"] ? addslashes(trim($_POST["admin_remarks"])) : "";
$pending_reason = $_POST["pending_reason"] ? addslashes(trim($_POST["pending_reason"])) : "";

if($the_ord_id!=''){
	 $sql5 = "update $gift_order_master set `status`='$admin_sl_ord_sts',`delivery_date`='$admin_del_dt',`amazon_order_id`='$admin_amz_ord_id',`amazon_order_link`='$admin_amz_ord_link',`remarks`='$admin_remarks',`pending_reason`='$pending_reason' where `g_order_id`='$the_ord_id'";
	$res5 = mysqli_query($conn,$sql5);
    
	  // ---------- Create log entry ----------
    $comment = "Amazon Order ID: $admin_amz_ord_id | Amazon Order Link: $admin_amz_ord_link";
    $old_status_esc = mysqli_real_escape_string($conn, $old_status);
    $new_status_esc = mysqli_real_escape_string($conn, $admin_sl_ord_sts);
    $comment_esc = mysqli_real_escape_string($conn, $comment);

	$admin_action_id=$_SESSION["start_stellar_admin"];
     $log_sql = "INSERT INTO gift_order_master_log 
                (`g_order_id`, `old_status`, `new_status`, `admin_action_id`, `comments`, `log_datetime`) 
                VALUES 
                ('$the_ord_id', '$old_status_esc', '$new_status_esc', '$admin_action_id', '$comment_esc', NOW())";
    mysqli_query($conn, $log_sql);


	//sk add condition to send notification
	// ---------------- Input Validation ----------------
		 $the_ord_id = isset($_POST['the_ord_id']) ? trim($_POST['the_ord_id']) : '';

		// ---------------- Fetch order details ----------------
		 $sql = "SELECT g.*, gm.gift_title 
				FROM gift_order_master g
				LEFT JOIN gift_master gm ON g.gift_id = gm.id
				WHERE g.g_order_id = '$the_ord_id' LIMIT 1";
		$result = mysqli_query($conn, $sql);

		if (!$result || mysqli_num_rows($result) == 0) {
			echo "Order not found";
		}

		$order = mysqli_fetch_assoc($result);
		$single_user_id = $order['user_id'];
		$order_id = $order['order_id'];
		$order_status = strtoupper(trim($admin_sl_ord_sts));
		$gift_title = $order['gift_title'];

		// ---------------- Prepare Notification ----------------
		$title = "";
		$message = "";

		// Define order status array
		$order_status_arr = array(
			"PENDING",
			"ORDER PLACED",
			"DELIVERED",
			"ACKNOWLEDGEMENT OF DELIVERY",
			"COMPLAINT/FEEDBACK",
			"UNDELIVERED",
			"REJECT"
		);

		switch ($order_status) {
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

		// ---------------- Insert Notification ---------------
		$title = mysqli_real_escape_string($conn, $title);
		$message = mysqli_real_escape_string($conn, $message);
		$single_user_id = mysqli_real_escape_string($conn, $single_user_id);
		
		$curr_datetime = date('Y-m-d H:i:s');
		  $insert_sql = "INSERT INTO notification_message 
		(`title`, `message`, `image_name`, `file_type`, `sending_count`, `status`, `user_type`, `branch_code`, `single_user_id`, `selected_user_ids`, `date_time`)
		VALUES 
		('$title', '$message', '', 'NONE', '0', 'STARTED', 'SINGLE_ENGINEER', '', '$single_user_id', '', '$curr_datetime')";
		if (mysqli_query($conn, $insert_sql)) {
    // After successful insert → send push notification
			//sendPushNotification($conn, $single_user_id, $title, $message);
			$data  = array("data" => "test value");
			// Fetch latest token from user_tokens table
			$sql_token = "SELECT token FROM user_tokens WHERE user_id = '$single_user_id' ORDER BY id DESC LIMIT 1";
			$res_token = mysqli_query($conn, $sql_token);
			if (!$res_token || mysqli_num_rows($res_token) == 0) {
				//return; // No token found, skip notification
                goto a;
			}

			$row = mysqli_fetch_assoc($res_token);
			$token = $row['token'];
			$result=send_fcm_notification($token, $title, $message, $data) ;
			//print_r($result);
		}

	//sk add for undelivered cases points to be added back
	// ---------------- If order is UNDELIVERED, refund points ----------------
    a:
		if ($order_status == 'UNDELIVERED' || $order_status == 'REJECT') {

			// Fetch gift point and engineer info
			$gift_point = intval($order['point_taken']); // from gift_order_master
			$the_engineer_id = $order['user_id'];

			// Get engineer current points
			$sql_eng = "SELECT e_points, e_address, e_pin, e_state, e_city_town 
						FROM engineer_master 
						WHERE eid = '$the_engineer_id' LIMIT 1";
			$res_eng = mysqli_query($conn, $sql_eng);

			if ($res_eng && mysqli_num_rows($res_eng) > 0) {
				$eng = mysqli_fetch_assoc($res_eng);
				$e_points = intval($eng['e_points']);
				

				// Refund points to engineer
				$rest_points_for_engineer = $e_points + $gift_point;

				 $sqlupdusr = "UPDATE engineer_master 
							SET e_points = '$rest_points_for_engineer'
							WHERE eid = '$the_engineer_id'";
				mysqli_query($conn, $sqlupdusr);

				// Insert into ledger_master
				$gift_title_esc = mysqli_real_escape_string($conn, $gift_title);
				$datetime = date('Y-m-d H:i:s');
				$sqlldgrin = "INSERT INTO ledger_master 
							(`user_id`, `description`, `point_earned`, `ldgr_type`, `related_id`, `ldgr_datetime`,`remaining_balance`)
							VALUES ('$the_engineer_id', '$gift_title_esc', '$gift_point', 'GIFT_REFUND', '$the_ord_id', '$datetime','$rest_points_for_engineer')";
				mysqli_query($conn, $sqlldgrin);
				
				

			}
		}


	//
        
 $res_msg = array("process_sts"=>"YES","process_msg"=>"Success");
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");
}


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


// ---------------- Firebase Push Notification Function ----------------
function sendPushNotification($conn, $user_id, $title, $message) {
    // Fetch latest token from user_tokens table
    $sql_token = "SELECT token FROM user_tokens WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 1";
    $res_token = mysqli_query($conn, $sql_token);
    if (!$res_token || mysqli_num_rows($res_token) == 0) {
        return; // No token found, skip notification
    }

    $row = mysqli_fetch_assoc($res_token);
    $token = $row['token'];

    // FCM API URL
    $url = "https://fcm.googleapis.com/fcm/send";
    $serverKey = "ya29.a0AQQ_BDTcLejgLctgL8CFdkegvily9hZD4c-Cv56B1muXenZ-RHfbYujEIQoP98Er2rbdDbfWxV0rXq5nuaStXUTR-EknLFXJW9UIeJ2wgZW60adFHHQzawmAlWSK1caIFhOuoz_gWce1wNfDUKeJ9KlwM_xlrZ2Xp1BLB8svuiIuhxkGE470OfEYQ59EfQKdWIAgRkIaCgYKAT4SARMSFQHGX2Mi9WvI_Ou23UsQ_80ElVQ5kg0206"; 

    $notification = [
        'title' => $title,
        'body' => $message,
        'sound' => 'default'
    ];

    $extraNotificationData = ["message" => $notification];

    $fcmNotification = [
        'to' => $token,
        'notification' => $notification,
        'data' => $extraNotificationData
    ];

    $headers = [
        'Authorization: key=' . $serverKey,
        'Content-Type: application/json'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
    $result = curl_exec($ch);
    curl_close($ch);

    // Optional: Log result for debugging
    // file_put_contents("fcm_log.txt", date("Y-m-d H:i:s") . " => " . $result . "\n", FILE_APPEND);
}
echo json_encode($res_msg);
mysqli_close($conn);
?>