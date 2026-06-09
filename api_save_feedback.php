<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


header('Content-Type: application/json');

include "star_connection.php";

// ----------- Helper function for response -----------
function send_response($status, $message) {
    echo json_encode(array(
        "status" => $status,
        "message" => $message
    ));
    exit;
}

// ----------- Set timezone to Kolkata -----------
date_default_timezone_set('Asia/Kolkata');

// ----------- Input Validation -----------
$order_id      = isset($_POST['order_id']) ? trim($_POST['order_id']) : '';
$user_id       = isset($_POST['user_id']) ? trim($_POST['user_id']) : '';
$gift_id       = isset($_POST['gift_id']) ? trim($_POST['gift_id']) : '';
$feedback_type = isset($_POST['feedback_type']) ? trim($_POST['feedback_type']) : '';
$feedback      = isset($_POST['feedback']) ? trim($_POST['feedback']) : '';

if ($order_id == '' || $user_id == '' || $gift_id == '' || $feedback_type == '') {
    send_response("error", "All fields are required.");
}

$allowed_types = array('ACKNOWLEDGEMENT', 'FEEDBACK');
if (!in_array(strtoupper($feedback_type), $allowed_types)) {
    send_response("error", "Invalid feedback_type. Allowed values: ACKNOWLEDGEMENT, FEEDBACK.");
}

// ----------- Escape inputs -----------
$order_id      = mysqli_real_escape_string($conn, $order_id);
$user_id       = mysqli_real_escape_string($conn, $user_id);
$gift_id       = mysqli_real_escape_string($conn, $gift_id);
$feedback_type = mysqli_real_escape_string($conn, strtoupper($feedback_type));
$feedback      = mysqli_real_escape_string($conn, $feedback);

// ----------- Fetch order details -----------
$order_check_sql = "SELECT status, delivery_date FROM gift_order_master WHERE order_id='$order_id'";
$order_check_res = mysqli_query($conn, $order_check_sql);

if (!$order_check_res || mysqli_num_rows($order_check_res) == 0) {
    send_response("error", "Order not found.");
}

$order_row = mysqli_fetch_assoc($order_check_res);

// ----------- Setting: number of open days -----------
$acknowledged_module_open_days = get_value_by_setting_key($conn, "acknowledged_module_open_days");
if ($acknowledged_module_open_days == "" || !is_numeric($acknowledged_module_open_days)) {
    $acknowledged_module_open_days = 0; // fallback if not set
}

// ----------- Apply time restriction only for FEEDBACK -----------
if ($feedback_type == 'FEEDBACK') {
    $delivery_date = $order_row['delivery_date'];

    if ($delivery_date == '' || $delivery_date == '0000-00-00') {
        send_response("error", "Delivery date not available for this order.");
    }

    $delivery_timestamp = strtotime($delivery_date);
    $current_timestamp  = time();
    $days_diff = floor(($current_timestamp - $delivery_timestamp) / (60 * 60 * 24));

    if ($days_diff > $acknowledged_module_open_days) {
        send_response("error", "You cannot submit FEEDBACK after $acknowledged_module_open_days days from delivery date.");
    }

    // ----------- Ensure ACKNOWLEDGEMENT is submitted before FEEDBACK -----------
    $ack_check_sql = "SELECT id FROM order_feedback 
                      WHERE order_id='$order_id' AND user_id='$user_id' AND feedback_type='ACKNOWLEDGEMENT'";
    $ack_check_res = mysqli_query($conn, $ack_check_sql);
    if (!$ack_check_res || mysqli_num_rows($ack_check_res) == 0) {
        send_response("error", "Please submit ACKNOWLEDGEMENT before submitting FEEDBACK.");
    }
}

// ----------- Prevent duplicate feedback per user/order/type -----------
$check_sql = "SELECT id FROM order_feedback 
              WHERE order_id='$order_id' AND user_id='$user_id' AND feedback_type='$feedback_type'";
$check_res = mysqli_query($conn, $check_sql);
if ($check_res && mysqli_num_rows($check_res) > 0) {
    send_response("error", "Feedback already submitted for this order and type.");
}

// ----------- Insert Feedback -----------
$current_time = date('Y-m-d H:i:s');
$sql = "INSERT INTO order_feedback (order_id, user_id, gift_id, feedback_type, feedback, date_time) 
        VALUES ('$order_id', '$user_id', '$gift_id', '$feedback_type', '$feedback', '$current_time')";

