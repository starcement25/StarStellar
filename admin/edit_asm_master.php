<?php
include "web_check.php";
include "star_connection.php";
$asm_master = "asm_master";
$te_master = "te_master";
$branch_master = "branch_master";

$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];

$sql1b = "select `branch_code`,`branch_name` from $branch_master order by `branch_name` asc";
$res1b = mysqli_query($conn,$sql1b);
$totres1b = mysqli_num_rows($res1b);


$img_dir = "noty_images/";
$image_link_url = $server_url."admin/noty_images/";
$mime_type_array = array("image/jpeg", "image/png","image/jpg");
$mime_type_array_pdf = array("application/pdf");
$n_dlt_msg = "";
$n_msg = "";
$submsg = "";
$add_page_name = "edit_asm_master.php";
$page_name = "edit_asm_master.php";
$back_to_page = "asm_master.php";

function get_branchs_of_te($conn,$te_code){
$each_branch_code_str = "";
$te_master = "te_master";
$te_code = $te_code ? addslashes(trim($te_code)) : "";
if($te_code!=""){
$sql8ckinup = "select `branch_code` from $te_master where `te_code`='$te_code'";
$res8ckinup = mysqli_query($conn,$sql8ckinup);
$totres8ckinup = mysqli_num_rows($res8ckinup);
if($totres8ckinup>0){
$row8ckinup=mysqli_fetch_assoc($res8ckinup);
$each_branch_code = addslashes(trim($row8ckinup["branch_code"]));
if($each_branch_code!=""){
	$each_branch_code_arr = explode(",",$each_branch_code);
	$each_branch_code_str = implode("','",$each_branch_code_arr);
}
}
}
return $each_branch_code_str;
}


function check_asm_branch_exist_for_save($conn,$bcod){
$brnch_data = array("exists"=>"NO","branch_name"=>"");
$asm_master = "asm_master";
$bcod = $bcod ? addslashes(trim($bcod)) : "";
if($bcod!=""){
$sql8ckinup = "select `branch_code`,`branch` from $asm_master where `branch_code`='$bcod'";
$res8ckinup = mysqli_query($conn,$sql8ckinup);
$totres8ckinup = mysqli_num_rows($res8ckinup);
if($totres8ckinup>0){
$row8ckinup = mysqli_fetch_assoc($res8ckinup);
$ftc_branch_name = $row8ckinup["branch"];	
$brnch_data = array("exists"=>"YES","branch_name"=>$ftc_branch_name);
}
}
return $brnch_data;
}

function check_asm_branch_exist_for_update($conn,$asmid,$bcod){
$brnch_data = array("exists"=>"NO","branch_name"=>"");
$asm_master = "asm_master";
$asmid = $asmid ? addslashes(trim($asmid)) : "";
$bcod = $bcod ? addslashes(trim($bcod)) : "";
if($bcod!="" && $asmid!=""){
$sql8ckinup = "select `branch_code`,`branch` from $asm_master where `branch_code`='$bcod' and `asm_id`!='$asmid' ";
$res8ckinup = mysqli_query($conn,$sql8ckinup);
$totres8ckinup = mysqli_num_rows($res8ckinup);
if($totres8ckinup>0){
$row8ckinup = mysqli_fetch_assoc($res8ckinup);
$ftc_branch_name = $row8ckinup["branch"];	
$brnch_data = array("exists"=>"YES","branch_name"=>$ftc_branch_name);
}
}
return $brnch_data;
}

if(isset($_GET["n_msg"]) and $_GET["n_msg"]!=""){
$n_msg = $_GET["n_msg"];
}else{
$n_msg = "";
}


