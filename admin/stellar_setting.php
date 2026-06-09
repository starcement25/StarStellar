<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "web_check.php";
include "star_connection.php";
$setting_master = "setting_master";
$home_screen_text_for_engineer = "home_screen_text_for_engineer";
$app_version = "app_version";
$product_master = "product_master";
$app_version_arr = array();
$app_version_arr["ANDROID"] = "1.0";
$app_version_arr["IOS"] = "1.0";


$sql_prod = "SELECT * FROM $product_master WHERE product_status = 'active' ORDER BY `prod_id` ASC";
$res_prod = mysqli_query($conn,$sql_prod);
$totres_prod = mysqli_num_rows($res_prod);

$sql_prod1 = "SELECT * FROM $product_master WHERE product_status = 'active' ORDER BY `prod_id` ASC";
$res_prod1 = mysqli_query($conn, $sql_prod1);
$totres_prod1 = mysqli_num_rows($res_prod1);

$sql_prod_bonus = "SELECT * FROM $product_master WHERE product_status = 'active' ORDER BY `prod_id` ASC";
$res_prod_bonus = mysqli_query($conn, $sql_prod_bonus);
$totres_prod_bonus = mysqli_num_rows($res_prod_bonus);

// //if ($totres_prod_bonus > 0)

$sqlapvrsn = "select * from $app_version";
$resapvrsn = mysqli_query($conn,$sqlapvrsn);
$totresapvrsn = mysqli_num_rows($resapvrsn);
if($totresapvrsn>0){
	while($roapvrsn=mysqli_fetch_assoc($resapvrsn)){
		$device_type = $roapvrsn["device_type"] ? strtoupper(trim($roapvrsn["device_type"])) : "";
		$app_version = $roapvrsn["app_version"] ? trim($roapvrsn["app_version"]) : "";
		if($device_type!=""){
			$app_version_arr[$device_type] = $app_version;
		}
	}
}

$home_screen_pic_dir = "../home_screen_pic/";
$home_screen_pic_url_prefix = $server_url."home_screen_pic/";
$the_image_url = "";
$sqlhst = "select * from $home_screen_text_for_engineer order by `id` asc limit 0,1";
$reshst = mysqli_query($conn,$sqlhst);
$totreshst = mysqli_num_rows($reshst);
if($totreshst>0){
	$rowhst=mysqli_fetch_assoc($reshst);
	$top_section_header_text = $rowhst["top_section_header_text"] ? trim($rowhst["top_section_header_text"]) : "";
	$top_section_description_text = $rowhst["top_section_description_text"] ? trim($rowhst["top_section_description_text"]) : "";
	$the_image = $rowhst["the_image"] ? trim($rowhst["the_image"]) : "";
	if($the_image!=""){
		if(file_exists($home_screen_pic_dir.$the_image)){
			$the_image_url =$home_screen_pic_url_prefix.$the_image;
		}
	}
}else{
$top_section_header_text = "";
$top_section_description_text = "";
$the_image = "";	
}


$app_setting_key_arr = array("signup_point"=>"","site_approved_point"=>"","birthday_point"=>"","anniversary_point"=>"","each_verified_site"=>"","bags_verification_limit_for_te"=>"","terms_condition"=>"","acknowledged_module_open_days"=>"");
$page_name = "stellar_setting.php";
$main_page_name = "stellar_setting.php";
$edt_page_name = "stellar_setting.php";

