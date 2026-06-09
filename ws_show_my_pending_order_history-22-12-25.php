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
$status_show = "PENDING";
$limit = 20;
$start_from = (($page_no-1)*$limit);
if($the_engineer_id!=""){
$sql2 = "select * from $gift_order_master left join $gift_master on $gift_order_master.`gift_id`=$gift_master.`id` where $gift_order_master.`user_id`='$the_engineer_id' and ($gift_order_master.`status`='$status_show' OR $gift_order_master.`status`='ORDER PLACED') order by $gift_order_master.`g_order_id` desc limit $start_from,$limit";
$res2 = mysqli_query($conn,$sql2);
$tot_res2 = mysqli_num_rows($res2);
if($tot_res2>0){
while($row2 = mysqli_fetch_assoc($res2)){
$order_id = $row2["g_order_id"];
$user_id = $row2["user_id"];
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
$point_taken_text = $point_taken." pts";
$status = $row2["status"];
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
$delivery_date = $row2["delivery_date"] ? trim($row2["delivery_date"]) : "";
if($delivery_date!=""){
	$delivery_date_text = date("d/m/y",strtotime($delivery_date));
}else{
	$delivery_date_text ="";
}
$order_data[] = array("order_id"=>$order_id,"gift_id"=>$gift_id,"gift_title"=>$gift_title,"gift_description"=>$gift_description,"gift_image_url"=>$gift_image_url,"point_taken"=>$point_taken,"point_taken_text"=>$point_taken_text,"status"=>$status,"city"=>$city,"state"=>$state,"address"=>$address,"pin"=>$pin,"datetime"=>$datetime_text,"expected_delivery_date"=>$delivery_date_text);
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