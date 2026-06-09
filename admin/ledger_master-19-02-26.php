<?php


include "web_check.php";
include "star_connection.php";
//$gift_master = "gift_master";
//$gift_order_master = "gift_order_master";
$ledger_master = "ledger_master";
$te_master = "te_master";
$engineer_master = "engineer_master";
$branch_master = "branch_master";
$recommended_site_master= "recommended_site_master";
$date_of_correction_in_point_calculation_as_per_new_SOP = "2024-03-19 00:00:00";

$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
//echo"<pre>";print_r($sql);die;
//set manualy all 23-12-25
$the_data_show_type='ALL';
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
$order_status_arr = array("POINT_EARNED","POINT_REDEEM"); // ,"BONUS"
$supported_mime_type = array("image/jpeg","image/png","image/jpg");
$img_dir = "../gift_pic/";
$default_image_name = "profile.png";
$image_url_prefix = $server_url."gift_pic/";
$new_qry_string_filtered = "";
$msg_txt = "";
$add_page_name = "ledger_master.php";
$page_name = "ledger_master.php";
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
			if($search_array_val=="POINT_EARNED"){ // points_earned
				$whr_str .= "$aand ($ledger_master.`point_earned` is not null or $ledger_master.`point_earned`!='') and ($ledger_master.`point_redeem` is null or  $ledger_master.`point_redeem`='')";
			}else if($search_array_val=="POINT_REDEEM"){
				$whr_str .= "$aand ($ledger_master.`point_redeem` is not null or $ledger_master.`point_redeem`!='') and ($ledger_master.`point_earned` is null or  $ledger_master.`point_earned`='')";
			}			
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
			$whr_str .= "$aand $engineer_master.`te_code`='$search_array_val' ";	
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
			$whr_str .= "$aand $ledger_master.`user_id`='$search_array_val' ";	
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
			
			$whr_str .= "$aand ($engineer_master.`e_name` like '%$search_array_val%' or $engineer_master.`te_code` like '%$search_array_val%' or $engineer_master.`e_mobile` like '%$search_array_val%' or $te_master.`te_name` like '%$search_array_val%' or $te_master.`te_mobile_no` like '%$search_array_val%' or $ledger_master.`description` like '%$search_array_val%' )";
			
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
			   $whr_str .= "$aand $ledger_master.`ldgr_datetime` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $ledger_master.`ldgr_datetime` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $ledger_master.`ldgr_datetime` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $ledger_master.`ldgr_datetime` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $ledger_master.`ldgr_datetime` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}