$sql_ckredius = "select `the_key_name`,`the_value` from $setting_master";
$res_ckredius = mysqli_query($conn,$sql_ckredius);
$totres_ckredius = mysqli_num_rows($res_ckredius);
if($totres_ckredius>0){
	while($row_ckredius=mysqli_fetch_assoc($res_ckredius)){
		$the_key_name = $row_ckredius["the_key_name"];
		$the_key_value = $row_ckredius["the_value"] ? trim($row_ckredius["the_value"]) : "";
		if($the_key_name!=""){
			$app_setting_key_arr[$the_key_name] = $the_key_value;
		}
	}
}
$add_page_name = "stellar_setting.php";
$page_name = "stellar_setting.php";
include "web_header.php";
?>
<style>
.span_clear{
	clear:both;
	display:block;
}
.signup_point,.site_approved_point,.birthday_point,.anniversary_point,.android_app_version,.ios_app_version,.the_prod_point,.each_verified_site,.bags_verification_limit_for_te,.more_than_bags_input,.bonus_points_input{
	text-align:center;
	width:100%;
	height:30px;
}
.top_section_header_text,.top_section_description_text{
width:100%;	
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
.the_hsimg{
	width:150px;
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
                          <h2>Settings</h2>
                        </div>
                        <div class="body">
 <div class="table-responsive">
<table class="table table-bordered table-striped table-hover setting_table">
<thead>
<tr>
<th>Description</th>
<th>Value</th>
<th>Action</th>
</tr>
</thead>
<tfoot>
<tr>
<th>Description</th>
<th>Value</th>
<th>Action</th>
</tr>
</tfoot>
<tbody>
<tr>
<td>Latest Android App Version</td>
<td>
<input type="text" class="android_app_version" id="android_app_version" value="<?php echo $app_version_arr["ANDROID"];?>"  autocomplete="off" /></td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_android_app_version_btn">Update</a>
<span class="android_app_version_msg_ldr"></span>
<span class="span_clear"></span>

</td>
</tr>
<tr>
<td>Latest IOS App Version</td>
<td>
<input type="text" class="ios_app_version" id="ios_app_version" value="<?php echo $app_version_arr["IOS"];?>" autocomplete="off"  /></td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_ios_app_version_btn">Update</a>
<span class="ios_app_version_msg_ldr"></span>
<span class="span_clear"></span>

</td>
</tr>
<tr>
<td>Signup points</td>
<td>
<?php
if(array_key_exists("signup_point",$app_setting_key_arr)){
$fetched_signup_point = $app_setting_key_arr["signup_point"];
}else{
$fetched_signup_point = "";	
}
?>
<input type="text" class="signup_point" id="signup_point" value="<?php echo $fetched_signup_point;?>" autocomplete="off"  /></td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_signup_point_btn">Update</a>
<span class="signup_point_msg_ldr"></span>
<span class="span_clear"></span>

</td>
</tr>

<tr>
<td>
Point for actual consumption of each bag of product<br/>
<select id="sel_prod_id" class="sel_prod_id" autocomplete="off">
<option value="">Select product to update point</option>
<?php
if($totres_prod>0){
	while($row_prod=mysqli_fetch_assoc($res_prod)){
		$the_prod_id = $row_prod["prod_id"];
		$the_prod_name = $row_prod["prod_name"]; ?>
        <option value="<?php echo $the_prod_id;?>"><?php echo $the_prod_name;?></option>
		
        <?php
	}
}
?>
</select>
</td>
<td>
<input type="text" class="the_prod_point" id="the_prod_point" autocomplete="off"  /></td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_sel_prod_point_btn">Update</a>
<span class="sel_prod_point_msg_ldr"></span>
<span class="span_clear"></span>
</td>
</tr>


<tr>
<td>Point for each verified site</td>
<td>
<?php
if(array_key_exists("each_verified_site",$app_setting_key_arr)){
$fetched_each_verified_site = $app_setting_key_arr["each_verified_site"];
}else{
$fetched_each_verified_site = "";	
}
?>
<input type="text" class="each_verified_site" id="each_verified_site" value="<?php echo $fetched_each_verified_site;?>" autocomplete="off"  /></td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_each_verified_site_btn">Update</a>
<span class="each_verified_site_msg_ldr"></span>
<span class="span_clear"></span>

</td>
</tr>



<tr>
    <td>
        Product Name for More than bags<br/>
        <select id="sel_prod_id1" class="sel_prod_id1" autocomplete="off">
            <option value="">Select product</option>
            <?php
            if($totres_prod1 > 0){
                while($row_prod1 = mysqli_fetch_assoc($res_prod1)){
                    $the_prod_id = $row_prod1["prod_id"];
                    $the_prod_name = $row_prod1["prod_name"];
                    ?>
                    <option value="<?php echo $the_prod_id;?>"><?php echo $the_prod_name;?></option>
                    <?php
                }
            }
            ?>
        </select>
    </td>
</tr>

<tr>
    <th>More Than Bags</th>
</tr>
<tr>
    <td>
        <input type="text" class="more_than_bags_input" id="more_than_bags_input" autocomplete="off" />
    </td>
    <td>
        <a href="javascript:void(0);" class="btn bg-red waves-effect upd_more_than_bags_btn1">Update</a>
        <span class="more_than_bags_msg_ldr"></span>
        <span class="span_clear"></span>
    </td>
</tr>

<!-- Bonus Points -->
<tr>
    <td>
        Product Name for Bonus<br/>
        <select id="sel_prod_id_bonus" class="sel_prod_id_bonus" autocomplete="off">
            <option value="">Select product</option>
            <?php
            if ($totres_prod_bonus > 0) {
                while ($row_prod_bonus = mysqli_fetch_assoc($res_prod_bonus)) {
                    $the_prod_id_bonus = $row_prod_bonus["prod_id"];
                    $the_prod_name_bonus = $row_prod_bonus["prod_name"];
                    ?>
                    <option value="<?php echo $the_prod_id_bonus; ?>"><?php echo $the_prod_name_bonus; ?></option>
                    <?php
                }
            } else {
                // Handle the case where no products are available
                echo '<option value="">No products available</option>';
            }
            ?>
        </select>
    </td>
</tr>

<tr>
    <th>Bonus Points</th>
</tr>
<tr>
    <td>
        <input type="text" class="bonus_points_input" id="bonus_points_input" autocomplete="off" />
    </td>
    <td>
        <a href="javascript:void(0);" class="btn bg-red waves-effect upd_bonus_points_btn">Update</a>
        <span class="bonus_points_msg_ldr"></span>
        <span class="span_clear"></span>
    </td>
</tr>




<tr>
<td>Bags verification limit for TE</td>
<td>
<?php
if(array_key_exists("bags_verification_limit_for_te",$app_setting_key_arr)){
$fetched_bags_verification_limit_for_te = $app_setting_key_arr["bags_verification_limit_for_te"];
}else{
$fetched_bags_verification_limit_for_te = "";	
}
?>
<input type="text" class="bags_verification_limit_for_te" id="bags_verification_limit_for_te" value="<?php echo $fetched_bags_verification_limit_for_te;?>" autocomplete="off"  /></td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_bags_verification_limit_for_te_btn">Update</a>
<span class="bags_verification_limit_for_te_msg_ldr"></span>
<span class="span_clear"></span>

</td>
</tr>

<tr>
<td>Birthday points</td>
<td>
<?php
if(array_key_exists("birthday_point",$app_setting_key_arr)){
$fetched_birthday_point = $app_setting_key_arr["birthday_point"];
}else{
$fetched_birthday_point = "";	
}
?>
<input type="text" class="birthday_point" id="birthday_point" value="<?php echo $fetched_birthday_point;?>"  autocomplete="off" /></td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_birthday_point_btn">Update</a>
<span class="birthday_point_msg_ldr"></span>
<span class="span_clear"></span>

</td>
</tr>

<tr>
<td>Anniversary points</td>
<td>
<?php
if(array_key_exists("anniversary_point",$app_setting_key_arr)){
$fetched_anniversary_point = $app_setting_key_arr["anniversary_point"];
}else{
$fetched_anniversary_point = "";	
}
?>
<input type="text" class="anniversary_point" id="anniversary_point" value="<?php echo $fetched_anniversary_point;?>"  autocomplete="off" /></td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_anniversary_point_btn">Update</a>
<span class="anniversary_point_msg_ldr"></span>
<span class="span_clear"></span>

</td>
</tr>
<tr>
<td>Acknowledgement of delivery days</td>
<td>
<?php
if(array_key_exists("acknowledged_module_open_days",$app_setting_key_arr)){
$acknowledged_module_open_days = $app_setting_key_arr["acknowledged_module_open_days"];
}else{
$acknowledged_module_open_days = "";	
}
?>
<input type="text" class="anniversary_point" id="acknowledged_module_open_days" value="<?php echo $acknowledged_module_open_days;?>"  autocomplete="off" /></td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_acknowledged_module_open_days_btn">Update</a>
<span class="acknowledged_module_open_days_msg_ldr"></span>
<span class="span_clear"></span>

</td>
</tr>

<tr>
<td>App Home Screen Header Text</td>
<td>
<textarea class="top_section_header_text" id="top_section_header_text"><?php echo $top_section_header_text;?></textarea>
</td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_top_section_header_text_btn">Update</a>
<span class="top_section_header_text_msg_ldr"></span>
<span class="span_clear"></span>
</td>
</tr>

<tr>
<td>App Home Screen Description Text</td>
<td>
<textarea class="top_section_description_text" id="top_section_description_text"><?php echo $top_section_description_text;?></textarea>
</td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_top_section_description_text_btn">Update</a>
<span class="top_section_description_text_msg_ldr"></span>
<span class="span_clear"></span>
</td>
</tr>
<?php 
if(array_key_exists("terms_condition",$app_setting_key_arr)){
$terms_condition = $app_setting_key_arr["terms_condition"];
}else{
$terms_condition = "";	
}
?>
<tr>
<td>Terms & Condition</td>
<td>
<textarea class="top_section_description_text textarea-custom-text" id="top_section_tc_text"><?php echo $terms_condition;?></textarea>
</td>
<td><a href="javascript:void(0);" class="btn bg-red waves-effect t_c_text_btn">Update</a>
<span class="top_section_t_c_text_msg_ldr"></span>
<span class="span_clear"></span>
</td>
</tr>
<!-- ✅ Include local CKEditor -->
<script src="plugins/tinymce/tinymce.min.js?v=0.0.6" referrerpolicy="origin"></script>

<script>
tinymce.init({
selector: '.textarea-custom-text',
license_key: 'gpl',
plugins: 'searchreplace autolink wordcount code table',
advcode_inline: true,
toolbar: "undo redo spellcheckdialog formatpainter | blocks fontfamily fontsize | bold italic underline forecolor backcolor | link image | alignleft aligncenter alignright alignjustify | code",
// height: '600px',
elementpath: false,
promotion: false,
branding: false,
});
</script>


<tr>
<td>App Home Screen Image
<div class="hsimg_div">
<?php if($the_image_url!=""){ ?>
	<img src="<?php echo $the_image_url;?>" class="the_hsimg" />
<?php } ?>
</div>

</td>
<td colspan="2">
<form style="float:left;" method="post" onsubmit="return false;" name="upload_hsi_form" id="upload_hsi_form" enctype="multipart/form-data" action="upload_home_screen_image.php">
<input type="hidden" name="image_form_submit" value="1"/>
<input type="file" name="hsi_image" id="hsi_image" >
<input type="submit" name="upload" class="btn bg-red waves-effect hsi_image_upload" style="margin-top:20px;" id="hsi_image_upload" value="Update">

<span class="percent" style="margin-top:10px;"></span>
<span class="span_clear"></span>
</form>
</td>
</tr>

</tbody>
</table>
                            </div>

                        </div>
                   
                    </div>
                </div>
            </div>
            <!-- #END# Basic Examples -->
            <!-- Exportable Table -->
            
            <!-- #END# Exportable Table -->
        </div>
    </section>
<script type="text/javascript">
jQuery(function(){
	var imgs = '<img src="images/ajax-loader.gif"/>';
	var done_img = '<img src="images/success_tick.png"/>';


var percent = jQuery('.percent');
jQuery('#upload_hsi_form').on('submit', function(e) {
e.preventDefault(); // <-- important
jQuery(this).ajaxSubmit({
uploadProgress: function(event, position, total, percentComplete) {
var percentVal = percentComplete + '%';
percent.html(percentVal);
},
beforeSubmit:function(e){	
count = 0;
exc_msg = "";
mx_img_siz = 2;
mx_sz_cnt = 0;
ext_cnt = 0;
no_of_img = 10;
val = jQuery.trim( jQuery('#hsi_image').val() );

if( val == '' ){
alert("Please select image.");
return false;
}else{
	var extension = val.split('.').pop().toUpperCase();
	if(extension!="PNG" && extension!="JPG" && extension!="JPEG"){
		alert("Please select image.");
		return false;
	}
}
},
success:function(e){

},
error:function(e){
},
resetForm: true,
complete: function(xhr) {
result = xhr.responseText;
result = $.parseJSON(result);
if( result.process_sts=="YES"){
jQuery('.percent').html(done_img);
setTimeout(function(){
jQuery(".percent").html("");
},5000);
jQuery('.hsimg_div').html( result.the_new_img );
}else{
jQuery('.percent').append( result.process_msg );
}	
}
});
});

jQuery("#signup_point,#site_approved_point,#android_app_version,#ios_app_version").keydown(function(event) {
// Allow only backspace and delete
if ( event.keyCode == 46 || event.keyCode == 8  || event.keyCode == 9 ) {
// let it happen, don't do anything
}
else {
// Ensure that it is a number and stop the keypress
if ((event.keyCode > 47 && event.keyCode < 58) || (event.keyCode > 95 && event.keyCode < 106) || event.keyCode == 110 || event.keyCode == 190 ) {

}else{
event.preventDefault(); 
}
}
});

jQuery(".upd_android_app_version_btn").click(function(){
var android_app_version = encodeURIComponent(jQuery.trim(jQuery("#android_app_version").val()));
jQuery(".android_app_version_msg_ldr").html(imgs);
jQuery.ajax({
url: 'ajax_update_android_app_version.php',
type: 'post',
dataType: "json",
data:"android_app_version="+android_app_version,
success: function(response) {
if(response.process_sts=="YES"){
jQuery(".android_app_version_msg_ldr").html(done_img);
setTimeout(function(){
jQuery(".android_app_version_msg_ldr").html("");
},5000);
}else{
jQuery(".android_app_version_msg_ldr").html("");			
}
}
});
});

jQuery(".upd_ios_app_version_btn").click(function(){
var ios_app_version = encodeURIComponent(jQuery.trim(jQuery("#ios_app_version").val()));
jQuery(".ios_app_version_msg_ldr").html(imgs);
jQuery.ajax({
url: 'ajax_update_ios_app_version.php',
type: 'post',
dataType: "json",
data:"ios_app_version="+ios_app_version,
success: function(response) {
if(response.process_sts=="YES"){
jQuery(".ios_app_version_msg_ldr").html(done_img);
setTimeout(function(){
jQuery(".ios_app_version_msg_ldr").html("");
},5000);
}else{
jQuery(".ios_app_version_msg_ldr").html("");			
}
}
});
});


jQuery(".upd_signup_point_btn").click(function(){
var signup_point = encodeURIComponent(jQuery.trim(jQuery("#signup_point").val()));
jQuery(".signup_point_msg_ldr").html(imgs);
jQuery.ajax({
url: 'ajax_update_signup_point.php',
type: 'post',
dataType: "json",
data:"signup_point="+signup_point,
success: function(response) {
if(response.process_sts=="YES"){
jQuery(".signup_point_msg_ldr").html(done_img);
setTimeout(function(){
jQuery(".signup_point_msg_ldr").html("");
},5000);
}else{
jQuery(".signup_point_msg_ldr").html("");			
}
}
});
});

jQuery("#sel_prod_id").change(function(){
	var sel_prod_id = jQuery(this).val();
	var the_prod_point_elmnt = jQuery("#the_prod_point");
	var the_loader_elmnt = jQuery(".sel_prod_point_msg_ldr");
	if(sel_prod_id!=""){
the_loader_elmnt.html(imgs);
jQuery.ajax({
url: "ajax_show_the_prod_point.php",
type: 'post',
dataType: "json",
data:"sel_prod_id="+sel_prod_id,
success: function(response) {
if(response.process_sts=="YES"){
	the_prod_point_elmnt.val(response.prod_point);
the_loader_elmnt.html(done_img);
setTimeout(function(){
the_loader_elmnt.html("");
},5000);
}else{
the_loader_elmnt.html("");			
}
}
});
	}else{
		the_loader_elmnt.html("");
		the_prod_point_elmnt.val("");
	}
	
});



jQuery(".upd_sel_prod_point_btn").click(function(){
var sel_prod_id = jQuery("#sel_prod_id").val();
var the_prod_point = encodeURIComponent(jQuery.trim(jQuery("#the_prod_point").val()));
var the_loader_elmnt = jQuery(".sel_prod_point_msg_ldr");
if(sel_prod_id!=""){
the_loader_elmnt.html(imgs);

jQuery.ajax({
url: "ajax_update_the_prod_point.php",
type: 'post',
dataType: "json",
data:"sel_prod_id="+sel_prod_id+"&the_prod_point="+the_prod_point,
success: function(response) {
if(response.process_sts=="YES"){
the_loader_elmnt.html(done_img);
setTimeout(function(){
the_loader_elmnt.html("");
},5000);
}else{
the_loader_elmnt.html("");			
}
}
});
}else{
the_loader_elmnt.html("Please select product.");
setTimeout(function(){
the_loader_elmnt.html("");
},5000);	
}
});


jQuery(".upd_bags_verification_limit_for_te_btn").click(function(){
var the_setting_val = encodeURIComponent(jQuery.trim(jQuery("#bags_verification_limit_for_te").val()));
var the_setting_key = "bags_verification_limit_for_te";
var the_loader_elmnt = jQuery(".bags_verification_limit_for_te_msg_ldr");
the_loader_elmnt.html(imgs);
jQuery.ajax({
url: "ajax_update_the_setting_value.php",
type: 'post',
dataType: "json",
data:"the_setting_key="+the_setting_key+"&the_setting_val="+the_setting_val,
success: function(response) {
if(response.process_sts=="YES"){
the_loader_elmnt.html(done_img);
setTimeout(function(){
the_loader_elmnt.html("");
},5000);
}else{
the_loader_elmnt.html("");			
}
}
});
});

//product names and more_than_bags
// JavaScript for More Than Bags
jQuery("#sel_prod_id1").change(function(){
    var sel_prod_id1 = jQuery(this).val();
    var more_than_bags_input_elmnt = jQuery("#more_than_bags_input");
    var more_than_bags_loader_elmnt = jQuery(".more_than_bags_msg_ldr");

    if(sel_prod_id1 != ""){
        more_than_bags_loader_elmnt.html(imgs);
        jQuery.ajax({
            url: "ajax_show_more_than_bags.php", // Update with the correct file
            type: 'post',
            dataType: "json",
            data: "sel_prod_id1=" + sel_prod_id1,
            success: function(response) {
                if(response.process_sts == "YES"){
                    more_than_bags_input_elmnt.val(response.more_than_bags);
                    more_than_bags_loader_elmnt.html(done_img);
                    setTimeout(function(){
                        more_than_bags_loader_elmnt.html("");
                    }, 5000);
                } else {
                    more_than_bags_loader_elmnt.html("");            
                }
            }
        });
    } else {
        more_than_bags_loader_elmnt.html("");
        more_than_bags_input_elmnt.val("");
    }
});

jQuery(".upd_more_than_bags_btn1").click(function(){
    var sel_prod_id1 = jQuery("#sel_prod_id1").val();
    var more_than_bags_input = encodeURIComponent(jQuery.trim(jQuery("#more_than_bags_input").val()));
    var more_than_bags_loader_elmnt = jQuery(".more_than_bags_msg_ldr");

    if(sel_prod_id1 != ""){
        more_than_bags_loader_elmnt.html(imgs);

        jQuery.ajax({
            url: "ajax_update_more_than_bags.php", // Update with the correct file
            type: 'post',
            dataType: "json",
            data: "sel_prod_id1=" + sel_prod_id1 + "&more_than_bags_input=" + more_than_bags_input,
            success: function(response) {
                if(response.process_sts == "YES"){
                    more_than_bags_loader_elmnt.html(done_img);
                    setTimeout(function(){
                        more_than_bags_loader_elmnt.html("");
                    }, 5000);
                } else {
                    more_than_bags_loader_elmnt.html("");            
                }
            }
        });
    } else {
        more_than_bags_loader_elmnt.html("Please select product.");
        setTimeout(function(){
            more_than_bags_loader_elmnt.html("");
        }, 5000);    
    }
});  


//for product name bonus point
jQuery("#sel_prod_id_bonus").change(function(){
    var sel_prod_id_bonus = jQuery(this).val();
    var bonus_points_input_elmnt = jQuery("#bonus_points_input");
    var bonus_points_loader_elmnt = jQuery(".bonus_points_msg_ldr");

    if(sel_prod_id_bonus != ""){
        bonus_points_loader_elmnt.html(imgs);
        jQuery.ajax({
            url: "ajax_show_bonus_points.php", // Update with the correct file
            type: 'post',
            dataType: "json",
            data: "sel_prod_id_bonus=" + sel_prod_id_bonus,
            success: function(response) {
                if(response.process_sts == "YES"){
                    bonus_points_input_elmnt.val(response.bonus_points);
                    bonus_points_loader_elmnt.html(done_img);
                    setTimeout(function(){
                        bonus_points_loader_elmnt.html("");
                    }, 5000);
                } else {
                    bonus_points_loader_elmnt.html("");            
                }
            }
        });
    } else {
        bonus_points_loader_elmnt.html("");
        bonus_points_input_elmnt.val("");
    }
});

jQuery(".upd_bonus_points_btn").click(function(){
    var sel_prod_id_bonus = jQuery("#sel_prod_id_bonus").val();
    var bonus_points_input = encodeURIComponent(jQuery.trim(jQuery("#bonus_points_input").val()));
    var bonus_points_loader_elmnt = jQuery(".bonus_points_msg_ldr");

    if(sel_prod_id_bonus != ""){
        bonus_points_loader_elmnt.html(imgs);

        jQuery.ajax({
            url: "ajax_update_bonus_points.php", // Update with the correct file
            type: 'post',
            dataType: "json",
            data: "sel_prod_id_bonus=" + sel_prod_id_bonus + "&bonus_points_input=" + bonus_points_input,
            success: function(response) {
                if(response.process_sts == "YES"){
                    bonus_points_loader_elmnt.html(done_img);
                    setTimeout(function(){
                        bonus_points_loader_elmnt.html("");
                    }, 5000);
                } else {
                    bonus_points_loader_elmnt.html("");            
                }
            }
        });
    } else {
        bonus_points_loader_elmnt.html("Please select product.");
        setTimeout(function(){
            bonus_points_loader_elmnt.html("");
        }, 5000);    
    }
});





jQuery(".upd_each_verified_site_btn").click(function(){
var the_setting_val = encodeURIComponent(jQuery.trim(jQuery("#each_verified_site").val()));
var the_setting_key = "each_verified_site";
var the_loader_elmnt = jQuery(".each_verified_site_msg_ldr");
the_loader_elmnt.html(imgs);
jQuery.ajax({
url: "ajax_update_the_setting_value.php",
type: 'post',
dataType: "json",
data:"the_setting_key="+the_setting_key+"&the_setting_val="+the_setting_val,
success: function(response) {
if(response.process_sts=="YES"){
the_loader_elmnt.html(done_img);
setTimeout(function(){
the_loader_elmnt.html("");
},5000);
}else{
the_loader_elmnt.html("");			
}
}
});
});

jQuery(".t_c_text_btn").click(function(){
var the_setting_val = tinymce.get('top_section_tc_text').getContent();
var the_setting_key = "terms_condition";
var the_loader_elmnt = jQuery(".each_verified_site_msg_ldr");
the_loader_elmnt.html(imgs);
jQuery.ajax({
url: "ajax_update_the_setting_value_t_c.php",
type: 'post',
dataType: "json",
data:"the_setting_key="+the_setting_key+"&the_setting_val="+the_setting_val,
success: function(response) {
if(response.process_sts=="YES"){

jQuery(".top_section_t_c_text_msg_ldr").html(done_img);
setTimeout(function(){
the_loader_elmnt.html("");
},5000);
}else{
the_loader_elmnt.html("");			
}
}
});
});


jQuery(".upd_birthday_point_btn").click(function(){
var birthday_point = encodeURIComponent(jQuery.trim(jQuery("#birthday_point").val()));
jQuery(".birthday_point_msg_ldr").html(imgs);
jQuery.ajax({
url: 'ajax_update_birthday_point.php',
type: 'post',
dataType: "json",
data:"birthday_point="+birthday_point,
success: function(response) {
if(response.process_sts=="YES"){
jQuery(".birthday_point_msg_ldr").html(done_img);
setTimeout(function(){
jQuery(".birthday_point_msg_ldr").html("");
},5000);
}else{
jQuery(".birthday_point_msg_ldr").html("");			
}
}
});
});

jQuery(".upd_anniversary_point_btn").click(function(){
var anniversary_point = encodeURIComponent(jQuery.trim(jQuery("#anniversary_point").val()));
jQuery(".anniversary_point_msg_ldr").html(imgs);
jQuery.ajax({
url: 'ajax_update_anniversary_point.php',
type: 'post',
dataType: "json",
data:"anniversary_point="+anniversary_point,
success: function(response) {
if(response.process_sts=="YES"){
jQuery(".anniversary_point_msg_ldr").html(done_img);
setTimeout(function(){
jQuery(".anniversary_point_msg_ldr").html("");
},5000);
}else{
jQuery(".anniversary_point_msg_ldr").html("");			
}
}
});
});

jQuery(".upd_acknowledged_module_open_days_btn").click(function(){
var acknowledged_module_open_days = $("#acknowledged_module_open_days").val();
jQuery(".acknowledged_module_open_days_msg_ldr").html(imgs);
jQuery.ajax({
url: 'acknowledged_module_open_days.php',
type: 'post',
dataType: "json",
data:"acknowledged_module_open_days="+acknowledged_module_open_days,
success: function(response) {
if(response.process_sts=="YES"){
jQuery(".acknowledged_module_open_days_msg_ldr").html(done_img);
setTimeout(function(){
jQuery(".acknowledged_module_open_days_msg_ldr").html("");
},5000);
}else{
jQuery(".acknowledged_module_open_days_msg_ldr").html("");			
}
}
});
});

jQuery(".upd_top_section_header_text_btn").click(function(){
var top_section_header_text = encodeURIComponent(jQuery.trim(jQuery("#top_section_header_text").val()));
jQuery(".top_section_header_text_msg_ldr").html(imgs);
jQuery.ajax({
url: 'ajax_update_top_section_header_text.php',
type: 'post',
dataType: "json",
data:"top_section_header_text="+top_section_header_text,
success: function(response) {
if(response.process_sts=="YES"){
jQuery(".top_section_header_text_msg_ldr").html(done_img);
setTimeout(function(){
jQuery(".top_section_header_text_msg_ldr").html("");
},5000);
}else{
jQuery(".top_section_header_text_msg_ldr").html("");			
}
}
});
});

jQuery(".upd_top_section_description_text_btn").click(function(){
var top_section_description_text = encodeURIComponent(jQuery.trim(jQuery("#top_section_description_text").val()));
jQuery(".top_section_description_text_msg_ldr").html(imgs);
jQuery.ajax({
url: 'ajax_update_top_section_description_text.php',
type: 'post',
dataType: "json",
data:"top_section_description_text="+top_section_description_text,
success: function(response) {
if(response.process_sts=="YES"){
jQuery(".top_section_description_text_msg_ldr").html(done_img);
setTimeout(function(){
jQuery(".top_section_description_text_msg_ldr").html("");
},5000);
}else{
jQuery(".top_section_description_text_msg_ldr").html("");			
}
}
});
});

});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>