<?php
include "web_check.php";
include "star_connection.php";
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// var_dump($_GET['edt_e_id']);

$engineer_master = "engineer_master";
$te_master = "te_master";
$branch_master = "branch_master";
$ediit_inactive_menu_array = array();
$submsg ="";
$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}
$csv_mimetypes = array(
    'text/csv',
    'application/csv',
	'application/x-csv',
    'text/comma-separated-values',
	'text/x-comma-separated-values',
	'text/tab-separated-values',
    'application/excel',
	'application/vnd.ms-excel'
);

function check_mobile_number_exists_for_update($conn,$tecode,$mob_no){
	$ck_sts = "NO";
	$te_master = "te_master";
	$sqlck1 = "select `te_mobile_no` from $te_master where `te_mobile_no`='$mob_no' and `te_code`!='$tecode'";
	$resck1 = mysqli_query($conn,$sqlck1);
	$totresck1 = mysqli_num_rows($resck1);
	if($totresck1>0){
		$ck_sts = "YES";
	}
return $ck_sts;
}

function check_mobile_number_exists_for_save($conn,$mob_no){
	$ck_sts = "NO";
	$te_master = "te_master";
	$sqlck1 = "select `te_mobile_no` from $te_master where `te_mobile_no`='$mob_no'";
	$resck1 = mysqli_query($conn,$sqlck1);
	$totresck1 = mysqli_num_rows($resck1);
	if($totresck1>0){
		$ck_sts = "YES";
	}
return $ck_sts;
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

function get_branch_codes_by_names($conn,$bnms){
$pnms = "";
$nmsarr = array();
$selected_pids_arr = array();
$branch_master = "branch_master";
$bnms = $bnms ? trim($bnms) : "" ;
	if($bnms!=""){
		$selected_pids_arr = explode(",",$bnms);
		$selected_pids_str_for_qry = implode("','",$selected_pids_arr);
	$sql1 = "select `branch_code` from $branch_master where `branch_name` in ('".$selected_pids_str_for_qry."') ";
	$res1 = mysqli_query($conn,$sql1);
	$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
	$the_prod_name_sho = trim($row1["branch_code"]);
	$nmsarr[] = $the_prod_name_sho;
	}
	$qpnms = implode(",",$nmsarr);
	}	 
	}