if($_POST["e_save"]=="Save"){
$sl_branch_codes = $_POST["sl_branch_codes"] ? addslashes(trim($_POST["sl_branch_codes"])) : "";
$asm_name = $_POST["asm_name"] ? addslashes(trim($_POST["asm_name"])) : "";
$asm_mobile_no = $_POST["asm_mobile_no"] ? addslashes(trim($_POST["asm_mobile_no"])) : "";
$asm_email = $_POST["asm_email"] ? addslashes(trim($_POST["asm_email"])) : "";
if($sl_branch_codes==""){
$n_msg = "Please select branch";
}else{

$branch_exist_data_arr = check_asm_branch_exist_for_save($conn,$sl_branch_codes);
$branch_exist_sts = $branch_exist_data_arr["exists"];
$branch_exist_name = $branch_exist_data_arr["branch_name"];
if($branch_exist_sts=="YES"){
$n_msg = "The branch $branch_exist_name is already exists. Please try another one";	
}else{		


if($asm_name==""){
$n_msg = "Please enter ASM name";
}else if($asm_email==""){
$n_msg = "Please enter email";
}else{

$sl_branch_name = "";
$sqlbdv = "select `branch_name` from $branch_master where `branch_code`='$sl_branch_codes'";
$resbdv = mysqli_query($conn,$sqlbdv);
$totresbdv = mysqli_num_rows($resbdv);	
if($totresbdv>0){
$rowbdv = mysqli_fetch_assoc($resbdv);
$sl_branch_name = $rowbdv["branch_name"];	
}


$sql_in = "insert into $asm_master (`branch_code`,`branch`,`asm_name`,`ph_no`,`email`) values ('$sl_branch_codes','$sl_branch_name','$asm_name','$asm_mobile_no','$asm_email')";
$res_in = mysqli_query($conn,$sql_in);
if($res_in){
	$last_saved_id = mysqli_insert_id($conn);
$n_msg = 'The ASM details successfully saved.';
header("location:".$page_name."?edt_asm_id=".$last_saved_id."&n_msg=".$n_msg);

}else{
$n_msg = 'Failed to save details.';	
}
}

}

}

}

if($_POST["e_update"]=="Update"){
$hid_edt_asm_id = $_POST["hid_edt_asm_id"] ? addslashes(trim($_POST["hid_edt_asm_id"])) : "";
$sl_branch_codes = $_POST["sl_branch_codes"] ? addslashes(trim($_POST["sl_branch_codes"])) : "";
$asm_name = $_POST["asm_name"] ? addslashes(trim($_POST["asm_name"])) : "";
$asm_mobile_no = $_POST["asm_mobile_no"] ? addslashes(trim($_POST["asm_mobile_no"])) : "";
$asm_email = $_POST["asm_email"] ? addslashes(trim($_POST["asm_email"])) : "";
if($sl_branch_codes==""){
$n_msg = "Please select branch";
}else{

$branch_exist_data_arr = check_asm_branch_exist_for_update($conn,$hid_edt_asm_id,$sl_branch_codes);
$branch_exist_sts = $branch_exist_data_arr["exists"];
$branch_exist_name = $branch_exist_data_arr["branch_name"];
if($branch_exist_sts=="YES"){
$n_msg = "The branch $branch_exist_name is already exists. Please try another one";	
}else{		


if($asm_name==""){
$n_msg = "Please enter ASM name";
}else if($asm_email==""){
$n_msg = "Please enter email";
}else{
$sl_branch_name = "";
$sqlbdv = "select `branch_name` from $branch_master where `branch_code`='$sl_branch_codes'";
$resbdv = mysqli_query($conn,$sqlbdv);
$totresbdv = mysqli_num_rows($resbdv);	
if($totresbdv>0){
$rowbdv = mysqli_fetch_assoc($resbdv);
$sl_branch_name = $rowbdv["branch_name"];	
}


$sql_in = "update $asm_master set `branch_code`='$sl_branch_codes',`branch`='$sl_branch_name',`asm_name`='$asm_name',`ph_no`='$asm_mobile_no',`email`='$asm_email' where `asm_id`='$hid_edt_asm_id'";
$res_in = mysqli_query($conn,$sql_in);
$n_msg = 'The ASM details successfully updated.';
header("location:".$page_name."?edt_asm_id=".$hid_edt_asm_id."&n_msg=".$n_msg);

}

}

}

}

