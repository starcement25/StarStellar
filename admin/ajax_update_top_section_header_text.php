<?php
include "star_connection.php";
$home_screen_text_for_engineer = "home_screen_text_for_engineer";
$res = array();
$top_section_header_text = $_POST["top_section_header_text"] ? addslashes(trim($_POST["top_section_header_text"])) : "";
$sqlupd = "update $home_screen_text_for_engineer set `top_section_header_text`='$top_section_header_text'";
$resupd = mysqli_query($conn,$sqlupd);
$res = array("process_sts"=>"YES","process_message"=>"Success");
mysqli_close($conn);
echo json_encode($res);
?>