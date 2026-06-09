<?php
include "web_check.php";
include "star_connection.php";
$table_name = "employee_master";
$branch_schemes_pdf = "branch_schemes_PDF";
$branch_master = "branch_master";
$img_dir = "../schemes/";
$mime_type_array_pdf = array("application/pdf");

if(isset($_GET["n_msg"]) and $_GET["n_msg"]!=""){
$n_msg = $_GET["n_msg"];
}else{
$n_msg = "";
}
/*
if($_POST["snd_btn"]=="Add Scheme"){
$the_astn_branch_code = $_POST["astn_branch_code"] ? addslashes(trim($_POST["astn_branch_code"])) : "";
$nimage_file_name = $_FILES["npdf_file"]["name"];
$nimage_file_type = $_FILES["npdf_file"]["type"];
$nimage_file_size = $_FILES["npdf_file"]["size"];
$nimage_file_tmp = $_FILES["npdf_file"]["tmp_name"];		

	if($the_astn_branch_code==""){
		$n_msg = "Please select branch";
	}else if($nimage_file_name==""){
		$n_msg = "Please select an PDF file.";
	}else{
		$curr_date_time = date("Y-m-d H:i:s");
			if(!in_array($nimage_file_type,$mime_type_array_pdf)){
			$n_msg = 'Please select an PDF file.';
			}else{
			$nimage_file_name = str_replace(" ","_",$nimage_file_name);
			$nimage_file_name = str_replace("-","_",$nimage_file_name);		
			$new_file_name = "pdf_".time()."_".$nimage_file_name;
			$file_up = move_uploaded_file($nimage_file_tmp, $img_dir.$new_file_name);
			$sql_in = "insert into $branch_schemes_pdf (`branch_code`,`PDF_file_name`,`download_time`) values('$the_astn_branch_code','$new_file_name','$curr_date_time')";
			$res_in = mysqli_query($conn,$sql_in);
			if($res_in){
			$n_msg = 'Scheme successfully added.';				
			}else{
			$n_msg = 'Something went wrong. Please try later.';
			}
			}
				
				
	}
}
*/
function show_branch_name_by_id($conn,$brid){
	$brnm = "";
	$branch_master = "branch_master";
	$brid = $brid ? addslashes(trim($brid)) : "";
	if($brid!=""){
		$brsql2 = "select `branch_name` from $branch_master where `branch_code`='$brid'";
		$brres2 = mysqli_query($conn,$brsql2);
		$total_brres2 = mysqli_num_rows($brres2);
		if($total_brres2>0){
			$brrow2 = mysqli_fetch_assoc($brres2);
			$brnm = $brrow2["branch_name"];
		}
	}
	return $brnm;
}
$brsql = "select `branch_code`,`branch_name` from $branch_master where `acedns`='Y' order by `branch_name` asc";
$brres = mysqli_query($conn,$brsql);
$total_brres = mysqli_num_rows($brres);

