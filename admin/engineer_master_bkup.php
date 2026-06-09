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
function check_mobile_number_exists_for_update($conn,$eid,$mob_no){
	$ck_sts = "NO";
	$engineer_master = "engineer_master";
	$sqlck1 = "select `e_mobile` from $engineer_master where `e_mobile`='$mob_no' and `eid`!='$eid'";
	$resck1 = mysqli_query($conn,$sqlck1);
	$totresck1 = mysqli_num_rows($resck1);
	if($totresck1>0){
		$ck_sts = "YES";
	}
return $ck_sts;
}

function check_mobile_number_exists_for_save($conn,$mob_no){
	$ck_sts = "NO";
	$engineer_master = "engineer_master";
	$sqlck1 = "select `e_mobile` from $engineer_master where `e_mobile`='$mob_no'";
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
function get_branch_ids_by_names($conn,$bnames){
$bids = "";
$bidsarr = array();
$selected_pnms_arr = array();
$branch_master = "branch_master";
$pnms = $pnms ? trim($pnms) : "" ;
	if($pnms!=""){
		$selected_pnms_arr = explode(",",$pnms);
		$selected_pnms_str_for_qry = implode("','",$selected_pnms_arr);
	$sql1 = "select `branch_code` from $branch_master where `branch_name` in ('".$selected_pnms_str_for_qry."') ";
	$res1 = mysqli_query($conn,$sql1);
	$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
	$the_prod_code_sho = trim($row1["branch_code"]);
	$bidsarr[] = $the_prod_code_sho;
	}
	$bids = implode(",",$bidsarr);
	}	 
	}
return $bids;
}
$add_page_name = "edit_engineer_master.php";
$page_name = "engineer_master.php";
$activity_status_arr = array("ACTIVE","SEMI_ACTIVE","INACTIVE");
$en_status_arr = array("ACTIVE","INACTIVE");
$img_dir = "../en_profile_pic/";
$default_image_name = "profile.png";
$image_url_prefix = $server_url."en_profile_pic/";


