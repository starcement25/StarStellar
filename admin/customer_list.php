<?php
include "web_check.php";
include "star_connection.php";
$table_name = "customer_master";
$branch_master = "branch_master";
$employee_master = "employee_master";
function get_branch_name_from_id($conn,$branch_id){
	$branch_master = "branch_master";
	$branch_name = "";
	$branch_id = $branch_id ? addslashes(trim($branch_id)) : "";
	if($branch_id!=''){
		$sqls = "select `branch_name` from $branch_master where `branch_code`='$branch_id'";
		$ress = mysqli_query($conn,$sqls);
		$totress = mysqli_num_rows($ress);
		if($totress>0){
			$rows = mysqli_fetch_assoc($ress);
			$branch_name = $rows["branch_name"] ? trim($rows["branch_name"]) : "";
		}
	}
	return $branch_name;
}
function get_branch_code_from_emp_master($conn,$dns_customer_code){
	$employee_master = "employee_master";
	$branch_code = "";
	$dns_customer_code = $dns_customer_code ? addslashes(trim($dns_customer_code)) : "";
	if($dns_customer_code!=''){
		$sqls = "select `branch_code` from $employee_master where `dns_emp_code`='$dns_customer_code'";
		$ress = mysqli_query($conn,$sqls);
		$totress = mysqli_num_rows($ress);
		if($totress>0){
			$rows = mysqli_fetch_assoc($ress);
			$branch_code = $rows["branch_code"] ? trim($rows["branch_code"]) : "";
		}
	}
	return $branch_code;
}
$new_qry_string_filtered = "";
$sl_branch = $_GET["sl_branch"] ? addslashes(trim($_GET["sl_branch"])) : "";
$srch_cust_dtls = $_GET["srch_cust_dtls"] ? addslashes(trim($_GET["srch_cust_dtls"])) : "";
$sl_cust_type = $_GET["sl_cust_type"] ? addslashes(trim($_GET["sl_cust_type"])) : "";
$whr_str = "";
$search_array = array("sl_branch"=>$sl_branch,"srch_cust_dtls"=>$srch_cust_dtls,"sl_cust_type"=>$sl_cust_type,"sl_dlr_actdat"=>"Y","not_show_cust_type"=>"Non Star");
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="sl_branch"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $table_name.`branch_code`='$search_array_val' ";
			$new_qry_string_filtered .= "&sl_branch=".$search_array_val;
		}
	}else if($search_array_key=="srch_cust_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand ($table_name.`dns_customer_code` like '%$search_array_val%' or $table_name.`customer_name` like '%$search_array_val%' or $table_name.`phone_no` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_cust_dtls=".$search_array_val;
		}
	}else if($search_array_key=="sl_cust_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $table_name.`cust_type`='$search_array_val' ";
			$new_qry_string_filtered .= "&sl_cust_type=".$search_array_val;		
		}		
	}else if($search_array_key=="not_show_cust_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $table_name.`cust_type`!='$search_array_val' ";	
		}		
	}else if($search_array_key=="sl_dlr_actdat"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $table_name.`acedns`='$search_array_val '";	
			$new_qry_string_filtered .= "&sl_dlr_actdat=".$search_array_val;		
		}		
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}

$sql22 = "select `branch_code`,`branch_name` from $branch_master where `acedns`='Y' order by `branch_name`";
$res22 = mysqli_query($conn,$sql22);
$totres22 = mysqli_num_rows($res22);

$page_name = "customer_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $table_name.`customer_code` from $table_name left join $branch_master on $table_name.`branch_code`=$branch_master.`branch_code` $new_whr_str";
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
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                          <h2>Customer List (<?php echo $total_pgres;?>)&nbsp; <a href="export_customer.php?get_type=all" class="btn bg-red waves-effe">Export&nbsp;all&nbsp;customer</a> &nbsp; <a href="export_customer.php?get_type=dealer" class="btn bg-red waves-effe">Export&nbsp;Dealer</a> &nbsp; <a href="export_customer.php?get_type=subdealer" class="btn bg-red waves-effe">Export&nbsp;Sub-Dealer</a></h2>

