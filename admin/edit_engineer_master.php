<?php
include "web_check.php";
include "star_connection.php";
$engineer_master = "engineer_master";
$te_master = "te_master";
$branch_master = "branch_master";

$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}

$img_dir = "noty_images/";
$image_link_url = $server_url."admin/noty_images/";
$mime_type_array = array("image/jpeg", "image/png","image/jpg");
$mime_type_array_pdf = array("application/pdf");
$n_dlt_msg = "";
$n_msg = "";
$submsg = "";
$add_page_name = "edit_engineer_master.php";
$page_name = "edit_engineer_master.php";
$back_to_page = "engineer_master.php";

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

function check_engineer_mobile_exist_for_save($conn,$emobile){
$exists = "NO";
$engineer_master = "engineer_master";
$emobile = $emobile ? addslashes(trim($emobile)) : "";
if($emobile!=""){
$sql8ckinup = "select `e_mobile` from $engineer_master where `e_mobile`='$emobile'";
$res8ckinup = mysqli_query($conn,$sql8ckinup);
$totres8ckinup = mysqli_num_rows($res8ckinup);
if($totres8ckinup>0){
$exists = "YES";
}
}
return $exists;
}
function check_engineer_mobile_exist_for_update($conn,$eid,$tmobile){
$exists = "NO";
$engineer_master = "engineer_master";
$eid = $eid ? addslashes(trim($eid)) : "";
$tmobile = $tmobile ? addslashes(trim($tmobile)) : "";
if($tmobile!="" && $eid!=""){
$sql8ckinup = "select `te_mobile_no` from $engineer_master where `e_mobile`='$tmobile' and `eid`!='$eid' ";
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

if($_POST["e_update"]=="Update"){
$hid_edt_e_id = $_POST["hid_edt_e_id"] ? addslashes(trim($_POST["hid_edt_e_id"])) : "";
$adt_e_name = $_POST["e_name"] ? addslashes(trim($_POST["e_name"])) : "";
$adt_e_mobile_no = $_POST["e_mobile_no"] ? addslashes(trim($_POST["e_mobile_no"])) : "";
$adt_e_email = $_POST["e_email"] ? addslashes(trim($_POST["e_email"])) : "";
$the_sl_branch_codes = $_POST["sl_branch_codes"];
if(count($the_sl_branch_codes)>0){
	$the_sl_branch_codes_str = implode(",",$the_sl_branch_codes);
}else{
	$the_sl_branch_codes_str = "";
}
$adt_e_dob = $_POST["e_dob"] ? addslashes(trim($_POST["e_dob"])) : "";
$adt_e_dom = $_POST["e_dom"] ? addslashes(trim($_POST["e_dom"])) : "";
$adt_e_address = $_POST["e_address"] ? addslashes(trim($_POST["e_address"])) : "";
$adt_e_pin = $_POST["e_pin"] ? addslashes(trim($_POST["e_pin"])) : "";
$adt_e_state = $_POST["e_state"] ? addslashes(trim($_POST["e_state"])) : "";
$adt_e_city_town = $_POST["e_city_town"] ? addslashes(trim($_POST["e_city_town"])) : "";
$adt_e_zone = $_POST["e_zone"] ? addslashes(trim($_POST["e_zone"])) : "";


	if($adt_e_name==""){
		$n_msg = "Please enter engineer name";
	}else if($adt_e_mobile_no==""){
		$n_msg = "Please enter mobile";
	}else if($adt_e_email==""){
		$n_msg = "Please enter email";
	}else{
		$temobile_exist_sts = check_engineer_mobile_exist_for_update($conn,$hid_edt_e_id,$adt_e_mobile_no);
if($temobile_exist_sts=="YES"){
$n_msg = "The mobile number $adt_e_mobile_no is already exists. Please try another one";	
}else{		
		
$curr_date_time = date("Y-m-d H:i:s");
$sql_in = "update $engineer_master set `e_name`='$adt_e_name',`e_mobile`='$adt_e_mobile_no',`e_email`='$adt_e_email',`branch_code`='$the_sl_branch_codes_str',`e_dob`='$adt_e_dob',`e_dom`='$adt_e_dom',`e_address`='$adt_e_address',`e_pin`='$adt_e_pin',`e_state`='$adt_e_state',`e_city_town`='$adt_e_city_town',`e_zone`='$adt_e_zone',`last_updated_datetime`='$curr_date_time' where `eid`='$hid_edt_e_id'";
$res_in = mysqli_query($conn,$sql_in);
$n_msg = 'The engineer details successfully updated.';
header("location:".$page_name."?edt_e_id=".$hid_edt_e_id."&n_msg=".$n_msg);
}
				
}
}

if(isset($_GET["edt_e_id"]) && $_GET["edt_e_id"]!=''){
 $edt_e_id = $_GET["edt_e_id"];
 if($the_access_user_type=="MANAGER"){
	/*if($data_show_type=="NE"){
		$sql8 = "select * from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where `eid`='$edt_e_id' and ($te_master.`zone` like '%A%' or $te_master.`zone` like '%B%' or $te_master.`zone` like '%C%' )";
	}else if($data_show_type=="OSNE"){
		$sql8 = "select * from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where `eid`='$edt_e_id' and ($te_master.`zone` like '%D%' or $te_master.`zone` like '%E%' )";
	}else{
		$sql8 = "select * from $engineer_master where `eid`='$edt_e_id'";
	}*/
	if($data_show_type=="ALL"){
		$sql8 = "select * from $engineer_master where `eid`='$edt_e_id'";
	}else{
		$sql8 = "select * from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where `eid`='$edt_e_id' and $te_master.`zone` like '%".$data_show_type."%' ";
	}
	
}else{
$sql8 = "select * from $engineer_master where `eid`='$edt_e_id'";
}
$res8 = mysqli_query($conn,$sql8);
$totres8 = mysqli_num_rows($res8);
if($totres8>0){
	$row8 = mysqli_fetch_assoc($res8);
	$the_e_name = $row8["e_name"];
	$the_e_mobile = $row8["e_mobile"];
	$the_te_code = trim($row8["te_code"]);
	$the_e_email = $row8["e_email"];
	$the_e_branch_code = trim($row8["branch_code"]);
	if($the_e_branch_code!=""){
		$selected_branch_code_arr = explode(",",$the_e_branch_code);
	}else{
		$selected_branch_code_arr = array();
	}
	$the_e_dob = $row8["e_dob"];
	$the_e_dom = $row8["e_dom"];
	$the_e_address = $row8["e_address"];
	$the_e_pin = $row8["e_pin"];
	$the_e_state = $row8["e_state"];
	$the_e_city_town = $row8["e_city_town"];
	$the_e_zone = $row8["e_zone"];
	$the_e_points = $row8["e_points"];
	$the_branch_code_for_te = get_branchs_of_te($conn,$the_te_code);
$sql1b = "select `branch_code`,`branch_name` from $branch_master where `branch_code` in('".$the_branch_code_for_te."') order by `branch_name` asc";
$res1b = mysqli_query($conn,$sql1b);
$totres1b = mysqli_num_rows($res1b);
	
	
}else{
header("location:".$back_to_page);
/*$selected_branch_code_arr = array();
$totres1b = 0;
$edt_e_id = "";
$the_e_name = "";
$the_e_mobile = "";
$the_te_code = "";
$the_e_email = "";
$the_e_dob = "";
$the_e_dom = "";
$the_e_address = "";
$the_e_pin = "";
$the_e_state = "";
$the_e_city_town = "";
$the_e_points = "";
*/}
}else{
header("location:".$back_to_page);
/*$selected_branch_code_arr = array();
$totres1b = 0;
$edt_e_id = "";
$the_e_name = "";
$the_e_mobile = "";
$the_te_code = "";
$the_e_email = "";
$the_e_dob = "";
$the_e_dom = "";
$the_e_address = "";
$the_e_pin = "";
$the_e_state = "";
$the_e_city_town = "";
$the_e_points = "";
*/}
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
                          <h2>Edit Engineer &nbsp;&nbsp;&nbsp;<span id="loaddr_msg"><?php if($n_msg!=""){ echo $n_msg;}?></span></h2>
                        </div>
                        <div class="body" style="padding:20px;">
<form action="" method="post" enctype="multipart/form-data" id="send_notification_form" class="send_notification_form">
<div class="form-group">
<label for="Engineer Name">Engineer Name</label>
<div class="form-line">
<input type="text" name="e_name"  id="e_name" class="form-control" value="<?php echo $the_e_name;?>" placeholder="Enter Engineer Name"  />
</div>
</div>
<div class="form-group">
<label for="TE Code">TE Code</label>
<div class="form-line">
<?php
if($edt_e_id!=""){ 
echo $the_te_code;
}
?>
</div>
</div>

<div class="form-group">
<label for="Engineer Mobile">Mobile</label>
<div class="form-line">
<input type="text" name="e_mobile_no" maxlength="10"  id="e_mobile_no" class="form-control" value="<?php echo $the_e_mobile;?>" placeholder="Enter Mobile Number"   />
</div>
</div>

<div class="form-group">
<label for="Engineer Email">Email</label>
<div class="form-line">
<input type="text" name="e_email"  id="e_email" class="form-control" value="<?php echo $the_e_email;?>" placeholder="Enter Email"   />
</div>
</div>

<div class="form-group">
<label class="Branch Name">Branch Names</label>
<div class="form-line">
<select class="form-control sl_branch_codes" id="sl_branch_codes" name="sl_branch_codes[]"  multiple="multiple" data-placeholder="Choose Branch Names...">
<?php
if(count($totres1b)>0){
	while($row1b=mysqli_fetch_assoc($res1b)){
		$the_brmc_code = $row1b["branch_code"];
		$the_brmc_name = $row1b["branch_name"];
	?>
<option value="<?php echo $the_brmc_code;?>" <?php if(in_array($the_brmc_code,$selected_branch_code_arr)){ ?> selected="selected" <?php } ?>><?php echo $the_brmc_name;?></option>
	<?php }
}
?>
</select>
</div>
</div>

<div class="form-group">
<label for="Engineer DOB">Birthday</label>
<div class="form-line">
<input type="text" name="e_dob"  id="e_dob" class="form-control" value="<?php echo $the_e_dob;?>" placeholder="Enter DOB"   />
</div>
</div>

<div class="form-group">
<label for="Engineer DOM">Anniversary</label>
<div class="form-line">
<input type="text" name="e_dom"  id="e_dom" class="form-control" value="<?php echo $the_e_dom;?>" placeholder="Enter DOM"   />
</div>
</div>

<div class="form-group">
<label for="Engineer Address">Address</label>
<div class="form-line">
<textarea name="e_address"  id="e_address" class="form-control" value="" placeholder="Enter Address"><?php echo $the_e_address;?></textarea>
</div>
</div>

<div class="form-group">
<label for="Engineer Pin">Pin</label>
<div class="form-line">
<input type="text" name="e_pin"  id="e_pin" class="form-control" value="<?php echo $the_e_pin;?>" placeholder="Enter Pin"   />
</div>
</div>

<div class="form-group">
<label for="Engineer State">State</label>
<div class="form-line">
<input type="text" name="e_state"  id="e_state" class="form-control" value="<?php echo $the_e_state;?>" placeholder="Enter State"   />
</div>
</div>

<div class="form-group">
<label for="Engineer City">City</label>
<div class="form-line">
<input type="text" name="e_city_town"  id="e_city_town" class="form-control" value="<?php echo $the_e_city_town;?>" placeholder="Enter City"   />
</div>
</div>

<div class="form-group">
	<label for="Engineer Zone">Zone</label>
	<div class="form-line">
		<select name="e_zone"  id="e_zone" class="form-control">
			<?php $sql = "SELECT DISTINCT zone FROM branch_master";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) { ?>
				<option value="<?php echo $row['zone']; ?>" <?php if ($the_e_zone == $row['zone']) echo 'selected'; ?>>
					<?php echo $row['zone']; ?>
				</option>
				<?php }
			} ?>
		</select>
	</div>
