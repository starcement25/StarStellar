<?php
include "star_connection.php";
date_default_timezone_set('Asia/Kolkata');
$te_master = "te_master";
$engineer_master = "engineer_master";
$engineer_data = array();
$upload_dir = "en_profile_pic/";
$image_url = $server_url."en_profile_pic/";
$curr_datetime = date("Y-m-d H:i");
$tot_res2 = 0;
$te_code = $_POST["te_code"] ? addslashes(trim($_POST["te_code"])) : "";
if($te_code!=""){
$sql2 = "SELECT $engineer_master.`eid`,$engineer_master.`e_mobile`,$engineer_master.`e_name`,$engineer_master.`e_city_town`,$engineer_master.`e_profile_image` FROM $engineer_master where $engineer_master.`te_code`='$te_code' and $engineer_master.`status_by_te`='PENDING' order by $engineer_master.`e_name` asc";
$res2 = mysqli_query($conn,$sql2);
$tot_res2 = mysqli_num_rows($res2);
if($tot_res2>0){
while($row2 = mysqli_fetch_assoc($res2)){
$eid = $row2["eid"];
$e_name = $row2["e_name"];
$e_mobile = $row2["e_mobile"];
$e_city_town = $row2["e_city_town"] ? trim($row2["e_city_town"]) : "";
$e_profile_image = $row2["e_profile_image"] ? trim($row2["e_profile_image"]) : "";
if($e_profile_image!=''){
if(file_exists($upload_dir.$e_profile_image)){
$e_profile_image_url = $image_url.$e_profile_image;
}else{
$e_profile_image_url ="";
}
}else{
$e_profile_image_url ="";
}
$engineer_data[] = array("eid"=>$eid,"e_name"=>$e_name,"e_mobile"=>$e_mobile,"e_city_town"=>$e_city_town,"e_profile_image_url"=>$e_profile_image_url);
}
$res_data = array("process_status"=>"YES","process_message"=>"Success.","total_record"=>$tot_res2,"engineer_data"=>$engineer_data);
}else{
$res_data = array("process_status"=>"NO","process_message"=>"No new record found.","total_record"=>$tot_res2,"engineer_data"=>$engineer_data);
}
}else{
$res_data = array("process_status"=>"NO","process_message"=>"Something went wrong.","total_record"=>$tot_res2,"engineer_data"=>$engineer_data);
}
echo json_encode($res_data);
mysqli_close($conn);
?>