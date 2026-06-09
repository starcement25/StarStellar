<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "web_check.php";
include "star_connection.php";


$start_user_type = $_SESSION["start_user_type"];
$engineer_master = "engineer_master";
$te_master= "te_master";
$order_query = "order_query";
$dnsbcarr = array();
$dnsbcstr ="";
$theactbcarr = array();
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$new_qry_string_filtered = "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$srch_order_dtls = $_GET["srch_order_dtls"] ? addslashes(trim($_GET["srch_order_dtls"])) : "";
$whr_str = "";
$export_filtered_str = "";
$search_array = array("srch_order_dtls"=>$srch_order_dtls,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_order_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand ($order_query.`order_id` like '%$search_array_val%' or $order_query.`linked_te_code` like '%$search_array_val%'  or $te_master.`te_name` like '%$search_array_val%' or $order_query.`prod_name` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_cust_dtls=".$search_array_val;
		}
	}
	else if($search_array_key=="daywise"){
		$the_sl_day_wise = $search_array_val["sl_day_wise"];
		$the_from_dt = $search_array_val["from_dt"];
		$the_to_dt = $search_array_val["to_dt"];
		if(trim($whr_str)!=""){
		$aand = " and";
		}else{
		$aand = "";
		}
		if($the_sl_day_wise=="Date_Range"){
			$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
			}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
			}
			if($the_from_dt!="" && $the_to_dt!=""){
			   $whr_str .= "$aand $order_query.`date_and_time` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $order_query.`date_and_time` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $order_query.`date_and_time` <= '".$the_to_dt." ".$to_hrs."' ";
				$new_qry_string_filtered .= "&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&to_dt=".$the_to_dt;
				}
			}
		}else{
			if($the_sl_day_wise=="Today"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $order_query.`date_and_time` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $order_query.`date_and_time` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "and ".$whr_str;
}else{
	$new_whr_str ="";
}

$page_name = "order_query_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $order_query.*,$engineer_master.`e_name`,$te_master.te_name from $order_query left join $engineer_master on $order_query.`engineer_code`=$engineer_master.`eid` left join $te_master ON $order_query.`linked_te_code`=$te_master.te_code where $order_query.`order_id`!=''   $new_whr_str ";
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
.each_mk_cncl_span{
	display:block;
	width:150px;
	margin-bottom: 7px;
margin-top: 7px;
}

.table-container {
  height: 400px; /* Set the height of the container to limit the height of the table */
  overflow-y: auto; /* Enable vertical scrolling */
}

table {
  border-collapse: collapse;
  width: 100%;
}

th {
  background-color: #ddd;
  position: sticky; /* Make the table header fixed */
  top: 0;
}

th,
td {
  padding: 8px;
  text-align: left;
  border-bottom: 1px solid #ddd;
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
                          <h2>Order Query List (<?php echo $total_pgres;?>) &nbsp;&nbsp;&nbsp;<span class="rpt_loader"></span> &nbsp;&nbsp;<a href="export_order_query.php?get_type=all<?php echo $export_filtered_str;?>" class="btn bg-red waves-effe">Export&nbsp;order&nbsp;query</a></h2>
    <div class="row clearfix">
    
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_order_dtls" value="<?php echo $srch_order_dtls;?>" placeholder="Search Order Details">
    </div>

  <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_day_wise" >
<option value="">Select Day-Wise</option>
<option value="Today" <?php if($sl_day_wise=="Today"){?> selected="selected" <?php } ?>>Today</option>
<option value="Yesterday" <?php if($sl_day_wise=="Yesterday"){?> selected="selected" <?php } ?>>Yesterday</option>
<option value="Date_Range" <?php if($sl_day_wise=="Date_Range"){?> selected="selected" <?php } ?>>Date Range</option>
</select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <input type="text" class="datepicker form-control" id="from_dt" <?php if($sl_day_wise!="Date_Range"){?> style="display:none;" <?php } ?> value="<?php echo $from_dt;?>" placeholder="Choose from date">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <input type="text" class="datepicker form-control" id="to_dt" <?php if($sl_day_wise!="Date_Range"){?> style="display:none;" <?php } ?> value="<?php echo $to_dt;?>" placeholder="Choose to date">
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
    <div class="wrapper_scrl_div"></div>
</div>
                            <div class="table-wrap">
                            <div class="table-responsive table-container">  
                                <table class="table-bordered">
                                    <thead>
                                        <tr>
                                            <th>SL&nbsp;No</th>
                                            <th>Order&nbsp;Id</th>
                                            <th>DATE TIME</th>
                                            <th>ENGINEER NAME</th>
                                            <!--th>SITE Name</th-->
                                            <th>Linked TE Code</th>
                                            <th>Linked TE Name</th>
											<th>Prod name</th>
											<th>Date of Lifting</th>
                                            <th>Quantity in Bags</th>
                                            <th>Remarks</th>
											<th>Status</th>
											<th>Status Remarks</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                      <tr>
                                         <th>SL&nbsp;No</th>
                                            <th>Order&nbsp;Id</th>
                                            <th>DATE TIME</th>
                                            <th>ENGINEER NAME</th>
                                            <!--th>SITE Name</th-->
                                            <th>Linked TE Code</th>
                                            <th>Linked TE Name</th>
											<th>Prod name</th>
											<th>Date of Lifting</th>
                                            <th>Quantity in Bags</th>
                                            <th>Remarks</th>
											<th>Status</th>
											<th>Status Remarks</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $order_query.*,$engineer_master.`e_name`,$te_master.te_name from $order_query left join $engineer_master on $order_query.`engineer_code`=$engineer_master.`eid` left join $te_master ON $order_query.`linked_te_code`=$te_master.te_code where $order_query.`order_id`!=''   $new_whr_str order by $order_query.`id` asc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
$the_sl_no = 1;
$the_sl_no = (($limit*($page-1))+1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$sl_no = $row1["id"];
		$order_id = $row1["order_id"];
		$date_and_time = $row1["date_and_time"];
		$e_name = $row1["e_name"];
		$te_name = $row1["te_name"];
		$linked_te_code = $row1["linked_te_code"];
		$engineer_code = $row1["engineer_code"];
		$prod_name = $row1["prod_name"];
		$qty_bags = $row1["qty_bags"];
		$date_of_lifting = $row1["date_of_lifting"];
		$remarks = $row1["remarks"];
		$status_from_app = $row1["status_from_app"];
		$status_remarks = $row1["status_remarks"];
		
		$sqlsite = "SELECT r_site_name FROM `recommended_site_master` WHERE `r_te_code` = '".$linked_te_code."' AND `r_engineer_id` = '".$engineer_code."'";	
		$ressite = mysqli_query($conn,$sqlsite);
		$rowsite= mysqli_fetch_assoc($ressite);
		$r_site_name=$rowsite['r_site_name'];
?>
<tr id="each_ord_tr_<?php echo $sl_no;?>">
<td><?php echo $sl_no;?></td>
<td><?php echo $order_id;?></td>
<td><?php echo $date_and_time;?></td>
<td><?php echo $e_name;?></td>
<!--td><?php //echo $r_site_name;?></td-->
<td><?php echo $linked_te_code;?></td>
<td><?php echo $te_name;?>
<td><?php echo $prod_name;?></td>
<td><?php echo $date_of_lifting;?></td>
<td><?php echo $qty_bags;?>
<td><?php echo $remarks;?></td>
<td><?php echo $status_from_app;?></td>
<td><?php echo $status_remarks;?></td>	
</tr>
<?php
$the_sl_no++;
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="26">No data found.</td>
</tr>
<?php
}
?>
</tbody>
</table>
</div>
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
jQuery(".vuChnlDtlsLink").colorbox({iframe:true,width:"90%",height:"80%",closeButton: true,scrolling: true});
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
	
	var imgs = '<img src="images/ajax-loader.gif"/>';
	var done_img = '<img src="images/success_tick.png"/>';
	
	jQuery('#from_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
	jQuery('#to_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
setTimeout(function(){
	jQuery(".ord_upd_msg").html("");
},8000);
/*jQuery('#trn_branch_id').change(function(){
		var trn_branch_id = jQuery(this).val();
		if(trn_branch_id!=''){
		var img = '<img src="images/ajax-loader.gif">';
				jQuery(".rpt_loader").html(img);
				jQuery.ajax({
				url: 'ajax_show_destination_by_branch_id.php',
				type: 'post',
				dataType: 'json',
				data: "trn_branch_id="+trn_branch_id,
				success: function(response){				
				if(response.process_sts=="YES"){					
					jQuery("#ds_code").html(response.destination_options);
					jQuery(".rpt_loader").html("");		
				}else{
					jQuery("#ds_code").html('<option value="">Select Destination</option>');
					jQuery(".rpt_loader").html(response.process_msg);
					setTimeout(function(){
					jQuery(".rpt_loader").html("");
					},3000);				
				}						
				}
				});
		}else{
			jQuery("#ds_code").html('<option value="">Select Destination</option>');
			
		}
	
	});*/
	
	
	jQuery("#sl_day_wise").change(function(){
		var sl_day_wise = jQuery(this).val();
		if(sl_day_wise==""){
			jQuery('#from_dt').hide();
			jQuery('#to_dt').hide();
			jQuery('#from_dt').val("");
			jQuery('#to_dt').val("");
		}else{
			if(sl_day_wise=="Date_Range"){
				jQuery('#from_dt').show();
				jQuery('#to_dt').show();
				jQuery('#from_dt').val("");
				jQuery('#to_dt').val("");
			}else{
				jQuery('#from_dt').hide();
				jQuery('#to_dt').hide();
				jQuery('#from_dt').val("");
				jQuery('#to_dt').val("");
			}
		}
	});
	
	jQuery(".srch_btn").click(function(){
		var srch_order_dtls = jQuery("#srch_order_dtls").val();
		var sl_day_wise = jQuery("#sl_day_wise").val();
		var from_dt = jQuery("#from_dt").val();
		var to_dt = jQuery("#to_dt").val();
		var qstring ="";
		var dtstring ="";
		var amp = "";
		if(sl_day_wise!="" || srch_order_dtls!=""){
		if(srch_order_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_order_dtls="+srch_order_dtls;
			}else{
				qstring = qstring+"srch_order_dtls="+srch_order_dtls;
			}
		}	
		
		if(sl_day_wise!=""){
			if(sl_day_wise=="Date_Range"){
				if(from_dt!="" && to_dt!=""){
					dtstring ="&from_dt="+from_dt+"&to_dt="+to_dt;
				}else if(from_dt!="" && to_dt==""){
					dtstring ="&from_dt="+from_dt;
				}else if(from_dt=="" && to_dt!=""){
					dtstring ="&to_dt="+to_dt;
				}else{
					dtstring ="";
				}
			}else{
				dtstring ="";
			}
			
			if(qstring!=""){
				qstring = qstring+"&sl_day_wise="+sl_day_wise+dtstring;
			}else{
				qstring = qstring+"sl_day_wise="+sl_day_wise+dtstring;
			}
		}
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "<?php echo $page_name;?>"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "<?php echo $page_name;?>";
	});
jQuery("#sync_sap").click(function(){
		var sync_msg_elmnt = jQuery("#sync_msg");
		sync_msg_elmnt.html(imgs);
		jQuery.ajax({
		url: 'http://starsaathi.com/SAP/order_status_update_new_cron.php',
		type: 'post',
		dataType: 'json',
		data: '',
		success: function(response){				
		if(response.process_sts=="YES"){
		sync_msg_elmnt.html(done_img);
		window.location = "order_list_invoice.php";
		setTimeout(function(){
		sync_msg_elmnt.html("");
		},2000);
		}else{
		sync_msg_elmnt.html("");
		alert(response.process_msg);				
		}						
		}
		});
	});
	
	
});
</script>
<?php
include "web_footer.php";
mysql_close();
?>