return $qpnms;
}
$insert_count = 0;
$update_count = 0;
$mobile_number_exist_count = 0;
if(@$_POST["bulk_upload_btn"]=="Upload TE CSV"){
$astn_upload_csv_file_name = $_FILES["upload_csv_file"]["name"];
$astn_upload_csv_file_tmp_name = $_FILES["upload_csv_file"]["tmp_name"];
$astn_upload_csv_file_type = $_FILES["upload_csv_file"]["type"];
	if($astn_upload_csv_file_name==''){
		$submsg = 'Please select a csv file.';
		$res_colour = 2;
	}else if(!in_array($astn_upload_csv_file_type, $csv_mimetypes)){
		$submsg = "Please select a csv file.";
		$res_colour = 2;
	}else{
		
		if(($handle = fopen($astn_upload_csv_file_tmp_name , "r")) !== FALSE) 
	{
		$data1 = fgetcsv($handle, 1000, ",");	
		$countscsvrow = 0;
		$countscsvrowupd = 0;
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){
$thecsv_te_emp_code = $data[0];
$thecsv_te_name = $data[1];
$thecsv_branch_names = $data[2];
$thecsv_reporting_to = $data[3];
$thecsv_mobile = $data[4];
$thecsv_email = $data[5];
$thecsv_designation = $data[6];
$thecsv_hq = $data[7];
$thecsv_state = $data[8];
$thecsv_zone = $data[9];
$thecsv_acedns = $data[10];

$thecsv_te_emp_code = $thecsv_te_emp_code ? addslashes(trim($thecsv_te_emp_code)) : "";
$thecsv_te_name = $thecsv_te_name ? addslashes(trim($thecsv_te_name)) : "";
$thecsv_branch_names = $thecsv_branch_names ? addslashes(trim($thecsv_branch_names)) : "";
$thecsv_reporting_to = $thecsv_reporting_to ? addslashes(trim($thecsv_reporting_to)) : "";
$thecsv_mobile = $thecsv_mobile ? addslashes(trim($thecsv_mobile)) : "";
$thecsv_email = $thecsv_email ? addslashes(trim($thecsv_email)) : "";
$thecsv_designation = $thecsv_designation ? addslashes(trim($thecsv_designation)) : "";
$thecsv_hq = $thecsv_hq ? addslashes(trim($thecsv_hq)) : "";
$thecsv_state = $thecsv_state ? addslashes(trim($thecsv_state)) : "";
$thecsv_zone = $thecsv_zone ? addslashes(trim($thecsv_zone)) : "";
$thecsv_acedns = $thecsv_acedns ? addslashes(trim($thecsv_acedns)) : "";


if($thecsv_te_emp_code!="" && $thecsv_te_name!="" && $thecsv_mobile!=""){
	$the_branch_ids = "";
	$the_branch_ids = get_branch_codes_by_names($conn,$thecsv_branch_names);
	
	$sql2ckin = "select `te_code`,`te_mobile_no` from $te_master where `te_code`='$thecsv_te_emp_code'";
	$res2ckin = mysqli_query($conn,$sql2ckin);
	$totres2ckin = mysqli_num_rows($res2ckin);
	if($totres2ckin>0){
		$check_te_mobile_exists = check_mobile_number_exists_for_update($conn,$thecsv_te_emp_code,$thecsv_mobile);
		if($check_te_mobile_exists=="YES"){
			$sql5produp = "update $te_master set `te_name`='$thecsv_te_name',`te_email`='$thecsv_email',`branch_code`='$the_branch_ids',`reporting_to`='$thecsv_reporting_to',`designation`='$thecsv_designation',`hq`='$thecsv_hq',`state`='$thecsv_state',`zone`='$thecsv_zone',`acedns`='$thecsv_acedns' where `te_code`='$thecsv_te_emp_code'";
			$mobile_number_exist_count++;
			
		}else{
			$sql5produp = "update $te_master set `te_name`='$thecsv_te_name',`te_mobile_no`='$thecsv_mobile',`te_email`='$thecsv_email',`branch_code`='$the_branch_ids',`reporting_to`='$thecsv_reporting_to',`designation`='$thecsv_designation',`hq`='$thecsv_hq',`state`='$thecsv_state',`zone`='$thecsv_zone',`acedns`='$thecsv_acedns' where `te_code`='$thecsv_te_emp_code'";
		}
	
	$res5produp = mysqli_query($conn,$sql5produp);
	$update_count++;
	}else{
		$check_te_mobile_exists = check_mobile_number_exists_for_save($conn,$thecsv_mobile);
if($check_te_mobile_exists=="YES"){
$sql5prodin = "insert into $te_master (`te_name`,`te_code`,`te_email`,`branch_code`,`reporting_to`,`designation`,`hq`,`state`,`zone`,`acedns`) values ('$thecsv_te_name','$thecsv_te_emp_code','$thecsv_email','$the_branch_ids','$thecsv_reporting_to','$thecsv_designation','$thecsv_hq','$thecsv_state','$thecsv_zone','$thecsv_acedns')";
$mobile_number_exist_count++;
}else{
$sql5prodin = "insert into $te_master (`te_name`,`te_code`,`te_mobile_no`,`te_email`,`branch_code`,`reporting_to`,`designation`,`hq`,`state`,`zone`,`acedns`) values ('$thecsv_te_name','$thecsv_te_emp_code','$thecsv_mobile','$thecsv_email','$the_branch_ids','$thecsv_reporting_to','$thecsv_designation','$thecsv_hq','$thecsv_state','$thecsv_zone','$thecsv_acedns')";
}

	$res5prodin = mysqli_query($conn,$sql5prodin);
	if($res5prodin){			
	$insert_count++;
	}
	}	

}
	}
	fclose($handle);
	}

$submsg = "TE details successfully inserted: ".$insert_count." and updated: ".$update_count.".";
if($mobile_number_exist_count>0){
$submsg .= " And ".$mobile_number_exist_count." mobile number not saved due to duplicate.";	
}
		}	
	
	}


