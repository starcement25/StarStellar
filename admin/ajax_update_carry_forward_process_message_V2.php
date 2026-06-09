<?php
include "star_connection.php";
$app_version = "app_version";
$res = array();
$str_carry_forward_process_message = isset($_POST["str_carry_forward_process_message"]) ? addslashes(trim($_POST["str_carry_forward_process_message"])) : "";
if ($str_carry_forward_process_message != "") {
    $sqlckrds = "select `the_value` from $app_setting_master where `the_key_name`='carry_forward_process_message'";
    $resckrds = mysql_query($sqlckrds);
    $totresckrds = mysql_num_rows($resckrds);
    $last_updated_datetime = date('Y-m-d H:i:s');

    if ($totresckrds > 0) {
        $sqlupd = "update $app_version set `the_value`='$str_carry_forward_process_message',`last_updated_datetime`='$last_updated_datetime' where `the_key_name`='carry_forward_process_message'";
	$resupd = mysql_query($sqlupd);
    } else {
        $sqlin = "insert into $app_version (`the_key_name`,`the_value`,`last_updated_datetime`) values ('carry_forward_process_message','$str_carry_forward_process_message','$last_updated_datetime')";
        $resin = mysql_query($sqlin);
    }

    $res = array("process_sts" => "YES", "process_message" => "Success");
} else {
    $res = array("process_sts" => "NO", "process_message" => "Something went wrong.");
}
mysql_close();
echo json_encode($res);
?>
