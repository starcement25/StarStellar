<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
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

$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
$new_qry_string_filtered = "";
$get_fetch_type = $_GET["get_type"] ? trim($_GET["get_type"]) : "all";
$trn_branch_id = $_GET["trn_branch_id"] ? addslashes(trim($_GET["trn_branch_id"])) : "";
$ds_code = $_GET["ds_code"] ? addslashes(trim($_GET["ds_code"])) : "";
if($get_fetch_type=="pending"){
	$sl_ord_sts = "Order received";
}else{
	$sl_ord_sts = "";
}

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


$t_apperpdo = "T_APPERPDO";
$customer_master = "customer_master";
$employee_master = "employee_master";
$branch_master = "branch_master";
$order_show_branch="";

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
	
	
	
	
}


$get_fetch_type = $get_fetch_type ? strtolower(trim($get_fetch_type)) : "all";
$curr_date = date("jS_M_Y_h_m_s_A");
if($get_fetch_type=="pending"){
	$the_file_name = "pending_orders_".$curr_date.".csv";
}else{
	$the_file_name = "all_orders_".$curr_date.".csv";
}
$output = "";
if($_SESSION["start_user_type"]=="MANAGER" && ($order_show_branch=="NE" || $order_show_branch=="NOTNE")){
$qry = "select $t_apperpdo.*,$employee_master.`branch_code` from $t_apperpdo left join $employee_master on $t_apperpdo.`dns_customer_code`=$employee_master.`dns_emp_code` where $t_apperpdo.`APPORDERNO`!='' and $employee_master.`branch_code` in('".$theactbcstr."') $new_whr_str order by $t_apperpdo.`order_date` asc";
}else{
$qry = "select $t_apperpdo.*,$employee_master.`branch_code` from $t_apperpdo left join $employee_master on $t_apperpdo.`dns_customer_code`=$employee_master.`dns_emp_code` where $t_apperpdo.`APPORDERNO`!='' $new_whr_str order by `order_date` asc";
}
$sql = mysqli_query($conn,$qry);
$columns_total = mysqli_num_fields($sql);

// Get The Field Name

$output .= '"APPORDERNO","ERPORDERNO","DATE","Branch_Name","Cust_Code","Customer_Name","Consignee_Name","Consignee_Address","Destination","Product_Name","qty(MT)","STATUS"';
$output .="\n";
// Get Records from the table

while ($row = mysqli_fetch_array($sql)) {
$apporder_no = $row["APPORDERNO"];
$erporder_no = $row["ERPORDERNO"];
$order_date = $row["order_date"];
$customer_branch_code = $row["branch_code"];
$customer_branch_name = get_branch_name_from_id($conn,$customer_branch_code);
$dns_customer_code = $row["dns_customer_code"];
$customer_code = $row["customer_code"];
$customer_name = get_customer_name_from_id($conn,$customer_code);
$consignee_name = "";
$consignee_address = "";
$consignee_address_arr = array();
$order_for = $row["order_for"] ? trim($row["order_for"]) : "";
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
$consignee_name = $consignee_name ? str_replace('"', '""',$consignee_name) : "";
$consignee_address = $consignee_address ? str_replace('"', '""',$consignee_address) : "";
$prod_code = $row["prod_code"];
$prod_display_name = $row["prod_display_name"];
$prod_qty = $row["QTY"];
$destination_name = $row["destination_name"];
$destination_name = $destination_name ? str_replace('"', '""',$destination_name) : "";
$prod_code = $row["prod_code"];
$prod_display_name = $row["prod_display_name"];
$prod_display_name = $prod_display_name ? str_replace('"', '""',$prod_display_name) : "";
$prod_qty = $row["QTY"];
$order_status = $row["STATUS"];
$output .= '"'.$apporder_no.'","'.$erporder_no.'","'.$order_date.'","'.$customer_branch_name.'","'.$dns_customer_code.'","'.$customer_name.'","'.$consignee_name.'","'.$consignee_address.'","'.$destination_name.'","'.$prod_display_name.'","'.$prod_qty.'","'.$order_status.'"';

$output .="\n";
}

// Download the file

$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename='.$filename);
header('Pragma: no-cache');    
header('Expires: 0');
echo $output;
exit;

mysqli_close($conn);
?>