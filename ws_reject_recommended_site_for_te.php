<?php
include "star_connection.php";
$te_master = "te_master";
$engineer_master = "engineer_master";
$ledger_master = "ledger_master";
$recommended_site_master = "recommended_site_master";
$te_code = $_POST["te_code"] ? addslashes(trim($_POST["te_code"])) : "";
$r_site_id = $_POST["r_site_id"] ? addslashes(trim($_POST["r_site_id"])) : "";
$comments = $_POST["comments"] ? addslashes(trim($_POST["comments"])) : "";

if ($te_code != "" && $r_site_id != '') {

    $sql2 = "SELECT te_code FROM $te_master WHERE te_code='$te_code'";
    $res2 = mysqli_query($conn, $sql2);
    $tot_res2 = mysqli_num_rows($res2);

    if ($tot_res2 > 0) {
        // $sql3 = "SELECT rs.r_site_id, rs.r_site_name, rs.r_engineer_id, eng.e_mobile FROM $recommended_site_master AS rs INNER JOIN $engineer_master AS eng ON rs.r_engineer_id = eng.e_id WHERE rs.r_site_id='$r_site_id' AND rs.r_te_code='$te_code'"; 
		$sql3 = "SELECT rs.r_site_id, rs.r_site_name, rs.r_engineer_id, eng.e_mobile FROM $recommended_site_master AS rs INNER JOIN $engineer_master AS eng ON rs.r_engineer_id = eng.eid WHERE rs.r_site_id='$r_site_id' AND rs.r_te_code='$te_code'";

		//echo $sql3;
        $res3 = mysqli_query($conn, $sql3);
        $tot_res3 = mysqli_num_rows($res3);

        if ($tot_res3 > 0) {
            $row3 = mysqli_fetch_assoc($res3);
            $e_mobile = $row3['e_mobile'];
			$r_site_name = $row3['r_site_name'];
            $r_submission_date = date("Y-m-d H:i:s");
            $r_te_interaction_date = date("Y-m-d");

            $sql4 = "UPDATE $recommended_site_master 
                     SET r_status='REJECTED',
                         r_te_interaction_comment='$comments',
                         r_te_interaction_date='$r_te_interaction_date',
                         r_last_updated_datetime='$r_submission_date' 
                     WHERE r_site_id='$r_site_id' AND r_te_code='$te_code'";
            $res4 = mysqli_query($conn, $sql4);

            $res_data = array("process_status" => "YES", "process_message" => "The site has been rejected successfully.");

            // Send SMS notification to the engineer
            $congratulatory_message_eng = "Site Name: " . $r_site_name . " successfully Approved/Rejected: Rejected. - Star Stellar";
								sendSMSNotification($e_mobile, $congratulatory_message_eng);
        } else {
            $res_data = array("process_status" => "NO", "process_message" => "The provided site ID does not belong to this engineer.");
        }
    } else {
        $res_data = array("process_status" => "NO", "process_message" => "The TE Code doesn't exist.");
    }
} else {
    $res_data = array("process_status" => "NO", "process_message" => "Something went wrong. Please try later.");
}
echo json_encode($res_data);
mysqli_close($conn);
?>
