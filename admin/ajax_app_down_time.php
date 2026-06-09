
<?php
include "star_connection.php";
$engineer_master = "engineer_master";
// Include necessary database connection and other required files

if (isset($_GET['engineerId'])) {
    $engineerId = $_GET['engineerId'];

    // Query to get the App Down Time for the given engineer
    $appDownTimeQuery = "SELECT app_down_time FROM engineer_master WHERE eid = $engineerId";
    $appDownTimeResult = mysqli_query($conn, $appDownTimeQuery);

    if ($appDownTimeResult) {
        $appDownTime = mysqli_fetch_assoc($appDownTimeResult)['app_down_time'];

        // Return JSON response
        echo json_encode(['appDownTime' => $appDownTime]);
    } else {
        // Handle database error
        echo json_encode(['error' => 'Database error']);
    }
} else {
    // Handle missing parameter error
    echo json_encode(['error' => 'Missing parameter']);
}
?>
