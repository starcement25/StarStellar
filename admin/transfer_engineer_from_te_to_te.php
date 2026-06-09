<?php
include "web_check.php";
include "star_connection.php";
$te_master = "te_master";
$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}
$the_te_master_array = array();
/*if($data_show_type=='NE'){
$sqldwd = "select `te_code`,`te_name` from $te_master where (`zone` like '%A%' or `zone` like '%B%' or `zone` like '%C%' ) order by `te_name` asc";
}else if($data_show_type=='OSNE'){
$sqldwd = "select `te_code`,`te_name` from $te_master where (`zone` like '%D%' or `zone` like '%E%' ) order by `te_name` asc";
}else{
$sqldwd = "select `te_code`,`te_name` from $te_master order by `te_name` asc";
}*/
if($data_show_type=='ALL'){
$sqldwd = "select `te_code`,`te_name` from $te_master order by `te_name` asc";	
}else{
$sqldwd = "select `te_code`,`te_name` from $te_master where `zone` like '%".$data_show_type."%' order by `te_name` asc";	
}


$res1dwd = mysqli_query($conn,$sqldwd);
$totres1dwd = mysqli_num_rows($res1dwd);
if($totres1dwd>0){
	while($row1dwd=mysqli_fetch_assoc($res1dwd)){
		$ftc_te_code = $row1dwd["te_code"];
		$ftc_te_name = $row1dwd["te_name"];
		$the_te_master_array[] = array("te_code"=>$ftc_te_code,"te_name"=>$ftc_te_name);
	}
}

$page_name = "transfer_engineer_from_te_to_te.php";
$main_page_name = "transfer_engineer_from_te_to_te.php";
if(@isset($_GET["submsg"]) && $_GET["submsg"]!=""){
	$submsg =$_GET["submsg"];
}else{
	$submsg ="";
}

