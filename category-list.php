<?php
include "star_connection.php";

$gift_master = "gift_master";
$category_master = "categories";

$category_data = array();

$page_no = isset($_POST["page_no"]) ? (int)$_POST["page_no"] : 1;
$limit = 20;
$start_from = ($page_no - 1) * $limit;

// Correct total count
$count_sql = "SELECT COUNT(*) as total FROM $category_master WHERE status = 1";
$count_res = mysqli_query($conn, $count_sql);
$total_records = mysqli_fetch_assoc($count_res)['total'];

// Main query
$sql = "SELECT 
            c.id AS category_id,
            c.name AS category_name,
            c.status AS category_status,
            COUNT(gm.id) AS gift_count
        FROM $category_master c
        LEFT JOIN $gift_master gm 
            ON gm.category_id = c.id 
            AND UPPER(gm.status) = 'ACTIVE'
        WHERE c.status = 1
        GROUP BY c.id, c.name, c.status
        ORDER BY c.name ASC
        LIMIT $start_from, $limit";

$res = mysqli_query($conn, $sql);

if (!$res) {
    die("Query Error: " . mysqli_error($conn)); 
}

$tot_res = mysqli_num_rows($res);

if ($tot_res > 0) {

    while ($row = mysqli_fetch_assoc($res)) {

        $category_data[] = array(
            "category_id"   => $row["category_id"],
            "category_name" => $row["category_name"],
            "status"        => $row["category_status"],
            "gift_count"    => (int)$row["gift_count"]
        );
    }

    $res_data = array(
        "process_status" => "YES",
        "process_message" => "Success.",
        "total_records" => (int)$total_records,
        "current_page" => $page_no,
        "category_data" => $category_data
    );

} else {

    $res_data = array(
        "process_status" => "NO",
        "process_message" => "No record found.",
        "total_records" => (int)$total_records,
        "current_page" => $page_no,
        "category_data" => $category_data
    );
}

echo json_encode($res_data);
mysqli_close($conn);
?>