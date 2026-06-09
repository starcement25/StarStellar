<?php
include "web_check.php";
include "star_connection.php";
$admin_master = "admin_master";
$menu_master = "menu_master";
$selected_menu_for_user = "selected_menu_for_user";
$the_menu_master_array = array();
$the_menu_selected_array = array();
$sqlmm = "select `menu_id`,`menu_name` from $menu_master order by `menu_name` asc";
$resmm = mysql_query($sqlmm);
$totresmm = mysql_num_rows($resmm);
if($totresmm>0){
	while($rowmm=mysql_fetch_assoc($resmm)){
		$menu_idmm = $rowmm["menu_id"];
		$menu_namemm = $rowmm["menu_name"];
		$the_menu_master_array[] = array("menu_id"=>$menu_idmm,"menu_name"=>$menu_namemm);
	}
}
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/starstellar/";
$image_link_url = $server_url1."admin/noty_images/";
$mime_type_array = array("image/jpeg", "image/png","image/jpg");
$mime_type_array_pdf = array("application/pdf");
$n_dlt_msg = "";
$n_msg = "";
$submsg = "";
$add_page_name = "add_edit_admin_user_master.php";
$page_name = "add_edit_admin_user_master.php";
$back_to_page = "admin_user_master.php";
$status_arr = array("ACTIVE","DEACTIVE");
$data_show_type_arr = array("ALL","NE1","NE2","BIHAR","NB1","NB2");

function check_username_exist_for_save($user_name){
$exists = "NO";
$admin_master = "admin_master";;
$user_name = $user_name ? addslashes(trim($user_name)) : "";
if($user_name!=""){
$sql8ckinup = "select `user_name` from $admin_master where `user_name`='$user_name' and `user_type`='MANAGER'";
$res8ckinup = mysql_query($sql8ckinup);
$totres8ckinup = mysql_num_rows($res8ckinup);
if($totres8ckinup>0){
$exists = "YES";
}
}
return $exists;
}
function check_username_exist_for_update($tid,$user_name){
$exists = "NO";
$admin_master = "admin_master";
$tid = $tid ? addslashes(trim($tid)) : "";
$user_name = $user_name ? addslashes(trim($user_name)) : "";
if($user_name!="" && $tid!=""){
$sql8ckinup = "select `user_name` from $admin_master where `user_name`='$user_name' and `user_type`='MANAGER' and `id`!='$tid' ";
$res8ckinup = mysql_query($sql8ckinup);
$totres8ckinup = mysql_num_rows($res8ckinup);
if($totres8ckinup>0){
$exists = "YES";
}
}
return $exists;
}

if(isset($_GET["n_msg"]) and $_GET["n_msg"]!=""){
$n_msg = $_GET["n_msg"];
}else{
$n_msg = "";
}