</div>

<?php
if($edt_e_id!=""){ ?>
<input type="submit" class="btn bg-red waves-effect snd_btn" name="e_update" id="e_update"  value="Update" /> 
<input type="hidden" name="hid_edt_e_id" value="<?php echo $edt_e_id;?>" />
<?php }else{
?>
<input type="submit" class="btn bg-red waves-effect snd_btn" name="e_save" id="e_save"  value="Save" />   
<?php
}
?>
<a href="<?php echo $back_to_page;?>" class="btn bg-red waves-effe" style="margin-left:20px;">Back&nbsp;To&nbsp;Engineer&nbsp;Master</a>
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


jQuery("#e_dob").datepicker({
dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});

jQuery("#e_dom").datepicker({
dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});

jQuery("form#send_notification_form").submit(function(){
	var e_name = jQuery.trim(jQuery("#e_name").val());
	var e_mobile_no = jQuery.trim(jQuery("#e_mobile_no").val());
	var e_email = jQuery.trim(jQuery("#e_email").val());
	if(e_name==""){
		alert("Please enter engineer name.");
		jQuery("#e_name").focus();
		return false;
	}else if(e_mobile_no==""){
		alert("Please enter mobile.");
		jQuery("#e_mobile_no").focus();
		return false;
	}else if(e_email==""){
		alert("Please enter email.");
		jQuery("#e_email").focus();
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