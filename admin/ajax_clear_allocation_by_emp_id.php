<?php
include "star_connection.php";
$changepassword = "changepassword";
$the_id = $_REQUEST["clemplyid"] ? addslashes(trim($_REQUEST["clemplyid"])) : "";
if($the_id!=""){
$sqlall = "select * from $changepassword where `emp_code`='$the_id'";
$resall = mysqli_query($conn,$sqlall);
$totall = mysqli_num_rows($resall);
if($totall>0){
	$sql="update $changepassword set `deviceid`='',`registrationid`='' where `emp_code`='$the_id'";
	$res=mysqli_query($conn,$sql);
}
$res_data = array("process_status"=>"YES","process_message"=>"Employee device allocation has been cleared successfully.");
}else{	
	$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong. Please try later.");
}	
echo json_encode($res_data);
mysqli_close($conn);
?>