if($_POST["te_update"]=="Update"){
$hid_edt_admin_id = $_POST["hid_edt_admin_id"] ? addslashes(trim($_POST["hid_edt_admin_id"])) : "";
$edt_user_name = $_POST["edt_user_name"] ? addslashes(trim($_POST["edt_user_name"])) : "";
$edt_password = $_POST["edt_password"] ? addslashes(trim($_POST["edt_password"])) : "";
$sl_status = $_POST["sl_status"] ? addslashes(trim($_POST["sl_status"])) : "ACTIVE";
$sl_data_show_type = $_POST["sl_data_show_type"] ? addslashes(trim($_POST["sl_data_show_type"])) : "ALL";
$asupd_user_admin_menu_menu = array();
$asupd_user_admin_menu_menu = $_POST["user_admin_menu"];

	if($edt_user_name==""){
		$n_msg = "Please enter user name";
	}else if($edt_password==""){
		$n_msg = "Please enter password";
	}else{
		$temobile_exist_sts = check_username_exist_for_update($hid_edt_admin_id,$edt_user_name);
if($temobile_exist_sts=="YES"){
$n_msg = "The username $edt_user_name is already exists. Please try another one";	
}else{		
		
$curr_date_time = date("Y-m-d H:i:s");
$sql_in = "update $admin_master set `user_name`='$edt_user_name',`password`='$edt_password',`status`='$sl_status',`data_show_type`='$sl_data_show_type' where `id`='$hid_edt_admin_id' and `user_type`='MANAGER'";
$res_in = mysql_query($sql_in);

$asupd_user_admin_menu_menu_new = array();
$asupd_user_admin_menu_menu_new = $asupd_user_admin_menu_menu;
$asupd_user_admin_menu_menu_str = implode("','",$asupd_user_admin_menu_menu);
$sqldtlfm = "delete from $selected_menu_for_user where `user_id`='$hid_edt_admin_id' and `menu_id` not in('".$asupd_user_admin_menu_menu_str."')";
$resdtlfm = mysql_query($sqldtlfm);
foreach($asupd_user_admin_menu_menu_new as $asupd_user_admin_menu_menu_new_val){
	$the_fmi = $asupd_user_admin_menu_menu_new_val;
	$sqlckfm = "select `menu_id`,`user_id` from $selected_menu_for_user where `user_id`='$hid_edt_admin_id' and `menu_id`='$the_fmi'";
	$resckfm = mysql_query($sqlckfm);
	$totresckfm = mysql_num_rows($resckfm);
	if($totresckfm==0){
		$sql_infm = "insert into $selected_menu_for_user (`user_id`,`menu_id`) values('$hid_edt_admin_id','".$the_fmi."')";
		$res_infm = mysql_query($sql_infm);
	}
}


$n_msg = 'The user details successfully updated.';
header("location:".$page_name."?edt_admin_id=".$hid_edt_admin_id."&n_msg=".$n_msg);
}
				
}
}

if($_POST["te_save"]=="Save"){
$edt_user_name = $_POST["edt_user_name"] ? addslashes(trim($_POST["edt_user_name"])) : "";
$edt_password = $_POST["edt_password"] ? addslashes(trim($_POST["edt_password"])) : "";
$sl_status = $_POST["sl_status"] ? addslashes(trim($_POST["sl_status"])) : "ACTIVE";
$sl_data_show_type = $_POST["sl_data_show_type"] ? addslashes(trim($_POST["sl_data_show_type"])) : "ALL";
$asupd_user_admin_menu_menu = array();
$asupd_user_admin_menu_menu = $_POST["user_admin_menu"];
	if($edt_user_name==""){
		$n_msg = "Please enter user name";
	}else if($edt_password==""){
		$n_msg = "Please enter password";
	}else{
		$temobile_exist_sts = check_username_exist_for_save($edt_user_name);
if($temobile_exist_sts=="YES"){
$n_msg = "The username $edt_user_name is already exists. Please try another one";	
}else{		
		
$curr_date_time = date("Y-m-d H:i:s");
$sql_in = "insert into $admin_master (`user_name`,`password`,`status`,`user_type`,`data_show_type`) values('$edt_user_name','$edt_password','$sl_status','MANAGER','$sl_data_show_type')";
$res_in = mysql_query($sql_in);
if($res_in){
$new_te_id = mysql_insert_id();

$asupd_user_admin_menu_menu_new = array();
$asupd_user_admin_menu_menu_new = $asupd_user_admin_menu_menu;
$asupd_user_admin_menu_menu_str = implode("','",$asupd_user_admin_menu_menu);
$sqldtlfm = "delete from $selected_menu_for_user where `user_id`='$new_te_id' and `menu_id` not in('".$asupd_user_admin_menu_menu_str."')";
$resdtlfm = mysql_query($sqldtlfm);
foreach($asupd_user_admin_menu_menu_new as $asupd_user_admin_menu_menu_new_val){
	$the_fmi = $asupd_user_admin_menu_menu_new_val;
	$sqlckfm = "select `menu_id`,`user_id` from $selected_menu_for_user where `user_id`='$new_te_id' and `menu_id`='$the_fmi'";
	$resckfm = mysql_query($sqlckfm);
	$totresckfm = mysql_num_rows($resckfm);
	if($totresckfm==0){
		$sql_infm = "insert into $selected_menu_for_user (`user_id`,`menu_id`) values('$new_te_id','".$the_fmi."')";
		$res_infm = mysql_query($sql_infm);
	}
}


$n_msg = 'The user details successfully saved.';
header("location:".$page_name."?edt_admin_id=".$new_te_id."&n_msg=".$n_msg);
}else{
$n_msg = 'Something went wrong. Please try later.';
header("location:".$page_name."?n_msg=".$n_msg);
}

}
				
}
}

