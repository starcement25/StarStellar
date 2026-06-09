<?php
include "web_check.php";
include "star_connection.php";
//echo"<pre>";print_r($_SESSION["start_stellar_admin"]);die;

$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}

$gift_master = "gift_master";
$gift_order_master = "gift_order_master";
$te_master = "te_master";
$engineer_master = "engineer_master";
//$order_status_arr = array("PENDING","DELIVERED","REJECT");
$order_status_arr = array(
    "PENDING",
    "ORDER PLACED",
    "DELIVERED",
    "ACKNOWLEDGEMENT OF DELIVERY",
    "COMPLAINT/FEEDBACK",
    "UNDELIVERED",
    "REJECT",
);
$supported_mime_type = array("image/jpeg","image/png","image/jpg");
$img_dir = "../gift_pic/";
$default_image_name = "profile.png";
$image_url_prefix = $server_url."gift_pic/";
$new_qry_string_filtered = "";
$msg_txt = "";
$new_whr_str="";
$add_page_name = "order_master.php";
$page_name = "order_master.php";
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
			
			$whr_str .= "$aand $gift_order_master.`status`='$search_array_val' ";
			
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

				//$branch_value_array=explode(',',$search_array_val);
				//$search_array_val1 = "".implode("','", $branch_value_array)."";

				//$whr_str .= "$aand $te_master.`zone` IN('".$search_array_val1."')";
				
				$zones = explode(",", $data_show_type); // Assuming $data_show_type is a comma-separated string of zones

				foreach ($zones as $zone) {
					$zone = trim($zone);
					if ($where_clause != "") {
						$where_clause .= " OR ";
					}
					$where_clause .= "$te_master.`zone` LIKE '%$zone%'";
				}
				$whr_str .= $aand."(".$where_clause.")";

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
			$whr_str .= "$aand $gift_order_master.`user_id`='$search_array_val' ";	
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
			
			$whr_str .= "$aand ($engineer_master.`e_name` like '%$search_array_val%' or $engineer_master.`te_code` like '%$search_array_val%' or $engineer_master.`e_mobile` like '%$search_array_val%' or $te_master.`te_name` like '%$search_array_val%' or $te_master.`te_mobile_no` like '%$search_array_val%' or $gift_master.`gift_title` like '%$search_array_val%' or $gift_order_master.`g_order_id` like '%$search_array_val%' or $gift_order_master.`address` like '%$search_array_val%' or $gift_order_master.`city` like '%$search_array_val%' or $gift_order_master.`pin` like '%$search_array_val%' or $gift_order_master.`state` like '%$search_array_val%' )";
			
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
			   $whr_str .= "$aand $gift_order_master.`datetime` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $gift_order_master.`datetime` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $gift_order_master.`datetime` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $gift_order_master.`datetime` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $gift_order_master.`datetime` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}


if($whr_str!=""){
	$new_whr_str = " where ".$whr_str;
}else{
	$new_whr_str ="";
}
//sk add line sk 30-10-25
if($data_show_type=="ALL"){
	if($new_whr_str!=""){
	$new_whr_str.=" AND te_master.zone IN (
    SELECT DISTINCT zone 
    FROM te_master) ";
	}else{
		$new_whr_str.=" WHERE te_master.zone IN (
    SELECT DISTINCT zone 
    FROM te_master)";
	}
}
//sk add end line sk 30-10-25

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



$pgsql = "select $gift_order_master.`g_order_id` from $gift_order_master left join $engineer_master on $gift_order_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join $gift_master on $gift_order_master.`gift_id`=$gift_master.`id` $new_whr_str ";

