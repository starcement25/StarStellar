<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$start_user_type = $_SESSION["start_user_type"];
$order_query = "order_query";
$engineer_master = "engineer_master";
$te_master= "te_master";
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "order_query_report_" . $curr_date . ".csv";
$new_qry_string_filtered = "";
$export_filtered_str = "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";
$srch_order_dtls = $_GET["srch_order_dtls"] ? addslashes(trim($_GET["srch_order_dtls"])) : "";
$whr_str = "";
$export_filtered_str = "";
$search_array = array("srch_order_dtls"=>$srch_order_dtls,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_order_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand ($order_query.`order_id` like '%$search_array_val%' or $order_query.`linked_te_code` like '%$search_array_val%'  or $customer_master.`customer_name` like '%$search_array_val%' or $order_query.`prod_name` like '%$search_array_val%') ";
			$new_qry_string_filtered .= "&srch_cust_dtls=".$search_array_val;
		}
	}
	else if($search_array_key=="daywise"){
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
			   $whr_str .= "$aand $order_query.`date_and_time` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			   $new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
				}
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $order_query.`date_and_time` >= '".$the_from_dt." ".$frm_hrs."' ";
				$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}else{
				$export_filtered_str .= "&from_dt=".$the_from_dt;
				}
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $order_query.`date_and_time` <= '".$the_to_dt." ".$to_hrs."' ";
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
				$whr_str .= "$aand $order_query.`date_and_time` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
				if($export_filtered_str!=""){
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}else{
				$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
				}
				$whr_str .= "$aand $order_query.`date_and_time` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}
if($whr_str!=""){
	$new_whr_str = "and ".$whr_str;
}else{
	$new_whr_str ="";
}

$sql_pg ="select $order_query.*,$engineer_master.`e_name`,$te_master.te_name from $order_query left join $engineer_master on $order_query.`engineer_code`=$engineer_master.`eid` left join $te_master ON $order_query.`linked_te_code`=$te_master.te_code where $order_query.`order_id`!=''   $new_whr_str order by $order_query.`id` asc";
$qry = $sql_pg;
$sql = mysqli_query($conn,$qry);
// Get The Field Name
$slno_cnt = 1;
$output .= '"SL No","Order Id","DATE TIME","ENGINEER NAME","Linked TE Code","Linked TE Name","Prod name","Date of Lifting","Quantity in Bags","Remarks","Status","Status Remarks"';
$output .= "\n";
// Get Records from the table
while ($row1 = mysqli_fetch_array($sql)) {
		$sl_no = $row1["id"];
		$order_id = $row1["order_id"];
		$date_and_time = $row1["date_and_time"];
		$e_name = $row1["e_name"];
		$te_name = $row1["te_name"];
		$linked_te_code = $row1["linked_te_code"];
		$engineer_code = $row1["engineer_code"];
		$prod_name = $row1["prod_name"];
		$qty_bags = $row1["qty_bags"];
		$date_of_lifting = $row1["date_of_lifting"];
		$remarks = $row1["remarks"];
		$status_from_app = $row1["status_from_app"];
		$status_remarks = $row1["status_remarks"];
		
		$sqlsite = "SELECT r_site_name FROM `recommended_site_master` WHERE `r_te_code` = '".$linked_te_code."' AND `r_engineer_id` = '".$engineer_code."'";	
		$ressite = mysqli_query($conn,$sqlsite);
		$rowsite= mysqli_fetch_assoc($ressite);
		$r_site_name=$rowsite['r_site_name'];

	$output .= '"' .$sl_no. '","' .$order_id. '","' .$date_and_time. '","' . $e_name . '","' . $linked_te_code . '","' . $te_name . '","' . $prod_name . '","' . $date_of_lifting . '","' . $qty_bags . '","' . $remarks . '","' . $status_from_app . '","' . $status_remarks . '"';
	$output .= "\n";
	$slno_cnt++;
}
// Download the file
$filename = $the_file_name;
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=' . $filename);
header('Pragma: no-cache');
header('Expires: 0');
echo $output;
exit;
mysql_close();
?>
