<?php
include "web_check.php";
include "star_connection.php";
$te_master = "te_master";
$img_dir = "noty_images/";
$branch_master = "branch_master";

$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}

$sql1b = "select `branch_code`,`branch_name` from $branch_master order by `branch_name` asc";
$res1b = mysqli_query($conn,$sql1b);
$totres1b = mysqli_num_rows($res1b);
$image_link_url = $server_url."admin/noty_images/";
$mime_type_array = array("image/jpeg", "image/png","image/jpg");
$mime_type_array_pdf = array("application/pdf");
$n_dlt_msg = "";
$n_msg = "";
$submsg = "";
$add_page_name = "add_edit_te_update.php";
$page_name = "add_edit_te_update.php";
$back_to_page = "te_master_update.php";

function check_te_code_exist_for_save($conn,$tecode){
$exists = "NO";
$te_master = "te_master";
$tecode = $tecode ? addslashes(trim($tecode)) : "";
if($tecode!=""){
$sql8ckinup = "select `te_code` from $te_master where `te_code`='$tecode'";
$res8ckinup = mysqli_query($conn,$sql8ckinup);
$totres8ckinup = mysqli_num_rows($res8ckinup);
if($totres8ckinup>0){
$exists = "YES";
}
}
return $exists;
}
function check_te_code_exist_for_update($conn,$tid,$tecode){
$exists = "NO";
$te_master = "te_master";
$tid = $tid ? addslashes(trim($tid)) : "";
$tecode = $tecode ? addslashes(trim($tecode)) : "";
if($tecode!="" && $tid!=""){
$sql8ckinup = "select `te_code` from $te_master where `te_code`='$tecode' and `te_id`!='$tid' ";
$res8ckinup = mysqli_query($conn,$sql8ckinup);
$totres8ckinup = mysqli_num_rows($res8ckinup);
if($totres8ckinup>0){
$exists = "YES";
}
}
return $exists;
}

