<?php
//include "web_check.php";
include "star_connection.php";
$recommended_site_master = "recommended_site_master";
$te_master = "te_master";
$engineer_master = "engineer_master";
$branch_master = "branch_master";
$support_master = "support_master";
header('Content-Type: application/json');
$dataArray = array();

// Define a function to handle query errors
function executeQuery($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        // Handle the query error here (e.g., log, return an error response)
        die("Query execution failed: " . mysqli_error($conn));
    }
    return $result;
}

$sqlbranchcode = "select branch_code,branch_name,branch_state,zone from $branch_master order by branch_name asc ";
$resbranchcode = executeQuery($conn, $sqlbranchcode);

if (mysqli_num_rows($resbranchcode) > 0) {
    while ($row = mysqli_fetch_assoc($resbranchcode)) {
        $dataArray[] = array(
            'branch_code' => $row["branch_code"],
            'branch_name' => $row["branch_name"],
            'branch_state' => $row["branch_state"],
            'zone' => $row["zone"]
        );
    }

    // Close the database connection if needed
    // mysqli_close($conn);

    // Encode the data array as JSON
    $jsonResponse = json_encode($dataArray);

    // Set the response headers to indicate JSON content
    

    // Output the JSON response
    echo $jsonResponse;
} else {
    echo json_encode(array('message' => 'Record not found'));
}
?>