if(isset($_GET["edt_asm_id"]) && $_GET["edt_asm_id"]!=''){
 $edt_asm_id = $_GET["edt_asm_id"];
$sql8 = "select * from $asm_master where `asm_id`='$edt_asm_id'";
$res8 = mysqli_query($conn,$sql8);
$totres8 = mysqli_num_rows($res8);
if($totres8>0){
	$row8 = mysqli_fetch_assoc($res8);
	$the_branch_code = $row8["branch_code"];
	$the_asm_name = $row8["asm_name"];
	$the_ph_no = trim($row8["ph_no"]);
	$the_email = $row8["email"];
}else{
$edt_asm_id = "";
$the_branch_code = "";
$the_asm_name = "";
$the_ph_no = "";
$the_email = "";
}
}else{
$edt_asm_id = "";
$the_branch_code = "";
$the_asm_name = "";
$the_ph_no = "";
$the_email = "";
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
                          <h2>Edit ASM &nbsp;&nbsp;&nbsp;<span id="loaddr_msg"><?php if($n_msg!=""){ echo $n_msg;}?></span></h2>
                        </div>
                        <div class="body" style="padding:20px;">
<form action="" method="post" enctype="multipart/form-data" id="asm_save_form" class="asm_save_form">

<div class="form-group">
<label class="Branch Name">Branch Names</label>
<div class="form-line">
<select class="form-control sl_branch_codes" id="sl_branch_codes" name="sl_branch_codes" autocomplete="off">
<option value="">Select Branch</option>
<?php
if(count($totres1b)>0){
	while($row1b=mysqli_fetch_assoc($res1b)){
		$the_brmc_code = $row1b["branch_code"];
		$the_brmc_name = $row1b["branch_name"];
	?>
<option value="<?php echo $the_brmc_code;?>" <?php if($the_brmc_code==$the_branch_code){ ?> selected="selected" <?php } ?>><?php echo $the_brmc_name;?></option>
	<?php }
}
?>
</select>
</div>
</div>

<div class="form-group">
<label for="ASM Name">ASM Name</label>
<div class="form-line">
<input type="text" name="asm_name"  id="asm_name" class="form-control" value="<?php echo $the_asm_name;?>" placeholder="Enter ASM Name"  />
</div>
</div>

<div class="form-group">
<label for="ASM Mobile">Mobile</label>
<div class="form-line">
<input type="text" name="asm_mobile_no" maxlength="10"  id="asm_mobile_no" class="form-control" value="<?php echo $the_ph_no;?>" placeholder="Enter Mobile Number"   />
</div>
</div>

<div class="form-group">
<label for="ASM Email">Email</label>
<div class="form-line">
<input type="text" name="asm_email"  id="asm_email" class="form-control" value="<?php echo $the_email;?>" placeholder="Enter Email"   />
</div>
</div>

<?php
if($edt_asm_id!=""){ ?>
<input type="submit" class="btn bg-red waves-effect snd_btn" name="e_update" id="e_update"  value="Update" /> 
<input type="hidden" name="hid_edt_asm_id" value="<?php echo $edt_asm_id;?>" />
<?php }else{
?>
<input type="submit" class="btn bg-red waves-effect snd_btn" name="e_save" id="e_save"  value="Save" />   
<?php
}
?>
<a href="<?php echo $back_to_page;?>" class="btn bg-red waves-effe" style="margin-left:20px;">Back&nbsp;To&nbsp;ASM&nbsp;Master</a>
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

setTimeout(function(){
	jQuery("#loaddr_msg").html("");
	jQuery("#loaddr_dlt_msg").html("");
},8000);

jQuery('#sl_branch_codes').chosen({width:"100%",no_results_text:'Oops, no branch found!'});


jQuery("form#asm_save_form").submit(function(){
	var sl_branch_codes = jQuery.trim(jQuery("#sl_branch_codes").val());
	var asm_name = jQuery.trim(jQuery("#asm_name").val());
	var asm_email = jQuery.trim(jQuery("#asm_email").val());
	if(sl_branch_codes==""){
		alert("Please select branch.");
		jQuery("#sl_branch_codes").focus();
		return false;
	}else if(asm_name==""){
		alert("Please enter ASM name.");
		jQuery("#asm_name").focus();
		return false;
	}else if(asm_email==""){
		alert("Please enter email.");
		jQuery("#asm_email").focus();
		return false;
	}else{
		return true;
	}
});



});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>