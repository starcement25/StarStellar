<?php
include "star_connection.php";
$product_master = "product_master";
$product_data = array();
//$sqlall = "select * from $product_master order by `prod_id` asc";
$sqlall = "SELECT * FROM $product_master WHERE product_status = 'active' ORDER BY `prod_id` ASC";
$resall = mysqli_query($conn,$sqlall);
$totall = mysqli_num_rows($resall);
if($totall>0){
	while($row11=mysqli_fetch_assoc($resall)){
		$prod_id = $row11["prod_id"];
		$prod_name = $row11["prod_name"];
		$product_data[] = array("prod_id"=>$prod_id,"prod_name"=>$prod_name);
	}	

$res_data = array("process_status"=>"YES","process_message"=>"Success.","product_data"=>$product_data);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"No product found.","product_data"=>$product_data);
}
echo json_encode($res_data);
mysqli_close($conn);
?>