include "web_header.php";
?>
<style>
.estarix_cls{
	color:#F00;
	margin-left:5px;
}
.branch_err_cls,.pdf_err_cls{
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
                          <h2>ADD NEW SCHEME&nbsp;&nbsp;
                          <a href="schemes.php" class="btn bg-red waves-effe">SHOW SCHEME</a></h2>
                        </div>
                        <div class="body" style="padding:20px;">
<form action="upload_multiple_schemes_pdf.php" method="post" onsubmit="return false;" enctype="multipart/form-data" id="multiple_upload_schemes_pdf_form" class="multiple_upload_schemes_pdf_form">
<div class="form-group">
<input type="hidden" name="pdf_form_submit" value="1"/>
<label class="Select Branch">Select Multiple Branches<span class="estarix_cls">*</span><span class="branch_err_cls"></span></label>
<select class="form-control" id="astn_branch_code" name="astn_branch_code[]" style="padding-left:2px;" data-placeholder="Choose Branches..." multiple>
<?php
if($total_brres>0){
	while($brrow=mysqli_fetch_assoc($brres)){
		$the_br_code = $brrow["branch_code"];
		$the_br_name = $brrow["branch_name"];?>
        <option value="<?php echo $the_br_code;?>"><?php echo $the_br_name." (".$the_br_code.")";?></option>
		<?php
	}
	
}
?>

</select>

</div>

<div class="row clearfix">
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
    <div class="form-group"> 
    <label>Start Date <span class="estarix_cls">*</span></label>
    <div class="form-line">
    <input type="text" class="form-control" readonly="readonly" id="start_dt" name="start_dt" placeholder="Choose start date">
    </div>
    </div>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 ">
    <div class="form-group"> 
    <label>End Date <span class="estarix_cls">*</span></label>
    <div class="form-line">
    <input type="text" class="form-control" readonly="readonly" id="end_dt" name="end_dt" placeholder="Choose end date">
    </div>
    </div>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12">
    <div class="form-group">
    <label>&nbsp;</label>
    <input type="button" value="Reset Date" class="btn bg-red waves-effect srch_reset_btn" />
    </div>
    </div>
    <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12">
    <label>&nbsp;</label>
    <span class="date_err_cls"></span>
    </div>
    </div>

<div class="form-group n_pdf_upload_section">
<label class="Select PDF">Select Multiple Scheme PDF Files<span class="estarix_cls">*</span>&nbsp;( Each scheme PDF file size: less than 10MB ,Max file upload limit:10)&nbsp;<span class="pdf_err_cls"></span></label>
<input type="file" class="form-control" id="npdf_file" name="npdf_file[]" multiple placeholder="Select PDF file">
</div>
<div class="form-group n_pdf_upload_section">
<input type="submit" class="btn btn-primary waves-effect snd_btn" name="snd_btn" id="snd_btn1"  value="Add Scheme" />
</div>
<div class="form-group n_pdf_upload_section"> 
<div class="loaddr_msg" id="loaddr_msg">
<span class="uploading" style="float:left;margin-left:10px;display:none;">
<label>&nbsp;</label>
<img src="images/uploading.gif"/>
</span>
<span class="percent" style="float:left;margin-left:10px;"></span>
<span style="clear:both;display:block;"></span>
</div>
</div>
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
jQuery('#astn_branch_code').chosen({width:"100%",no_results_text:'Oops, no branch found!',search_contains: true});

jQuery(".srch_reset_btn").click(function(){
jQuery("#start_dt").val("");
jQuery("#end_dt").val("");
});

jQuery( "#start_dt" ).datepicker({
changeMonth: true,//this option for allowing user to select month
changeYear: true, //this option for allowing user to select from year range
dateFormat: 'yy-mm-dd',
yearRange: '1910:<?php echo date("Y")+1;?>',
onSelect: function(selected) {
jQuery("#end_dt").datepicker("option","minDate", selected);
}
});

jQuery( "#end_dt" ).datepicker({
changeMonth: true,//this option for allowing user to select month
changeYear: true, //this option for allowing user to select from year range
dateFormat: 'yy-mm-dd',
yearRange: '1910:<?php echo date("Y")+1;?>',
onSelect: function(selected) {
}
});

var percent = jQuery('.percent');
jQuery('#multiple_upload_schemes_pdf_form').on('submit', function(e) {
e.preventDefault(); /* important */
var astn_branch_code = jQuery.trim(jQuery("#astn_branch_code").val());
var start_dt = jQuery.trim(jQuery("#start_dt").val());
var end_dt = jQuery.trim(jQuery("#end_dt").val());
if(astn_branch_code==""){
jQuery(".branch_err_cls").html("Please select branch.");
jQuery("#astn_branch_code").focus();
setTimeout(function(){
jQuery(".branch_err_cls").html("");
},5000);
return false;
}else if(start_dt==""){
jQuery(".date_err_cls").html("Please select start date.");
jQuery("#start_dt").focus();
setTimeout(function(){
jQuery(".date_err_cls").html("");
},5000);
return false;
}else if(end_dt==""){
jQuery(".date_err_cls").html("Please select end date.");
jQuery("#end_dt").focus();
setTimeout(function(){
jQuery(".date_err_cls").html("");
},5000);
return false;
}else{
		jQuery(this).ajaxSubmit({
uploadProgress: function(event, position, total, percentComplete) {
var percentVal = percentComplete + '%';
percent.html(percentVal);
},
beforeSubmit:function(e){	
count = 0;
exc_msg = "";
mx_img_siz = 10;
mx_sz_cnt = 0;
ext_cnt = 0;
no_of_img = 10;
val = jQuery.trim( jQuery('#npdf_file').val() );

if( val == '' ){
count= 1;
exc_msg = exc_msg+" Please select atleast one pdf file.";
}

if( jQuery('#npdf_file').get(0).files.length > no_of_img ){
count= 1;
exc_msg = exc_msg+" Maximum pdf upload limit is "+no_of_img+".";
}

if(count == 0){
for (var i = 0; i < jQuery('#npdf_file').get(0).files.length; ++i) {
img = jQuery('#npdf_file').get(0).files[i].name;
img_siz = jQuery('#npdf_file').get(0).files[i].size;
img_siz_in_mb = (img_siz/(1024*1024));
if(img_siz_in_mb>mx_img_siz){
mx_sz_cnt = mx_sz_cnt+ 1;
count= count+ 1
}
var extension = img.split('.').pop().toUpperCase();
if(extension!="PDF"){
count= count+ 1
ext_cnt = ext_cnt+ 1;
}
}
}

if( count> 0){
if(ext_cnt>0){
exc_msg = exc_msg+" Please select valid pdf file.";
jQuery( ".percent" ).html(exc_msg);
}else if(mx_sz_cnt>0){
exc_msg = exc_msg+" PDF size should be less than "+mx_img_siz+".";
jQuery( ".percent" ).html(exc_msg);
}else{
jQuery( ".percent" ).html(exc_msg);
}
}

if( count> 0){
jQuery( ".percent" ).html( exc_msg );
setTimeout(function(){
jQuery('.percent').html("");	
},8000);
return false;
} else {
jQuery('.uploading').show();
}				

},
success:function(e){
jQuery('.uploading').hide();
},
error:function(e){
},
resetForm: true,
complete: function(xhr) {
result = xhr.responseText;
result = $.parseJSON(result);
if( result.process_sts=="YES"){
jQuery('#astn_branch_code option:selected').removeAttr('selected');
jQuery('#astn_branch_code').trigger('chosen:updated');
jQuery("#start_dt").val("");
jQuery("#end_dt").val("");
jQuery('.percent').html( result.process_msg );
setTimeout(function(){
jQuery('.percent').html("");	
},8000);
}else{
jQuery('.percent').html( result.process_msg );
setTimeout(function(){
jQuery('.percent').html("");	
},8000);
}	
}
});
	}


});


});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>