//echo $pgsql;exit();
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
}
.teEachField{
	display:block;
	width:150px;
}
.siteEachField{
	display:block;
	width:200px;
}
.contactPersonEachField{
	display:block;
	width:200px;
}
.gtitleEachField{
	display:block;
	width:150px;
}
.eachField{
	display:block;
	width:200px;
}
.adminActivityField{
	display:block;
	width:100%;
	margin-bottom:8px;
}
.admin_ord_upd_ldr{
	width:20px;
	height:20px;
	margin-left:20px;
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
<h2>Order Master (<?php echo $total_pgres;?>)&nbsp;&nbsp;&nbsp;<a href="export_order_master.php?paged=<?php echo $page.$export_filtered_str;?>" class="btn bg-red waves-effe">Export</a> &nbsp;&nbsp;<span class="rpt_loader"></span> <span class="shomsg"><?php if($msg_txt!=""){ echo $msg_txt;}?></span>

                          
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
<input type="text" class="form-control" id="srch_eng_te_site_dtls" value="<?php echo $srch_eng_te_site_dtls;?>" placeholder="Search By Engineer,TE And Order Details">
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
                                            <th style="width:150px;">Engineer&nbsp;Details</th>
                                            <th style="width:150px;">TE&nbsp;Details</th>
                                            <th style="width:200px;">Address&nbsp;Details</th>
                                            <th style="width:150px;">Gift&nbsp;Details</th>
                                            <th>Admin&nbsp;Activity</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                       <tr>
                                            <th style="width:150px;">Engineer&nbsp;Details</th>
                                            <th style="width:150px;">TE&nbsp;Details</th>
                                            <th style="width:200px;">Address&nbsp;Details</th>
                                            <th style="width:150px;">Gift&nbsp;Details</th>
                                            <th>Admin&nbsp;Activity</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php

$sql1 = "select $gift_order_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`te_mobile_no`,$gift_master.`gift_title`,$gift_master.`gift_image` from $gift_order_master left join $engineer_master on $gift_order_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join $gift_master on $gift_order_master.`gift_id`=$gift_master.`id` $new_whr_str order by $gift_order_master.`g_order_id` desc limit $start_from,$limit";

// $sql1 = "select $gift_order_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`te_mobile_no`,$gift_master.`gift_title`,$gift_master.`gift_image` from $gift_order_master left join $engineer_master on $gift_order_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join $gift_master on $gift_order_master.`gift_id`=$gift_master.`id` $new_whr_str order by $gift_order_master.`g_order_id` desc";

// echo $sql1;


/*$sql1="select gift_order_master.*,engineer_master.e_name,engineer_master.e_mobile,engineer_master.te_code,te_master.te_name,te_master.te_mobile_no,gift_master.gift_title,gift_master.gift_image from gift_order_master left join engineer_master on gift_order_master.user_id=engineer_master.eid left join te_master on engineer_master.te_code=te_master.te_code left join gift_master on gift_order_master.gift_id=gift_master.id where te_master.zone IN('NE1','NE2','BIHAR','NB1','NB2') order by gift_order_master.g_order_id desc limit 0,100";*/

//echo $sql1;//exit();


$res1 = mysqli_query($conn,$sql1);

$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$the_g_order_id = $row1["g_order_id"];		
		$order_id = $row1["order_id"];		
		
$r_te_code = $row1["te_code"] ? trim($row1["te_code"]) : "";
$r_engineer_id = $row1["user_id"] ? trim($row1["user_id"]) : "";
$r_engineer_email = $row1["user_email"] ? trim($row1["user_email"]) : "";
$r_engineer_name = $row1["e_name"] ? trim($row1["e_name"]) : "";
$r_engineer_mobile = $row1["e_mobile"] ? trim($row1["e_mobile"]) : "";
$r_point_taken = $row1["point_taken"] ? trim($row1["point_taken"]) : "0";
$tds = $row1["tds"] ? trim($row1["tds"]) : "0";
$product_point = $row1["product_point"] ? trim($row1["product_point"]) : $row1["point_taken"];
$r_te_name = $row1["te_name"] ? trim($row1["te_name"]) : "";
$r_te_mobile_no = $row1["te_mobile_no"] ? trim($row1["te_mobile_no"]) : "";
$r_gift_id = $row1["gift_id"] ? trim($row1["gift_id"]) : "";
$r_city = $row1["city"] ? trim($row1["city"]) : "";
$r_pin = $row1["pin"] ? trim($row1["pin"]) : "";
$r_state = $row1["state"] ? trim($row1["state"]) : "";
$r_address = $row1["address"] ? trim($row1["address"]) : "";
$r_status = $row1["status"] ? trim($row1["status"]) : "";
$r_datetime = $row1["datetime"] ? trim($row1["datetime"]) : "";
$user_email = $row1["user_email"] ? trim($row1["user_email"]) : "";
$phone = $row1["phone"] ? trim($row1["phone"]) : "";

$r_delivery_date = $row1["delivery_date"] ? trim($row1["delivery_date"]) : "";
if($r_delivery_date!=""){
$r_delivery_date = str_replace("0000-00-00","",$r_delivery_date);	
}
$r_gift_title = $row1["gift_title"] ? trim($row1["gift_title"]) : "";
$r_gift_image = $row1["gift_image"] ? trim($row1["gift_image"]) : "";

$r_amazon_order_id  = $row1["amazon_order_id"] ? trim($row1["amazon_order_id"]) : "";
$r_amazon_order_link  = $row1["amazon_order_link"] ? trim($row1["amazon_order_link"]) : "";
$is_order_received  = $row1["is_order_received"] ? trim($row1["is_order_received"]) : "NO";
$remarks  = $row1["remarks"] ? trim($row1["remarks"]) : "";

if($r_gift_image!=""){
if(file_exists($img_dir.$r_gift_image)){
$r_gift_image_url = $image_url_prefix.$r_gift_image;
}else{
$r_gift_image_url = "";
}
}else{
$r_gift_image_url = "";
}


if($r_status=="PENDING"){
$stsStyle = "color:#D42A1D;";
}else if($r_status=="DELIVERED"){
$stsStyle = "color:#1C6E00;";
}else{
$stsStyle = "";
}

if($r_datetime!=""){
	$r_datetime = date("dS M, Y",strtotime($r_datetime));
}
?>
<tr>
<td style="width:150px;">
<div style="width:150px;">
<?php if($order_id!=""){echo '<span class="engineerEachField"><b>Order ID:</b> '.$order_id.'</span>';}?>
<?php if($r_engineer_name!=""){echo '<span class="engineerEachField"><b>Name: </b> '.$r_engineer_name.'</span>';}?>
<?php if($r_engineer_email!=""){echo '<span class="engineerEachField"><b>Email: </b> '.$r_engineer_email.'</span>';}?>
<?php if($r_engineer_mobile!=""){echo '<span class="engineerEachField"><b>Mobile:</b> '.$r_engineer_mobile.'</span>';}?>
<?php if($product_point!=""){echo '<span class="engineerEachField"><b>Product Point:</b> '.$product_point.' PT</span>';}?>
<?php if($tds!=""){echo '<span class="engineerEachField"><b>TDS Point:</b> '.$tds.' PT</span>';}?>
<?php if($r_point_taken!=""){echo '<span class="engineerEachField"><b>Point Reedem:</b> '.$r_point_taken.' PT</span>';}?>
<?php if($r_datetime!=""){echo '<span class="engineerEachField"><b>Ordered On:</b></span><span class="engineerEachField">'.$r_datetime.'</span>';}?>
<?php
if($r_status=="DELIVERED"){
echo '<span class="engineerEachField"><b>Order Received:</b></span><span class="engineerEachField">'.$is_order_received.'</span>';	
}
?>
</div>
</td>
<td style="width:150px;">
<div style="width:150px;">
<?php if($r_te_name!=""){echo '<span class="teEachField"><b>Name: </b> '.$r_te_name.'</span>';}?>
<?php if($r_te_mobile_no!=""){echo '<span class="teEachField"><b>Mobile:</b> '.$r_te_mobile_no.'</span>';}?>
<?php if($r_te_code!=""){echo '<span class="teEachField"><b>Code:</b> '.$r_te_code.'</span>';}?>
</div>
</td>

<td style="width:200px;">
<div style="width:200px;">
<?php if($r_city!=""){echo '<span class="contactPersonEachField"><b>Email:</b> '.$user_email.'</span>';}?>
<?php if($r_city!=""){echo '<span class="contactPersonEachField"><b>Phone:</b> '.$phone.'</span>';}?>
<?php if($r_city!=""){echo '<span class="contactPersonEachField"><b>City:</b> '.$r_city.'</span>';}?>
<?php if($r_pin!=""){echo '<span class="contactPersonEachField"><b>Pin:</b> '.$r_pin.'</span>';}?>
<?php if($r_state!=""){echo '<span class="contactPersonEachField"><b>State:</b> '.$r_state.'</span>';}?>
<?php if($r_address!=""){echo '<span class="contactPersonEachField"><b>Address:</b></span><span class="contactPersonEachField">'.$r_address.'</span>';}?>
</div>
</td>
<td style="width:150px;">
<div style="width:150px;">
<?php
if($r_gift_image_url!=""){
?>
<img src="<?php echo $r_gift_image_url;?>" class="prfl_img" />
<?php
}else{
	echo "Not set yet";
}
?>
<?php if($r_gift_title!=""){echo '<span class="gtitleEachField">'.$r_gift_title.'</span>';}?>
</div>
</td>
<td>
<div>
<span class="adminActivityField">
<input type="hidden" id="admin_old_status_<?php echo $the_g_order_id;?>" value="<?php echo $r_status;?>" />
<select class="form-control admin_sl_ord_sts" id="admin_sl_ord_sts_<?php echo $the_g_order_id;?>">

<?php
if (count($order_status_arr) > 0) {
	foreach ($order_status_arr as $order_status_arr_val2) {
		$isSelected = ($order_status_arr_val2 == $r_status) ? 'selected="selected"' : '';
		
		// Disable only if it's one of the special statuses AND not currently selected
		$isDisabled = '';
		if (($order_status_arr_val2 == 'ACKNOWLEDGEMENT OF DELIVERY' || $order_status_arr_val2 == 'COMPLAINT/FEEDBACK') 
		    && $order_status_arr_val2 != $r_status) {
			$isDisabled = 'disabled';
		}
		?>
		<option value="<?php echo $order_status_arr_val2; ?>" <?php echo $isSelected . ' ' . $isDisabled; ?>>
			<?php echo $order_status_arr_val2; ?>
		</option>
	<?php 
	}
}
?>
</select>
</span>
<span class="adminActivityField">
<?php 
	//if($r_status!='PENDING' ){
?>
<input type="text" class="datepicker form-control admin_del_dt" id="admin_del_dt_<?php echo $the_g_order_id;?>" value="<?php echo $r_delivery_date;?>" placeholder="Choose Delivery Date">
</span>
<?php // } ?>
	<?php 
	$read_only='';
	if($r_status=='DELIVERED'){
		$read_only="readonly";
	}
?>
<span class="adminActivityField">
<input type="text" class="form-control" id="admin_amz_ord_id_<?php echo $the_g_order_id;?>" value="<?php echo $r_amazon_order_id;?>" placeholder="Order Id" <?php echo $read_only;?>  >
</span>
<span class="adminActivityField">
<input type="text" class="form-control" id="admin_amz_ord_link_<?php echo $the_g_order_id;?>" value="<?php echo $r_amazon_order_link;?>" placeholder="Order Link" <?php echo $read_only;?> >
</span>
<span class="adminActivityField">
<input type="text" class="form-control" id="admin_remarks_<?php echo $the_g_order_id;?>" value="<?php echo $remarks;?>" 		placeholder="Remarks" <?php echo $read_only;?> >
</span>
<span class="adminActivityField">
	<?php 
	if($r_status=='REJECT' || $r_status=='UNDELIVERED'){

	}else{
?>
<a href="javascript:void(0);" class="btn bg-red waves-effect admin_update_order_data" the_ord_id="<?php echo $the_g_order_id;?>">Update</a>
<?php } ?>
<?php if($r_status=='ACKNOWLEDGEMENT OF DELIVERY' || $r_status=='COMPLAINT/FEEDBACK'){ ?>
<button type="button" class="btn btn-info btn-sm view-ack" data-orderid="<?php echo $order_id; ?>">
    View Acknowledgement
</button>
<?php } 
if($r_status=='COMPLAINT/FEEDBACK'){ 
?>
<button type="button" class="btn btn-warning btn-sm view-feedback" data-orderid="<?php echo $order_id; ?>">
    View Feedback
</button>
<?php } 

?>
<strong id="admin_ord_upd_ldr_<?php echo $the_g_order_id;?>" class="admin_ord_upd_ldr"></strong>
</span>
</div>
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
	<!-- Dynamic Modal -->
<div class="modal fade" id="dynamicModal" tabindex="-1" role="dialog" aria-labelledby="dynamicModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="dynamicModalLabel">Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div id="modalContent">Loading...</div>
      </div>
    </div>
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
		var admin_old_status = jQuery("#admin_old_status_"+the_ord_id).val();
		var admin_del_dt = jQuery("#admin_del_dt_"+the_ord_id).val();
		var admin_amz_ord_id = encodeURIComponent(jQuery.trim(jQuery("#admin_amz_ord_id_"+the_ord_id).val()));
		var admin_amz_ord_link = encodeURIComponent(jQuery.trim(jQuery("#admin_amz_ord_link_"+the_ord_id).val()));
		var admin_remarks = jQuery("#admin_remarks_"+the_ord_id).val();

		//  Prevent update if old status is REJECT or UNDELIVERED
        if (admin_old_status === "REJECT" || admin_old_status === "UNDELIVERED") {
            alert("Point reverted back. You can’t update this order.");
            return false;

        }
		//  Validation checks before proceeding
        if (admin_sl_ord_sts === "ORDER PLACED") {
            if (admin_amz_ord_id === "" || admin_amz_ord_link === "") {
                alert("Order ID and Order Link are mandatory for ORDER PLACED status.");
                return false;
            }
        }

        if (admin_sl_ord_sts === "DELIVERED") {
            if (admin_del_dt === "") {
                alert("Delivery Date is mandatory for DELIVERED status.");
                return false;
            }
        }
		if (admin_sl_ord_sts === "REJECT") {
            if (admin_remarks === "") {
                alert("Remarks is mandatory for REJECT status.");
                return false;
				
            }
        }
		element_admin_ord_upd_ldr.html(imgs);
		jQuery.ajax({
				url: 'ajax_update_order_data_by_admin.php',
				type: 'post',
				dataType: 'json',
				data: "the_ord_id="+the_ord_id+"&admin_sl_ord_sts="+admin_sl_ord_sts+"&admin_del_dt="+admin_del_dt+"&admin_amz_ord_id="+admin_amz_ord_id+"&admin_old_status="+admin_old_status+"&admin_amz_ord_link="+admin_amz_ord_link+"&admin_remarks="+admin_remarks,
				success: function(response){				
				if(response.process_sts=="YES"){					
					element_admin_ord_upd_ldr.html(done_img);
					jQuery(".admin_update_order_data").prop("disabled", true);
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
<script>
$(document).ready(function(){

  // View Acknowledgement button click
  $('.view-ack').click(function(){
      var orderId = $(this).data('orderid');
      $('#dynamicModalLabel').text('Acknowledgement for Order ID: ' + orderId);
      $('#modalContent').html('Loading...');
      $('#dynamicModal').modal('show');

      $.ajax({
          url: 'fetch_acknowledgement.php',
          type: 'POST',
          data: { order_id: orderId },
          success: function(response){
              $('#modalContent').html(response);
          },
          error: function(){
              $('#modalContent').html('<p class="text-danger">Error loading acknowledgement data.</p>');
          }
      });
  });

  // View Feedback button click
  $('.view-feedback').click(function(){
      var orderId = $(this).data('orderid');
      $('#dynamicModalLabel').text('Feedback for Order ID: ' + orderId);
      $('#modalContent').html('Loading...');
      $('#dynamicModal').modal('show');

      $.ajax({
          url: 'fetch_feedback.php',
          type: 'POST',
          data: { order_id: orderId },
          success: function(response){
              $('#modalContent').html(response);
          },
          error: function(){
              $('#modalContent').html('<p class="text-danger">Error loading feedback data.</p>');
          }
      });
  });

});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>