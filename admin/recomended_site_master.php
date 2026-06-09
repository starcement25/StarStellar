<?php
include "web_check.php";
include "star_connection.php";
$recommended_site_master = "recommended_site_master";
$te_master = "te_master";
$engineer_master = "engineer_master";
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

$actual_bag_cons_approve_limit = get_value_by_setting_key($conn,"bags_verification_limit_for_te");
if($actual_bag_cons_approve_limit==""){
$actual_bag_cons_approve_limit = 0;
}

$order_status_arr = array("PENDING","APPROVED","REJECTED");
$supported_mime_type = array("image/jpeg","image/png","image/jpg");
$img_dir = "../recomend_site_pic/";
$approve_img_dir = "../approved_recomend_site_pic/";
$default_image_name = "profile.png";
$image_url_prefix = $server_url."recomend_site_pic/";
$approve_image_url_prefix = $server_url."approved_recomend_site_pic/";
$new_qry_string_filtered = "";
$msg_txt = "";
$add_page_name = "recomended_site_master.php";
$page_name = "recomended_site_master.php";
if(isset($_GET["msg_txt"]) && @$_GET["msg_txt"]!=""){
	$msg_txt = $_GET["msg_txt"];
}
/*if($data_show_type=='NE'){
$sqltefltr = "select `te_code`,`te_name` from $te_master where (`zone` like '%A%' or `zone` like '%B%' or `zone` like '%C%' ) order by `te_name` asc";
}else if($data_show_type=='OSNE'){
$sqltefltr = "select `te_code`,`te_name` from $te_master where (`zone` like '%D%' or `zone` like '%E%' ) order by `te_name` asc";
}else{
$sqltefltr = "select `te_code`,`te_name` from $te_master order by `te_name` asc";
}*/

if($data_show_type=="ALL"){
$sqltefltr = "select `te_code`,`te_name` from $te_master order by `te_name` asc";
}else{
$sqltefltr = "select `te_code`,`te_name` from $te_master where `zone` like '%".$data_show_type."%' order by `te_name` asc";

}

$restefltr = mysqli_query($conn,$sqltefltr);
$tottefltr = mysqli_num_rows($restefltr);

