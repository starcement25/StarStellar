<?php
include "star_connection.php"; 
$order_query = "order_query";

$response = array();
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";

$te_code = $_REQUEST["te_code"] ? addslashes(trim($_REQUEST["te_code"])) : "";
//$year_month=$_REQUEST["year_month"] ? addslashes(trim($_REQUEST["year_month"])) : "";
$start_date = $_REQUEST["start_date"] ? addslashes(trim($_REQUEST["start_date"])) :"";
$end_date = $_REQUEST["end_date"] ? addslashes(trim($_REQUEST["end_date"])) : "";

$order_query_date = date("Y-m-d H:i:s");
$cur_year_month=date("Y-m");
list($year, $month) = explode('-', $cur_year_month);
	
if($start_date!="" && $end_date!=""){
		$date_qry = " AND `date_and_time` between '".$start_date." ".$frm_hrs."' and '".$end_date." ".$to_hrs."'";
}else{
		$date_qry = " AND YEAR(date_and_time) = '$year' AND MONTH(date_and_time) = '$month'";
}
if ($te_code == "") {
    $response["process_status"] = "NO";
    $response["process_message"] = "Te Code is missing.";
} else {
    $sqlin = "SELECT id, date_and_time, order_id, engineer_code, dns_prod_code, prod_name, qty_bags, date_of_lifting, remarks ,status_from_app,status_remarks
              FROM $order_query 
              WHERE linked_te_code='$te_code' $date_qry 
              ORDER BY date_and_time DESC";

    $resin = mysqli_query($conn,$sqlin);

    if (!$resin) {
        $response["process_status"] = "ERROR";
        $response["process_message"] = "Error: " . mysql_error();
    } else {
        if (mysqli_num_rows($resin) > 0) {
            $response["process_status"] = "YES";
            $response["process_message"] = "Order Query fetched successfully.";
            $order_query_data = array();
            while ($row = mysqli_fetch_assoc($resin)) {
                $id = $row['id'];
                $order_id = $row['order_id'];
                $engineer_code = $row['engineer_code'];
                $dns_prod_code = $row['dns_prod_code'];
                $prod_name = $row['prod_name'];
                $qty_bags = $row['qty_bags'];
                $date_and_time = $row['date_and_time'];
                $date_of_lifting = $row['date_of_lifting'];
                $remarks = $row['remarks'];
                $status_from_app = $row['status_from_app'];
                $status_remarks = $row['status_remarks'];

                $sqlengineer = "SELECT e_name FROM engineer_master WHERE eid='".$engineer_code."'";
                $rsengineer = mysqli_query($conn,$sqlengineer);
                $rowengineer = mysqli_fetch_array($rsengineer);
                $e_name = $rowengineer['e_name'];

                if (is_null($status_from_app)) {
                    $status_from_app="";
                }
                if (is_null($status_remarks)) {
                    $status_remarks="";
                }

                $order_query_data[] = array(
                    "order_query_id" => $id,
                    "order_id" => $order_id,
                    "prod_name" => $prod_name,
                    "qty_bags" => $qty_bags,
                    "date_and_time" => $date_and_time,
                    "e_name" => $e_name,
                    "date_of_lifting" => $date_of_lifting,
                    "remarks" => $remarks,
                    "status_from_app" => $status_from_app,
                    "status_remarks" => $status_remarks

                );
            }

            $response["order_query_data"] = $order_query_data;
        } else {
            $response["process_status"] = "NO";
            $response["process_message"] = "No Order Query data found for the Te.";
        }
    }
}

echo json_encode($response);
?>
