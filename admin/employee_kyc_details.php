<?php
include "web_check.php";
include "star_connection.php";
$employee_master = "employee_master";
$employee_kyc_master = "employee_kyc_master";

$new_qry_string_filtered = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$whr_str = "";
$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($employee_master.`emp_code` like '%$search_array_val%' or $employee_master.`dns_emp_code` like '%$search_array_val%' or $employee_master.`emp_name` like '%$search_array_val%' or $employee_kyc_master.`whatsapp_no` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
		}
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "employee_kyc_details.php";
$page_name = "employee_kyc_details.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $employee_kyc_master.`emp_code` from $employee_kyc_master left join $employee_master on $employee_kyc_master.`emp_code`=$employee_master.`emp_code` $new_whr_str";
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
                          <h2>Employee KYC Details (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                          <?php /*?><a href="export_dealer.php?get_type=loggedin" class="btn bg-red waves-effe">Export&nbsp;loggedin&nbsp;dealer</a> &nbsp; <a href="export_dealer.php?get_type=notloggedin" class="btn bg-red waves-effe">Export&nbsp;not&nbsp;loggedin&nbsp;dealer</a><?php */?>
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Employee/Dealer Details">
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" >Reset</button>
    </div>
         <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">

    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
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
 <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Employee&nbsp;Code</th>
                                            <th>Dealer&nbsp;ID</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Whatsapp&nbsp;No.</th>
                                            <th>DOB</th>
                                            <th>DOM</th>
                                            <th>Email</th>
                                            <th>Updated&nbsp;On</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Employee&nbsp;Code</th>
                                            <th>Dealer&nbsp;ID</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Whatsapp&nbsp;No.</th>
                                            <th>DOB</th>
                                            <th>DOM</th>
                                            <th>Email</th>
                                            <th>Updated&nbsp;On</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $employee_kyc_master.*,$employee_master.`dns_emp_code`,$employee_master.`emp_name` from $employee_kyc_master left join $employee_master on $employee_kyc_master.`emp_code`=$employee_master.`emp_code` $new_whr_str order by $employee_kyc_master.`emp_code` asc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$emp_code = $row1["emp_code"];
		$dns_emp_code = $row1["dns_emp_code"];
		$emp_name = $row1["emp_name"];
		$whatsapp_no = $row1["whatsapp_no"];
		$dob = $row1["dob"];
		$dom = $row1["dom"];
		$email_id = $row1["email_id"];
		$last_updated_datetime = $row1["last_updated_datetime"];
?>
<tr>
<td><?php echo $emp_code;?></td>
<td><?php echo $dns_emp_code;?></td>
<td><?php echo $emp_name;?></td>
<td><?php echo $whatsapp_no;?></td>
<td><?php echo $dob;?></td>
<td><?php echo $dom;?></td>
<td><?php echo $email_id;?></td>
<td><?php echo $last_updated_datetime;?></td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="8">No data found.</td>
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
		var qstring ="";
		var amp = "";
		if(srch_dlr_dtls!=""){
		if(srch_dlr_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}else{
				qstring = qstring+"srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}
		}
		
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "employee_kyc_details.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "employee_kyc_details.php";
	});
});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>