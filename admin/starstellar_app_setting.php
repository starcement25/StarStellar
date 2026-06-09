<?php

include "web_check.php";
include "star_connection.php";
include "web_header.php";
include "web_footer.php";

?>

<?php

$app_setting_key_arr = array("carry_forward_process"=>"ACTIVE","carry_forward_process_message"=>"");

$carry_forward_process_arr = array("ACTIVE","INACTIVE");
$reg_btn_option_arr = array("YES","NO");

$sql_ckredius = "select `the_key_name`,`the_value` from $app_setting_master";
$res_ckredius = mysql_query($sql_ckredius);
$totres_ckredius = mysql_num_rows($res_ckredius);
if($totres_ckredius>0){
	while($row_ckredius=mysql_fetch_assoc($res_ckredius)){
		$the_key_name = $row_ckredius["the_key_name"];
		$the_key_value = $row_ckredius["the_value"] ? trim($row_ckredius["the_value"]) : "";
		if($the_key_name!=""){
			$app_setting_key_arr[$the_key_name] = $the_key_value;
		}
	}
}

$submsg = "";
$add_page_name = "starstellar_app_setting.php";
$page_name = "starstellar_app_setting.php";

?>

<style>
.estarix_cls{
	color:#F00;
	margin-left:5px;
}
.span_clear{
	clear:both;
	display:block;
}
.att_radius{
	text-align:center;
	width:100%;
	height:30px;
}
.att_late_count_with_respect_to_one_absent{
	text-align:center;
	width:100%;
	height:30px;
}
.att_send_leave_request_to_this_mail{
	text-align:left;
	width:100%;
	height:30px;
	padding-left:5px;
	padding-top:5px;
	
}
.setting_table tbody tr th,.setting_table tbody tr td{
	width:33%;
}
.setting_table tbody tr td span.small_msg{
font-size: 11px;
font-weight: bold;
}
.setting_table tbody tr td p{
margin-bottom: 4px;
}

.the_float_btn{
	float:left;
}
.the_float_msg{
	float:left;
	margin-left:10px;
	width:30px;
	height:30px;
}
</style>
<section class="content">
<div class="container-fluid">
<div class="block-header">

</div>
<!-- Basic Examples -->
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<div class="card">
<div class="header">
<h2>App Setting</h2>
</div>
<div class="body" style="padding:20px;">

<table class="table table-bordered table-striped table-hover setting_table">
<thead>
<tr>
<th>Settings&nbsp;Name</th>
<th>Value</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<tr>
<td><p>Carry Forward Process</p>
<span class="small_msg"></span>
</td>
<td>
<?php
$fetched_carry_forward_process = $app_setting_key_arr["carry_forward_process"];
?>
<select class="form-control str_carry_forward_process" id="str_carry_forward_process">
<?php
foreach($carry_forward_process_arr as $carry_forward_process_arr_val){
?>
<option value="<?php echo $carry_forward_process_arr_val;?>" <?php if($carry_forward_process_arr_val==$fetched_carry_forward_process){?> selected="selected" <?php } ?>><?php echo $carry_forward_process_arr_val;?></option>
<?php } ?>
</select>

</td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_str_carry_forward_btn the_float_btn">Update</a>
<span class="str_carry_forward_msg_ldr the_float_msg"></span>
<span class="span_clear"></span>

</td>
</tr>

<tr>
<td><p>Carry Forward Process Message</p>
<span class="small_msg">End user will see this message if "Carry Forward" status is "INACTIVE"</span>
</td>
<td>
<?php
$fetched_carry_forward_process_message = $app_setting_key_arr["carry_forward_process_message"];
?>
<textarea  class="form-control str_carry_forward_process_message" id="str_carry_forward_process_message" rows="3"><?php echo $fetched_carry_forward_process_message;?></textarea>
</td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_str_carry_forward_message_btn the_float_btn">Update</a>
<span class="str_carry_forward_message_msg_ldr the_float_msg"></span>
<span class="span_clear"></span>

</td>
</tr>

</tbody>
</table>


</div>
</div>
</div>
</div>
<!-- #END# Basic Examples -->
</div>
</section>
<script type="text/javascript">

jQuery(function(){
var img = '<img style="min-width:16px;width:16px;height:16px;margin:0 auto;" src="images/ajax-loader.gif">';
var success_tik = '<img style="min-width:16px;width:16px;height:16px;margin:0 auto;" src="images/success_tick.png">';



jQuery(".upd_is_show_reg_btn_btn").click(function(){
var is_show_reg_btn = jQuery.trim(jQuery("#is_show_reg_btn").val());
jQuery(".is_show_reg_btn_msg_ldr").html(img);
jQuery.ajax({
url: 'ajax_update_enable_reg_button.php',
type: 'post',
dataType: "json",
data:"is_show_reg_btn="+is_show_reg_btn,
success: function(response) {
if(response.process_sts=="YES"){
jQuery(".is_show_reg_btn_msg_ldr").html(success_tik);
setTimeout(function(){
jQuery(".is_show_reg_btn_msg_ldr").html("");
},5000);
}else{
jQuery(".is_show_reg_btn_msg_ldr").html("");			
}
}
});
});


jQuery(".upd_str_carry_forward_btn").click(function(){
var str_carry_forward_process = jQuery.trim(jQuery("#str_carry_forward_process").val());
jQuery(".str_carry_forward_msg_ldr").html(img);
jQuery.ajax({
url: 'ajax_update_carry_forward_process.php',
type: 'post',
dataType: "json",
data:"str_carry_forward_process="+str_carry_forward_process,
success: function(response) {
if(response.process_sts=="YES"){
jQuery(".str_carry_forward_msg_ldr").html(success_tik);
setTimeout(function(){
jQuery(".str_carry_forward_msg_ldr").html("");
},5000);
}else{
jQuery(".str_carry_forward_msg_ldr").html("");			
}
}
});
});

jQuery(".upd_str_carry_forward_message_btn").click(function(){
var str_carry_forward_process_message = jQuery.trim(jQuery("#str_carry_forward_process_message").val());
jQuery(".str_carry_forward_message_msg_ldr").html(img);
jQuery.ajax({
url: 'ajax_update_carry_forward_process_message.php',
type: 'post',
dataType: "json",
data:"str_carry_forward_process_message="+encodeURIComponent(str_carry_forward_process_message),
success: function(response) {
if(response.process_sts=="YES"){
jQuery(".str_carry_forward_message_msg_ldr").html(success_tik);
setTimeout(function(){
jQuery(".str_carry_forward_message_msg_ldr").html("");
},5000);
}else{
jQuery(".str_carry_forward_message_msg_ldr").html("");			
}
}
});
});


});
</script>


<?php
mysql_close();
?>