include "web_header.php";
?>
<style>
#all_tf_gdn_stock_data_div_container .media_menu,#all_tt_gdn_stock_data_div_container .media_menu{
	margin-bottom:5px;
	padding:5px;
}
#all_tf_gdn_stock_data_div_container .media_menu .media-body .media-heading,#all_tt_gdn_stock_data_div_container .media_menu .media-body .media-heading{
margin:0px 0px 5px 0px;
font-size:12px
}
#all_tf_gdn_stock_data_div_container .media_menu .media-body{
	position:relative;
}
#all_tf_gdn_stock_data_div_container .media_menu .media-body .checkbox{
	margin: 0;
    position: absolute;
    right: 0;
    top: 0;
}
#all_tf_gdn_stock_data_div_container .media_menu .media-body .checkbox input[type="checkbox"]{
left: 0px !important;
opacity: 1 !important;
margin:0px !important;
}
#all_tf_gdn_stock_data_div_container .media_menu .media-body .media_font2,#all_tt_gdn_stock_data_div_container .media_menu .media-body .media_font2{
	margin:0px;
	text-align:right;
}
#all_tf_gdn_stock_data_div_container .media_menu .media-body .media_font2 .edt_fm_in_btn,#all_tf_gdn_stock_data_div_container .media_menu .media-body .media_font2 .down_in_fm_btn,#all_tf_gdn_stock_data_div_container .media_menu .media-body .media_font2 .up_in_fm_btn,#all_tt_gdn_stock_data_div_container .media_menu .media-body .media_font2 .edt_sm_in_btn,#all_tt_gdn_stock_data_div_container .media_menu .media-body .media_font2 .down_in_sm_btn,#all_tt_gdn_stock_data_div_container .media_menu .media-body .media_font2 .up_in_sm_btn{
	width:25px;
	cursor: pointer;
}
.cross_btn_stk{
position: absolute;
right: 5px;
top: 4px;
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
                          <h2>Transfer Engineer</h2>

                        </div>
<div class="body">
<div class="table-responsive">

<div class="row clearfix" style="margin:0px;">
<div class="col-xs-12 col-sm-5 col-md-5 col-lg-5" style="padding-right:0px;padding-left:0px;"> 
      <div class="panel panel-default">
         <div class="panel-heading accordion_head" style="padding-top:3px;padding-left: 5px;
    padding-right: 5px;">
         <div class="form-group" style="margin-bottom:0px;">
        <label for="Transfer From" style="width:100%;">Transfer From TE <span id="tf_te_loader" style="margin-left:5px; width:30px;"></span> &nbsp;&nbsp;<a href="javascript:void(0);" class="ckalleng">Check all</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" class="unckalleng">Uncheck all</a></label>
<div class="form-line">
<select name="tf_te_id" id="tf_te_id" class="form-control">
<option value="">Select a TE</option>
<?php
if(count($the_te_master_array)>0){
	foreach($the_te_master_array as $ki=>$tf_te_dtl){
		$the_tf_te_code = $tf_te_dtl["te_code"];
		$the_tf_te_name = $tf_te_dtl["te_name"];
?>
<option value="<?php echo $the_tf_te_code;?>"><?php echo $the_tf_te_name." (".$the_tf_te_code.")";?></option>
<?php
	}
}
?>
</select>
</div>
</div>
<div class="form-group" style="margin-bottom:0px;margin-top:6px;">
<div class="form-line">
<div style="width:78%;float:left;position:relative;">
<a href="javascript:void(0);" class="cross_btn_stk"><img src="images/circle_cross.png" /></a>
<input class="form-control" id="tf_stk_chass_nm" value="" name="tf_stk_chass_nm" style="padding:2px 2px 2px 6px;" placeholder="Search Engineer (Name,Mobile,Email)" type="text">
</div>
<div style="width:22%;float:left;text-align: right;"><input type="button" name="tf_stk_srch_btn" class="btn bg-red waves-effect tf_stk_srch_btn" id="tf_stk_srch_btn" value="Search" /></div>
<span style="clear:both;display:block;"></span>
</div>
</div>

</div>
<div class="panel-body accordion_body" id="all_tf_gdn_stock_data_div_container" style="height:500px; background-color:#E8E8E8; overflow:scroll;padding:5px;">

         </div>
    </div>
    </div>
<div class="col-xs-12 col-sm-2 col-md-2 col-lg-2" style="padding-right:0px;text-align:center;">
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-right:0px;text-align:center;">
       <input type="button" name="stk_transfer" class="btn bg-red waves-effect stk_transfer" id="stk_transfer" value="Transfer" />
</div>
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="padding-right:0px;text-align:center;">
<span id="transfer_msg"></span>
</div>
   </div>
<div class="col-xs-12 col-sm-5 col-md-5 col-lg-5" style="padding-right:0px;">      
      <div class="panel panel-default">
         <div class="panel-heading accordion_head3" style="padding-top:3px;">
         <div class="form-group" style="margin-bottom:0px;">
        <label for="Transfer To" style="width:100%;">Transfer To TE <span id="tt_te_loader" style="margin-left:5px; width:30px;"></span></label>
<div class="form-line">
<select name="tt_te_id" id="tt_te_id" class="form-control">
<option value="">Select a TE</option>
<?php
if(count($the_te_master_array)>0){
	foreach($the_te_master_array as $ki2=>$tt_te_dtl){
		$the_tt_te_code = $tt_te_dtl["te_code"];
		$the_tt_te_name = $tt_te_dtl["te_name"];
?>
<option value="<?php echo $the_tt_te_code;?>"><?php echo $the_tt_te_name." (".$the_tt_te_code.")";?></option>
<?php
	}
}
?>
</select>
</div>
</div>
         </div>
<div class="panel-body accordion_body3" id="all_tt_gdn_stock_data_div_container" style="background-color:#E8E8E8; overflow:scroll; height:500px;padding:5px;">
         
         </div>
    </div>
    </div>
</div>

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

var xhr_load_tf_te_engineer;
var xhr_load_tt_te_engineer;


jQuery(".ckalleng").click(function(){
jQuery(".tf_ck_id").prop("checked", true);
});

jQuery(".unckalleng").click(function(){
jQuery(".tf_ck_id").prop("checked", false);
});


jQuery('#tf_te_id').chosen({width:"100%",search_contains: true,no_results_text:'Oops, no TE found!'}).change(function(){
	var tf_te_code = jQuery.trim(jQuery(this).val());
	var tf_stk_chass_nm = jQuery.trim(jQuery("#tf_stk_chass_nm").val());
	load_tf_te_engineer(tf_te_code,tf_stk_chass_nm);
});

jQuery('#tt_te_id').chosen({width:"100%",search_contains: true,no_results_text:'Oops, no TE found!'}).change(function(){
	var tt_te_id = jQuery.trim(jQuery(this).val());
	load_tt_te_engineer(tt_te_id);
});

jQuery("#tf_stk_srch_btn").click(function(){
	var tf_te_code = jQuery("#tf_te_id").val();
	var tf_stk_chass_nm = jQuery.trim(jQuery("#tf_stk_chass_nm").val());
	if(tf_te_code==""){
		alert("Please select a TE.");
		jQuery("#tf_te_id").focus();
	}else{
	load_tf_te_engineer(tf_te_code,tf_stk_chass_nm);
	}
});


jQuery('#tf_stk_chass_nm').keyup(function () {
var tf_stk_chass_nm = jQuery.trim(jQuery(this).val());
if(tf_stk_chass_nm!=""){
jQuery('.cross_btn_stk').show();	
}else{
jQuery('.cross_btn_stk').hide();	
}
});

jQuery(".cross_btn_stk").click(function(){
	var tf_te_id = jQuery("#tf_te_id").val();
	var tf_stk_chass_nm = "";
	if(tf_te_id==""){
		jQuery("#tf_stk_chass_nm").val("");
		jQuery('.cross_btn_stk').hide();
	}else{
		jQuery("#tf_stk_chass_nm").val("");
		jQuery('.cross_btn_stk').hide();
	load_tf_te_engineer(tf_te_id,tf_stk_chass_nm);
	}
});


var xhrMakeStockTransfer;
jQuery("#stk_transfer").click(function(){
	var tf_gdn_id = jQuery("#tf_te_id").val();
	var tt_gdn_id = jQuery("#tt_te_id").val();
	var tf_stk_chass_nm = jQuery.trim(jQuery("#tf_stk_chass_nm").val());
	var selected_tf_selMulti=[];
	jQuery("#all_tf_gdn_stock_data_div_container .tf_ck_id:checked").each(function() {
	selected_tf_selMulti.push(encodeURIComponent(this.value));
	});
	var quick_tf_ids = selected_tf_selMulti.join(";");
	if(tf_gdn_id==""){
		alert("Please select Transfer From TE.");
		jQuery("#tf_te_id").focus();
	}else if(selected_tf_selMulti.length==0){
		alert("Please check atleast one engineer of Transfer From TE.");
	}else if(tt_gdn_id==""){
		alert("Please select Transfer TO TE.");
		jQuery("#tt_te_id").focus();
	}else{
		var img1 = '<img src="images/ajax-loader.gif">';
		jQuery("#transfer_msg").html(img1);
		if(xhrMakeStockTransfer && xhrMakeStockTransfer.readystate != 4){
		xhrMakeStockTransfer.abort();
		}
xhrMakeStockTransfer = jQuery.ajax({
	url: 'ajax_make_eng_transfer.php',
	type: 'post',
	dataType: 'json',
	data: "tf_te_code="+tf_gdn_id+"&tt_te_code="+tt_gdn_id+"&quick_tf_ids="+encodeURIComponent(quick_tf_ids),
	success: function(response){
		if(response.process_sts=="YES"){
			load_tf_te_engineer(tf_gdn_id,tf_stk_chass_nm);
			load_tt_te_engineer(tt_gdn_id);
		jQuery("#transfer_msg").html(response.process_msg);
		setTimeout(function(){
			jQuery("#transfer_msg").html("");
		},6000);
		}else{
			jQuery("#transfer_msg").html(response.process_msg);
			setTimeout(function(){
			jQuery("#transfer_msg").html("");
		},6000);
		}
								
	},
	timeout : 0
	});
	return false;		
	}
});





function load_tf_te_engineer(tf_te_code,search_text){
if(xhr_load_tf_te_engineer && xhr_load_tf_te_engineer.readystate != 4){
xhr_load_tf_te_engineer.abort();
}
var img1 = '<img src="images/ajax-loader.gif">';
jQuery("#tf_te_loader").html(img1);
xhr_load_tf_te_engineer = jQuery.ajax({
url: 'ajax_load_tf_te_eng.php',
type: 'post',
dataType: 'json',
data: "tf_te_code="+tf_te_code+"&search_text="+encodeURIComponent(search_text),
success: function(response){
	if(response.process_sts=="YES"){
		var tf_te_eng_data = response.tf_te_eng_data;
		var tot_tf_eng_count = response.tot_tf_eng_count;
		jQuery("#tf_te_loader").html("("+tot_tf_eng_count+")");
		jQuery("#all_tf_gdn_stock_data_div_container").html(tf_te_eng_data);
	}else{
		jQuery("#tf_te_loader").html("(0)");
		jQuery("#all_tf_gdn_stock_data_div_container").html("");
	}						
},
timeout : 0
});		
}


function load_tt_te_engineer(tt_te_code){
if(xhr_load_tt_te_engineer && xhr_load_tt_te_engineer.readystate != 4){
xhr_load_tt_te_engineer.abort();
}
var img1 = '<img src="images/ajax-loader.gif">';
jQuery("#tt_te_loader").html(img1);
xhr_load_tt_te_engineer = jQuery.ajax({
url: 'ajax_load_tt_te_eng.php',
type: 'post',
dataType: 'json',
data: "tt_te_code="+tt_te_code,
success: function(response){
	if(response.process_sts=="YES"){
		var tt_te_eng_data = response.tt_te_eng_data;
		var tot_tt_eng_count = response.tot_tt_eng_count;
		jQuery("#tt_te_loader").html("("+tot_tt_eng_count+")");
		jQuery("#all_tt_gdn_stock_data_div_container").html(tt_te_eng_data);
	}else{
		jQuery("#tt_te_loader").html("(0)");
		jQuery("#all_tt_gdn_stock_data_div_container").html("");
	}						
},
timeout : 0
});		
}

});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>