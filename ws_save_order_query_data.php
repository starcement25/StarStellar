<?php
include "star_connection.php";
$order_query   = "order_query";
$product_master = "product_master";
$res_data = array();


$engineer_code = $_REQUEST["engineer_code"] ? addslashes(trim($_REQUEST["engineer_code"])) : "";
$order_date_time = date("Y-m-d H:i:s");
$order_query_data = $_REQUEST["order_query_data"] ? $_REQUEST["order_query_data"] : array();
if (count($order_query_data) > 0) {

	foreach ($order_query_data as $ki => $order_query_data_val) {
		$order_id  = $order_query_data_val["order_id"] ? addslashes(trim($order_query_data_val["order_id"])) : "";
		$linked_te_code  = $order_query_data_val["linked_te_code"] ? addslashes(trim($order_query_data_val["linked_te_code"])) : "";
		$dns_prod_code  = $order_query_data_val["dns_prod_code"] ? addslashes(trim($order_query_data_val["dns_prod_code"])) : "";
		$prod_name  = $order_query_data_val["prod_name"] ? addslashes(trim($order_query_data_val["prod_name"])) : "";
		$qty_bags = $order_query_data_val["qty_bags"] ? addslashes(trim($order_query_data_val["qty_bags"])) : "";
		$date_of_lifting = $order_query_data_val["date_of_lifting"] ? addslashes(trim($order_query_data_val["date_of_lifting"])) : "";
		$remarks = $order_query_data_val["remarks"] ? addslashes(trim($order_query_data_val["remarks"])) : "";
		date_default_timezone_set('Asia/Kolkata'); // Set the timezone to IST

		$sqlin = "insert into $order_query (`date_and_time`,`order_id`,engineer_code,`linked_te_code`,`dns_prod_code`,`prod_name`,`qty_bags`, `date_of_lifting`, `remarks`) values('$order_date_time','$order_id','$engineer_code','$linked_te_code','$dns_prod_code','$prod_name','$qty_bags', '$date_of_lifting', '$remarks')";
		$resin = mysqli_query($conn,$sqlin);
		
		
		/*if($resin){
$created_last_id = mysql_insert_id();
$new_order_id_val = str_pad($created_last_id, 7, "0", STR_PAD_LEFT);
$apporderno = "POP".$new_order_id_val;
${'apporderno'.$cntslno}=$apporderno;
$sqlupd1 = "update $t_apperpdo_pop set `APPORDERNO`='$apporderno' where `id`='$created_last_id'";
$resupd1 = mysql_query($sqlupd1);
$cntslno++;
}*/
		

	}


	$res_data = array("process_status" => "YES", "process_message" => "Order Query successfully saved.");
} else {
	$res_data = array("process_status" => "NO", "process_message" => "Failed to save Order Query.");
}
echo json_encode($res_data);
//mysql_close();
//For SFA INSERT
mysqli_close($conn);
?>