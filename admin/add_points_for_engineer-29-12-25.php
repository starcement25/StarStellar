<?php
include "web_check.php";
include "star_connection.php";
$recommended_site_master = "recommended_site_master";
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
function get_branch_names_by_ids($conn,$bids){
$pnms = "Not set yet";
$nmsarr = array();
$selected_pids_arr = array();
$branch_master = "branch_master";
$bids = $bids ? trim($bids) : "" ;
	if($bids!=""){
		$selected_pids_arr = explode(",",$bids);
		$selected_pids_str_for_qry = implode("','",$selected_pids_arr);
	$sql1 = "select `branch_name` from $branch_master where `branch_code` in ('".$selected_pids_str_for_qry."') ";
	$res1 = mysqli_query($conn,$sql1);
	$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
	$the_prod_name_sho = trim($row1["branch_name"]);
	$nmsarr[] = $the_prod_name_sho;
	}
	$qpnms = implode(",",$nmsarr);
	}	 
	}
return $qpnms;
}
$add_page_name = "add_points_for_engineer.php";
$page_name = "add_points_for_engineer.php";
$activity_status_arr = array("ACTIVE","SEMI_ACTIVE","INACTIVE");
$en_status_arr = array("ACTIVE","INACTIVE");
$img_dir = "../en_profile_pic/";
$default_image_name = "profile.png";
$image_url_prefix = $server_url."en_profile_pic/";

$date_before_three_month = date('Y-m-d H:i:s',strtotime("-3 month"));
$date_before_six_month = date('Y-m-d H:i:s',strtotime("-6 month"));
$date_before_three_month_stamp = strtotime($date_before_three_month);
$date_before_six_month_stamp = strtotime($date_before_six_month);

$new_qry_string_filtered = "";
$srch_eng_dtls = $_GET["srch_eng_dtls"] ? addslashes(trim($_GET["srch_eng_dtls"])) : "";
$sl_activity_status = $_GET["sl_activity_status"] ? addslashes(trim($_GET["sl_activity_status"])) : "";
$whr_str = "";
$msg_txt = "";
$search_array = array("srch_eng_dtls"=>$srch_eng_dtls,"sl_activity_status"=>$sl_activity_status,"data_show_type"=>$data_show_type);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_eng_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($engineer_master.`e_name` like '%$search_array_val%' or $engineer_master.`te_code` like '%$search_array_val%' or $engineer_master.`e_mobile` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_eng_dtls=".$search_array_val;
		}
	}else if($search_array_key=="sl_activity_status"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
	if($search_array_val=="ACTIVE"){	
	$whr_str .= "$aand `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`>='$date_before_three_month' ";
	}else if($search_array_val=="SEMI_ACTIVE"){
	$whr_str .= "$aand `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_three_month' and `latest_recommended_site_master`.`r_submission_date`>='$date_before_six_month' ";
	}else if($search_array_val=="INACTIVE"){
	$whr_str .= "$aand `latest_recommended_site_master`.`r_submission_date` is null or (`latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_six_month') ";
	}

			$new_qry_string_filtered .= "&sl_activity_status=".$search_array_val;
		}
	}else if($search_array_key=="data_show_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			/*if($search_array_val=='NE'){
$whr_str .= "$aand ($te_master.`zone` like '%A%' or $te_master.`zone` like '%B%' or $te_master.`zone` like '%C%' ) ";
			}else if($search_array_val=='OSNE'){
$whr_str .= "$aand ($te_master.`zone` like '%D%' or $te_master.`zone` like '%E%' ) ";
			}*/
			if($search_array_val!='ALL'){
$whr_str .= "$aand $te_master.`zone` like '%".$search_array_val."%' ";
			}
			
		}
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
if(isset($_GET["msg_txt"]) && @$_GET["msg_txt"]!=""){
	$msg_txt = $_GET["msg_txt"];
}

if(isset($_GET["dlt_engid"]) && @$_GET["dlt_engid"]!=""){
$dlt_engid = $_GET["dlt_engid"] ? trim($_GET["dlt_engid"]) : "";
if($dlt_engid!=""){		
$sqldel = "delete from $engineer_master where `eid`='$dlt_engid'";
$resdel = mysqli_query($conn,$sqldel);
}
$msg_txt = "Engineer successfully deleted.";
header("location:".$page_name."?msg_txt=".$msg_txt);
}



