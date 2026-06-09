<?php
include "web_check.php";
include "star_connection.php";
$support_master = "support_master";
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

$add_page_name = "support_master.php";
$page_name = "support_master.php";
$support_status_arr = array("PENDING","RESOLVE");
$new_qry_string_filtered = "";
$srch_eng_dtls = $_GET["srch_eng_dtls"] ? addslashes(trim($_GET["srch_eng_dtls"])) : "";
$sl_supp_status = $_GET["sl_supp_status"] ? addslashes(trim($_GET["sl_supp_status"])) : "";
$whr_str = "";
$msg_txt = "";
$search_array = array("srch_eng_dtls"=>$srch_eng_dtls,"sl_supp_status"=>$sl_supp_status,"data_show_type"=>$data_show_type);
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
	}else if($search_array_key=="sl_supp_status"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($support_master.`status`='".$search_array_val."' ) ";
			$new_qry_string_filtered .= "&sl_supp_status=".$search_array_val;
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

if(isset($_GET["dlt_sid"]) && @$_GET["dlt_sid"]!=""){
$dlt_sid = $_GET["dlt_sid"] ? trim($_GET["dlt_sid"]) : "";
if($dlt_sid!=""){		
$sqldel = "delete from $support_master where `sid`='$dlt_sid'";
$resdel = mysqli_query($conn,$sqldel);
}
$msg_txt = "Support details successfully deleted.";
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

$pgsql = "select $support_master.`sid` from $support_master left join $engineer_master on $support_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` $new_whr_str";
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
	word-wrap: break-word;
	font-size:12px;
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
						<?php 
						if($data_show_type=="ALL"){

							$sql1 = "select $support_master.*,$support_master.`status` as `s_status`,$engineer_master.* from $support_master left join $engineer_master on $support_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` $new_whr_str order by $support_master.`submitted_datetime` desc";
						}else{
							$where_clause = "";
							$zones = explode(",", $data_show_type); // Assuming $data_show_type is a comma-separated string of zones
							
							foreach ($zones as $zone) {
								$zone = trim($zone);
								if ($where_clause != "") {
									$where_clause .= " OR ";
								}
								$where_clause .= "te_master.`zone` LIKE '%$zone%'";
							}
							$sql1 = "select $support_master.*,$support_master.`status` as `s_status`,$engineer_master.* from $support_master left join $engineer_master on $support_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where $where_clause order by $support_master.`submitted_datetime` desc";
						}
						$res1 = mysqli_query($conn,$sql1);
						$totres1 = mysqli_num_rows($res1); ?>
                          <h2>Support Master (<?php if($total_pgres){ echo $total_pgres; }else{ echo $totres1; } ?>)&nbsp;&nbsp;<span class="shomsg"><?php if($msg_txt!=""){ echo $msg_txt;}?></span>&nbsp;&nbsp;<a href="export_support_master.php" class="btn bg-red waves-effe">Export</a>
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_eng_dtls" value="<?php echo $srch_eng_dtls;?>" placeholder="Search Engineer Details">
    </div>
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_supp_status">
<option value="" >All Status</option>
<?php
if(count($support_status_arr)>0){
	foreach($support_status_arr as $support_status_arr_src){ ?>
<option value="<?php echo $support_status_arr_src;?>" <?php if($support_status_arr_src==$sl_supp_status){?> selected="selected" <?php } ?>><?php echo $support_status_arr_src;?></option>
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
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
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
                                            <th>Order&nbsp;ID</th>
                                            <th style="width:150px;">Engineer&nbsp;Details</th>
                                            <th>Type</th>
                                            <th>Comment</th>
                                            <th>Status</th>
                                            <th>Date&nbsp;Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Order&nbsp;ID</th>
                                            <th style="width:150px;">Engineer&nbsp;Details</th>
                                            <th>Type</th>
                                            <th>Comment</th>
                                            <th>Status</th>
                                            <th>Date&nbsp;Time</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
if($data_show_type=="ALL"){

	$sql1 = "select $support_master.*,$support_master.`status` as `s_status`,$engineer_master.* from $support_master left join $engineer_master on $support_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` $new_whr_str order by $support_master.`submitted_datetime` desc limit $start_from,$limit";
}else{
	$where_clause = "";
	$zones = explode(",", $data_show_type); // Assuming $data_show_type is a comma-separated string of zones
	
	foreach ($zones as $zone) {
		$zone = trim($zone);
		if ($where_clause != "") {
			$where_clause .= " OR ";
		}
		$where_clause .= "te_master.`zone` LIKE '%$zone%'";
	}
	$sql1 = "select $support_master.*,$support_master.`status` as `s_status`,$engineer_master.* from $support_master left join $engineer_master on $support_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where $where_clause order by $support_master.`submitted_datetime` desc limit $start_from,$limit";
}
//echo $sql1;
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$the_sid = $row1["sid"];
		$the_order_id = $row1["order_id"];
		$the_e_name = $row1["e_name"];
		$the_e_mobile = $row1["e_mobile"];
		$the_te_code = $row1["te_code"];
		$the_branch_code_selected = $row1["branch_code"];
		$the_e_email = $row1["e_email"];
		$the_s_type = $row1["s_type"];
		$the_s_comment = $row1["s_comment"];
		$the_s_status = $row1["s_status"] ? trim($row1["s_status"]) : "";
		$the_s_submitted_datetime = $row1["submitted_datetime"] ? trim($row1["submitted_datetime"]) : "";
		if($the_s_submitted_datetime!=""){
		$the_s_submitted_datetime = date("dS M, Y",strtotime($the_s_submitted_datetime));
		}

?>
<tr>
<td><?php echo $the_order_id;?></td>
<td style="width:150px;">
<div style="width:150px;">
<?php if($the_e_name!=""){echo '<span class="engineerEachField">'.$the_e_name.'</span>';}?>
<?php if($the_e_mobile!=""){echo '<span class="engineerEachField"><b>Mobile:</b> '.$the_e_mobile.'</span>';}?>
<?php if($the_e_email!=""){echo '<span class="engineerEachField"><b>Email:</b></span><span class="engineerEachField">'.$the_e_email.'</span>';}?>
<?php if($the_te_code!=""){echo '<span class="engineerEachField"><b>TE Code:</b> '.$the_te_code.'</span>';}?>
</div>
</td>
<td><?php echo $the_s_type;?></td>
<td><?php echo $the_s_comment;?></td>
<td>
<div>
<span class="adminActivityField">
<select class="form-control sl_supp_sts" id="sl_supp_sts_<?php echo $the_sid;?>" s_id="<?php echo $the_sid;?>">
<?php
if(count($support_status_arr)>0){
	foreach($support_status_arr as $support_status_arr_val){ ?>
<option value="<?php echo $support_status_arr_val;?>" <?php if($support_status_arr_val==$the_s_status){?> selected="selected" <?php } ?>><?php echo $support_status_arr_val;?></option>
	<?php }
}
?>
</select>
</span>
<span class="adminActivityField3" id="sl_eng_sts_ldr_<?php echo $the_sid;?>"></span>
</div>
</td>
<td><?php echo $the_s_submitted_datetime;?></td>

<td>
<div>
<span class="adminActivityField2">
<!-- <?php if(!in_array($_SESSION["menu_id"],$ediit_inactive_menu_array)){?>
<a href="<?php echo $add_page_name;?>?edt_e_id=<?php echo $the_sid;?>" class="btn bg-red waves-effe delete_support" style="margin-right:10px;">Delete</a>
	<?php }?> -->
	 <?php if(strtoupper($the_access_user_type)=="ADMIN"){?>
<a href="javascript:void(0);" class="btn bg-red waves-effe delete_support" dlt_sid="<?php echo $the_sid;?>">Delete</a>
	<?php }?> 
<!-- <a href="javascript:void(0);" class="btn bg-red waves-effe delete_support" dlt_sid="<?php echo $the_sid;?>">Delete</a> -->
</span>
</div>
</td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="15">No data found.</td>
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


jQuery(".delete_support").click(function(){
	var dlt_sid = jQuery(this).attr("dlt_sid");
	if(dlt_sid!=""){
		var cb = confirm("Do you want to delete this support details?");
		if(cb == true){
			window.location = "<?php echo $page_name;?>?dlt_sid="+dlt_sid;
		}
	}
});	


jQuery('.sl_supp_sts').change(function(){
		var supp_sts = jQuery(this).val();
		var s_id = jQuery(this).attr("s_id");
		if(supp_sts!='' && s_id!=''){
			var ldr_elmnt = jQuery("#sl_eng_sts_ldr_"+s_id);
				ldr_elmnt.html(imgs);
				jQuery.ajax({
				url: 'ajax_update_support_status.php',
				type: 'post',
				dataType: 'json',
				data: "s_id="+s_id+"&supp_sts="+supp_sts,
				success: function(response){				
				if(response.process_sts=="YES"){					
					ldr_elmnt.html(done_img);
					setTimeout(function(){
					ldr_elmnt.html("");
					},5000);
				}else{
					ldr_elmnt.html("");				
				}						
				}
				});
		}
	
	});

	
	jQuery(".srch_btn").click(function(){
		var srch_eng_dtls = jQuery("#srch_eng_dtls").val();
		var sl_supp_status = jQuery("#sl_supp_status").val();
		var qstring ="";
		var amp = "";
		
		if(srch_eng_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_eng_dtls="+encodeURIComponent(srch_eng_dtls);
			}else{
				qstring = qstring+"srch_eng_dtls="+encodeURIComponent(srch_eng_dtls);
			}
		}
		if(sl_supp_status!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_supp_status="+encodeURIComponent(sl_supp_status);
			}else{
				qstring = qstring+"sl_supp_status="+encodeURIComponent(sl_supp_status);
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