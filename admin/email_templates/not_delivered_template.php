<?php
// All variables must be passed from main file:
// $order_detail, $customer_name, $sap_code, $dealer_id, $zone, $request_time, $customer_message, $product_image
?>

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
                        Star Saathi – Support/Feedback Request
                    </td>
                </tr>

                <!-- BODY CONTENT -->
                <tr>
                    <td style="padding:25px; color:#333; font-size:14px; line-height:22px;">

                        Hello Support Team,<br><br>
                        A customer has requested assistance regarding a product they have already received.
                        The details are provided below.<br><br>

                        <!-- DETAILS BOX -->
                        <table width="100%" border="0" cellspacing="0" cellpadding="10" style="background:#f1f5ff; border-left:4px solid #c62828; border-radius:4px;">
                            <tr>
                                <td>

                                    <strong>Customer Name:</strong> <?php echo $customer_name; ?><br>
                                    <strong>SAP Code:</strong> <?php echo $sap_code; ?><br>
                                    <strong>Dealer ID:</strong> <?php echo $dealer_id; ?><br>
                                    <strong>Zone:</strong> <?php echo $zone; ?><br>
                                    <strong>Request Time:</strong> <?php echo $request_time; ?><br><br>

                                    <strong>Product Description:</strong><br>
                                    <?php echo $order_detail['gift_title']; ?><br><br>

                                    <!-- PRODUCT IMAGE -->
                                    <div style="text-align:center; margin-top:10px;">
                                        <img src="<?php echo $product_image; ?>" width="150" style="border-radius:6px;">
                                    </div>

                                </td>
                            </tr>
                        </table>

                        <br><br>

                        <strong>Customer Message:</strong>
                        <div style="background:#eef5ff; padding:15px; border-radius:6px; margin-top:5px;">
                            <?php echo nl2br($customer_message); ?>
                        </div>

                        <br>

                        Please look into the matter and assist the customer accordingly.<br><br>

                        <!-- BUTTON -->
                        <a href="https://your-support-dashboard-url" 
                           style="background:#b71c1c; padding:12px 20px; color:#fff; 
                                  text-decoration:none; border-radius:6px; font-weight:bold;">
                            Open Support Dashboard
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
