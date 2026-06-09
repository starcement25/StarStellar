<?php
include "web_check.php";
include "star_connection.php";
$table_name = "employee_master";
$changepassword = "changepassword";

$new_qry_string_filtered = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$sl_dlr_actdat = $_GET["sl_dlr_actdat"] ? addslashes(trim($_GET["sl_dlr_actdat"])) : "";
$sl_dlr_alocated_type = $_GET["sl_dlr_alocated_type"] ? addslashes(trim($_GET["sl_dlr_alocated_type"])) : "";
$sl_dlr_logedin_type = $_GET["sl_dlr_logedin_type"] ? addslashes(trim($_GET["sl_dlr_logedin_type"])) : "";
$whr_str = "";
$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls,"sl_dlr_actdat"=>"Y","sl_dlr_alocated_type"=>$sl_dlr_alocated_type,"sl_dlr_logedin_type"=>$sl_dlr_logedin_type);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($table_name.`dns_emp_code` like '%$search_array_val%' or $table_name.`emp_name` like '%$search_array_val%' or $table_name.`phone_no` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
		}
	}else if($search_array_key=="sl_dlr_actdat"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $table_name.`acedns`='$search_array_val' ";	
			$new_qry_string_filtered .= "&sl_dlr_actdat=".$search_array_val;		
		}		
	}else if($search_array_key=="sl_dlr_alocated_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			if($search_array_val=="Allocated"){
				$whr_str .= "$aand ($changepassword.`deviceid`!='' and $changepassword.`deviceid` is not null) ";
			}else if($search_array_val=="Not-Allocated"){
				$whr_str .= "$aand ($changepassword.`deviceid`='' or $changepassword.`deviceid` is null) ";
			}				
			$new_qry_string_filtered .= "&sl_dlr_alocated_type=".$search_array_val;		
		}		
	}else if($search_array_key=="sl_dlr_logedin_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			if($search_array_val=='loggedin'){
			$whr_str .= "$aand $table_name.`sms_otp`!='' ";	
			}else if($search_array_val=='notloggedin'){
			$whr_str .= "$aand $table_name.`sms_otp`='' ";	
			}
			$new_qry_string_filtered .= "&sl_dlr_logedin_type=".$search_array_val;
			
		}		
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "edit_dealer_list.php";
$page_name = "dealer_list.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $table_name.`emp_code`,$changepassword.`deviceid` from $table_name left join $changepassword on $table_name.`emp_code`=$changepassword.`emp_code` $new_whr_str";
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
<script type="text/javascript">
jQuery(function () {
	jQuery('.srch_btn').on('click', function () {
		var srch_trm = encodeURIComponent(jQuery.trim(jQuery(".srch_trm").val()));
		if(srch_trm!=""){
			var qstring ="";
			var amp = "";
			var paged=1;
		  var curr_urrl = window.location.href;
		  if (curr_urrl.indexOf("?") >= 0){
		  var fst_url = curr_urrl.split("?");
		  var the_path = fst_url[0];
		  var fst_url_str_val = fst_url[1];
		  var qry_str_arr = fst_url_str_val.split("&");
		  for(i=0;i<qry_str_arr.length;i++){
			  if(qstring==""){
				  amp = "";
				  }else{
				  amp = "&";
				  }
			  var crr_str = qry_str_arr[i].split("=");
			  if(crr_str[0]=="srch_trm"){
				  qstring = qstring+amp+"srch_trm="+srch_trm;
			  }else{
				  qstring = qstring+amp+crr_str[0]+"="+crr_str[1];
			  }
			  
			  }
			  if (qstring.indexOf("srch_trm") < 0){
				  if(qstring==""){
			  qstring = qstring+"srch_trm="+srch_trm;
				  }else{
					qstring = qstring+"&srch_trm="+srch_trm; 
				  }
			  }
			  var new_url = fst_url[0]+"?"+qstring;
		  }else{
			  qstring = 'srch_trm='+srch_trm;
		      var new_url = curr_urrl+'?'+qstring;  
		  }
		  
		   window.location = new_url;
		}
	});
	
	jQuery('.srch_reset_btn').on('click', function () {
		window.location = "dealer_list.php";
	});
	
});
</script>
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                          <h2>Dealer List (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                          <a href="export_dealer.php?get_type=loggedin" class="btn bg-red waves-effe">Export&nbsp;loggedin&nbsp;dealer</a> &nbsp; <a href="export_dealer.php?get_type=notloggedin" class="btn bg-red waves-effe">Export&nbsp;not&nbsp;loggedin&nbsp;dealer</a>
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Dealer Details">
    </div>
    <?php /*?><div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <select class="form-control" id="sl_dlr_actdat">
<option value="">Dealer Status</option>
<option value="Y" <?php if($sl_dlr_actdat=="Y"){?> selected="selected" <?php } ?>>Y</option>
<option value="N" <?php if($sl_dlr_actdat=="N"){?> selected="selected" <?php } ?>>N</option>
</select>

    </div><?php */?>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_dlr_alocated_type">
<option value="">Allocation Status</option>
<option value="Allocated" <?php if($sl_dlr_alocated_type=="Allocated"){?> selected="selected" <?php } ?>>Allocated</option>
<option value="Not-Allocated" <?php if($sl_dlr_alocated_type=="Not-Allocated"){?> selected="selected" <?php } ?>>Not-Allocated</option>
</select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_dlr_logedin_type">
<option value="">Login Status</option>
<option value="loggedin" <?php if($sl_dlr_logedin_type=="loggedin"){?> selected="selected" <?php } ?>>Logged in</option>
<option value="notloggedin" <?php if($sl_dlr_logedin_type=="notloggedin"){?> selected="selected" <?php } ?>>Not Logged in</option>
</select>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" >Reset</button>
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
                                            <th>App&nbsp;Version</th>
                                            <th>Allocation&nbsp;Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Dealer&nbsp;ID</th>
                                            <th>Dealer&nbsp;Name</th>
                                            <th>Phone</th>
                                            <th>App&nbsp;Version</th>
                                            <th>Allocation&nbsp;Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $table_name.*,$changepassword.`deviceid`,$changepassword.`app_version` from $table_name left join $changepassword on $table_name.`emp_code`=$changepassword.`emp_code` $new_whr_str order by $table_name.`emp_code` asc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$emp_code = $row1["emp_code"];
		$dns_emp_code = $row1["dns_emp_code"];
		$emp_name = $row1["emp_name"];
		$acedns = $row1["acedns"];
		$phone_no = $row1["phone_no"];
		$deviceid = $row1["deviceid"] ? trim($row1["deviceid"]) : "";
		$app_version = $row1["app_version"];
?>
<tr>
<td><?php echo $dns_emp_code;?></td>
<td><?php echo $emp_name;?></td>
<td><?php echo $phone_no;?></td>
<td><?php echo $app_version;?></td>
<td style="text-align:center;"><?php
if($deviceid!=""){
?>
<a href="javascript:void(0);" class="clemply" clemplyid="<?php echo $emp_code;?>">Clear Allocation</a>
<span class="os_ldr" id="ca_ldr_<?php echo $emp_code;?>"></span>
<?php
}else{
	echo '--';
}
?></td>
<td><a href="<?php echo $add_page_name."?theempid=".$emp_code."&paged=".$page?>" class="btn bg-red waves-effect">Edit</a></td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="6">No data found.</td>
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
	
	jQuery(".clemply").click(function(){
		var ancr_elmnt = jQuery(this);
		var clemplyid = ancr_elmnt.attr("clemplyid");
		if(clemplyid!=""){
			var theldrid = "ca_ldr_"+clemplyid;
			var for_loader = jQuery("#"+theldrid);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_clear_allocation_by_emp_id.php',
			type: 'post',
			dataType: "JSON",
			data: "clemplyid="+clemplyid,
			success: function(response){
			if(response.process_status=="YES"){
				ancr_elmnt.html("--");
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
	
	
	
	jQuery(".srch_btn").click(function(){
		var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();
		var sl_dlr_actdat = jQuery("#sl_dlr_actdat").val();
		var sl_dlr_alocated_type = jQuery("#sl_dlr_alocated_type").val();
		var sl_dlr_logedin_type = jQuery("#sl_dlr_logedin_type").val();	
		var qstring ="";
		var amp = "";
		if(srch_dlr_dtls!="" || sl_dlr_actdat!="" || sl_dlr_alocated_type!="" || sl_dlr_logedin_type!=""){
		if(srch_dlr_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}else{
				qstring = qstring+"srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}
		}
		if(sl_dlr_actdat!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_dlr_actdat="+sl_dlr_actdat;
			}else{
				qstring = qstring+"sl_dlr_actdat="+sl_dlr_actdat;
			}
		}
		if(sl_dlr_alocated_type!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_dlr_alocated_type="+sl_dlr_alocated_type;
			}else{
				qstring = qstring+"sl_dlr_alocated_type="+sl_dlr_alocated_type;
			}
		}
		
		if(sl_dlr_logedin_type!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_dlr_logedin_type="+sl_dlr_logedin_type;
			}else{
				qstring = qstring+"sl_dlr_logedin_type="+sl_dlr_logedin_type;
			}
		}
		
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "dealer_list.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "dealer_list.php";
	});
});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>