$new_qry_string_filtered = "";

$srch_eng_dtls = isset($_GET["srch_eng_dtls"]) ? addslashes(trim($_GET["srch_eng_dtls"])) : "";

//$srch_eng_dtls = $_GET["srch_eng_dtls"] ? addslashes(trim($_GET["srch_eng_dtls"])) : "";
$whr_str = "";
$search_array = array("srch_eng_dtls"=>$srch_eng_dtls,"data_show_type"=>$data_show_type);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_eng_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand (`te_name` like '%$search_array_val%' or `te_code` like '%$search_array_val%' or `te_mobile_no` like '%$search_array_val%' ) ";
			$new_qry_string_filtered .= "&srch_eng_dtls=".$search_array_val;
		}
	}else if($search_array_key=="data_show_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			/*if($search_array_val=='NE'){
$whr_str .= "$aand (`zone` like '%A%' or `zone` like '%B%' or `zone` like '%C%' ) ";
			}else if($search_array_val=='OSNE'){
$whr_str .= "$aand (`zone` like '%D%' or `zone` like '%E%' ) ";
			}*/
			if($search_array_val!='ALL'){
				//$whr_str .= "$aand `zone` like '%".$search_array_val."%' ";
				$search_array_val_parts=explode(",",$search_array_val);
				$condition_one=" $aand (";
				$condition_two='';
				foreach($search_array_val_parts as $search_array_val_parts_val)
				{
					$condition_two.=" FIND_IN_SET( '".$search_array_val_parts_val."',zone) OR";
				}
				$condition_two=substr($condition_two,0,-2);
				$condition_one.=$condition_two.")";
				$whr_str .=$condition_one;
			}
		}
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "add_edit_te_update.php";
$page_name = "te_master_update.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";

