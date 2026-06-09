<?php
function base64($data){
return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
}


function pushNotificationInIOS($noti_mode,$message_body,$noty_title,$deviceToken,$noty_image){
$pn_data_arr = array("sts"=>"","error_msg"=>"");
$error_msg = "";
$pnres = "FALSE";
$keyfile = 'AuthKey_A4PUG4TL3P.p8';               # <- Your AuthKey file
$keyid = 'A4PUG4TL3P';                            # <- Your Key ID
$teamid = 'LTCHGYC6BS';                           # <- Your Team ID (see Developer Portal)
$bundleid = 'com.forcepower.StarStellar';         # <- Your Bundle ID
if($noti_mode=="development"){
$url = 'https://api.development.push.apple.com';  # <- development url, or use http://api.push.apple.com for production environment
}else{
$url = 'https://api.push.apple.com';	
}
$arr_msg = array();
$arr_msg["aps"]["alert"] = array("title"=>$noty_title,"body"=>$message_body);
$arr_msg["aps"]["sound"] = "default";
//$arr_msg["aps"]["badge"] = 1;
$arr_msg["aps"]["mutable-content"] = 1;
if($noty_image!=""){
$arr_msg['mediaUrl'] = $noty_image;
}
$message = json_encode($arr_msg);

$key = openssl_pkey_get_private('file://'.$keyfile);

$header["alg"] = "ES256";
$header["kid"] = $keyid;

$claims["iss"] = $teamid;
$claims["iat"] = time();

$header_encoded = base64($header);
$claims_encoded = base64($claims);

$signature = '';
openssl_sign($header_encoded . '.' . $claims_encoded, $signature, $key, 'sha256');
$jwt = $header_encoded . '.' . $claims_encoded . '.' . base64_encode($signature);

// only needed for PHP prior to 5.5.24
if (!defined('CURL_HTTP_VERSION_2_0')) {
define('CURL_HTTP_VERSION_2_0', 3);
}

$http2ch = curl_init();
curl_setopt_array($http2ch, array(
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
CURLOPT_URL => "$url/3/device/$deviceToken",
CURLOPT_PORT => 443,
CURLOPT_HTTPHEADER => array(
"apns-topic: {$bundleid}",
"authorization: bearer $jwt"
),
CURLOPT_POST => TRUE,
CURLOPT_POSTFIELDS => $message,
CURLOPT_RETURNTRANSFER => TRUE,
CURLOPT_TIMEOUT => 30,
CURLOPT_HEADER => 1
));

$result = curl_exec($http2ch);
if ($result === FALSE) {
$error_msg = curl_error($http2ch);
$error_msg = stripslashes($error_msg);
$pn_data_arr = array("sts"=>"FALSE","error_msg"=>$error_msg);
}else{
	echo $result."<br>";
$pn_status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
if($pn_status==200){
$pn_data_arr = array("sts"=>"TRUE","error_msg"=>"");	
}else{
$error_msg = curl_error($http2ch);
$error_msg = stripslashes($error_msg);
$pn_data_arr = array("sts"=>"FALSE","error_msg"=>$error_msg);	
}
}

return $pn_data_arr;
  
}

$reg_id = $_REQUEST["reg_id"] ? trim($_REQUEST["reg_id"]) : "";
if($reg_id!=""){
echo "reg_id:".$reg_id."<br>";
$the_pn_msg = "Test PN Message Star Stellar";
$nt_title = "Test PN Title Star Stellar";
$the_m_image_link = "https://www.starstellar.com/admin/images/logo.png";
$sent_msg_arr = pushNotificationInIOS("",$the_pn_msg,$nt_title,$reg_id,$the_m_image_link);
$sent_sts_prod = $sent_msg_arr["sts"];
$error_message = $sent_msg_arr["error_msg"];
$sent_msg_arr2 = pushNotificationInIOS("development",$the_pn_msg,$nt_title,$reg_id,$the_m_image_link);
$sent_sts_dev = $sent_msg_arr2["sts"];
$error_message2 = $sent_msg_arr2["error_msg"];
echo "<br>dev:".$sent_sts_prod.",Error:".$error_message;
echo "<br>prod:".$sent_sts_dev.",Error:".$error_message2;
}else{
	echo "send reg_id";
}

?>