$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$new_qry_string_filtered = "";
$trn_te_code = $_GET["trn_te_code"] ? addslashes(trim($_GET["trn_te_code"])) : "";
$sl_en_id = $_GET["sl_en_id"] ? addslashes(trim($_GET["sl_en_id"])) : "";
$srch_eng_te_site_dtls = $_GET["srch_eng_te_site_dtls"] ? addslashes(trim($_GET["srch_eng_te_site_dtls"])) : "";
$sl_ord_sts = $_GET["sl_ord_sts"] ? addslashes(trim($_GET["sl_ord_sts"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$whr_str = "";
$export_filtered_str = "";
$search_array = array("trn_te_code"=>$trn_te_code,"sl_en_id"=>$sl_en_id,"srch_eng_te_site_dtls"=>$srch_eng_te_site_dtls,"sl_ord_sts"=>$sl_ord_sts,"data_show_type"=>$data_show_type,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="sl_ord_sts"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			
			$whr_str .= "$aand $recommended_site_master.`r_status`='$search_array_val' ";
			
			$new_qry_string_filtered .= "&sl_ord_sts=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_ord_sts=".$search_array_val;
			}else{
				$export_filtered_str .= "&sl_ord_sts=".$search_array_val;
			}	
			
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
	}else if($search_array_key=="trn_te_code"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $recommended_site_master.`r_te_code`='$search_array_val' ";	
			$new_qry_string_filtered .= "&trn_te_code=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&trn_te_code=".$search_array_val;
			}else{
				$export_filtered_str .= "&trn_te_code=".$search_array_val;
			}		
		}		
	}else if($search_array_key=="sl_en_id"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $recommended_site_master.`r_engineer_id`='$search_array_val' ";	
			$new_qry_string_filtered .= "&sl_en_id=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_en_id=".$search_array_val;
			}else{
				$export_filtered_str .= "&sl_en_id=".$search_array_val;
			}	
		}		
	}else if($search_array_key=="srch_eng_te_site_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			
			$whr_str .= "$aand ($engineer_master.`e_name` like '%$search_array_val%' or $engineer_master.`te_code` like '%$search_array_val%' or $engineer_master.`e_mobile` like '%$search_array_val%' or $te_master.`te_name` like '%$search_array_val%' or $te_master.`te_mobile_no` like '%$search_array_val%' or $recommended_site_master.`r_site_name` like '%$search_array_val%' or $recommended_site_master.`r_contact_person_name` like '%$search_array_val%' or $recommended_site_master.`r_mobile_no` like '%$search_array_val%' or $recommended_site_master.`r_address` like '%$search_array_val%' or $recommended_site_master.`r_site_potential_in_mt` like '%$search_array_val%' or $recommended_site_master.`r_contact_person_category_name` like '%$search_array_val%' )";
			
			$new_qry_string_filtered .= "&srch_eng_te_site_dtls=".$search_array_val;
			if($export_filtered_str!=""){
				$export_filtered_str .= "&srch_eng_te_site_dtls=".$search_array_val;
			}else{
				$export_filtered_str .= "&srch_eng_te_site_dtls=".$search_array_val;
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
			   $whr_str .= "$aand $recommended_site_master.`r_submission_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $recommended_site_master.`r_submission_date` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $recommended_site_master.`r_submission_date` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $recommended_site_master.`r_submission_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $recommended_site_master.`r_submission_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}


if($whr_str!=""){
	$new_whr_str = " where ".$whr_str;
}else{
	$new_whr_str ="";
}

//?sl_branch_code=B0056&sl_ord_sts=Pending&sl_day_wise=Date_Range&from_dt=2018-07-01&to_dt=2018-07-31
if($trn_te_code!=""){
	$sql5dcode = "select `eid`,`e_name` from $engineer_master where `te_code`='$trn_te_code' order by `e_name` asc ";
	$res5dcode = mysqli_query($conn,$sql5dcode);
	$tot_res5dcode = mysqli_num_rows($res5dcode);
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

$pgsql = "select $recommended_site_master.`r_site_id` from $recommended_site_master left join $engineer_master on $recommended_site_master.`r_engineer_id`=$engineer_master.`eid` left join $te_master on $recommended_site_master.`r_te_code`=$te_master.`te_code` $new_whr_str ";
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
.selected_prfl_img{
width:80px;
height:50px;
margin-bottom:10px;
}
.blbl{
	width:100%;
	text-align:left;
}
.engineerEachField{
	display:block;
	width:150px;
	word-wrap: break-word;
}
.teEachField{
	display:block;
	width:150px;
	word-wrap: break-word;
}
.siteEachField{
	display:block;
	width:200px;
	word-wrap: break-word;
}
.contactPersonEachField{
	display:block;
	width:150px;
	word-wrap: break-word;
}
.eachField{
	display:block;
	width:200px;
	word-wrap: break-word;
}
.recomSiteEachField{
	display:block;
	width:200px;
	word-wrap: break-word;
}

.pending_status_class,.pending_status_class a{
	color:#D42A1D;
	text-decoration:none;
}
.approved_status_class,.approved_status_class a{
	color:#1C6E00;
	text-decoration:none;
}
.rejected_status_class,.rejected_status_class a{
	color:#FF7200;
	text-decoration:none;
}
.sho_point{
	font-weight:normal;
}
.rtoa_ldr{
	margin-left:10px;
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
								$sql1 = "select $recommended_site_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`branch_code` as `eng_branch_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $recommended_site_master left join $engineer_master on $recommended_site_master.`r_engineer_id`=$engineer_master.`eid` left join $te_master on $recommended_site_master.`r_te_code`=$te_master.`te_code` $new_whr_str order by $recommended_site_master.`r_site_id` desc";
							}
							else{
								$where_clause = "";
								$zones = explode(",", $data_show_type); // Assuming $data_show_type is a comma-separated string of zones
								
								foreach ($zones as $zone) {
									$zone = trim($zone);
									if ($where_clause != "") {
										$where_clause .= " OR ";
									}
									$where_clause .= "te_master.`zone` LIKE '%$zone%'";
								}
								$sql1 = "select $recommended_site_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`branch_code` as `eng_branch_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $recommended_site_master left join $engineer_master on $recommended_site_master.`r_engineer_id`=$engineer_master.`eid` left join $te_master on $recommended_site_master.`r_te_code`=$te_master.`te_code` where $where_clause order by $recommended_site_master.`r_site_id` desc";
							}

							$res1 = mysqli_query($conn,$sql1);
							$totres1 = mysqli_num_rows($res1); ?>
<h2>Recommended Site Master (<?php if($total_pgres){ echo $total_pgres; }else{ echo $totres1; } ?>)&nbsp;&nbsp;&nbsp;<a href="export_recomended_site_master.php?paged=<?php echo $page.$export_filtered_str;?>" class="btn bg-red waves-effe">Export&nbsp;Recommended&nbsp;Site&nbsp;Master</a> &nbsp;&nbsp;<span class="rpt_loader"></span> <span class="shomsg"><?php if($msg_txt!=""){ echo $msg_txt;}?></span>

                          </h2>
    <div class="row clearfix">
<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<select name="trn_te_code" id="trn_te_code" class="form-control">
<option value="">Select TE</option>
<?php
if($tottefltr>0){
	while($rowtefltr=mysqli_fetch_assoc($restefltr)){
		$te_code_fltr = $rowtefltr["te_code"];
		$te_name_fltr = $rowtefltr["te_name"];
?>
<option value="<?php echo $te_code_fltr;?>" <?php if($te_code_fltr == $trn_te_code){?> selected="selected" <?php } ?> ><?php echo $te_name_fltr." (".$te_code_fltr.")";?></option>
<?php
	}
}
?>
</select>
</div>
<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
<select name="sl_en_id" id="sl_en_id" class="form-control">
<option value="">Select Engineer</option>
<?php
if($tot_res5dcode>0){
	while($row1dcode=mysqli_fetch_assoc($res5dcode)){
		$eid_sdf = $row1dcode["eid"];
		$e_name_sdf = $row1dcode["e_name"];
?>
<option value="<?php echo $eid_sdf;?>" <?php if($eid_sdf == $sl_en_id){?> selected="selected" <?php } ?> ><?php echo $e_name_sdf;?></option>
<?php
	}
}
?>
</select>
    </div>
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_eng_te_site_dtls" value="<?php echo $srch_eng_te_site_dtls;?>" placeholder="Search By Engineer,TE And Site Details">
</div>
<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">

</div>
    </div>
    <div class="row clearfix">
    
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_ord_sts">
<option value="" >All Status</option>
<?php
if(count($order_status_arr)>0){
	foreach($order_status_arr as $order_status_arr_val){ ?>
<option value="<?php echo $order_status_arr_val;?>" <?php if($order_status_arr_val==$sl_ord_sts){?> selected="selected" <?php } ?>><?php echo $order_status_arr_val;?></option>
	<?php }
}
?>
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
    <div class="wrapper_scrl_div">
    </div>
</div>

 <div class="table-responsive tr_for_scroll">
                                <table class="table table-bordered table-striped table-hover table_for_scroll">
                                    <thead>
                                        <tr>
                                            <th>Recommendation&nbsp;Details</th>
                                            <th>Engineer&nbsp;Details</th>
                                            <th>TE&nbsp;Details</th>
                                            <th>Site&nbsp;Details</th>
                                            <th>Contact&nbsp;Details</th>
                                            <th>Expected&nbsp;Product&nbsp;Details</th>
                                            <th>Actual&nbsp;Product&nbsp;Details</th>
                                            <th>Site&nbsp;Image</th>
                                            <th>TE&nbsp;Interaction&nbsp;Image</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                       <tr>
                                            <th>Recommendation&nbsp;Details</th>
                                            <th>Engineer&nbsp;Details</th>
                                            <th>TE&nbsp;Details</th>
                                            <th>Site&nbsp;Details</th>
                                            <th>Contact&nbsp;Details</th>
                                            <th>Expected&nbsp;Product&nbsp;Details</th>
                                            <th>Actual&nbsp;Product&nbsp;Details</th>
                                            <th>Site&nbsp;Image</th>
                                            <th>TE&nbsp;Interaction&nbsp;Image</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php

if($data_show_type=="ALL"){
	$sql1 = "select $recommended_site_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`branch_code` as `eng_branch_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $recommended_site_master left join $engineer_master on $recommended_site_master.`r_engineer_id`=$engineer_master.`eid` left join $te_master on $recommended_site_master.`r_te_code`=$te_master.`te_code` $new_whr_str order by $recommended_site_master.`r_site_id` desc limit $start_from,$limit";
}
else{
	$where_clause = "";
	$zones = explode(",", $data_show_type); // Assuming $data_show_type is a comma-separated string of zones
	
	foreach ($zones as $zone) {
		$zone = trim($zone);
		if ($where_clause != "") {
			$where_clause .= " OR ";
		}
		$where_clause .= "te_master.`zone` LIKE '%$zone%'";
	}
	$sql1 = "select $recommended_site_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`branch_code` as `eng_branch_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no` from $recommended_site_master left join $engineer_master on $recommended_site_master.`r_engineer_id`=$engineer_master.`eid` left join $te_master on $recommended_site_master.`r_te_code`=$te_master.`te_code` where $where_clause order by $recommended_site_master.`r_site_id` desc limit $start_from,$limit";
}


// SELECT recommended_site_master.*, engineer_master.e_name, engineer_master.e_mobile, engineer_master.branch_code AS eng_branch_code, te_master.te_name, te_master.branch_code, te_master.te_mobile_no FROM recommended_site_master LEFT JOIN engineer_master ON recommended_site_master.r_engineer_id = engineer_master.eid LEFT JOIN te_master ON recommended_site_master.r_te_code = te_master.te_code WHERE te_master.zone LIKE '%BIHAR%' OR te_master.zone LIKE '%NB1%' OR te_master.zone LIKE '%NB2%' ORDER BY recommended_site_master.r_site_id DESC LIMIT 0, 100

//echo $sql1;

$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$the_r_site_id = $row1["r_site_id"];	
$r_existing_id = $row1["existing_id"] ? trim($row1["existing_id"]) : "";
$r_te_code = $row1["r_te_code"] ? trim($row1["r_te_code"]) : "";
$r_engineer_id = $row1["r_engineer_id"] ? trim($row1["r_engineer_id"]) : "";
$r_engineer_name = $row1["e_name"] ? trim($row1["e_name"]) : "";
$r_engineer_mobile = $row1["e_mobile"] ? trim($row1["e_mobile"]) : "";
$r_te_name = $row1["te_name"] ? trim($row1["te_name"]) : "";
$r_te_mobile_no = $row1["te_mobile_no"] ? trim($row1["te_mobile_no"]) : "";
$r_site_name = $row1["r_site_name"] ? trim($row1["r_site_name"]) : "";
$r_contact_person_name = $row1["r_contact_person_name"] ? trim($row1["r_contact_person_name"]) : "";
$r_mobile_no = $row1["r_mobile_no"] ? trim($row1["r_mobile_no"]) : "";
$r_address = $row1["r_address"] ? trim($row1["r_address"]) : "";
$r_site_potential_in_mt = $row1["r_site_potential_in_mt"] ? trim($row1["r_site_potential_in_mt"]) : "";
$r_contact_person_category_name = $row1["r_contact_person_category_name"] ? trim($row1["r_contact_person_category_name"]) : "";
$r_recomended_site_image = $row1["r_recomended_site_image"] ? trim($row1["r_recomended_site_image"]) : "";
$r_status = $row1["r_status"] ? trim($row1["r_status"]) : "";
$r_submission_date = $row1["r_submission_date"] ? trim($row1["r_submission_date"]) : "";
$r_te_interaction_date = $row1["r_te_interaction_date"] ? trim($row1["r_te_interaction_date"]) : "";
$r_site_verification_image = $row1["r_site_verification_image"] ? trim($row1["r_site_verification_image"]) : "";
$r_te_interaction_comment = $row1["r_te_interaction_comment"] ? trim($row1["r_te_interaction_comment"]) : "";
$r_point_earned_by_engineer = $row1["r_point_earned_by_engineer"] ? trim($row1["r_point_earned_by_engineer"]) : "0";

$expected_product_id = $row1["expected_product_id"] ? trim($row1["expected_product_id"]) : "";
$expected_product_name = $row1["expected_product_name"] ? trim($row1["expected_product_name"]) : "";
$expected_consumption = $row1["expected_consumption"] ? trim($row1["expected_consumption"]) : "0";

$actual_product_id = $row1["actual_product_id"] ? trim($row1["actual_product_id"]) : "";
$actual_product_name = $row1["actual_product_name"] ? trim($row1["actual_product_name"]) : "";
$actual_consumption = $row1["actual_consumption"] ? trim($row1["actual_consumption"]) : "";
$purchased_from = $row1["purchased_from"] ? trim($row1["purchased_from"]) : "";
$purchased_from_name = $row1["purchased_from_name"] ? trim($row1["purchased_from_name"]) : "";
$purchased_from_area = $row1["purchased_from_area"] ? trim($row1["purchased_from_area"]) : "";
$purchased_from_contact_no = $row1["purchased_from_contact_no"] ? trim($row1["purchased_from_contact_no"]) : "";
$is_mail_sent_to_asm = $row1["is_mail_sent_to_asm"] ? trim($row1["is_mail_sent_to_asm"]) : "";

$r_asm_id = $row1["r_asm_id"] ? trim($row1["r_asm_id"]) : "";
$r_asm_name = $row1["r_asm_name"] ? trim($row1["r_asm_name"]) : "";
$r_asm_email = $row1["r_asm_email"] ? trim($row1["r_asm_email"]) : "";
$r_asm_ph_no = $row1["r_asm_ph_no"] ? trim($row1["r_asm_ph_no"]) : "";
$r_asm_branch = $row1["r_asm_branch"] ? trim($row1["r_asm_branch"]) : "";

if($r_asm_id!=""){
$the_app_asm_data = get_asm_data_by_id($conn,$r_asm_id);
if($the_app_asm_data["sts"]=="YES"){
$r_asm_name = $the_app_asm_data["asm_name"];
$r_asm_email = $the_app_asm_data["email"];
$r_asm_ph_no = $the_app_asm_data["ph_no"];
$r_asm_branch = $the_app_asm_data["branch"];	
}
}

if($r_status=="PENDING"){
$stsClass = "pending_status_class";
}else if($r_status=="APPROVED"){
$stsClass = "approved_status_class";
}else if($r_status=="REJECTED"){
$stsClass = "rejected_status_class";
}else{
$stsClass = "";
}
$the_eng_branch_name_selected = "";	
$the_eng_branch_code_selected = $row1["eng_branch_code"];
if($the_eng_branch_code_selected!=""){
$the_eng_branch_name_selected = get_branch_names_by_ids($conn,$the_eng_branch_code_selected);
}


$the_branch_code_selected = $row1["branch_code"];
if($the_branch_code_selected!=""){
$the_branch_code_selected = get_branch_names_by_ids($conn,$the_branch_code_selected);
}else{
$the_branch_code_selected = "";	
}

if($r_submission_date!=""){
	$r_submission_date = date("dS M, Y",strtotime($r_submission_date));
}
if($r_te_interaction_date!=""){
	$r_te_interaction_date = date("dS M, Y",strtotime($r_te_interaction_date));
}


	if($r_recomended_site_image!=""){
	if(file_exists($img_dir.$r_recomended_site_image)){
	$r_recomended_site_image_url = $image_url_prefix.$r_recomended_site_image;
	}else{
	$r_recomended_site_image_url = "";
	}
	}else{
	$r_recomended_site_image_url = "";
	}

	if($r_site_verification_image!=""){
	if(file_exists($approve_img_dir.$r_site_verification_image)){
	$r_site_verification_image_url = $approve_image_url_prefix.$r_site_verification_image;
	}else{
	$r_site_verification_image_url = "";
	}
	}else{
	$r_site_verification_image_url = "";
	}
?>
<tr>
<td>
<div>
<span class="recomSiteEachField"><b>ID:</b> <?php echo $the_r_site_id;?><strong id="rtoa_ldr_<?php echo $the_r_site_id;?>" class="rtoa_ldr"></strong></span>
<?php 
if($r_existing_id!=""){
?>
<span class="recomSiteEachField" style="color: #f00;"><b>Existing Site ID: <?php echo $r_existing_id;?></b></span>
<?php
}
?>
<?php if($r_status!=""){
if($r_status=="REJECTED"){

echo '<span class="recomSiteEachField"><b>Status:</b> <strong class="'.$stsClass.'" id="sts_id_'.$the_r_site_id.'"><a href="javascript:void(0);" class="rejectToPendingBtn" rtaid="'.$the_r_site_id.'">'.$r_status.'</a></strong></span>';


/*echo '<span class="recomSiteEachField"><b>Status:</b> <strong class="'.$stsClass.'" id="sts_id_'.$the_r_site_id.'"><a href="javascript:void(0);" class="rejectToApproveBtn" rtaid="'.$the_r_site_id.'">'.$r_status.'</a></strong></span>';*/

}else{
echo '<span class="recomSiteEachField"><b>Status:</b> <strong class="'.$stsClass.'">'.$r_status.'</strong></span>';	
}
}?>
<?php if($r_submission_date!=""){echo '<span class="recomSiteEachField"><b>Recommended On:</b></span><span class="recomSiteEachField">'.$r_submission_date.'</span>';}?>

<?php
if($r_status=="PENDING"){
if($expected_consumption>=$actual_bag_cons_approve_limit){
if($is_mail_sent_to_asm=="YES"){
$smtasmbtntxt = "RESEND MAIL TO ASM";	
}else{
$smtasmbtntxt = "SEND MAIL TO ASM";	
}


 ?>
<span class="recomSiteEachField"><b>Is mail send to ASM:</b> <strong id="ims_sts_<?php echo $the_r_site_id;?>"><?php echo $is_mail_sent_to_asm;?></strong></span>
<?php if($r_asm_name!=""){ ?>
<span class="recomSiteEachField"><b>ASM Name:</b> <strong><?php echo $r_asm_name;?></strong></span>
<?php } ?>
<?php if($r_asm_ph_no!=""){ ?>
<span class="recomSiteEachField"><b>Ph No.:</b> <strong><?php echo $r_asm_ph_no;?></strong></span>
<?php } ?>
<?php if($r_asm_email!=""){ ?>
<span class="recomSiteEachField"><b>Email:</b> <strong><?php echo $r_asm_email;?></strong></span>
<?php } ?>
<?php if($r_asm_branch!=""){ ?>
<span class="recomSiteEachField"><b>Branch:</b> <strong><?php echo $r_asm_branch;?></strong></span>
<?php } ?>
<?php if($is_mail_sent_to_asm=="YES"){
		
}else{
?>
<span class="recomSiteEachField">

<a href="javascript:void(0);" class="btn btn-primary smtasmBtn" id="smtasmBtn_<?php echo $the_r_site_id;?>" rsid="<?php echo $the_r_site_id;?>" rstcode="<?php echo $r_te_code;?>">SEND MAIL TO ASM</a>

</span>
<?php	
}
?>


<?php }

}else if($r_status=="APPROVED"){
?>
<?php if($r_asm_name!=""){ ?>
<span class="recomSiteEachField"><b>ASM Name:</b></span><span class="recomSiteEachField"><?php echo $r_asm_name;?></span>
<?php } ?>
<?php if($r_asm_ph_no!=""){ ?>
<span class="recomSiteEachField"><b>Ph No.:</b></span><span class="recomSiteEachField"><?php echo $r_asm_ph_no;?></span>
<?php } ?>
<?php if($r_asm_email!=""){ ?>
<span class="recomSiteEachField"><b>Email:</b></span><span class="recomSiteEachField"><?php echo $r_asm_email;?></span>
<?php } ?>
<?php if($r_asm_branch!=""){ ?>
<span class="recomSiteEachField"><b>Branch:</b></span><span class="recomSiteEachField"><?php echo $r_asm_branch;?></span>
<?php } ?>
<?php } ?>

</div>
</td>
<td>
<div>
<?php if($r_engineer_name!=""){echo '<span class="engineerEachField">'.$r_engineer_name.'</span>';}?>
<?php if($r_engineer_mobile!=""){echo '<span class="engineerEachField"><b>Mobile:</b> '.$r_engineer_mobile.'</span>';}?>
<span class="engineerEachField"><b>Point Earned:</b> <strong class="sho_point" id="sho_point_<?php echo $the_r_site_id;?>"><?php echo $r_point_earned_by_engineer." PT";?></strong></span>
<?php if($the_eng_branch_name_selected!=""){echo '<span class="engineerEachField"><b>Branch:</b></span><span class="engineerEachField">'.$the_eng_branch_name_selected.'</span>';}?>
</div>
</td>
<td>
<div>
<?php if($r_te_name!=""){echo '<span class="teEachField">'.$r_te_name.'</span>';}?>
<?php if($r_te_mobile_no!=""){echo '<span class="teEachField"><b>Mobile:</b> '.$r_te_mobile_no.'</span>';}?>
<?php if($r_te_code!=""){echo '<span class="teEachField"><b>Code:</b> '.$r_te_code.'</span>';}?>
<?php if($r_te_interaction_date!=""){echo '<span class="teEachField"><b>Interacted On:</b></span><span class="teEachField">'.$r_te_interaction_date.'</span>';}?>
<?php if($r_te_interaction_comment!=""){echo '<span class="teEachField"><b>Comment:</b></span><span class="teEachField">'.$r_te_interaction_comment.'</span>';}?>
<?php if($the_branch_code_selected!=""){echo '<span class="teEachField"><b>Branch:</b></span><span class="teEachField">'.$the_branch_code_selected.'</span>';}?>
</div>
</td>
<td>
<div>
<?php if($r_site_name!=""){echo '<span class="siteEachField">'.$r_site_name.'</span>';}?>
<?php if($r_site_potential_in_mt!=""){echo '<span class="siteEachField"><b>Site&nbsp;Potential:</b> '.$r_site_potential_in_mt.' MT</span>';}?>
<?php if($r_address!=""){echo '<span class="siteEachField"><b>Address:</b></span><span class="siteEachField">'.$r_address.'</span>';}?>
</div>
</td>
<td>
<div>
<?php if($r_contact_person_name!=""){echo '<span class="contactPersonEachField">'.$r_contact_person_name.'</span>';}?>
<?php if($r_mobile_no!=""){echo '<span class="contactPersonEachField"><b>Mobile:</b> '.$r_mobile_no.'</span>';}?>
<?php if($r_contact_person_category_name!=""){echo '<span class="contactPersonEachField"><b>Category&nbsp;Name:</b></span><span class="contactPersonEachField">'.$r_contact_person_category_name.'</span>';}?>
</div>
</td>
<td>
<span class="contactPersonEachField"><?php echo $expected_product_name;?></span>
<span class="contactPersonEachField"><b>Bags:</b> <?php echo $expected_consumption;?></span>
</td>
<td>
<span class="contactPersonEachField"><?php echo $actual_product_name;?></span>
<span class="contactPersonEachField"><b>Bags:</b> <?php echo $actual_consumption;?></span>
<span class="contactPersonEachField"><b>Purchased From:</b> <?php echo $purchased_from;?></span>
<span class="contactPersonEachField"><b>Name:</b> <?php echo $purchased_from_name;?></span>
<span class="contactPersonEachField"><b>Area:</b> <?php echo $purchased_from_area;?></span>
<span class="contactPersonEachField"><b>Contact:</b> <?php echo $purchased_from_contact_no;?></span>
</td>
<td>
<?php
if($r_recomended_site_image_url!=""){
?>
<img src="<?php echo $r_recomended_site_image_url;?>" class="prfl_img" />
<?php
}else{
	echo "Not set yet";
}
?>
</td>
<td>
<?php
if($r_site_verification_image_url!=""){
?>
<img src="<?php echo $r_site_verification_image_url;?>" class="prfl_img" />
<?php
}else{
	echo "Not set yet";
}
?>
</td>

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

jQuery('#from_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
jQuery('#to_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });

jQuery('#trn_te_code').change(function(){
		var trn_te_code = jQuery(this).val();
		if(trn_te_code!=''){
		var img = '<img src="images/ajax-loader.gif">';
				jQuery(".rpt_loader").html(img);
				jQuery.ajax({
				url: 'ajax_show_engineer_by_te_code.php',
				type: 'post',
				dataType: 'json',
				data: "trn_te_code="+trn_te_code,
				success: function(response){				
				if(response.process_sts=="YES"){					
					jQuery("#sl_en_id").html(response.en_options);
					jQuery(".rpt_loader").html("");		
				}else{
					jQuery("#sl_en_id").html('<option value="">Select Engineer</option>');
					jQuery(".rpt_loader").html(response.process_msg);
					setTimeout(function(){
					jQuery(".rpt_loader").html("");
					},3000);				
				}						
				}
				});
		}else{
			jQuery("#sl_en_id").html('<option value="">Select Engineer</option>');
			
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



jQuery(".smtasmBtn").click(function(){
	var rsid = jQuery(this).attr("rsid");
	var rstcode = jQuery(this).attr("rstcode");
	if(rsid!="" && rstcode!=""){
	var status_element = jQuery("#ims_sts_"+rsid);
	var smtasmBtn_element = jQuery("#smtasmBtn_"+rsid);
	var rtoa_ldr_element = jQuery("#rtoa_ldr_"+rsid);
		rtoa_ldr_element.html(imgs);
		jQuery.ajax({
		url: 'ajax_send_mail_to_asm_for_confirm_site_from_admin.php',
		type: 'post',
		dataType: 'json',
		data: "r_site_id="+rsid+"&te_code="+rstcode,
		success: function(response){				
		if(response.process_status=="YES"){
		status_element.html("YES");
		smtasmBtn_element.html("RESEND MAIL TO ASM");
		rtoa_ldr_element.html(done_img);
		setTimeout(function(){
		rtoa_ldr_element.html("");
		},4000);		
		}else{
		rtoa_ldr_element.html("");
		alert(response.process_message);					
		}						
		}
		});
	
	}
});


jQuery(".rejectToPendingBtn").click(function(){
	var rtaid = jQuery(this).attr("rtaid");
	if(rtaid!=""){
	var status_element = jQuery("#sts_id_"+rtaid);
	var rtoa_ldr_element = jQuery("#rtoa_ldr_"+rtaid);
	var cb2 = confirm("Do you want to change status from rejected to pending?");
	if(cb2 == true) {
		var img = '<img src="images/ajax-loader.gif">';
		rtoa_ldr_element.html(img);
		jQuery.ajax({
		url: 'ajax_update_recomended_site_from_reject_to_pending.php',
		type: 'post',
		dataType: 'json',
		data: "rtaid="+rtaid,
		success: function(response){				
		if(response.process_sts=="YES"){
		var curr_sts = 	response.curr_sts;
		status_element.html(curr_sts);
		status_element.removeClass("rejected_status_class");
		status_element.addClass("pending_status_class");
		rtoa_ldr_element.html("");		
		}else{
		rtoa_ldr_element.html("");
		alert(response.process_msg);					
		}						
		}
		});
	}
	}
});

/*jQuery(".rejectToApproveBtn").click(function(){
	var rtaid = jQuery(this).attr("rtaid");
	if(rtaid!=""){
	var status_element = jQuery("#sts_id_"+rtaid);
	var point_sho_element = jQuery("#sho_point_"+rtaid);
	var rtoa_ldr_element = jQuery("#rtoa_ldr_"+rtaid);
	var cb2 = confirm("Do you want to change status from rejected to approved?");
	if(cb2 == true) {
		var img = '<img src="images/ajax-loader.gif">';
		rtoa_ldr_element.html(img);
		jQuery.ajax({
		url: 'ajax_update_recomended_site_from_reject_to_appreved.php',
		type: 'post',
		dataType: 'json',
		data: "rtaid="+rtaid,
		success: function(response){				
		if(response.process_sts=="YES"){
		var curr_sts = 	response.curr_sts;
		var curr_point = 	response.curr_point;
		status_element.html(curr_sts);
		point_sho_element.html(curr_point);
		status_element.removeClass("rejected_status_class");
		status_element.addClass("approved_status_class");
		rtoa_ldr_element.html("");		
		}else{
		rtoa_ldr_element.html("");
		alert(response.process_msg);					
		}						
		}
		});
	}
	}
});*/

jQuery(".srch_btn").click(function(){
		var trn_te_code = jQuery("#trn_te_code").val();
		var sl_en_id = jQuery("#sl_en_id").val();
		var srch_eng_te_site_dtls = jQuery("#srch_eng_te_site_dtls").val();
		var sl_ord_sts = jQuery("#sl_ord_sts").val();
		var sl_day_wise = jQuery("#sl_day_wise").val();
		var from_dt = jQuery("#from_dt").val();
		var to_dt = jQuery("#to_dt").val();
		var qstring ="";
		var dtstring ="";
		var amp = "";
		if(trn_te_code!="" || sl_ord_sts!="" || sl_day_wise!="" || srch_eng_te_site_dtls!=""){
			
			
		if(trn_te_code!=""){
		if(qstring==""){
		qstring = qstring+"trn_te_code="+trn_te_code;
		}else{
		qstring = qstring+"&trn_te_code="+trn_te_code;  
		}
		
		if(sl_en_id!=""){
		if(qstring==""){
		qstring = qstring+"sl_en_id="+sl_en_id;
		}else{
		qstring = qstring+"&sl_en_id="+sl_en_id;  
		}
		}
		}
		
		if(srch_eng_te_site_dtls!=""){
		if(qstring==""){
		qstring = qstring+"srch_eng_te_site_dtls="+srch_eng_te_site_dtls;
		}else{
		qstring = qstring+"&srch_eng_te_site_dtls="+srch_eng_te_site_dtls;  
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
		window.location = "<?php echo $page_name;?>"+qstring;
		}else{
			alert("Please select atleast one field to search.");
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