$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $engineer_master.`eid`,`latest_recommended_site_master`.`r_submission_date` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` $new_whr_str";
$pgres = mysqli_query($conn,$pgsql);
$total_pgres = mysqli_num_rows($pgres);
$start_from = (($page-1)*$limit);
$prev = $page - 1;							//previous page is page - 1
$next = $page + 1;							//next page is page + 1
$lastpage = ceil($total_pgres/$limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;

/*---------PAGINATION RELATED CODE START----------*/


include "web_header.php";
?>
<style>
.wrapper_scrl{
border: none;
overflow-x: scroll;
overflow-y:hidden;
height: 20px;
}
.wrapper_scrl_div{
height: 20px;	
}
.prfl_img{
	width:100px;
	height:80px;
}
.adminActivityField{
	display:block;
	width:100px;
	margin-bottom:8px;
}
.adminActivityField3{
	display:block;
	width:100px;
	height:20px;
}
.adminActivityField2{
	display:block;
	width:150px;
}
.actvt_clr_cls{
	display:block;
	width:40px;
	height:40px;
	border:1px solid #666666;
	margin: 0 auto;
	border-radius: 27px;
}
.actvt_clr_cls_small{
	display:block;
	width:10px;
	height:20px;
	border:1px solid #666666;
	margin: 0 auto;
	border-radius: 27px;
}
.engineerEachField{
	display:block;
	width:150px;
	font-size:12px;
}
.teEachField{
	display:block;
	width:150px;
}
.adminActivityField{
	display:block;
	width:100%;
	margin-bottom:8px;
}
.point_add_ldr{
position: absolute;
right: 3px;
top: 0px;
}
.point_deduct_button{
	margin-left:20px;
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
                          <h2>Add/Deduct Points For Engineers (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_eng_dtls" value="<?php echo $srch_eng_dtls;?>" placeholder="Search Engineer Details">
    </div>
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_activity_status">
<option value="" >All Activity Status</option>
<?php
if(count($activity_status_arr)>0){
	foreach($activity_status_arr as $activity_status_arr_val){ ?>
<option value="<?php echo $activity_status_arr_val;?>" <?php if($activity_status_arr_val==$sl_activity_status){?> selected="selected" <?php } ?>><?php echo $activity_status_arr_val;?></option>
	<?php }
}
?>
</select>
    </div>
   <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" >Reset</button>
    </div>
    
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">

    </div>
    
        
    </div>
<span style="clear:both;display:block;"></span>
                        </div>
                        <div class="body">
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>

<div class="wrapper_scrl">
    <div class="wrapper_scrl_div">
    </div>
</div>

 <div class="table-responsive tr_for_scroll">
                                <table class="table table-bordered table-striped table-hover table_for_scroll">
                                    <thead>
                                        <tr>
                                            <th style="width:100px;">Image</th>
                                            <th style="width:150px;">Engineer&nbsp;Details</th>
                                            <th style="width:200px;">Points</th>
                                            <th>Branch</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                       <tr>
                                            <th style="width:100px;">Image</th>
                                            <th style="width:150px;">Engineer&nbsp;Details</th>
                                            <th style="width:200px;">Points</th>
                                            <th>Branch</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $engineer_master.*,$te_master.`te_name`,$te_master.`branch_code`,`latest_recommended_site_master`.`r_submission_date` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` $new_whr_str order by $engineer_master.`e_name` asc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$the_eid = $row1["eid"];
		$the_e_name = $row1["e_name"];
		$the_e_mobile = $row1["e_mobile"];
		$the_te_code = $row1["te_code"];
		$the_te_name = $row1["te_name"];
		$the_branch_code_selected = $row1["branch_code"];
		if($the_branch_code_selected!=""){
	$the_branch_code_selected = get_branch_names_by_ids($conn,$the_branch_code_selected);
	}else{
	$the_branch_code_selected = "";	
	}
		$the_e_email = $row1["e_email"];
		$the_e_dob = $row1["e_dob"];
		$the_e_dom = $row1["e_dom"];
		
		$the_e_address = $row1["e_address"];
		$the_e_pin = $row1["e_pin"];
		$the_e_state = $row1["e_state"];
		$the_e_city_town = $row1["e_city_town"];
		$the_e_points = $row1["e_points"];
		$the_e_status = $row1["status"] ? trim($row1["status"]) : "";
		
		$the_e_profile_image = $row1["e_profile_image"] ? trim($row1["e_profile_image"]) : "";
		if($the_e_profile_image!=""){
			if(file_exists($img_dir.$the_e_profile_image)){
				$the_e_profile_image_url = $image_url_prefix.$the_e_profile_image;
			}else{
				$the_e_profile_image_url = $image_url_prefix.$default_image_name;
			}
		}else{
			$the_e_profile_image_url = $image_url_prefix.$default_image_name;
		}

