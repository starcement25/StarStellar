<?php
include "star_connection.php";
$engineer_master = "engineer_master";
$res_msg = array();
$en_options = '<option value="">Select Engineer</option>';
$trn_te_code = $_POST["trn_te_code"] ? trim($_POST["trn_te_code"]) : "";
if($trn_te_code!=''){
	$sql5 = "select `eid`,`e_name` from $engineer_master where `te_code`='$trn_te_code' order by `e_name` asc ";
	$res5 = mysqli_query($conn,$sql5);
	$tot_res5 = mysqli_num_rows($res5);
	if($tot_res5>0){
		while($row5 = mysqli_fetch_assoc($res5)){
			$eid=$row5["eid"];
			$e_name=$row5["e_name"];
			$en_options .= '<option value="'.$eid.'">'.$e_name.'</option>';
		}
	}

$res_msg = array("process_sts"=>"YES","process_msg"=>"Success","en_options"=>$en_options);
}else{
	$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong.","en_options"=>$en_options);
}
echo json_encode($res_msg);
mysqli_close($conn);
?>