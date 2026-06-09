<?php
include "star_connection.php";
$product_master = "product_master";
$res = array();
$sel_prod_id = $_POST["sel_prod_id"] ? addslashes(trim($_POST["sel_prod_id"])) : "";
$the_prod_point = $_POST["the_prod_point"] ? addslashes(trim($_POST["the_prod_point"])) : "";

if($sel_prod_id!=""){
$sqlckrds = "select `prod_id` from $product_master where `prod_id`='$sel_prod_id'";
$resckrds = mysqli_query($conn,$sqlckrds);
$totresckrds = mysqli_num_rows($resckrds);
if($totresckrds>0){
	$sqlupd = "update $product_master set `point_per_bag`='$the_prod_point' where `prod_id`='$sel_prod_id'";
	$resupd = mysqli_query($conn,$sqlupd);
	
	$res = array("process_sts"=>"YES","process_message"=>"Success");
}else{
	$res = array("process_sts"=>"NO","process_message"=>"Product not found.");
}

}else{
$res = array("process_sts"=>"NO","process_message"=>"Something went wrong.");	
}
mysqli_close($conn);
echo json_encode($res);
?>