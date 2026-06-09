<?php
include "web_check.php";
include "star_connection.php";
function get_customer_name_from_id($conn,$cust_id){
	$customer_master = "customer_master";
	$custname = "";
	$cust_id = $cust_id ? addslashes(trim($cust_id)) : "";
	if($cust_id!=''){
		$sqls = "select `customer_name` from $customer_master where `customer_code`='$cust_id'";
		$ress = mysqli_query($conn,$sqls);
		$totress = mysqli_num_rows($ress);
		if($totress>0){
			$rows = mysqli_fetch_assoc($ress);
			$custname = $rows["customer_name"] ? trim($rows["customer_name"]) : "";
		}
	}
	return $custname;
}
function get_branch_name_from_id($conn,$brnch_id){
	$branch_master = "branch_master";
	$brnchname = "";
	$brnch_id = $brnch_id ? addslashes(trim($brnch_id)) : "";
	if($brnch_id!=''){
		$sqls = "select `branch_name` from $branch_master where `branch_code`='$brnch_id'";
		$ress = mysqli_query($conn,$sqls);
		$totress = mysqli_num_rows($ress);
		if($totress>0){
			$rows = mysqli_fetch_assoc($ress);
			$brnchname = $rows["branch_name"] ? trim($rows["branch_name"]) : "";
		}
	}
	return $brnchname;
}
$order_header = "order_header";
$customer_master = "customer_master";
$employee_master = "employee_master";
$location = "location";
$branch_master = "branch_master";
$destination_master = "destination_master";
$order_show_branch="";
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$dnsbcarr = array();
$dnsbcstr ="";
$theactbcarr = array();
$theactbcstr ="";

if($_SESSION["start_user_type"]=="MANAGER"){
	$order_show_branch = $_SESSION["order_show_branch"] ? trim($_SESSION["order_show_branch"]) : "";
	
	if($order_show_branch=="NE"){
	$dns_branchcode_master = "north_east_branch";
	}else if($order_show_branch=="NOTNE"){
	$dns_branchcode_master = "not_north_east_branch";
	}
	if($order_show_branch=="NE" || $order_show_branch=="NOTNE"){	
	$sqlbm = "select `branch_code` from $dns_branchcode_master";
	$resbm = mysqli_query($conn,$sqlbm);
	$totresbm = mysqli_num_rows($resbm);
	if($totresbm>0){
		$dnsbcarr = array();
		while($rowbm=mysqli_fetch_assoc($resbm)){
			$the_dns_bc = $rowbm["branch_code"] ? trim($rowbm["branch_code"]) : "";
			if($the_dns_bc!=""){
				$dnsbcarr[] = $the_dns_bc;
			}
		}
		if(count($dnsbcarr)>0){
			$dnsbcstr = implode("','",$dnsbcarr);
	$sqlabc = "select `branch_code` from $branch_master where `dns_branch_code` in('".$dnsbcstr."')";
	$resabc = mysqli_query($conn,$sqlabc);
	$totresabc = mysqli_num_rows($resabc);
	if($totresabc>0){
		while($rowabc=mysqli_fetch_assoc($resabc)){
			$the_bc = $rowabc["branch_code"] ? trim($rowabc["branch_code"]) : "";
			if($the_bc!=""){
				$theactbcarr[] = $the_bc;
			}
		}
		if(count($theactbcarr)>0){
			$theactbcstr = implode("','",$theactbcarr);
			
		}
		
	}
	
		}
	}
	}
	if($theactbcstr!=""){
	$sqlftcbrnc = "select `branch_code`,`branch_name` from $branch_master where `branch_code` in('".$theactbcstr."') order by `branch_name` asc";
	$res1dftftcbrnc = mysqli_query($conn,$sqlftcbrnc);
	$totres1dftftcbrnc = mysqli_num_rows($res1dftftcbrnc);
	}else{
	$totres1dftftcbrnc = 0;	
	}	
	
}else{
	$sqlftcbrnc = "select `branch_code`,`branch_name` from $branch_master order by `branch_name` asc";
	$res1dftftcbrnc = mysqli_query($conn,$sqlftcbrnc);
	$totres1dftftcbrnc = mysqli_num_rows($res1dftftcbrnc);
}



