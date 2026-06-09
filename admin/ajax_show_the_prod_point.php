<?php
include "star_connection.php";
$product_master = "product_master";
$res = array();
$sel_prod_id = $_POST["sel_prod_id"] ? addslashes(trim($_POST["sel_prod_id"])) : "";
$prod_point = 0;
if($sel_prod_id!=""){
$sqlckrds = "select `prod_id`,`point_per_bag` from $product_master where `prod_id`='$sel_prod_id'";
$resckrds = mysqli_query($conn,$sqlckrds);
$totresckrds = mysqli_num_rows($resckrds);
if($totresckrds>0){
$rowckrds = mysqli_fetch_assoc($resckrds);
$prod_point = $rowckrds["point_per_bag"] ? trim($rowckrds["point_per_bag"]) : 0;
if($prod_point==""){
$prod_point = 0;	
}
$res = array("process_sts"=>"YES","process_message"=>"Success","prod_point"=>$prod_point);
}else{
$res = array("process_sts"=>"NO","process_message"=>"Product not found.","prod_point"=>$prod_point);
}

}else{
$res = array("process_sts"=>"NO","process_message"=>"Something went wrong.","prod_point"=>$prod_point);	
}
mysqli_close($conn);
echo json_encode($res);
?>