$insert_count = 0;
$update_count = 0;
$mobile_number_exist_count = 0;
if(@$_POST["bulk_upload_btn"]=="Import"){
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
		$data1 = fgetcsv($handle, 5000, ",");	
		$countscsvrow = 0;
		$countscsvrowupd = 0;
	while (($data = fgetcsv($handle, 5000, ",")) !== FALSE){
		
$thecsv_e_name = $data[0];
$thecsv_e_mobile = $data[1];
$thecsv_e_email = $data[2];
$thecsv_e_point = $data[3];
$thecsv_e_branch = $data[4];
$thecsv_e_dob = $data[5];
$thecsv_e_dom = $data[6];
$thecsv_e_te_code = $data[7];
$thecsv_e_te_name = $data[8];
$thecsv_e_address = $data[9];
$thecsv_e_pin = $data[10];
$thecsv_e_state = $data[11];
$thecsv_e_city = $data[12];
$thecsv_e_status = $data[13];
$thecsv_e_status_by_te = $data[14];
$thecsv_e_reg_datetime = $data[15];

$thecsv_e_name = $thecsv_e_name ? addslashes(trim($thecsv_e_name)) : "";
$thecsv_e_mobile = $thecsv_e_mobile ? addslashes(trim($thecsv_e_mobile)) : "";
$thecsv_e_email = $thecsv_e_email ? addslashes(trim($thecsv_e_email)) : "";

$thecsv_e_point = $thecsv_e_point ? addslashes(trim($thecsv_e_point)) : "";
$thecsv_e_branch = $thecsv_e_branch ? addslashes(trim($thecsv_e_branch)) : "";

$thecsv_e_dob = $thecsv_e_dob ? addslashes(trim($thecsv_e_dob)) : "";
$thecsv_e_dom = $thecsv_e_dom ? addslashes(trim($thecsv_e_dom)) : "";
$thecsv_e_te_code = $thecsv_e_te_code ? addslashes(trim($thecsv_e_te_code)) : "";
$thecsv_e_te_name = $thecsv_e_te_name ? addslashes(trim($thecsv_e_te_name)) : "";

$thecsv_e_address = $thecsv_e_address ? addslashes(trim($thecsv_e_address)) : "";
$thecsv_e_pin = $thecsv_e_pin ? addslashes(trim($thecsv_e_pin)) : "";
$thecsv_e_state = $thecsv_e_state ? addslashes(trim($thecsv_e_state)) : "";
$thecsv_e_city = $thecsv_e_city ? addslashes(trim($thecsv_e_city)) : "";
$thecsv_e_status = $thecsv_e_status ? addslashes(trim($thecsv_e_status)) : "";
$thecsv_e_status_by_te = $thecsv_e_status_by_te ? addslashes(trim($thecsv_e_status_by_te)) : "";
$thecsv_e_reg_datetime = $thecsv_e_reg_datetime ? addslashes(trim($thecsv_e_reg_datetime)) : "";
if($thecsv_e_dob!=""){
$thecsv_e_dob = date("Y-m-d",strtotime($thecsv_e_dob));	
}
if($thecsv_e_dom!=""){
$thecsv_e_dom = date("Y-m-d",strtotime($thecsv_e_dom));	
}
if($thecsv_e_reg_datetime!=""){
$thecsv_e_reg_datetime = date("Y-m-d H:i:s",strtotime($thecsv_e_reg_datetime));	
}
$last_updated_datetime = date("Y-m-d H:i:s");	
if($thecsv_e_name!="" && $thecsv_e_mobile!="" && $thecsv_e_te_code!=""){

$thecsv_e_branch_ids = "";
if(trim($thecsv_e_branch)!=""){
$thecsv_e_branch_ids = get_branch_ids_by_names($conn,$thecsv_e_branch);
if(trim($thecsv_e_branch_ids)==""){
$thecsv_e_branch_ids = get_te_branchcode_by_tecode($conn,$thecsv_e_te_code);	
}
}else{
$thecsv_e_branch_ids = get_te_branchcode_by_tecode($conn,$thecsv_e_te_code);	
}

	$sql2ckin = "select `eid`,`te_code`,`e_mobile` from $engineer_master where `e_mobile`='$thecsv_e_mobile'";
	$res2ckin = mysqli_query($conn,$sql2ckin);
	$totres2ckin = mysqli_num_rows($res2ckin);
	if($totres2ckin>0){
		$row2ckin = mysqli_fetch_assoc($res2ckin);
		$the_eid_upd = $row2ckin["eid"];
		$thecsv_e_status_qry = "";
		$thecsv_e_status_by_te_qry = "";
		$thecsv_e_branch_ids_qry = "";
		$thecsv_e_reg_datetime_qry = "";
		if($thecsv_e_status!=""){
		$thecsv_e_status_qry = ",`status`='$thecsv_e_status'";	
		}
		if($thecsv_e_status_by_te!=""){
		$thecsv_e_status_by_te_qry = ",`status_by_te`='$thecsv_e_status_by_te'";	
		}
		if($thecsv_e_branch_ids!=""){
		$thecsv_e_branch_ids_qry = ",`branch_code`='$thecsv_e_branch_ids'";	
		}
		
		if($thecsv_e_reg_datetime!=""){
		$thecsv_e_reg_datetime_qry = ",`reg_date`='$thecsv_e_reg_datetime'";	
		}
		
		$check_te_mobile_exists = check_mobile_number_exists_for_update($conn,$the_eid_upd,$thecsv_e_mobile);
		if($check_te_mobile_exists=="YES"){
			$sql5produp = "update $engineer_master set `e_name`='$thecsv_e_name',`e_email`='$thecsv_e_email',`e_points`='$thecsv_e_point',`e_dob`='$thecsv_e_dob',`e_dom`='$thecsv_e_dom',`te_code`='$thecsv_e_te_code',`e_address`='$thecsv_e_address',`e_pin`='$thecsv_e_pin',`e_state`='$thecsv_e_state',`e_city_town`='$thecsv_e_city',`last_updated_datetime`='$last_updated_datetime'".$thecsv_e_status_qry.$thecsv_e_status_by_te_qry.$thecsv_e_branch_ids_qry.$thecsv_e_reg_datetime_qry." where `eid`='$the_eid_upd'";
			$mobile_number_exist_count++;
		}else{
			$sql5produp = "update $engineer_master set `e_name`='$thecsv_e_name',`e_mobile`='$thecsv_e_mobile',`e_email`='$thecsv_e_email',`e_points`='$thecsv_e_point',`e_dob`='$thecsv_e_dob',`e_dom`='$thecsv_e_dom',`te_code`='$thecsv_e_te_code',`e_address`='$thecsv_e_address',`e_pin`='$thecsv_e_pin',`e_state`='$thecsv_e_state',`e_city_town`='$thecsv_e_city',`last_updated_datetime`='$last_updated_datetime'".$thecsv_e_status_qry.$thecsv_e_status_by_te_qry.$thecsv_e_branch_ids_qry.$thecsv_e_reg_datetime_qry." where `eid`='$the_eid_upd'";
		}
	
	$res5produp = mysqli_query($conn,$sql5produp);
	$update_count++;
	}else{
		if($thecsv_e_status==""){
		$thecsv_e_status = "ACTIVE";	
		}
		if($thecsv_e_status_by_te==""){
		$thecsv_e_status_by_te = "PENDING";	
		}

if($thecsv_e_reg_datetime==""){
$thecsv_e_reg_datetime = date("Y-m-d H:i:s");	
}		
		
		$check_te_mobile_exists = check_mobile_number_exists_for_save($conn,$thecsv_e_mobile);
if($check_te_mobile_exists=="YES"){
$sql5prodin = "insert into $engineer_master (`e_name`,`e_email`,`e_points`,`branch_code`,`e_dob`,`e_dom`,`te_code`,`e_address`,`e_pin`,`e_state`,`e_city_town`,`status`,`status_by_te`,`reg_date`,`last_updated_datetime`) values ('$thecsv_e_name','$thecsv_e_email','$thecsv_e_point','$thecsv_e_branch_ids','$thecsv_e_dob','$thecsv_e_dom','$thecsv_e_te_code','$thecsv_e_address','$thecsv_e_pin','$thecsv_e_state','$thecsv_e_city','$thecsv_e_status','$thecsv_e_status_by_te','$thecsv_e_reg_datetime','$last_updated_datetime')";
$mobile_number_exist_count++;
}else{
$sql5prodin = "insert into $engineer_master (`e_name`,`e_mobile`,`e_email`,`e_points`,`branch_code`,`e_dob`,`e_dom`,`te_code`,`e_address`,`e_pin`,`e_state`,`e_city_town`,`status`,`status_by_te`,`reg_date`,`last_updated_datetime`) values ('$thecsv_e_name','$thecsv_e_mobile','$thecsv_e_email','$thecsv_e_point','$thecsv_e_branch_ids','$thecsv_e_dob','$thecsv_e_dom','$thecsv_e_te_code','$thecsv_e_address','$thecsv_e_pin','$thecsv_e_state','$thecsv_e_city','$thecsv_e_status','$thecsv_e_status_by_te','$thecsv_e_reg_datetime','$last_updated_datetime')";
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

$submsg = "Engineer details successfully inserted: ".$insert_count." and updated: ".$update_count.".";
if($mobile_number_exist_count>0){
$submsg .= " And ".$mobile_number_exist_count." mobile number not saved due to duplicate.";	
}
		}	
	
	}



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
	width:20px;
	height:20px;
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
	width:188px;
	word-wrap: break-word;
	font-size: 13px;
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
                          <h2>Engineer Master (<?php echo $total_pgres;?>)&nbsp;&nbsp;<span class="shomsg"><?php if($msg_txt!=""){ echo $msg_txt;}?></span>
                          <a href="export_engineer_master.php" class="btn bg-red waves-effe">Export</a>
                          &nbsp;&nbsp;&nbsp;<form action="" method="POST" enctype="multipart/form-data" name="the_upload_frm" class="the_upload_frm" id="the_upload_frm" style="display:inline-block;">
<input type="file" id="upload_csv_file" name="upload_csv_file" style="display:inline-block;width: 100px; font-size:16px;">
<input type="submit" class="btn bg-red waves-effect bulk_upload_btn" name="bulk_upload_btn" value="Import" style="display:inline-block;" />
</form>&nbsp;&nbsp;&nbsp;
                          <span style="text-align: left;font-size: 12px;display: inline-block;" id="success_msg" ><?php echo $submsg; ?></span>
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
                                            <th>Image</th>
                                            <th>Details</th>
                                            <th>Birthday</th>
                                            <th>Anniversary</th>
                                            <th>Points</th>
                                            <th>Branch</th>
                                            <th>Linked&nbsp;TE&nbsp;Code</th>
                                            <th>Linked&nbsp;TE&nbsp;Name</th>
                                            <th>Address</th>
                                            <th>Pin</th>
                                            <th>State</th>
                                            <th>City</th>
                                            <th>Reg_Date</th>
                                            <th>App&nbsp;Access&nbsp;Status</th>
                                            <th>Activity&nbsp;Status</th>
                                            <th>Status&nbsp;By&nbsp;TE</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Image</th>
                                            <th>Details</th>
                                            <th>Birthday</th>
                                            <th>Anniversary</th>
                                            <th>Points</th>
                                            <th>Branch</th>
                                            <th>Linked&nbsp;TE&nbsp;Code</th>
                                            <th>Linked&nbsp;TE&nbsp;Name</th>
                                            <th>Address</th>
                                            <th>Pin</th>
                                            <th>State</th>
                                            <th>City</th>
                                            <th>Reg_Date</th>
                                            <th>App&nbsp;Access&nbsp;Status</th>
                                            <th>Activity&nbsp;Status</th>
                                            <th>Status&nbsp;By&nbsp;TE</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select $engineer_master.*,$te_master.`te_name`,`latest_recommended_site_master`.`r_submission_date` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` $new_whr_str order by $engineer_master.`reg_date` desc limit $start_from,$limit";
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
		$the_reg_date = $row1["reg_date"] ? trim($row1["reg_date"]) : "";
		
		$the_e_address = $row1["e_address"];
		$the_e_pin = $row1["e_pin"];
		$the_e_state = $row1["e_state"];
		$the_e_city_town = $row1["e_city_town"];
		$the_e_points = $row1["e_points"];
		$the_e_status = $row1["status"] ? trim($row1["status"]) : "";
		$the_status_by_te = $row1["status_by_te"] ? trim($row1["status_by_te"]) : "";
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
$e_sts_text = "Site recommendation within last 3 months";
}else if($the_date_time_stamp<$date_before_three_month_stamp && $the_date_time_stamp>=$date_before_six_month_stamp){
	$e_status = "SEMI_ACTIVE";
$e_status_show = "Semi Active";
$inac_bg_color = "#f79d16";
$e_sts_text = "Site recommendation between last 3 months to 6 months";
}else{
	$e_status = "INACTIVE";
$e_status_show = "Inactive";
$inac_bg_color = "#f0311f";
$e_sts_text = "Site recommendation not done within last 6 months";
}
}else{
$e_status = "INACTIVE";
$e_status_show = "Inactive";
$inac_bg_color = "#f0311f";
$e_sts_text = "Site recommendation not done within last 6 months";	
}

?>
<tr>
<td>
<img src="<?php echo $the_e_profile_image_url;?>" class="prfl_img" />
</td>
<td>
<div>
<span class="engineerEachField"><?php echo $the_e_name;?></span>
<?php if($the_e_mobile!=""){?>
<span class="engineerEachField"><b>Mobile:</b><?php echo $the_e_mobile;?></span>
<?php }?>
<?php if($the_e_email!=""){?>
<span class="engineerEachField"><b>Email:</b></span>
<span class="engineerEachField"><?php echo $the_e_email;?></span>
<?php }?>
</div>
</td>
<td><?php echo $the_e_dob;?></td>
<td><?php echo $the_e_dom;?></td>
<td><?php echo $the_e_points;?></td>
<td><?php echo $the_branch_code_selected;?></td>
<td><?php echo $the_te_code;?></td>
<td><?php echo $the_te_name;?></td>
<td><?php echo $the_e_address;?></td>
<td><?php echo $the_e_pin;?></td>
<td><?php echo $the_e_state;?></td>
<td><?php echo $the_e_city_town;?></td>
<td><?php echo $the_reg_date;?></td>
<td>
<div>
<span class="adminActivityField">
<select class="form-control sl_eng_sts" id="sl_eng_sts_<?php echo $the_eid;?>" eng_id="<?php echo $the_eid;?>">
<?php
if(count($en_status_arr)>0){
	foreach($en_status_arr as $en_status_arr_val){ ?>
<option value="<?php echo $en_status_arr_val;?>" <?php if($en_status_arr_val==$the_e_status){?> selected="selected" <?php } ?>><?php echo $en_status_arr_val;?></option>
	<?php }
}
?>
</select>
</span>
<span class="adminActivityField3" id="sl_eng_sts_ldr_<?php echo $the_eid;?>"></span>
</div>
</td>
<td><div>
<button type="button" class="actvt_clr_cls" style="background-color:<?php echo $inac_bg_color;?>;" data-toggle="tooltip" data-placement="top" title="<?php echo $e_sts_text;?>">
</button>

</div></td>
<td><?php echo $the_status_by_te;?></td>
<td>
<div>
<span class="adminActivityField2">
<a href="<?php echo $add_page_name;?>?edt_e_id=<?php echo $the_eid;?>" class="btn bg-red waves-effe" style="margin-right:10px;">Edit</a>
<a href="javascript:void(0);" class="btn bg-red waves-effe delete_engineer" dlt_engid="<?php echo $the_eid;?>">Delete</a>
</span>
</div>
</td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="17">No data found.</td>
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
jQuery('[data-toggle="tooltip"]').tooltip()
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
	jQuery("form#the_upload_frm").submit(function(){
	var upload_csv_file = jQuery("#upload_csv_file").val();
	if(upload_csv_file=="" || upload_csv_file==null){
		alert("Please choose a csv file.");
		return false;
	}else{
		return true;
	}
});
});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>