$page = isset($_GET['paged']) ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select `te_id` from $te_master $new_whr_str";
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
.branchCodeEachField{
	display:block;
	width:200px;
	word-wrap: break-word;
	font-size:11px;
	cursor:pointer;
}
/* .adminActivityField3{
	display:block;
	width:100px;
	height:20px;
} */
.adminActivityField2{
	display:block;
	width:150px;
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
                          <h2>TE Master (<?php echo $total_pgres;?>)&nbsp;&nbsp;<a href="add_edit_te.php" class="btn bg-red waves-effe">Add&nbsp;New&nbsp;TE</a>
                          &nbsp;&nbsp;<a href="export_te_master.php" class="btn bg-red waves-effe">Export&nbsp;TE&nbsp;Master</a>
                          &nbsp;&nbsp;&nbsp;<form action="" method="POST" enctype="multipart/form-data" name="the_upload_frm" class="the_upload_frm" id="the_upload_frm" style="display:inline-block;">
<input type="file" id="upload_csv_file" name="upload_csv_file" style="display:inline-block;width: 100px; font-size:16px;">
<input type="submit" class="btn bg-red waves-effect bulk_upload_btn" name="bulk_upload_btn" value="Upload TE CSV" style="display:inline-block;" />
</form>&nbsp;&nbsp;&nbsp;
                          <span style="text-align: left;font-size: 12px;width: 246px;display: inline-block;" id="success_msg" ><?php echo $submsg; ?></span>
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_eng_dtls" value="<?php echo $srch_eng_dtls;?>" placeholder="Search TE Details">
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
                                            <th>TE&nbsp;Code&nbsp;/&nbsp;Employee&nbsp;Code</th>
                                            <th>TE&nbsp;Name&nbsp;/&nbsp;Employee&nbsp;Name</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Reporting&nbsp;To</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>Designation</th>
                                            <th>HQ</th>
                                            <th>State</th>
                                            <th>Zone</th>
                                            <th>Acedns</th>
											<th>Status&nbsp;Update&nbsp;Datetime</th>
                                            <th>Device&nbsp;type</th>
                                            <th>App&nbsp;Version</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>TE&nbsp;Code&nbsp;/&nbsp;Employee&nbsp;Code</th>
                                            <th>TE&nbsp;Name&nbsp;/&nbsp;Employee&nbsp;Name</th>
                                            <th>Branch&nbsp;Name</th>
                                            <th>Reporting&nbsp;To</th>
                                            <th>Mobile</th>
                                            <th>Email</th>
                                            <th>Designation</th>
                                            <th>HQ</th>
                                            <th>State</th>
                                            <th>Zone</th>
                                            <th>Acedns</th>
											<th>Status&nbsp;Update&nbsp;Datetime</th>
                                            <th>Device&nbsp;type</th>
                                            <th>App&nbsp;Version</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select * from $te_master $new_whr_str order by `te_name` asc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$the_te_id = $row1["te_id"];
		$the_te_name = $row1["te_name"];
		$the_te_mobile_no = $row1["te_mobile_no"];
		$the_te_code = $row1["te_code"];
		$the_te_email = $row1["te_email"];
		$the_branch_name_selected = "";
	$the_branch_code_selected = $row1["branch_code"] ? trim($row1["branch_code"]) : "";
	if($the_branch_code_selected!=""){
	$the_branch_name_selected = get_branch_names_by_ids($conn,$the_branch_code_selected);
	}else{
	$the_branch_name_selected = "";	
	}
	
	$the_te_reporting_to = $row1["reporting_to"];
	$the_te_designation = $row1["designation"];
	$the_te_hq = $row1["hq"];
	$the_te_state = $row1["state"];
	$the_te_zone = $row1["zone"];
	$the_te_acedns = $row1["acedns"] ? $row1["acedns"] : "N";
	$the_device_type = $row1["device_type"] ? trim($row1["device_type"]) : "";
	$the_app_version = $row1["app_version"] ? trim($row1["app_version"]) : "";
	$status_update_datetime = $row1["status_update_datetime"] ? trim($row1["status_update_datetime"]) : "";
	
		
?>
<tr>
<td><?php echo $the_te_code;?></td>
<td><?php echo $the_te_name;?></td>
<td>
<div style="width:200px !important;">
<span class="branchCodeEachField">
<?php echo $the_branch_name_selected;?>
</span>
</div>
</td>
<td><?php echo $the_te_reporting_to;?></td>
<td><?php echo $the_te_mobile_no;?></td>
<td><?php echo $the_te_email;?></td>
<td><?php echo $the_te_designation;?></td>
<td><?php echo $the_te_hq;?></td>
<td><?php echo $the_te_state;?></td>
<td><?php echo $the_te_zone;?></td>
<td><?php echo $the_te_acedns;?></td>
<td><?php echo $status_update_datetime;?></td>	
<td><?php echo $the_device_type;?></td>
<td><?php echo $the_app_version;?></td>
<td>
<div>

<span class="adminActivityField2">
<a href="<?php echo $add_page_name;?>?edt_te_id=<?php echo $the_te_id;?>" class="btn bg-red waves-effe" style="margin-right:10px;">Edit</a>
	
</span>
</div>
</td>
</tr>

<?php
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
	var imgs = '<img src="images/ajax-loader.gif"/>';
	var done_img = '<img src="images/success_tick.png"/>';


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

jQuery("form#the_upload_frm").submit(function(){
	var upload_csv_file = jQuery("#upload_csv_file").val();
	if(upload_csv_file=="" || upload_csv_file==null){
		alert("Please choose a csv file.");
		return false;
	}else{
		return true;
	}
});

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
		var srch_eng_dtls = jQuery("#srch_eng_dtls").val();
		var qstring ="";
		var amp = "";
		if(srch_eng_dtls!=""){
		if(srch_eng_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_eng_dtls="+encodeURIComponent(srch_eng_dtls);
			}else{
				qstring = qstring+"srch_eng_dtls="+encodeURIComponent(srch_eng_dtls);
			}
		}
		
		
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "<?php echo $page_name;?>"+qstring;
		}else{
			alert("Please enter TE details.");
		}
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