if($whr_str!=""){
	$new_whr_str = " where $ledger_master.`ldgr_datetime` >= '$date_of_correction_in_point_calculation_as_per_new_SOP' AND ".$whr_str;
}else{
	$new_whr_str =" where $ledger_master.`ldgr_datetime` >= '$date_of_correction_in_point_calculation_as_per_new_SOP' ";
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

$pgsql = "select $ledger_master.`ldgr_id` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` $new_whr_str";
//echo $pgsql;
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
	width:200px;
	word-wrap: break-word;
}
.eachField{
	display:block;
	width:200px;
	word-wrap: break-word;
}
.adminActivityField{
	display:block;
	width:100%;
	margin-bottom:8px;
	word-wrap: break-word;
}
.admin_ord_upd_ldr{
	width:20px;
	height:20px;
	margin-left:20px;
}
.pointEarnedEachField{
	display:block;
	width:100px;
	color:#1C6E00;
	text-align:right;
	font-weight:bold;
	word-wrap: break-word;
}
.pointRedeemEachField{
	display:block;
	width:100px;
	color:#D42A1D;
	text-align:right;
	font-weight:bold;
	word-wrap: break-word;
}
.pointRemainingEachField{
	display:block;
	width:100px;
	color:#250edd;
	text-align:right;
	font-weight:bold;
	word-wrap: break-word;
}
.pointBonusEachField{
	display:block;
	width:100px;
	color:#1C6E00;
	text-align:right;
	font-weight:bold;
	word-wrap: break-word;
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
							<?php if($data_show_type=="ALL"){
							$sql1 = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`zone`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` $new_whr_str order by $ledger_master.`ldgr_id` asc";
							}
							else{
								$where_clause = "$ledger_master.`ldgr_datetime` >= '$date_of_correction_in_point_calculation_as_per_new_SOP'";
								$zones = explode(",", $data_show_type); // Assuming $data_show_type is a comma-separated string of zones
								
								foreach ($zones as $index => $zone) {
									$zone = trim($zone);
									if ($index != 0) {
										$where_clause .= " OR ";
									}
									else
									{
										$where_clause .= " AND ";
									}
									$where_clause .= "te_master.`zone` LIKE '%$zone%'";
								}
								$sql1 = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`zone`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where $where_clause order by $ledger_master.`ldgr_id` asc";

							}	

							$res1 = mysqli_query($conn,$sql1);
							$totres1 = mysqli_num_rows($res1); ?>
                          <h2>Ledger Master (<?php if($total_pgres){ echo $total_pgres; }else{ echo $totres1; } ?>)&nbsp;&nbsp;&nbsp;<a href="export_ledger_master.php?paged=<?php echo $page.$export_filtered_str;?>" class="btn bg-red waves-effe">Export</a> 
						  <?php if($_SESSION['start_stellar_admin_name']=='accounts' OR $_SESSION['start_stellar_admin_name']=='admin'){ ?>
						  &nbsp;&nbsp;
						  &nbsp;&nbsp;&nbsp;<a href="export_ledger_master_all.php?paged=<?php echo $page.$export_filtered_str;?>" class="btn bg-red waves-effe">Export Complete Data</a> &nbsp;&nbsp;
							<?php }?>
						  <span class="rpt_loader"></span> <span class="shomsg"><?php if($msg_txt!=""){ echo $msg_txt;}?></span>
                          
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
<input type="text" class="form-control" id="srch_eng_te_site_dtls" value="<?php echo $srch_eng_te_site_dtls;?>" placeholder="Search By Engineer,TE And Ledger Details">
</div>
<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">

</div>
    </div>
    <div class="row clearfix">
    
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
<select class="form-control" id="sl_ord_sts">
<option value="" >All Type</option>
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
                                            <th style="width:150px;">Engineer&nbsp;Details</th>
                                            <th style="width:150px;">TE&nbsp;Details</th>
                                            <th style="width:200px;">Description</th>
                                            <th style="width:100px;">Points&nbsp;Earned</th>
                                            <th style="width:100px;">Points&nbsp;Redeem</th>
                                            <th style="width:100px;">Remaining&nbsp;Points</th>
											<!-- <th style="width:150px;">Bonus&nbsp;Points</th> -->
                                            <th style="width:150px;">Date</th>
											
                                        </tr>
                                    </thead>
                                    <tfoot>
                                       <tr>
                                            <th style="width:150px;">Engineer&nbsp;Details</th>
                                            <th style="width:150px;">TE&nbsp;Details</th>
                                            <th style="width:200px;">Description</th>
                                            <th style="width:100px;">Points&nbsp;Earned</th>
                                            <th style="width:100px;">Points&nbsp;Redeem</th>
                                            <th style="width:100px;">Remaining&nbsp;Points</th>
											<!-- <th style="width:150px;">Bonus&nbsp;Points</th> -->
                                            <th style="width:150px;">Date</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
if($data_show_type=="ALL"){
$sql1 = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`zone`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` $new_whr_str order by $ledger_master.`ldgr_id` asc limit $start_from,$limit";
}
else{
	$where_clause = "$ledger_master.`ldgr_datetime` >= '$date_of_correction_in_point_calculation_as_per_new_SOP'";
	$zones = explode(",", $data_show_type); // Assuming $data_show_type is a comma-separated string of zones
	
	foreach ($zones as $index => $zone) {
		$zone = trim($zone);
		if ($index != 0) {
			$where_clause .= " OR ";
		}
		else
		{
			$where_clause .= " AND ";
		}
		$where_clause .= "te_master.`zone` LIKE '%$zone%'";
	}
	$sql1 = "select $ledger_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`zone`,$te_master.`te_mobile_no` from $ledger_master left join $engineer_master on $ledger_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where $where_clause order by $ledger_master.`ldgr_id` asc limit $start_from,$limit";

}	

// $sql1 = "SELECT $ledger_master.*,$engineer_master.e_name,$engineer_master.e_mobile,$engineer_master.te_code,$te_master.te_name,$te_master.branch_code,$te_master.te_mobile_no,$recommended_site_master.existing_id FROM $ledger_master LEFT JOIN $engineer_master ON $ledger_master.user_id = $engineer_master.eid LEFT JOIN $te_master ON $engineer_master.te_code = $te_master.te_code LEFT JOIN $recommended_site_master ON $ledger_master.user_id = $recommended_site_master r_engineer_id $new_whr_str ORDER BY $ledger_master.ldgr_id ASC LIMIT $start_from, $limit";

//echo $sql1;

$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$the_g_order_id = $row1["g_order_id"];		
		
$r_te_code = $row1["te_code"] ? trim($row1["te_code"]) : "";
$r_engineer_id = $row1["user_id"] ? trim($row1["user_id"]) : "";
$r_engineer_name = $row1["e_name"] ? trim($row1["e_name"]) : "";
$r_engineer_mobile = $row1["e_mobile"] ? trim($row1["e_mobile"]) : "";
$r_te_name = $row1["te_name"] ? trim($row1["te_name"]) : "";
$r_te_mobile_no = $row1["te_mobile_no"] ? trim($row1["te_mobile_no"]) : "";
$r_description = $row1["description"] ? trim($row1["description"]) : "";
$r_point_earned = $row1["point_earned"] ? trim($row1["point_earned"]) : "";
$r_point_redeem = $row1["point_redeem"] ? trim($row1["point_redeem"]) : "";
$tds = $row1["tds"] ? trim($row1["tds"]) : "";
$product_point = $row1["product_point"] ? trim($row1["product_point"]) : "";
$remaining_balance = $row1["remaining_balance"] ? trim($row1["remaining_balance"]) : "";
$site_id = $row1["r_site_id"] ? trim($row1["r_site_id"]) : "";
$r_existing_id = $row1["existing_id"] ? trim($row1["existing_id"]) : "";
$related_id = $row1["related_id"] ? trim($row1["related_id"]) : "";
$ldgr_id = $row1["ldgr_id"] ? trim($row1["ldgr_id"]) : "";


$ldgr_datetime = $row1["ldgr_datetime"] ? trim($row1["ldgr_datetime"]) : "";
$the_branch_code_selected = $row1["branch_code"];
if($the_branch_code_selected!=""){
$the_branch_code_selected = get_branch_names_by_ids($conn,$the_branch_code_selected);
}else{
$the_branch_code_selected = "";	
}

$onlyDate = date('Y-m-d', strtotime($ldgr_datetime));

if($ldgr_datetime!=""){
	$ldgr_datetime = date("dS M, Y H:i:s",strtotime($ldgr_datetime));
}
?>
<?php



?>

<tr>
<td style="width:150px;">
<div style="width:150px;">
<?php if($r_engineer_name!=""){echo '<span class="engineerEachField"><b>Name:</b>'.$r_engineer_name.'</span>';}?>
<?php if($r_engineer_mobile!=""){echo '<span class="engineerEachField"><b>Mobile: </b> '.$r_engineer_mobile.'</span>';}?>

<?php 
$sql133="SELECT * FROM `recommended_site_master` where `r_site_id`='".$related_id."' ORDER BY `r_site_id` DESC";
$res133 = mysqli_query($conn,$sql133);
	$totres133 = mysqli_num_rows($res133);
	if($totres133>0){
	while($row133=mysqli_fetch_assoc($res133)){
	$site_id = trim($row133["r_site_id"]);
	$r_existing_id = trim($row133["existing_id"]);
	
	}
	}

if($site_id!=""){echo'<span class="engineerEachField"><b> Site ID: </b>'. $site_id;'</span>';}

if($r_existing_id!=""){echo'<span class="engineerEachField"><b>Existing Site ID: </b>'. $r_existing_id;'</span>';}?> 


<?php if($r_point_taken!=""){echo '<span class="engineerEachField"><b>Point Reedem:</b> '.$r_point_taken.' PT</span>';}?>
<?php if($r_datetime!=""){echo '<span class="engineerEachField"><b>Ordered On:</b></span><span class="engineerEachField">'.$r_datetime.'</span>';}?>


<?php if($r_point_taken!=""){echo '<span class="engineerEachField"><b>Point Reedem:</b> '.$r_point_taken.' PT</span>';}?>
<?php if($r_datetime!=""){echo '<span class="engineerEachField"><b>Ordered On:</b></span><span class="engineerEachField">'.$r_datetime.'</span>';}?>

</div>
</td>
<td style="width:150px;">
<div style="width:150px;">
<?php if($r_te_name!=""){echo '<span class="teEachField"><b>Name: </b>'.$r_te_name.'</span>';}?>
<?php if($r_te_mobile_no!=""){echo '<span class="teEachField"><b>Mobile:</b> '.$r_te_mobile_no.'</span>';}?>
<?php if($r_te_code!=""){echo '<span class="teEachField"><b>Code:</b> '.$r_te_code.'</span>';}?>
<?php if($the_branch_code_selected!=""){echo '<span class="teEachField"><b>Branch:</b></span><span class="teEachField">'.$the_branch_code_selected.'</span>';}?>
</div>
</td>

<td style="width:200px;">
	<div style="width:200px;">
		<?php if($r_point_redeem!=""){
			//$sqlgift_id="SELECT GOM.order_id,tds,product_point FROM `gift_master` GF,gift_order_master GOM WHERE GOM.gift_id=GF.id and GF.`gift_title`='".$r_description."' AND GOM.user_id='".$r_engineer_id."' AND GOM.point_taken = '$r_point_redeem'";
			 $sqlgift_id = "
				SELECT 
					GOM.order_id,
					GOM.tds,
					GOM.product_point
				FROM gift_master GF
				JOIN gift_order_master GOM ON GOM.gift_id = GF.id
				WHERE GF.id LIKE '%".$related_id."%'
				AND GOM.user_id = '".$r_engineer_id."'
				AND GOM.point_taken = '".$r_point_redeem."'
				AND DATE(GOM.datetime) = '".$onlyDate."'
				";
			$resgift_id = mysqli_query($conn,$sqlgift_id);
			$rowgift_id=mysqli_fetch_assoc($resgift_id);
			$g_order_id = $rowgift_id["order_id"];
			$tds = $rowgift_id["tds"];
			if($rowgift_id["product_point"]!=''){
			$product_point=$rowgift_id["product_point"];
			}else{
				$product_point=$r_point_redeem;
			}
	
		?>
		<?php echo '<span class="teEachField"><b>Order Id: </b>'.$g_order_id.'</span>';?>
		<?php echo '<span class="teEachField"><b>Product Price: </b>'.$product_point.'</span>';?>
		<?php echo '<span class="teEachField"><b>TDS: </b>'.$tds.'</span>';?>
		<?php }
		//add condition reject case - 09-01-26
		if($r_point_earned!=""){
			//$sqlgift_id="SELECT GOM.order_id,tds,product_point FROM `gift_master` GF,gift_order_master GOM WHERE GOM.gift_id=GF.id and GF.`gift_title`='".$r_description."' AND GOM.user_id='".$r_engineer_id."' AND GOM.point_taken = '$r_point_redeem'";
			  $sqlgift_id = "
				SELECT 
					GOM.order_id,
					GOM.tds,
					GOM.remarks,
					GOM.product_point
				FROM gift_master GF
				JOIN gift_order_master GOM ON GOM.gift_id = GF.id
				WHERE GF.gift_title LIKE '%".$r_description."%'
				AND GOM.user_id = '".$r_engineer_id."'
				AND GOM.point_taken = '".$r_point_earned."'
				
				";
			$resgift_id = mysqli_query($conn,$sqlgift_id);
			$rowgift_id=mysqli_fetch_assoc($resgift_id);
			$g_order_id = $rowgift_id["order_id"];
			$tds = $rowgift_id["tds"];
			$remarks = $rowgift_id["remarks"];
			if($rowgift_id["product_point"]!=''){
			$product_point=$rowgift_id["product_point"];
			}else{
				$product_point=$r_point_redeem;
			}
	
		?>
		<?php if(!empty($g_order_id)){  echo '<span class="teEachField"><b>Order Id: </b>'.$g_order_id.'</span>';}?>
		<?php if(!empty($product_point)){ echo '<span class="teEachField"><b>Product Price: </b>'.$product_point.'</span>'; }?>
		<?php if(!empty($tds)){ echo '<span class="teEachField"><b>TDS: </b>'.$tds.'</span>'; }?>
		<?php if(!empty($remarks)){ echo '<span class="teEachField"><b>Remarks: </b>'.$remarks.'</span>'; }?>
		<?php } ?>
		
		<span class="teEachField"><b>Description: </b>
		<?php echo $r_description;?> </span>
	</div
</td>
<td style="width:100px;">
<div style="width:100px;">
<?php if($r_point_earned!=""){echo '<span class="pointEarnedEachField">'.$r_point_earned.'</span>';}?>
</div>
</td>
<td style="width:100px;">
<div style="width:100px;">
<?php if($r_point_redeem!=""){echo '<span class="pointRedeemEachField">'.$r_point_redeem.'</span>';}?>
</div>
</td>
<td style="width:100px;">
<div style="width:100px;">
<?php if($remaining_balance!=""){echo '<span class="pointRemainingEachField">'.$remaining_balance.'</span>';}?>
</div>
</td>

<td>
<?php echo $ldgr_datetime;?>
</td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="5">No data found.</td>
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

jQuery('.admin_del_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });


jQuery(".admin_update_order_data").click(function(){
	var the_ord_id = jQuery(this).attr("the_ord_id");
	if(the_ord_id!=""){
		var element_admin_ord_upd_ldr = jQuery("#admin_ord_upd_ldr_"+the_ord_id);
		var admin_sl_ord_sts = jQuery("#admin_sl_ord_sts_"+the_ord_id).val();
		var admin_del_dt = jQuery("#admin_del_dt_"+the_ord_id).val();
		element_admin_ord_upd_ldr.html(imgs);
		jQuery.ajax({
				url: 'ajax_update_order_data_by_admin.php',
				type: 'post',
				dataType: 'json',
				data: "the_ord_id="+the_ord_id+"&admin_sl_ord_sts="+admin_sl_ord_sts+"&admin_del_dt="+admin_del_dt,
				success: function(response){				
				if(response.process_sts=="YES"){					
					element_admin_ord_upd_ldr.html(done_img);
					setTimeout(function(){
					element_admin_ord_upd_ldr.html("");
					},6000);		
				}else{
					element_admin_ord_upd_ldr.html("");
					alert(response.process_msg);				
				}						
				}
				});
	}
});

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
