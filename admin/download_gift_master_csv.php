<?php
include "web_check.php";
include "star_connection.php";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=gift_master_full_data.csv');

$output = fopen('php://output', 'w');

$sql = "SELECT id,gift_title,point_require,status FROM gift_master ORDER BY id DESC";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {


    $fields = array_keys($result->fetch_assoc());

   
    fputcsv($output, $fields);

   
    $result->data_seek(0);

    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
exit;