if (mysqli_query($conn, $sql)) {
    $insert_id = mysqli_insert_id($conn);

    // ----------- Update order status -----------
    if ($feedback_type == 'ACKNOWLEDGEMENT') {
        $status = "ACKNOWLEDGEMENT OF DELIVERY";
        $email_subject="Order Not Delivered - Acknowledgement Alert";
    } elseif ($feedback_type == 'FEEDBACK') {
        $status = "COMPLAINT/FEEDBACK";
        $email_subject="Feedback Alert";

    }

    $update_sql = "UPDATE gift_order_master 
                   SET status='$status' 
                   WHERE order_id='$order_id'";
    mysqli_query($conn, $update_sql);

    // ----------- SEND EMAIL WHEN ACKNOWLEDGEMENT = Not Delivered -----------
        if (($feedback_type == 'ACKNOWLEDGEMENT' && strtolower($feedback) == 'not delivered') || ($feedback_type == 'FEEDBACK')) {

            // Fetch order & product details
            $detail_sql = "
                SELECT gom.order_id, gom.order_id,gom.datetime, gom.delivery_date, gom.status, gom.gift_title, pm.e_name, pm.e_mobile, 
                gom.point_taken FROM gift_order_master gom LEFT JOIN engineer_master pm ON gom.user_id = pm.eid WHERE 
                gom.order_id = '$order_id'
            ";

            $detail_res = mysqli_query($conn, $detail_sql);
            $order_detail = mysqli_fetch_assoc($detail_res);
            $e_name    = $order_detail['e_name'];
            $e_mobile         = $order_detail['e_mobile'];
           
            $request_time     = $order_detail['delivery_date'];;
            $customer_message = $feedback; // or your variable
            $order_id    = $order_detail['order_id']; // adjust accordingly
            $datetime    = $order_detail['datetime']; // adjust accordingly
            // Prepare HTML email template
          $email_html = '

            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Support / Feedback Request</title>
            </head>

            <body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;">

            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center">

                        <table width="650" border="0" cellspacing="0" cellpadding="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">

                            <!-- HEADER -->
                            <tr>
                                <td style="background:#b71c1c; padding:18px; color:#fff; font-size:20px; font-weight:bold;">
                                    Star Stellar – Not delivered Request / Feedback Request
                                </td>
                            </tr>

                            <!-- BODY CONTENT -->
                            <tr>
                                <td style="padding:25px; color:#333; font-size:14px; line-height:22px;">

                                    Hello Support Team,<br><br>
                                    A customer has requested assistance regarding a product they have not received.
                                    The details are provided below.<br><br>

                                    <!-- DETAILS BOX -->
                                    <table width="100%" border="0" cellspacing="0" cellpadding="10" style="background:#f1f5ff; border-left:4px solid #c62828; border-radius:4px;">
                                        <tr>
                                            <td>
                                                <strong>Request Type:</strong> '.$status.'<br><br>
                                                <strong>Engineer Name:</strong> '.$e_name.'<br>
                                                <strong>Engineer Mobile:</strong> '.$e_mobile.'<br>
                                                <strong>Order ID:</strong> '.$order_id.'<br>
                                                <strong>Order Date:</strong> '.$datetime.'<br>
                                                
                                                <strong>Delivery Date:</strong> '.$request_time.'<br><br>

                                                <strong>Product Description:</strong><br>
                                                '.$order_detail["gift_title"].'<br><br>

                                                <!-- PRODUCT IMAGE -->
                                               

                                            </td>
                                        </tr>
                                    </table>

                                    <br><br>

                                    <strong>Customer Message:</strong>
                                    <div style="background:#eef5ff; padding:15px; border-radius:6px; margin-top:5px;">
                                        '.nl2br($customer_message).'
                                    </div>

                                    <br>

                                    Please look into the matter and assist the customer accordingly.<br><br>

                                    <!-- BUTTON -->
                                    <a href="starstellar.com/admin/"
                                    style="background:#b71c1c; padding:12px 20px; color:#fff;
                                            text-decoration:none; border-radius:6px; font-weight:bold;">
                                        Open Dashboard
                                    </a>

                                    <br><br>

                                </td>
                            </tr>

                            <!-- FOOTER -->
                            <tr>
                                <td style="background:#f7f7f7; text-align:center; padding:12px; color:#777; font-size:12px;">
                                    This is an automated notification. Please do not reply to this email.
                                </td>
                            </tr>

                        </table>

                    </td>
                </tr>
            </table>

            </body>
            </html>

            ';


           /* $email_html = "<html>
                    <body>
                        <h3>Order Not Delivered</h3>
                        <p><strong>Order ID:</strong> <?php echo $order_detail['order_id']; ?></p>
                        <p><strong>Gift:</strong> <?php echo $order_detail['gift_title']; ?></p>
                        <p><strong>Points Used:</strong> <?php echo $order_detail['point_require']; ?></p>
                        <p><strong>Status:</strong> Not Delivered</p>
                    </body>
                    </html>";*/

            // List of recipients (array)
          
            $to_email =get_value_by_setting_key($conn,"loyalty_not_delivered");
            $to_email_arr = array("$to_email");;

            // Load PHPMailer
           require 'PHPMailer/PHPMailer.php';
            require 'PHPMailer/SMTP.php';
            require 'PHPMailer/Exception.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            try {

                // Enable debug 
                $mail->SMTPDebug = 2;  
                $mail->Debugoutput = 'error_log';

                $mail->isSMTP();
                $mail->Host = "cloudmail2.up99plus.com";
                $mail->Port = 25;
                $mail->SMTPAuth = true;
                $mail->Username = "starcement@cloudmail.up99plus.com";
                $mail->Password = "K2TTvLxATyULV2um";
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;

                $mail->setFrom('starcement@cloudmail.up99plus.com', 'Starsaathi');

                foreach ($to_email_arr as $to_email_arr_val) {
                    if (filter_var(trim($to_email_arr_val), FILTER_VALIDATE_EMAIL)) {
                        $mail->addAddress(trim($to_email_arr_val));
                    }
                }

                $mail->isHTML(true);
                $mail->Subject = $email_subject;
                $mail->Body = $email_html;

                $mail->send();

            } catch (Exception $e) {
                error_log("MAIL ERROR → " . $mail->ErrorInfo);
                send_response("error", "Email sending failed: " . $mail->ErrorInfo);
            }
        }



    send_response("success", "Feedback saved successfully. ID: " . $insert_id);
} else {
    send_response("error", "Database error: " . mysqli_error($conn));
}
?>