if(isset($_GET["edt_admin_id"]) && $_GET["edt_admin_id"]!=''){
 $edt_admin_id = $_GET["edt_admin_id"];
$sql8 = "select * from $admin_master where `id`='$edt_admin_id' and `user_type`='MANAGER'";
$res8 = mysql_query($sql8);
$totres8 = mysql_num_rows($res8);
if($totres8>0){
	$row8 = mysql_fetch_assoc($res8);
	$the_user_name = $row8["user_name"];
	$the_password = $row8["password"];
	$the_status = $row8["status"];
	$the_data_show_type = $row8["data_show_type"] ? $row8["data_show_type"] : "ALL";
	
	$sqlfm = "select `menu_id`,`user_id` from $selected_menu_for_user where `user_id`='$edt_admin_id'";
	$resfm = mysql_query($sqlfm);
	$totresfm = mysql_num_rows($resfm);
	if($totresfm>0){
	while($rowfm=mysql_fetch_assoc($resfm)){
	$menu_idfm = $rowfm["menu_id"];
	$the_menu_selected_array[] = $menu_idfm;
	}
	}
	
}else{
header("location:".$back_to_page);
}
}else{
$edt_admin_id = "";
$the_user_name = "";
$the_password = "";
$the_status = "ACTIVE";
$the_data_show_type ="ALL";
}
include "web_header.php";
?>
<style>
.estarix_cls{
	color:#F00;
	margin-left:5px;
}
.title_err_cls,.msg_err_cls,.img_err_cls,.pdf_err_cls{
	color:#F00;
	margin-left:5px;
}
.noty_curr_count,.noty_count_loader,.show_noty_sending_count{
	float:left;
	margin-left:5px;
}
.noty_curr_count{
	width:35px;
}
.noty_count_loader{
	width:24px;
}
.show_noty_sending_count{
	width:20px;
}
.show_noty_sending_count img{
	width:100%;;
}
.clear_class{
	clear:both;
	display:block;
}
.n_image_upload_section,.n_pdf_upload_section{
	display:none;
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
                          <h2>Add/Edit Admin User &nbsp;&nbsp;&nbsp;<span id="loaddr_msg"><?php if($n_msg!=""){ echo $n_msg;}?></span></h2>
                        </div>
                        <div class="body" style="padding:20px;">
<form action="" method="post" enctype="multipart/form-data" id="send_notification_form" class="send_notification_form">

<div class="form-group">
<label class="Username">Username</label>
<div class="form-line">
<input type="text" name="edt_user_name"  id="edt_user_name" class="form-control" placeholder="Enter Username" value="<?php echo $the_user_name;?>"  />
</div>
</div>

<div class="form-group">
<label class="Password">Password</label>
<div class="form-line">
<input type="text" name="edt_password"  id="edt_password" class="form-control" value="<?php echo $the_password;?>" placeholder="Enter Password"  />
</div>
</div>

<div class="form-group">
<label class="Status">Status</label>
<div class="form-line">
<select class="form-control sl_status" id="sl_status" name="sl_status">
<?php
foreach($status_arr as $status_arr_val){
	?>
<option value="<?php echo $status_arr_val;?>" <?php if($status_arr_val==$the_status){ ?> selected="selected" <?php } ?>><?php echo $status_arr_val;?></option>
	<?php }
?>
</select>
</div>
</div>

<div class="form-group">
<label class="Type">Zone</label>
<div class="form-line">
<select class="form-control sl_data_show_type" id="sl_data_show_type" name="sl_data_show_type">
<?php
foreach($data_show_type_arr as $data_show_type_arr_val){
	?>
<option value="<?php echo $data_show_type_arr_val;?>" <?php if($data_show_type_arr_val==$the_data_show_type){ ?> selected="selected" <?php } ?>><?php echo $data_show_type_arr_val;?></option>
	<?php }
?>
</select>
</div>
</div>

<div class="form-group">
<label class="Select Menus">Select Menus &nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="btn bg-red waves-effe select_all_cls">Select All</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="btn bg-red waves-effe deselect_all_cls">Deselect All</a></label>
<div class="form-line">
<select name="user_admin_menu[]" id="user_admin_menu" class="chosen-select user_admin_menu" multiple data-placeholder="Choose menus">
<?php
if(count($the_menu_master_array)>0){
	foreach($the_menu_master_array as $the_menu_master_array_val){
		$get_ftr_mn_id = $the_menu_master_array_val["menu_id"];
		$get_ftr_mn_nm = $the_menu_master_array_val["menu_name"]; ?>
	<option <?php if(in_array($get_ftr_mn_id,$the_menu_selected_array)){ ?> selected="selected" <?php } ?> value="<?php echo $get_ftr_mn_id;?>"><?php echo $get_ftr_mn_nm;?></option>
	<?php }
}
?>
</select>
</div>
</div>




<?php
if($edt_admin_id!=""){ ?>
<input type="submit" class="btn bg-red waves-effect snd_btn" name="te_update" id="te_update"  value="Update" /> 
<input type="hidden" name="hid_edt_admin_id" value="<?php echo $edt_admin_id;?>" />
<?php }else{
?>
<input type="submit" class="btn bg-red waves-effect snd_btn" name="te_save" id="te_save"  value="Save" />   
<?php
}
?>
<a href="<?php echo $back_to_page;?>" class="btn bg-red waves-effe" style="margin-left:20px;">Back&nbsp;To&nbsp;Admin&nbsp;User&nbsp;Master</a>
</form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Examples -->
        </div>
    </section>
<script type="text/javascript">
jQuery(function(){
jQuery('#user_admin_menu').chosen({width:"100%",no_results_text:'Oops, no menu found!'});
//jQuery('#sl_branch_codes').chosen({width:"100%",no_results_text:'Oops, no branch found!'});

setTimeout(function(){
	jQuery("#loaddr_msg").html("");
	jQuery("#loaddr_dlt_msg").html("");
},8000);

jQuery(".select_all_cls").click(function(){
jQuery('#user_admin_menu option').prop('selected', true);  
jQuery('#user_admin_menu').trigger('chosen:updated');
});
jQuery(".deselect_all_cls").click(function(){
jQuery('#user_admin_menu option:selected').removeAttr('selected'); 
jQuery('#user_admin_menu').trigger('chosen:updated');
});


jQuery("form#send_notification_form").submit(function(){
	var edt_user_name = jQuery.trim(jQuery("#edt_user_name").val());
	var edt_password = jQuery.trim(jQuery("#edt_password").val());	
	if(edt_user_name==""){
		alert("Please enter user name.");
		jQuery("#edt_user_name").focus();
		return false;
	}else if(edt_password==""){
		alert("Please enter password.");
		jQuery("#edt_password").focus();
		return false;
	}else{
		return true;
		
	}
});



});
</script>
<?php
include "web_footer.php";
mysql_close();
?>