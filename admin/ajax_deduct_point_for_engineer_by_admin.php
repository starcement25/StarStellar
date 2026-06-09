<?php
session_start();
include "star_connection.php";
$engineer_master = "engineer_master";
$ledger_master = "ledger_master";
$log_table       = "engineer_point_action_log";
$action= "DEDUCT";
$res_msg = array();
$the_eid = $_POST["the_eid"] ? trim($_POST["the_eid"]) : "";
$added_point = $_POST["added_point"] ? trim($_POST["added_point"]) : 0;
$added_description = $_POST["added_description"] ? addslashes(trim($_POST["added_description"])) : "";
$admin_id = isset($_SESSION['start_stellar_admin']) ? $_SESSION['start_stellar_admin'] : 0;
 date_default_timezone_set('Asia/Kolkata');

$now = date("Y-m-d H:i:s");
if($the_eid!=''){
	if($added_point==''){
		$res_msg = array("process_sts"=>"NO","process_msg"=>"Please enter point.");
	}else if($added_description==''){
		$res_msg = array("process_sts"=>"NO","process_msg"=>"Please enter description.");
	}else{
		/* Fetch current point sk 16-12-25*/
		$sql = "SELECT e_points FROM $engineer_master WHERE eid='$the_eid'";
		$res = mysqli_query($conn, $sql);
		$row = mysqli_fetch_assoc($res);
		$old_point = (int)$row['e_points'];

		$sql1 = "select `e_points` from $engineer_master where `eid`='$the_eid'";
		$res1 = mysqli_query($conn,$sql1);
		$totres1 = mysqli_num_rows($res1);
		if($totres1>0){
			$row1 = mysqli_fetch_assoc($res1);
			$present_point = $row1["e_points"] ? trim($row1["e_points"]) : 0;
			if($present_point==""){
				$present_point = 0;
			}
			$calculated_point = ($present_point - $added_point);
			$sql5 = "update $engineer_master set `e_points`='$calculated_point' where `eid`='$the_eid'";
			$res5 = mysqli_query($conn,$sql5);
			$curr_datetime = date("Y-m-d H:i:s");
			$sqlin = "insert into $ledger_master (`user_id`,`ldgr_type`,`description`,`point_redeem`,`ldgr_datetime`,`remaining_balance`) values ('$the_eid','DEDUCTED_BY_ADMIN','$added_description','$added_point','$curr_datetime','$calculated_point')";
			$resin = mysqli_query($conn,$sqlin);
			/* Action Log */
			mysqli_query($conn,"
				INSERT INTO $log_table
				(engineer_id, action_type,  old_point, change_point, new_point, action_by, action_at)
				VALUES
				('$the_eid','$action','$old_point','$added_point','$calculated_point','$admin_id','$now')
			");
			$res_msg = array("process_sts"=>"YES","process_msg"=>"Success","calculated_point"=>$calculated_point);
		}else{
			$res_msg = array("process_sts"=>"NO","process_msg"=>"The engineer details doesn't exist.");
		}	
	}
}else{
$res_msg = array("process_sts"=>"NO","process_msg"=>"Something went wrong.");
}
echo json_encode($res_msg);
mysqli_close($conn);
?>