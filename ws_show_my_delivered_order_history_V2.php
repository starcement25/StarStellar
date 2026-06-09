<?php
include "star_connection.php";
$engineer_master = "engineer_master";
$gift_master = "gift_master";
$gift_order_master ="gift_order_master";
$order_data = array();
$upload_dir = "gift_pic/";
$image_url = $server_url."gift_pic/";
$curr_datetime = date("Y-m-d H:i");
$page_no = $_POST["page_no"] ? $_POST["page_no"] : 1;
$the_engineer_id = $_POST["the_engineer_id"] ? addslashes(trim($_POST["the_engineer_id"])) : "";
$status_show = "DELIVERED";
$limit = 20;
$start_from = (($page_no-1)*$limit);
if($the_engineer_id!=""){
// $sql2 = "select * from $gift_order_master left join $gift_master on $gift_order_master.`gift_id`=$gift_master.`id` where $gift_order_master.`user_id`='$the_engineer_id' and $gift_order_master.`status`='$status_show' order by $gift_order_master.`g_order_id` desc limit $start_from,$limit";
// AND (go.status = 'DELIVERED' OR go.status = 'ACKNOWLEDGEMENT OF DELIVERY' OR go.status = 'COMPLAINT/FEEDBACK')
$sql2 = "SELECT go.*, gm.gift_title, gm.description, gm.gift_image, em.e_mobile
         FROM $gift_order_master go
         LEFT JOIN $gift_master gm ON go.gift_id = gm.id
         LEFT JOIN $engineer_master em ON go.user_id = em.eid
         WHERE go.user_id = '$the_engineer_id' 
         ORDER BY go.g_order_id DESC
         LIMIT $start_from, $limit";
//echo $sql2;

$res2 = mysqli_query($conn,$sql2);
$tot_res2 = mysqli_num_rows($res2);
if($tot_res2>0){
while($row2 = mysqli_fetch_assoc($res2)){
//$order_id = $row2["g_order_id"];
$order_id = $row2["order_id"];
$user_id = $row2["user_id"];
$remarks = $row2["remarks"];
$engineer_mobile = $row2["e_mobile"];
$gift_id = $row2["gift_id"];
$gift_title = $row2["gift_title"];
$gift_description = $row2["description"];
$gift_image = $row2["gift_image"] ? trim($row2["gift_image"]) : "";
if($gift_image!=''){
if(file_exists($upload_dir.$gift_image)){
$gift_image_url = $image_url.$gift_image;
}else{
$gift_image_url ="";
}
}else{
$gift_image_url ="";
}
$point_taken = $row2["point_taken"] ? trim($row2["point_taken"]) : 0;
$product_point = $row2["product_point"] ? trim($row2["product_point"]) : 0;
$tds = $row2["tds"] ? trim($row2["tds"]) : 0;
$point_taken_text = $point_taken." pts";
$product_point_text = $product_point." pts";
$tds_text = $tds." pts";
//$status = $row2["status"];
$status = $row2["support_status"];
$pending_reason = $row2["pending_reason"];
// If status is null, replace it with an empty string
$status = isset($row2["status"]) ? trim($row2["status"]) : "";
if($status == 'PENDING'){
$remarks = $pending_reason;
}
$city = $row2["city"];
$pin = $row2["pin"];
$state = $row2["state"];
$address = $row2["address"];
$datetime = $row2["datetime"] ? trim($row2["datetime"]) : "";
if($datetime!=""){
	$datetime_text = date("d/m/y",strtotime($datetime));
}else{
	$datetime_text ="";
}
$delivery_date_text ="";
$delivery_date = $row2["delivery_date"] ? trim($row2["delivery_date"]) : "";
if($delivery_date!=""){
	$delivery_date = str_replace("0000-00-00","",$delivery_date);
	if($delivery_date!=""){
	$delivery_date_text = date("d/m/y",strtotime($delivery_date));
	}
}

$amazon_order_id  = $row2["amazon_order_id"] ? trim($row2["amazon_order_id"]) : "";
$amazon_order_link  = $row2["amazon_order_link"] ? trim($row2["amazon_order_link"]) : "";
$is_order_received  = $row2["is_order_received"] ? trim($row2["is_order_received"]) : "NO";
if($status !='ORDER PLACED'){
	$amazon_order_link='';
}

$is_order_received='';
if($is_order_received === 'YES' && $status === 'DELIVERED') {
	$congratulatory_delivery_message = "Gift Name: " . $gift_title . " successfully delivered. - Star Stellar";
	sendSMSNotification($engineer_mobile, $congratulatory_delivery_message);
}
$acknowledgement_btn="NO";
$feedback_btn="NO";
if($status=='DELIVERED'){
	$acknowledgement_btn="YES";
}
if($status=='ACKNOWLEDGEMENT OF DELIVERY'){
	// If delivery_date is within last 3 days
	if ($delivery_date != "") {
		$today = date("Y-m-d");
		$datediff = (strtotime($today) - strtotime($delivery_date)) / 86400; // convert seconds to days

		if ($datediff >= 0 && $datediff <= 3) {
		$feedback_btn="YES";
		}
	}
}
$order_data[] = array("order_id"=>$order_id,"gift_id"=>$gift_id,"gift_title"=>$gift_title,"gift_description"=>$gift_description,"gift_image_url"=>$gift_image_url,"point_taken"=>$point_taken,"point_taken_text"=>$point_taken_text,"product_point_text"=>$product_point_text,"tds_text"=>$tds_text,"status"=>$status,"city"=>$city,"state"=>$state,"address"=>$address,"pin"=>$pin,"datetime"=>$datetime_text,"delivery_date"=>$delivery_date_text,"is_order_received"=>$is_order_received,"acknowledgement_btn"=>$acknowledgement_btn,"feedback_btn"=>$feedback_btn,"amazon_order_id"=>$amazon_order_id, "amazon_order_link"=>$amazon_order_link, "remarks"=>$remarks);
}
$res_data = array("process_status"=>"YES","process_message"=>"Success.","order_data"=>$order_data);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"No record found.","order_data"=>$order_data);
}


}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>