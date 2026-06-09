<?php
$the_noty_id = $_POST["the_noty_id"] ? trim($_POST["the_noty_id"]) : "";
if($the_noty_id!=""){
$new_gen_noty_id_enc = base64_encode($the_noty_id);
$ggmm = exec("php /var/www/html/admin/send_notification_in_background.php $new_gen_noty_id_enc ");	
}
?>