<?php
include "web_check.php";
include "star_connection.php";

$order_id = isset($_POST['order_id']) ? trim($_POST['order_id']) : '';

if ($order_id != '') {

    // Escape value for safety (since mysql_real_escape_string is supported in PHP 5.6)
    $safe_order_id = mysqli_real_escape_string($conn,$order_id);

    $sql = "SELECT * FROM order_feedback WHERE order_id = '".$safe_order_id."' and feedback_type ='ACKNOWLEDGEMENT'";
    $res = mysqli_query($conn,$sql);

    if (!$res) {
        echo "<p class='text-danger'>SQL Error: ".mysql_error()."</p>";
        exit;
    }

    if (mysqli_num_rows($res) > 0) {
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<div class='border-bottom mb-2 pb-2'>";
            echo "<p><strong>Acknowledgement:</strong> ".htmlspecialchars($row['feedback'])."</p>";
            echo "<p><strong>Date:</strong> ".htmlspecialchars($row['date_time'])."</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No feedback found for this order.</p>";
    }

} else {
    echo "<p>Invalid request.</p>";
}
?>
