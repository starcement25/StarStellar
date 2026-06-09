<?php
include "star_connection.php";

set_time_limit(0); // no timeout
ini_set('memory_limit', '-1'); // no memory limit

$table_name = "categories";
$filename = "categories_" . date("Y-m-d_H-i-s") . ".csv";

// headers
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// open output
$output = fopen("php://output", "w");

// header row
fputcsv($output, ['id', 'name', 'status']);

// chunk settings
$limit = 1000;
$offset = 0;

while (true) {

    $sql = "SELECT id, name, status FROM $table_name 
            ORDER BY id ASC 
            LIMIT $offset, $limit";

    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) == 0) {
        break;
    }

    while ($row = mysqli_fetch_assoc($res)) {
        fputcsv($output, $row);
    }

    // flush output to browser (VERY IMPORTANT)
    fflush($output);

    $offset += $limit;
}

// close
fclose($output);
mysqli_close($conn);
exit;
?>