$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$new_qry_string_filtered = "";
$trn_branch_id = $_GET["trn_branch_id"] ? addslashes(trim($_GET["trn_branch_id"])) : "";
$ds_code = $_GET["ds_code"] ? addslashes(trim($_GET["ds_code"])) : "";
$sl_ord_sts = $_GET["sl_ord_sts"] ? addslashes(trim($_GET["sl_ord_sts"])) : "Order received";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$whr_str = "";
$export_filtered_str = "";
$search_array = array("trn_branch_id"=>$trn_branch_id,"ds_code"=>$ds_code,"sl_ord_sts"=>$sl_ord_sts,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="sl_ord_sts"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			if($search_array_val=="DO_approved_n_Dispatched"){
				$whr_str .= "$aand ($t_apperpdo.`STATUS`='DO approved' or $t_apperpdo.`STATUS`='Dispatched') ";
			}else{
				$whr_str .= "$aand $t_apperpdo.`STATUS`='$search_array_val' ";
			}
			$new_qry_string_filtered .= "&sl_ord_sts=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_ord_sts=".$search_array_val;
			}else{
				$export_filtered_str .= "&sl_ord_sts=".$search_array_val;
			}	
			
		}		
	}else if($search_array_key=="trn_branch_id"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $employee_master.`branch_code`='$search_array_val' ";	
			$new_qry_string_filtered .= "&trn_branch_id=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&trn_branch_id=".$search_array_val;
			}else{
				$export_filtered_str .= "&trn_branch_id=".$search_array_val;
			}		
		}		
	}else if($search_array_key=="ds_code"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $t_apperpdo.`destination_code`='$search_array_val' ";	
			$new_qry_string_filtered .= "&ds_code=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&ds_code=".$search_array_val;
			}else{
				$export_filtered_str .= "&ds_code=".$search_array_val;
			}	
		}		
	}else if($search_array_key=="daywise"){
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
			   $whr_str .= "$aand $t_apperpdo.`order_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $t_apperpdo.`order_date` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $t_apperpdo.`order_date` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $t_apperpdo.`order_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $t_apperpdo.`order_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}


if($whr_str!=""){
	$new_whr_str = "and ".$whr_str;
}else{
	$new_whr_str ="";
}

//?sl_branch_code=B0056&sl_ord_sts=Pending&sl_day_wise=Date_Range&from_dt=2018-07-01&to_dt=2018-07-31
if($trn_branch_id !=""){
	$sql5dcode = "select `destination_code`,`destination_name` from $destination_master where `dns_destination_code`='$trn_branch_id' order by `destination_name` asc ";
	$res5dcode = mysqli_query($conn,$sql5dcode);
	$tot_res5dcode = mysqli_num_rows($res5dcode);
}


$page_name = "order_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

if($_SESSION["start_user_type"]=="MANAGER" && ($order_show_branch=="NE" || $order_show_branch=="NOTNE")){
$pgsql = "select $t_apperpdo.* from $t_apperpdo left join $employee_master on $t_apperpdo.`dns_customer_code`=$employee_master.`dns_emp_code` where $t_apperpdo.`APPORDERNO`!='' and $employee_master.`branch_code` in('".$theactbcstr."') $new_whr_str ";
}else{
$pgsql = "select $t_apperpdo.* from $t_apperpdo left join $employee_master on $t_apperpdo.`dns_customer_code`=$employee_master.`dns_emp_code` where $t_apperpdo.`APPORDERNO`!='' $new_whr_str ";
}
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
.table-bordered thead tr th{
	padding:7px;
}
.wrapper_scrl{
border: none;
overflow-x: scroll;
overflow-y:hidden;
height: 20px;
}
.wrapper_scrl_div{
height: 20px;	
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
                          <h2>Order List (<?php echo $total_pgres;?>) &nbsp;&nbsp;&nbsp;<span class="rpt_loader"></span> &nbsp;&nbsp;<a href="export_order2.php?get_type=all<?php echo $export_filtered_str;?>" class="btn bg-red waves-effe">Export&nbsp;all&nbsp;orders</a> &nbsp; <a href="export_order2.php?get_type=pending<?php echo $export_filtered_str;?>" class="btn bg-red waves-effe">Export&nbsp;pending&nbsp;orders</a></h2>
    <div class="row clearfix">
<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<select name="trn_branch_id" id="trn_branch_id" class="form-control">
<option value="">Select Branch Name</option>
<?php
if($totres1dftftcbrnc>0){
	while($row1dftftcbrnc=mysqli_fetch_assoc($res1dftftcbrnc)){
		$branch_code_iddwd = $row1dftftcbrnc["branch_code"];
		$branch_name_iddwd = $row1dftftcbrnc["branch_name"];
?>
<option value="<?php echo $branch_code_iddwd;?>" <?php if($branch_code_iddwd == $trn_branch_id){?> selected="selected" <?php } ?> ><?php echo $branch_name_iddwd;?></option>
<?php
	}
}
?>
</select>
</div>
<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<select name="ds_code" id="ds_code" class="form-control">
<option value="">Select Destination</option>
<?php
if($tot_res5dcode>0){
	while($row1dcode=mysqli_fetch_assoc($res5dcode)){
		$destination_code_sdf = $row1dcode["destination_code"];
		$destination_name_sdf = $row1dcode["destination_name"];
?>
<option value="<?php echo $destination_code_sdf;?>" <?php if($destination_code_sdf == $ds_code){?> selected="selected" <?php } ?> ><?php echo $destination_name_sdf;?></option>
<?php
	}
}
?>
</select>
    </div>
<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 add_top_bottom_padding">

</div>
    </div>
    <div class="row clearfix">
    
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_ord_sts">
<option value="Order received" <?php if($sl_ord_sts=="Order received"){?> selected="selected" <?php } ?>>Order received</option>
<option value="DO approved" <?php if($sl_ord_sts=="DO approved"){?> selected="selected" <?php } ?>>DO approved</option>
<option value="Dispatched" <?php if($sl_ord_sts=="Dispatched"){?> selected="selected" <?php } ?>>Dispatched</option>
<option value="DO_approved_n_Dispatched" <?php if($sl_ord_sts=="DO_approved_n_Dispatched"){?> selected="selected" <?php } ?>>DO approved + Dispatched</option>
</select>
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
                            <div class="table-responsive  tr_for_scroll">
                                <table class="table table-bordered table-striped table-hover table_for_scroll">
                                    <thead>
                                        <tr>
                                            <th>SL&nbsp;No</th>
                                            <th>App&nbsp;Order&nbsp;No</th>
                                            <th>ERP&nbsp;No</th>
                                            <th>DATE</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Cust&nbsp;Code</th>
                                            <th>Customer&nbsp;Name</th>
                                            <th>Consignee&nbsp;Name</th>
                                            <th>Consignee&nbsp;Address</th>
                                            <th>Destination</th>
                                            <th>Product&nbsp;Name</th>
                                            <th>qty&nbsp;(MT)</th>
                                            <th>Status</th>
                                            <th>Show&nbsp;Challan&nbsp;Details</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                      <tr>
                                            <th>SL&nbsp;No</th>
                                            <th>App&nbsp;Order&nbsp;No</th>
                                            <th>ERP&nbsp;No</th>
                                            <th>DATE</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Cust&nbsp;Code</th>
                                            <th>Customer&nbsp;Name</th>
                                            <th>Consignee&nbsp;Name</th>
                                            <th>Consignee&nbsp;Address</th>
                                            <th>Destination</th>
                                            <th>Product&nbsp;Name</th>
                                            <th>qty&nbsp;(MT)</th>
                                            <th>Status</th>
                                            <th>Show&nbsp;Challan&nbsp;Details</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
if($_SESSION["start_user_type"]=="MANAGER" && ($order_show_branch=="NE" || $order_show_branch=="NOTNE")){
$sql1 = "select $t_apperpdo.*,$employee_master.`branch_code` from $t_apperpdo left join $employee_master on $t_apperpdo.`dns_customer_code`=$employee_master.`dns_emp_code` where $t_apperpdo.`APPORDERNO`!='' and $employee_master.`branch_code` in('".$theactbcstr."') $new_whr_str order by $t_apperpdo.`order_date` desc limit $start_from,$limit";
}else{
$sql1 = "select $t_apperpdo.*,$employee_master.`branch_code` from $t_apperpdo left join $employee_master on $t_apperpdo.`dns_customer_code`=$employee_master.`dns_emp_code` where $t_apperpdo.`APPORDERNO`!='' $new_whr_str order by `order_date` desc limit $start_from,$limit";
}
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
$the_sl_no = 1;
$the_sl_no = (($limit*($page-1))+1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$sl_no = $row1["id"];
		
		$apporder_no = $row1["APPORDERNO"];
		$erporder_no = $row1["ERPORDERNO"];
		$erporder_date = $row1["ERPORDERDT"];
		$order_status = $row1["STATUS"];
		$order_date = $row1["order_date"];
		$destination_name = $row1["destination_name"];
		$consignee_name = "";
		$consignee_address = "";
		$consignee_address_arr = array();
		$order_for = $row1["order_for"] ? trim($row1["order_for"]) : "";
		if($order_for!=""){
			if( strpos($order_for,",") !== false ) {
				$ofrarr = array();
				$ofrarr = explode(",",$order_for);
				if(count($ofrarr)>0){
					for($i=0;$i<count($ofrarr);$i++){
					
					if($i==0){
						$consignee_name = $ofrarr[$i];
					}else{
						$consignee_address_arr[] = $ofrarr[$i];
					}
					
					}
				}
			}
		}
		if(count($consignee_address_arr)>0){
			$consignee_address = implode(",",$consignee_address_arr);
			unset($consignee_address_arr);
		}
		$prod_code = $row1["prod_code"];
		$prod_display_name = $row1["prod_display_name"];
		$prod_qty = $row1["QTY"];
		
		$dns_customer_code = $row1["dns_customer_code"];
		$customer_code = $row1["customer_code"];
		$customer_name = get_customer_name_from_id($conn,$customer_code);
		$customer_branch_code = $row1["branch_code"];
		$customer_branch_name = get_branch_name_from_id($conn,$customer_branch_code);
		
?>
<tr>
<td><?php echo $the_sl_no;?></td>
<td><?php echo $apporder_no;?></td>
<td><?php echo $erporder_no;?></td>
<td><?php echo $order_date;?></td>
<td><?php echo $customer_branch_name;?></td>
<td><?php echo $dns_customer_code;?></td>
<td><?php echo $customer_name;?></td>
<td><?php echo $consignee_name;?></td>
<td><?php echo $consignee_address;?></td>
<td><?php echo $destination_name;?></td>
<td><?php echo $prod_display_name;?></td>
<td><?php echo $prod_qty;?></td>
<td><?php echo $order_status;?></td>
<td>
<?php if($order_status=="Dispatched"){ ?>
	<a href="ajax_show_challan_details_by_app_erp_id.php?apporder_no=<?php echo $apporder_no;?>&erporder_no=<?php echo $erporder_no;?>" class="btn bg-red vuChnlDtlsLink">Show&nbsp;Challan</a>
<?php } ?></td>
</tr>

<?php
$the_sl_no++;
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="14">No data found.</td>
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


jQuery('#trn_branch_id').change(function(){
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
	
	});



	jQuery(".pcsts").change(function(){
		var stsval = jQuery(this).val();
		var curr_ordid = jQuery(this).attr("sltoid");
		if(stsval!="" && curr_ordid!=""){
			var theldrid = "os_ldr_"+curr_ordid;
			var for_loader = jQuery("#"+theldrid);
			for_loader.html(imgs);
jQuery.ajax({
url: 'ajax_update_ord_sts_by_ord_id.php',
type: 'post',
dataType: "JSON",
data: "curr_ordid="+curr_ordid+"&stsval="+stsval,
success: function(response){
	if(response.process_status=="YES"){
	for_loader.html(done_img);
	setTimeout(function (){
			for_loader.html("");
		},3000);
	}else{
		for_loader.html("");
		alert(response.process_message);
	}
}
});
			
		}
	});
	
	
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
		var trn_branch_id = jQuery("#trn_branch_id").val();
		var ds_code = jQuery("#ds_code").val();
		var sl_ord_sts = jQuery("#sl_ord_sts").val();
		var sl_day_wise = jQuery("#sl_day_wise").val();
		var from_dt = jQuery("#from_dt").val();
		var to_dt = jQuery("#to_dt").val();
		var qstring ="";
		var dtstring ="";
		var amp = "";
		if(trn_branch_id!="" || sl_ord_sts!="" || sl_day_wise!=""){
			
			
		if(trn_branch_id!=""){
		if(qstring==""){
		qstring = qstring+"trn_branch_id="+trn_branch_id;
		}else{
		qstring = qstring+"&trn_branch_id="+trn_branch_id;  
		}
		
		if(ds_code!=""){
		if(qstring==""){
		qstring = qstring+"ds_code="+ds_code;
		}else{
		qstring = qstring+"&ds_code="+ds_code;  
		}
		}
		}
			
			
		if(sl_ord_sts!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_ord_sts="+sl_ord_sts;
			}else{
				qstring = qstring+"sl_ord_sts="+sl_ord_sts;
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
		window.location = "order_list.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "order_list.php";
	});
	
	
	
});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>