$r_submission_date = $row1["r_submission_date"] ? trim($row1["r_submission_date"]) : "";
if($r_submission_date!=""){
$the_date_time_stamp = strtotime($r_submission_date);
if($the_date_time_stamp>=$date_before_three_month_stamp){
	$e_status = "ACTIVE";
$e_status_show = "Active";
$inac_bg_color = "#42f548";
}else if($the_date_time_stamp<$date_before_three_month_stamp && $the_date_time_stamp>=$date_before_six_month_stamp){
	$e_status = "SEMI_ACTIVE";
$e_status_show = "Semi Active";
$inac_bg_color = "#f79d16";
}else{
	$e_status = "INACTIVE";
$e_status_show = "Inactive";
$inac_bg_color = "#f0311f";
}
}else{
$e_status = "INACTIVE";
$e_status_show = "Inactive";
$inac_bg_color = "#f0311f";	
}

?>
<tr>
<td style="width:100px;">
<img src="<?php echo $the_e_profile_image_url;?>" class="prfl_img" />
</td>
<td style="width:150px;">
<div style="width:150px;">
<?php if($the_e_name!=""){echo '<span class="engineerEachField">'.$the_e_name.'</span>';}?>
<?php if($the_e_mobile!=""){echo '<span class="engineerEachField"><b>Mobile:</b> '.$the_e_mobile.'</span>';}?>
<?php if($the_e_email!=""){echo '<span class="engineerEachField"><b>Email:</b></span><span class="engineerEachField">'.$the_e_email.'</span>';}?>
<?php if($the_te_name!=""){echo '<span class="engineerEachField"><b>TE Name:</b></span><span class="engineerEachField">'.$the_te_name.'</span>';}?>
<?php if($the_te_code!=""){echo '<span class="engineerEachField"><b>TE Code:</b> '.$the_te_code.'</span>';}?>
</div>
</td>
<td style="width:200px;">
<div style="width:200px;position:relative;">
<strong id="point_add_ldr_<?php echo $the_eid;?>" class="point_add_ldr"></strong>
<span class="adminActivityField" id="curr_point_<?php echo $the_eid;?>" style="text-align:center;">
<?php echo $the_e_points;?>
</span>
<span class="adminActivityField">
<input type="text" class="form-control added_point_field" id="added_point_<?php echo $the_eid;?>" placeholder="Enter Point" style="text-align:center;">
</span>
<span class="adminActivityField">
<input type="text" class="form-control added_description_field" id="added_description_<?php echo $the_eid;?>" placeholder="Description">
</span>
<span class="adminActivityField" style="text-align:center;">
<a href="javascript:void(0);" class="btn bg-red waves-effect point_add_button" the_eid="<?php echo $the_eid;?>">Add</a>
<a href="javascript:void(0);" class="btn bg-red waves-effect point_deduct_button" the_eid="<?php echo $the_eid;?>">Deduct</a>

</span>
</div>

</td>
<td><?php echo $the_branch_code_selected;?></td>

</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="4">No data found.</td>
</tr>
<?php
}
?>
</tbody>
</table>
                            </div>
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
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
setTimeout(function(){
	jQuery(".shomsg").html("");
},8000);

var tr_for_scroll = jQuery(".tr_for_scroll").width();
var table_for_scroll = jQuery(".table_for_scroll").width();
jQuery(".wrapper_scrl").css("width",tr_for_scroll+"px");
jQuery(".wrapper_scrl_div").css("width",table_for_scroll+"px");

jQuery(".wrapper_scrl").scroll(function(){
jQuery(".tr_for_scroll")
.scrollLeft(jQuery(".wrapper_scrl").scrollLeft());
});
jQuery(".tr_for_scroll").scroll(function(){
jQuery(".wrapper_scrl")
.scrollLeft(jQuery(".tr_for_scroll").scrollLeft());
});	

jQuery(".point_add_button").click(function(){
	var the_eid = jQuery(this).attr("the_eid");
	if(the_eid!=""){
		var element_admin_ord_upd_ldr = jQuery("#point_add_ldr_"+the_eid);
		var curr_point_element = jQuery("#curr_point_"+the_eid);
		var added_point = encodeURIComponent(jQuery.trim(jQuery("#added_point_"+the_eid).val()));
		var added_description = encodeURIComponent(jQuery.trim(jQuery("#added_description_"+the_eid).val()));
		if(added_point==""){
			alert("Please enter point.");
			jQuery("#added_point_"+the_eid).focus();
		}else if(added_description==""){
			alert("Please enter description.");
			jQuery("#added_description_"+the_eid).focus();
		}else{
		element_admin_ord_upd_ldr.html(imgs);
		jQuery.ajax({
				url: 'ajax_add_point_for_engineer_by_admin.php',
				type: 'post',
				dataType: 'json',
				data: "the_eid="+the_eid+"&added_point="+added_point+"&added_description="+added_description,
				success: function(response){				
				if(response.process_sts=="YES"){
					jQuery("#added_point_"+the_eid).val("");
					jQuery("#added_description_"+the_eid).val("");
					var calculated_point = response.calculated_point;
					curr_point_element.html(calculated_point);				
					element_admin_ord_upd_ldr.html(done_img);
					setTimeout(function(){
					element_admin_ord_upd_ldr.html("");
					},6000);		
				}else{
					element_admin_ord_upd_ldr.html("");
					alert(response.process_msg);				
				}						
				}
				});
		}
	}
});