<div class="row clearfix">
<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_branch">
<option value="">Select Branch</option>
<?php
if($totres22>0){
	while($row22=mysqli_fetch_assoc($res22)){
		$the_branch_code = $row22["branch_code"];
		$the_branch_name = $row22["branch_name"];
		?>
 <option value="<?php echo $the_branch_code;?>" <?php if($the_branch_code==$sl_branch){?> selected="selected" <?php } ?>><?php echo $the_branch_name;?></option>
        <?php
	}
}
?>
</select>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_cust_dtls" value="<?php echo $srch_cust_dtls;?>" placeholder="Search Dealer Details">
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_cust_type" style="padding-left:2px;">
<option value="">Select Cust Type</option>
<option value="Dealer" <?php if($sl_cust_type=="Dealer"){?> selected="selected" <?php } ?>>Dealer</option>
<option value="Sub Dealer" <?php if($sl_cust_type=="Sub Dealer"){?> selected="selected" <?php } ?>>Sub Dealer</option>
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
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Dealer&nbsp;ID</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Phone</th>
                                            <th>Branch&nbsp;Code</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Dealer&nbsp;Type</th>
                                            <th>Active&nbsp;(Y/N)</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                      <tr>
                                            <th>Dealer&nbsp;ID</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Phone</th>
                                            <th>Branch&nbsp;Code</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Dealer&nbsp;Type</th>
                                            <th>Active&nbsp;(Y/N)</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $table_name.*,$branch_master.`branch_name` from $table_name left join $branch_master on $table_name.`branch_code`=$branch_master.`branch_code` $new_whr_str order by $table_name.`dns_customer_code` asc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		
		//$the_branch_name = get_branch_name_from_id($conn,$branch_code);
		$dns_customer_code = $row1["dns_customer_code"];
		$customer_name = $row1["customer_name"];
		$branch_code = $row1["branch_code"];
		$branch_name = $row1["branch_name"];
		$phone_no = $row1["phone_no"];
		$route_code = $row1["route_code"];
		$cust_type = $row1["cust_type"];
		$acedns = $row1["acedns"];
		$current_balance = $row1["current_balance"];
		$credit_limit = $row1["credit_limit"];
		$credit_days = $row1["credit_days"];
?>
<tr>
<td><?php echo $dns_customer_code;?></td>
<td><?php echo $customer_name;?></td>
<td><?php echo $phone_no;?></td>
<td><?php echo $branch_code;?></td>
<td><?php echo $branch_name;?></td>
<td><?php echo $cust_type;?></td>
<td><?php echo $acedns;?></td>

</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="7">No data found.</td>
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



	jQuery(".srch_btn").click(function(){
		var sl_branch = jQuery("#sl_branch").val();
		var srch_cust_dtls = jQuery("#srch_cust_dtls").val();
		var sl_cust_type = jQuery("#sl_cust_type").val();
		var qstring ="";
		var amp = "";
		if(sl_branch!="" || srch_cust_dtls!="" || sl_cust_type!=""){
		if(sl_branch!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_branch="+encodeURIComponent(sl_branch);
			}else{
				qstring = qstring+"sl_branch="+encodeURIComponent(sl_branch);
			}
		}
		if(srch_cust_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_cust_dtls="+encodeURIComponent(srch_cust_dtls);
			}else{
				qstring = qstring+"srch_cust_dtls="+encodeURIComponent(srch_cust_dtls);
			}
		}
		if(sl_cust_type!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_cust_type="+sl_cust_type;
			}else{
				qstring = qstring+"sl_cust_type="+sl_cust_type;
			}
		}
		
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "customer_list.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "customer_list.php";
	});
});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>