function check_te_mobile_exist_for_save($conn,$temobile){
$exists = "NO";
$te_master = "te_master";
$temobile = $temobile ? addslashes(trim($temobile)) : "";
if($temobile!=""){
$sql8ckinup = "select `te_mobile_no` from $te_master where `te_mobile_no`='$temobile'";
$res8ckinup = mysqli_query($conn,$sql8ckinup);
$totres8ckinup = mysqli_num_rows($res8ckinup);
if($totres8ckinup>0){
$exists = "YES";
}
}
return $exists;
}
function check_te_mobile_exist_for_update($conn,$tid,$temobile){
$exists = "NO";
$te_master = "te_master";
$tid = $tid ? addslashes(trim($tid)) : "";
$temobile = $temobile ? addslashes(trim($temobile)) : "";
if($temobile!="" && $tid!=""){
$sql8ckinup = "select `te_mobile_no` from $te_master where `te_mobile_no`='$temobile' and `te_id`!='$tid' ";
$res8ckinup = mysqli_query($conn,$sql8ckinup);
$totres8ckinup = mysqli_num_rows($res8ckinup);
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
$hid_edt_te_id = $_POST["hid_edt_te_id"] ? addslashes(trim($_POST["hid_edt_te_id"])) : "";
$adt_te_name = $_POST["te_name"] ? addslashes(trim($_POST["te_name"])) : "";
$adt_te_mobile_no = $_POST["te_mobile_no"] ? addslashes(trim($_POST["te_mobile_no"])) : "";
$adt_te_email = $_POST["te_email"] ? addslashes(trim($_POST["te_email"])) : "";

$the_sl_branch_codes = $_POST["sl_branch_codes"];
if(count($the_sl_branch_codes)>0){
	$the_sl_branch_codes_str = implode(",",$the_sl_branch_codes);
}else{
	$the_sl_branch_codes_str = "";
}

$adt_te_reporting_to = $_POST["te_reporting_to"] ? addslashes(trim($_POST["te_reporting_to"])) : "";
$adt_te_designation = $_POST["te_designation"] ? addslashes(trim($_POST["te_designation"])) : "";
$adt_te_hq = $_POST["te_hq"] ? addslashes(trim($_POST["te_hq"])) : "";
$adt_te_state = $_POST["te_state"] ? addslashes(trim($_POST["te_state"])) : "";
$adt_te_zone = $_POST["te_zone"] ? addslashes(trim($_POST["te_zone"])) : "";
$adt_sl_te_acedns = $_POST["sl_te_acedns"] ? addslashes(trim($_POST["sl_te_acedns"])) : "N";

	if($adt_te_name==""){
		$n_msg = "Please enter TE name";
	}else if($adt_te_mobile_no==""){
		$n_msg = "Please enter Mobile";
	}else{
		$temobile_exist_sts = check_te_mobile_exist_for_update($conn,$hid_edt_te_id,$adt_te_mobile_no);
if($temobile_exist_sts=="YES"){
$n_msg = "The mobile number $adt_te_mobile_no is already exists. Please try another one";	
}else{		
		
$curr_date_time = date("Y-m-d H:i:s");
	
	$sqltestat = "select `acedns`,status_update_datetime from $te_master where `te_id`='$hid_edt_te_id'";
	$restestat = mysqli_query($conn,$sqltestat);
	$rowtestat = mysqli_fetch_assoc($restestat);
	$prev_acedns = $rowtestat["acedns"];
	$prev_status_update_datetime = $rowtestat["status_update_datetime"];
	
	if($prev_acedns!=$adt_sl_te_acedns) {
		$date=gmdate('d',strtotime('+330 minute'));
		$month=gmdate('m',strtotime('+330 minute'));
		$year=gmdate('Y',strtotime('+330 minute'));

		$hour=gmdate('H',strtotime('+330 minute'));
		$minute=gmdate('i',strtotime('+330 minute'));
		$second=gmdate('s',strtotime('+330 minute'));
					//$location_date=$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		$datetime =$year.'-'.$month.'-'.$date.' '.$hour.':'.$minute.':'.$second;
		
		$status_update_datetime=$datetime;
	}
	else 								$status_update_datetime=$prev_status_update_datetime;
	
$sql_in = "update $te_master set `te_name`='$adt_te_name',`te_mobile_no`='$adt_te_mobile_no',`te_email`='$adt_te_email',`branch_code`='$the_sl_branch_codes_str',`reporting_to`='$adt_te_reporting_to',`designation`='$adt_te_designation',`hq`='$adt_te_hq',`state`='$adt_te_state',`zone`='$adt_te_zone',`acedns`='$adt_sl_te_acedns',`last_updated_datetime`='$curr_date_time',`status_update_datetime`='$status_update_datetime' where `te_id`='$hid_edt_te_id'";
	
$res_in = mysqli_query($conn,$sql_in);
$new_gen_noty_id = mysqli_insert_id($conn);
$n_msg = 'The TE details successfully updated.';
header("location:".$page_name."?edt_te_id=".$hid_edt_te_id."&n_msg=".$n_msg);
}
				
}
}

if($_POST["te_save"]=="Save"){
$adt_te_name = $_POST["te_name"] ? addslashes(trim($_POST["te_name"])) : "";
$adt_te_code = $_POST["te_code"] ? addslashes(trim($_POST["te_code"])) : "";
$adt_te_mobile_no = $_POST["te_mobile_no"] ? addslashes(trim($_POST["te_mobile_no"])) : "";
$adt_te_email = $_POST["te_email"] ? addslashes(trim($_POST["te_email"])) : "";
$the_sl_branch_codes = $_POST["sl_branch_codes"];
if(count($the_sl_branch_codes)>0){
	$the_sl_branch_codes_str = implode(",",$the_sl_branch_codes);
}else{
	$the_sl_branch_codes_str = "";
}

$adt_te_reporting_to = $_POST["te_reporting_to"] ? addslashes(trim($_POST["te_reporting_to"])) : "";
$adt_te_designation = $_POST["te_designation"] ? addslashes(trim($_POST["te_designation"])) : "";
$adt_te_hq = $_POST["te_hq"] ? addslashes(trim($_POST["te_hq"])) : "";
$adt_te_state = $_POST["te_state"] ? addslashes(trim($_POST["te_state"])) : "";
$adt_te_zone = $_POST["te_zone"] ? addslashes(trim($_POST["te_zone"])) : "";
$adt_sl_te_acedns = $_POST["sl_te_acedns"] ? addslashes(trim($_POST["sl_te_acedns"])) : "N";

	 if($adt_te_code==""){
		$n_msg = "Please enter TE code / Employee Code";
	}else if($adt_te_name==""){
		$n_msg = "Please enter TE name / Employee name";
	}else if($adt_te_mobile_no==""){
		$n_msg = "Please enter Mobile";
	}else{
		$tecode_exist_sts = check_te_code_exist_for_save($conn,$adt_te_code);
		$temobile_exist_sts = check_te_mobile_exist_for_save($conn,$adt_te_mobile_no);
if($tecode_exist_sts=="YES"){
$n_msg = "The TE code $adt_te_code is already exists. Please try another one";	
}else if($temobile_exist_sts=="YES"){
$n_msg = "The mobile number $adt_te_mobile_no is already exists. Please try another one";	
}else{		
		
$curr_date_time = date("Y-m-d H:i:s");
$sql_in = "insert into $te_master (`te_name`,`te_code`,`te_mobile_no`,`te_email`,`branch_code`,`reporting_to`,`designation`,`hq`,`state`,`zone`,`acedns`,`last_updated_datetime`) values('$adt_te_name','$adt_te_code','$adt_te_mobile_no','$adt_te_email','$the_sl_branch_codes_str','$adt_te_reporting_to','$adt_te_designation','$adt_te_hq','$adt_te_state','$adt_te_zone','$adt_sl_te_acedns','$curr_date_time')";
$res_in = mysqli_query($conn,$sql_in);
if($res_in){
$new_te_id = mysqli_insert_id($conn);
$n_msg = 'The TE details successfully saved.';
header("location:".$page_name."?edt_te_id=".$new_te_id."&n_msg=".$n_msg);
}else{
$n_msg = 'Something went wrong. Please try later.';
header("location:".$page_name."?n_msg=".$n_msg);
}

}
				
}
}

if(isset($_GET["edt_te_id"]) && $_GET["edt_te_id"]!=''){
 $edt_te_id = $_GET["edt_te_id"];
if($the_access_user_type=="MANAGER"){
	/*if($data_show_type=="NE"){
		$sql8 = "select * from $te_master where `te_id`='$edt_te_id' and (`zone` like '%A%' or `zone` like '%B%' or `zone` like '%C%' )";
	}else if($data_show_type=="OSNE"){
		$sql8 = "select * from $te_master where `te_id`='$edt_te_id' and (`zone` like '%D%' or `zone` like '%E%' )";
	}else{
		$sql8 = "select * from $te_master where `te_id`='$edt_te_id'";
	}*/
	if($data_show_type=="ALL"){
		$sql8 = "select * from $te_master where `te_id`='$edt_te_id'";
	}else{
		$sql8 = "select * from $te_master where `te_id`='$edt_te_id' and `zone` like '%".$data_show_type."%' ";
	}
}else{
$sql8 = "select * from $te_master where `te_id`='$edt_te_id'";
}
$res8 = mysqli_query($conn,$sql8);
$totres8 = mysqli_num_rows($res8);
if($totres8>0){
	$row8 = mysqli_fetch_assoc($res8);
	$the_te_name = $row8["te_name"];
	$the_te_code = $row8["te_code"];
	$the_te_mobile_no = $row8["te_mobile_no"];
	$the_te_email = $row8["te_email"];
	$the_branch_code_selected = $row8["branch_code"] ? trim($row8["branch_code"]) : "";
	if($the_branch_code_selected!=""){
		$the_branch_code_selected_arr = explode(",",$the_branch_code_selected);
	}else{
		$the_branch_code_selected_arr = array();
	}
	$the_te_reporting_to = $row8["reporting_to"];
	$the_te_designation = $row8["designation"];
	
	$the_te_hq = $row8["hq"];
	$the_te_state = $row8["state"];
	$the_te_zone = $row8["zone"];
	$the_te_acedns = $row8["acedns"] ? $row8["acedns"] : "N";
	
}else{
header("location:".$back_to_page);
}
}else{
$edt_te_id = "";
$the_te_name = "";
$the_te_code = "";
$the_te_mobile_no = "";
$the_te_email = "";
$the_branch_code_selected_arr = array();
$the_te_reporting_to = "";
$the_te_designation = "";
$the_te_hq = "";
$the_te_state = "";
$the_te_zone = "";
$the_te_acedns = "N";
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
                          <h2>Add/Edit TE &nbsp;&nbsp;&nbsp;<span id="loaddr_msg"><?php if($n_msg!=""){ echo $n_msg;}?></span></h2>
                        </div>
                        <div class="body" style="padding:20px;">
<form action="" method="post" enctype="multipart/form-data" id="send_notification_form" class="send_notification_form">

<div class="form-group">
<label class="TE Code">TE Code / Employee Code</label>
<div class="form-line">
<?php
if($edt_te_id!=""){ 
echo $the_te_code;
}else{ ?>
<input type="text" name="te_code"  id="te_code" class="form-control" placeholder="Enter TE Code"   />
<?php
}
?>
</div>
</div>

<div class="form-group">
<label class="TE Name">TE Name / Employee Name</label>
<div class="form-line">
<input type="text" name="te_name"  id="te_name" class="form-control" value="<?php echo $the_te_name;?>" placeholder="Enter TE Name"  />
</div>
</div>

<div class="form-group">
<label class="TE Name">Branch Names</label>
<div class="form-line">
<select class="form-control sl_branch_codes" id="sl_branch_codes" name="sl_branch_codes[]"  multiple="multiple" data-placeholder="Choose Branch Names...">
<?php
if(count($totres1b)>0){
	while($row1b=mysqli_fetch_assoc($res1b)){
		$the_brmc_code = $row1b["branch_code"];
		$the_brmc_name = $row1b["branch_name"];
	?>
<option value="<?php echo $the_brmc_code;?>" <?php if(in_array($the_brmc_code,$the_branch_code_selected_arr)){ ?> selected="selected" <?php } ?>><?php echo $the_brmc_name;?></option>
	<?php }
}
?>
</select>
</div>
</div>


<div class="form-group">
<label class="Reporting To">Reporting To</label>
<div class="form-line">
<input type="text" name="te_reporting_to"  id="te_reporting_to" class="form-control" value="<?php echo $the_te_reporting_to;?>" placeholder="Enter Reporting To"  />
</div>
</div>

<div class="form-group">
<label class="TE Mobile">Mobile</label>
<div class="form-line">
<input type="text" name="te_mobile_no" maxlength="10"  id="te_mobile_no" class="form-control" value="<?php echo $the_te_mobile_no;?>" placeholder="Enter Mobile Number"   />
</div>
</div>

<div class="form-group">
<label class="TE Email">Email</label>
<div class="form-line">
<input type="text" name="te_email"  id="te_email" class="form-control" value="<?php echo $the_te_email;?>" placeholder="Enter Email"   />
</div>
</div>

<div class="form-group">
<label class="Designation">Designation</label>
<div class="form-line">
<input type="text" name="te_designation"  id="te_designation" class="form-control" value="<?php echo $the_te_designation;?>" placeholder="Enter Designation"  />
</div>
</div>

<div class="form-group">
<label class="HQ">HQ</label>
<div class="form-line">
<input type="text" name="te_hq"  id="te_hq" class="form-control" value="<?php echo $the_te_hq;?>" placeholder="Enter HQ"  />
</div>
</div>

<div class="form-group">
<label class="State">State</label>
<div class="form-line">
<input type="text" name="te_state"  id="te_state" class="form-control" value="<?php echo $the_te_state;?>" placeholder="Enter State"  />
</div>
</div>

<div class="form-group">
<label class="Zone">Zone</label>
<div class="form-line">
<input type="text" name="te_zone"  id="te_zone" class="form-control" value="<?php echo $the_te_zone;?>" placeholder="Enter Zone"  />
</div>
</div>

<div class="form-group">
<label class="Acedns">Acedns</label>
<div class="form-line">
<select class="form-control sl_te_acedns" id="sl_te_acedns" name="sl_te_acedns">
<option value="Y" <?php if($the_te_acedns=="Y"){?> selected="selected" <?php } ?>>Y</option>
<option value="N" <?php if($the_te_acedns=="N"){?> selected="selected" <?php } ?>>N</option>
</select>
</div>
</div>


<?php
if($edt_te_id!=""){ ?>
<input type="submit" class="btn bg-red waves-effect snd_btn" name="te_update" id="te_update"  value="Update" /> 
<input type="hidden" name="hid_edt_te_id" value="<?php echo $edt_te_id;?>" />
<?php }else{
?>
<input type="submit" class="btn bg-red waves-effect snd_btn" name="te_save" id="te_save"  value="Save" />   
<?php
}
?>
<a href="<?php echo $back_to_page;?>" class="btn bg-red waves-effe" style="margin-left:20px;">Back&nbsp;To&nbsp;TE&nbsp;Master</a>
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

jQuery('#sl_branch_codes').chosen({width:"100%",no_results_text:'Oops, no branch found!'});

setTimeout(function(){
	jQuery("#loaddr_msg").html("");
	jQuery("#loaddr_dlt_msg").html("");
},8000);

jQuery(document).on('click', '.dlt_noty_cls', function(event){
var dlt_noty_id = jQuery(this).attr("dlt_noty_id");
if(dlt_noty_id!=''){
	var r = confirm("Do you want to delete the notification details?");
	if (r == true) {
	window.location = '<?php echo $page_name;?>?dlt_noty_id='+dlt_noty_id+'&paged=<?php echo $page;?>';
	} else {
	return false;
	}
}
}); 



jQuery("form#send_notification_form").submit(function(){
	var te_name = jQuery.trim(jQuery("#te_name").val());
	var te_mobile_no = jQuery.trim(jQuery("#te_mobile_no").val());
	var snd_btn = jQuery.trim(jQuery(".snd_btn").val());
	if(te_name==""){
		alert("Please enter TE name.");
		jQuery("#te_name").focus();
		return false;
	}else if(te_mobile_no==""){
		alert("Please enter mobile.");
		jQuery("#te_mobile_no").focus();
		return false;
	}else{
		if(snd_btn=="Save"){
		var te_code = jQuery.trim(jQuery("#te_code").val());
		if(te_code==""){
		alert("Please enter TE code.");
		jQuery("#te_code").focus();
		return false;
		}else{
		return true;
		}
		}else{
		return true;
		}
	}
});



});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>