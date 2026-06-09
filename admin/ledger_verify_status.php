<?php
include "web_check.php";
include "star_connection.php";
$customer_master = "customer_master";
$verify_ledger_details = "verify_ledger_details";
$month_arr = array("01"=>"JAN","02"=>"FEB","03"=>"MAR","04"=>"APR","05"=>"MAY","06"=>"JUN","07"=>"JUL","08"=>"AUG","09"=>"SEP","10"=>"OCT","11"=>"NOV","12"=>"DEC");   
$new_qry_string_filtered = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$astn_sl_status = $_GET["sl_status"] ? addslashes(trim($_GET["sl_status"])) : "";

$astn_sl_year = $_GET["sl_year"] ? addslashes(trim($_GET["sl_year"])) : "";
$astn_sl_month = $_GET["sl_month"] ? addslashes(trim($_GET["sl_month"])) : "";

$whr_str = "";
$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls,"astn_sl_status"=>$astn_sl_status,"year_month"=>array("astn_sl_year"=>$astn_sl_year,"astn_sl_month"=>$astn_sl_month));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ( $customer_master.`dns_customer_code` like '%$search_array_val%' or $customer_master.`customer_name` like '%$search_array_val%' or $customer_master.`customer_code` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
		}
	}else if($search_array_key=="astn_sl_status"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand $verify_ledger_details.`status`='$search_array_val' ";
			$new_qry_string_filtered .= "&sl_status=".$search_array_val;
		}
	}else if($search_array_key=="year_month"){
		$the_astn_sl_year = $search_array_val["astn_sl_year"] ? $search_array_val["astn_sl_year"] : "";
		$the_astn_sl_month = $search_array_val["astn_sl_month"] ? $search_array_val["astn_sl_month"] : "";
		if(trim($whr_str)!=""){
		$aand = " and";
		}else{
		$aand = "";
		}
		if($the_astn_sl_year!="" && $the_astn_sl_month!=""){
$whr_str .= "$aand DATE_FORMAT(STR_TO_DATE($verify_ledger_details.`ledger_year_month_day`, '%Y-%m-%d'), '%Y-%m') = '".$the_astn_sl_year."-".$the_astn_sl_month."' ";
$new_qry_string_filtered .= "&sl_year=".$the_astn_sl_year."&sl_month=".$the_astn_sl_month;
			}else if($the_astn_sl_year!="" && $the_astn_sl_month==""){
$whr_str .= "$aand DATE_FORMAT(STR_TO_DATE($verify_ledger_details.`ledger_year_month_day`, '%Y-%m-%d'), '%Y') = '".$the_astn_sl_year."' ";
				$new_qry_string_filtered .= "&sl_year=".$the_astn_sl_year;
			}else if($the_astn_sl_year=="" && $the_astn_sl_month!=""){
$whr_str .= "$aand DATE_FORMAT(STR_TO_DATE($verify_ledger_details.`ledger_year_month_day`, '%Y-%m-%d'), '%m') = '".$the_astn_sl_month."' ";
				$new_qry_string_filtered .= "&sl_month=".$the_astn_sl_month;
			}
		
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "ledger_verify_status.php";
$page_name = "ledger_verify_status.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $verify_ledger_details.`vld_id` from $verify_ledger_details left join $customer_master on $verify_ledger_details.`customer_code`=$customer_master.`customer_code` $new_whr_str";
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
.add_top_bottom_padding{
	padding:5px 8px;
	text-align:left;
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
                          <h2>Ledger Verify Status (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                          <?php /*?><a href="export_dealer.php?get_type=loggedin" class="btn bg-red waves-effe">Export&nbsp;loggedin&nbsp;dealer</a> &nbsp; <a href="export_dealer.php?get_type=notloggedin" class="btn bg-red waves-effe">Export&nbsp;not&nbsp;loggedin&nbsp;dealer</a><?php */?>
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
    <label>Enter Customer/Dealer Details</label>
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Customer/Dealer Details">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <label>Status</label>
<select class="form-control sl_status" id="sl_status" name="sl_status">
<option value="">All</option>
<option value="APPROVED" <?php if($astn_sl_status=="APPROVED"){ ?> selected="selected" <?php }?> >LEDGER CONFIRMED</option>
<option value="REJECTED" <?php if($astn_sl_status=="REJECTED"){ ?> selected="selected" <?php }?> >REJECTED</option>
</select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <label>Year</label>
<select class="form-control sl_year" id="sl_year" name="sl_year">
<option value="">All</option>
<option value="2017" <?php if($astn_sl_year=="2017"){ ?> selected="selected" <?php }?> >2017</option>
<option value="2018" <?php if($astn_sl_year=="2018"){ ?> selected="selected" <?php }?> >2018</option>
<option value="2019" <?php if($astn_sl_year=="2019"){ ?> selected="selected" <?php }?> >2019</option>
</select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <label>Month</label>
<select class="form-control sl_month" id="sl_month" name="sl_month">
<option value="">All</option>
<?php
foreach($month_arr as $key_month=>$month_name){
?>
<option value="<?php echo $key_month?>" <?php if($astn_sl_month==$key_month){ ?> selected="selected" <?php }?> ><?php echo $month_name?></option>
<?php
}
?>
</select>
    </div> 
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
    <label style="width:100%;">&nbsp;</label>
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" style="margin-left:10px;">Reset</button>
    </div>
    
         
      
    </div>
<span style="clear:both;display:block;"></span>
                        </div>
                        <div class="body">
<?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
 <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Customer&nbsp;Code</th>
                                            <th>Dealer&nbsp;ID</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Ledger&nbsp;Month</th>
                                            <th>Debit&nbsp;Amount</th>
                                            <th>Credit&nbsp;Amount</th>
                                            <th>Status</th>
                                            <th>Comment</th>
                                            <th>Updated&nbsp;On</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                       <tr>
                                            <th>Customer&nbsp;Code</th>
                                            <th>Dealer&nbsp;ID</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Ledger&nbsp;Month</th>
                                            <th>Debit&nbsp;Amount</th>
                                            <th>Credit&nbsp;Amount</th>
                                            <th>Status</th>
                                            <th>Comment</th>
                                            <th>Updated&nbsp;On</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $verify_ledger_details.*,$customer_master.`customer_code`,$customer_master.`dns_customer_code`,$customer_master.`customer_name` from $verify_ledger_details left join $customer_master on $verify_ledger_details.`customer_code`=$customer_master.`customer_code` $new_whr_str order by $verify_ledger_details.`saved_datetime` desc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$cust_code = $row1["customer_code"];
		$dns_cust_code = $row1["dns_customer_code"];
		$cust_name = $row1["customer_name"];
		$ledger_year_month_day = $row1["ledger_year_month_day"] ? trim($row1["ledger_year_month_day"]) : "";
		if($ledger_year_month_day!=""){
			$ledger_month_year = date("M Y",strtotime($ledger_year_month_day));
		}else{
			$ledger_month_year = "";
		}
		$total_amount_dr = $row1["total_amount_dr"];
		$total_amount_cr = $row1["total_amount_cr"];
		$status = $row1["status"];
		$comment = $row1["comment"];
		$saved_datetime = $row1["saved_datetime"];
?>
<tr>
<td><?php echo $cust_code;?></td>
<td><?php echo $dns_cust_code;?></td>
<td><?php echo $cust_name;?></td>
<td><?php echo $ledger_month_year;?></td>
<td><?php echo $total_amount_dr;?></td>
<td><?php echo $total_amount_cr;?></td>
<td><?php if($status=="APPROVED"){ echo "LEDGER CONFIRMED";}else{ echo $status;}?></td>
<td><?php echo $comment;?></td>
<td><?php echo $saved_datetime;?></td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="9">No data found.</td>
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
	
	
	
	jQuery(".srch_btn").click(function(){
		var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();
		var sl_status = jQuery("#sl_status").val();
		var sl_year = jQuery("#sl_year").val();
		var sl_month = jQuery("#sl_month").val();
		var qstring ="";
		var amp = "";
		
		if(srch_dlr_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}else{
				qstring = qstring+"srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}
		}
		
		if(sl_status!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_status="+encodeURIComponent(sl_status);
			}else{
				qstring = qstring+"sl_status="+encodeURIComponent(sl_status);
			}
		}
		if(sl_year!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_year="+encodeURIComponent(sl_year);
			}else{
				qstring = qstring+"sl_year="+encodeURIComponent(sl_year);
			}
		}
		if(sl_month!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_month="+encodeURIComponent(sl_month);
			}else{
				qstring = qstring+"sl_month="+encodeURIComponent(sl_month);
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