jQuery(".point_deduct_button").click(function(){
	var the_eid = jQuery(this).attr("the_eid");
	if(the_eid!=""){
		var element_admin_ord_upd_ldr = jQuery("#point_add_ldr_"+the_eid);
		var curr_point_element = jQuery("#curr_point_"+the_eid);
		var added_point = encodeURIComponent(jQuery.trim(jQuery("#added_point_"+the_eid).val()));
		var added_description = encodeURIComponent(jQuery.trim(jQuery("#added_description_"+the_eid).val()));

		if(added_point==""){
			alert("Please enter point.");
			jQuery("#added_point_"+the_eid).focus();
			return false;
		}else if(isNaN(added_point) || parseFloat(added_point) <= 0){
			alert("Please enter a valid positive number for points.");
			jQuery("#added_point_"+the_eid).focus();
			return false;
		}else if(added_description==""){
			alert("Please enter description.");
			jQuery("#added_description_"+the_eid).focus();
			return false;
		}

		var current_points = parseFloat(jQuery.trim(curr_point_element.text()));
		var deducted_points_num = parseFloat(added_point);

		// sk 12-11-25 Check if deduction exceeds current available points
		if(deducted_points_num > current_points){
			alert("You cannot deduct more points than the current available points (" + current_points + ").");
			jQuery("#added_point_"+the_eid).focus();
			return false;
		}

		element_admin_ord_upd_ldr.html(imgs);
		jQuery.ajax({
			url: 'ajax_deduct_point_for_engineer_by_admin.php',
			type: 'post',
			dataType: 'json',
			data: "the_eid="+the_eid+"&added_point="+added_point+"&added_description="+added_description,
			success: function(response){				
				if(response.process_sts=="YES"){
					jQuery("#added_point_"+the_eid).val("");
					jQuery("#added_description_"+the_eid).val("");
					var calculated_point = response.calculated_point;
					curr_point_element.html(calculated_point);				
					element_admin_ord_upd_ldr.html(done_img);
					setTimeout(function(){
						element_admin_ord_upd_ldr.html("");
					},6000);		
				}else{
					element_admin_ord_upd_ldr.html("");
					alert(response.process_msg);				
				}						
			}
		});
	}
});


jQuery(".added_point_field").keydown(function(event) {
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

jQuery(".delete_engineer").click(function(){
	var dlt_engid = jQuery(this).attr("dlt_engid");
	if(dlt_engid!=""){
		var cb = confirm("Do you want to delete this engineer?");
		if(cb == true){
			window.location = "<?php echo $page_name;?>?dlt_engid="+dlt_engid;
		}
	}
});	


jQuery('.sl_eng_sts').change(function(){
		var eng_sts = jQuery(this).val();
		var eng_id = jQuery(this).attr("eng_id");
		if(eng_sts!='' && eng_id!=''){
			var ldr_elmnt = jQuery("#sl_eng_sts_ldr_"+eng_id);
				ldr_elmnt.html(imgs);
				jQuery.ajax({
				url: 'ajax_update_eng_status.php',
				type: 'post',
				dataType: 'json',
				data: "eng_id="+eng_id+"&eng_sts="+eng_sts,
				success: function(response){				
				if(response.process_sts=="YES"){					
					ldr_elmnt.html(done_img);
				}else{
					ldr_elmnt.html("");				
				}						
				}
				});
		}
	
	});

	
	jQuery(".srch_btn").click(function(){
		var srch_eng_dtls = jQuery("#srch_eng_dtls").val();
		var sl_activity_status = jQuery("#sl_activity_status").val();
		var qstring ="";
		var amp = "";
		
		if(srch_eng_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_eng_dtls="+encodeURIComponent(srch_eng_dtls);
			}else{
				qstring = qstring+"srch_eng_dtls="+encodeURIComponent(srch_eng_dtls);
			}
		}
		if(sl_activity_status!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_activity_status="+encodeURIComponent(sl_activity_status);
			}else{
				qstring = qstring+"sl_activity_status="+encodeURIComponent(sl_activity_status);
			}
		}
		
		
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "<?php echo $page_name;?>"+qstring;
		
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "<?php echo $page_name;?>";
	});
});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>