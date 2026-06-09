<?php
session_start();
include "web_check.php";
ini_set('memory_limit', '999M');
set_time_limit(0);
include "star_connection.php";
$branch_master = "branch_master";
$current_date = date("Y-m-d");
$yesterday_date = date('Y-m-d',strtotime("-1 days"));
$frm_hrs = "00:00:00";
$to_hrs = "23:59:59";
function get_branch_names_by_ids($conn,$bids){
$pnms = "";
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
$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}
$get_fetch_type = $_GET["get_type"] ? trim($_GET["get_type"]) : "";

$trn_te_code = $_GET["trn_te_code"] ? addslashes(trim($_GET["trn_te_code"])) : "";
$srch_eng_dtls = $_GET["srch_eng_dtls"] ? addslashes(trim($_GET["srch_eng_dtls"])) : "";
$sl_activity_status = $_GET["sl_activity_status"] ? addslashes(trim($_GET["sl_activity_status"])) : "";
$sl_dlr_logedin_type = $_GET["sl_dlr_logedin_type"] ? addslashes(trim($_GET["sl_dlr_logedin_type"])) : "";
$sl_status_by_te = $_GET["sl_status_by_te"] ? addslashes(trim($_GET["sl_status_by_te"])) : "";
$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";


startCreatEngineerMasterCsvfile($conn,$get_fetch_type,$trn_te_code,$srch_eng_dtls,$sl_activity_status,$sl_dlr_logedin_type,$sl_status_by_te,$sl_day_wise,$from_dt,$to_dt);


function startCreatEngineerMasterCsvfile($conn,$get_fetch_type,$trn_te_code,$srch_eng_dtls,$sl_activity_status,$sl_dlr_logedin_type,$sl_status_by_te,$sl_day_wise,$from_dt,$to_dt){
$server_url = "https://" . $_SERVER['SERVER_NAME']."/";
$img_dir = "../en_profile_pic/";
$default_image_name = "profile.png";
$image_url_prefix = $server_url."en_profile_pic/";
$date_before_three_month = date('Y-m-d H:i:s',strtotime("-3 month"));
$date_before_six_month = date('Y-m-d H:i:s',strtotime("-6 month"));
$date_before_three_month_stamp = strtotime($date_before_three_month);
$date_before_six_month_stamp = strtotime($date_before_six_month);
$date_before_twelve_month = date('Y-m-d H:i:s',strtotime("-12 month"));
$date_before_twelve_month_stamp = strtotime($date_before_twelve_month);
$recommended_site_master = "recommended_site_master";
$engineer_master = "engineer_master";
$te_master = "te_master";
$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}
$curr_date = date("jS_M_Y_h_m_s_A");
$the_file_name = "engineer_master_".$curr_date.".csv";
$output = "";
/*if($data_show_type=="NE"){
$qry = "select $engineer_master.*,$te_master.`zone`,$te_master.`te_name` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where ($te_master.`zone` like '%A%' or $te_master.`zone` like '%B%' or $te_master.`zone` like '%C%' )";
}else if($data_show_type=="OSNE"){
$qry = "select $engineer_master.*,$te_master.`zone`,$te_master.`te_name` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where ($te_master.`zone` like '%D%' or $te_master.`zone` like '%E%' )";
}else{
$qry = "select *,$te_master.`te_name` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` order by `e_name` asc";
}*/
if($get_fetch_type=="notloggedin"){
	$the_file_name = "notloggedin_engineer_".$curr_date.".csv";
	$where_qry = " and ($engineer_master.`device_id`='' or $engineer_master.`device_id` is null) ";
	$where_qry_all = " WHERE $engineer_master.`device_id`='' or $engineer_master.`device_id` is null ";
}else if($get_fetch_type=="loggedin"){
	$the_file_name = "loggedin_engineer_".$curr_date.".csv";
	$where_qry = " and ($engineer_master.`device_id`!='' and $engineer_master.`device_id` is not null) ";
	$where_qry_all = " WHERE $engineer_master.`device_id` !='' or $engineer_master.`device_id` is not null ";
}else{
	$the_file_name = "engineer_master_".$curr_date.".csv";
	$where_qry = "";
	
	$where_qry_all ="";
	$status_te_qry = "";


$whr_str = "";
$whr_rs_str = "";
$msg_txt = "";
$search_array = array("trn_te_code"=>$trn_te_code,"sl_status_by_te"=>$sl_status_by_te,"srch_eng_dtls"=>$srch_eng_dtls,"sl_activity_status"=>$sl_activity_status,"data_show_type"=>$data_show_type,"sl_dlr_logedin_type"=>$sl_dlr_logedin_type,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="trn_te_code"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand $engineer_master.`te_code`='$search_array_val' ";
$whr_rs_str = "  where `r_te_code`='$search_array_val'";
		}
	}else if($search_array_key=="sl_status_by_te"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			$whr_str .= "$aand $engineer_master.`status_by_te`='$search_array_val' ";
			
		}		
	}else if($search_array_key=="srch_eng_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand ($engineer_master.`e_name` like '%$search_array_val%' or $engineer_master.`e_mobile` like '%$search_array_val%' ) ";

		}
	}else if($search_array_key=="sl_activity_status"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
	if($search_array_val=="ACTIVE"){	
	$whr_str .= "$aand `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`>='$date_before_twelve_month' ";
	}else if($search_array_val=="INACTIVE"){
	$whr_str .= "$aand `latest_recommended_site_master`.`r_submission_date` is null or (`latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_twelve_month') ";
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
	}else if($search_array_key=="sl_dlr_logedin_type"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			if($search_array_val=='loggedin'){
			$whr_str .= "$aand ($engineer_master.`device_id`!='' and $engineer_master.`device_id` is not null) ";	
			}else if($search_array_val=='notloggedin'){
			$whr_str .= "$aand ($engineer_master.`device_id`='' or $engineer_master.`device_id` is null) ";	
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
			if($the_from_dt!="" && $the_to_dt!=""){
			   $whr_str .= "$aand $engineer_master.`reg_date` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
			}else if($the_from_dt!="" && $the_to_dt==""){
				$whr_str .= "$aand $engineer_master.`reg_date` >= '".$the_from_dt." ".$frm_hrs."' ";
			}else if($the_from_dt=="" && $the_to_dt!=""){
				$whr_str .= "$aand $engineer_master.`reg_date` <= '".$the_to_dt." ".$to_hrs."' ";
			}
		}else{
			if($the_sl_day_wise=="Today"){
				$whr_str .= "$aand $engineer_master.`reg_date` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
			}else if($the_sl_day_wise=="Yesterday"){
				$whr_str .= "$aand $engineer_master.`reg_date` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
			
			}
		}
	}
}

if($whr_str!=""){
	$where_qry_all = "where ".$whr_str;
}else{
	$where_qry_all ="";
}
	
	
}
if($data_show_type=="ALL"){
	
$qry = "select $engineer_master.*,$te_master.`te_code`,$te_master.`te_name`,$te_master.`zone`,`latest_recommended_site_master`.`r_submission_date` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master $whr_rs_str GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` $where_qry_all order by $engineer_master.`reg_date` desc";

/*$qry="select $engineer_master.*,$te_master.`te_code`,$te_master.`te_name`,$te_master.`zone`,`latest_recommended_site_master`.`r_submission_date` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id`  $where_qry_all order by $engineer_master.`reg_date` desc ";*/
}else{

// $qry = "select $engineer_master.*,$te_master.`te_code`,$te_master.`te_name`,$te_master.`zone` from $engineer_master left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` where $te_master.`zone` like '%".$data_show_type."%' $where_qry";

// $qry = "SELECT engineer_master.*,te_master.`te_name`,latest_recommended_site_master.`r_submission_date` FROM
// engineer_master LEFT JOIN te_master ON engineer_master.`te_code` = te_master.`te_code`LEFT JOIN (SELECT `r_engineer_id`,MAX(`r_submission_date`) AS `r_submission_date`FROM recommended_site_master
// GROUP BY `r_engineer_id`) AS latest_recommended_site_master ON engineer_master.`eid` = latest_recommended_site_master.`r_engineer_id`
// WHERE te_master.`zone` LIKE '%NB1' OR te_master.`zone` LIKE '%NB2' OR te_master.`zone` LIKE '%NE1' OR te_master.`zone` LIKE '%NE2' OR te_master.`zone` LIKE '%BIHAR' ORDER BY engineer_master.`reg_date`";

//echo $qry;
// $qry = "SELECT $engineer_master.*, $te_master.`te_code`, $te_master.`te_name`, 
// $te_master.`zone`,`latest_recommended_site_master`.`r_submission_date` FROM $engineer_master LEFT JOIN $te_master ON $engineer_master.`te_code` = $te_master.`te_code` LEFT JOIN (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id`WHERE $te_master.`zone` LIKE '%".$data_show_type."%' $where_qry";

$where_clause = "";
    $zones = explode(",", $data_show_type); // Assuming $data_show_type is a comma-separated string of zones
    
    foreach ($zones as $zone) {
        $zone = trim($zone); // Removing leading/trailing spaces
        if ($where_clause != "") {
            $where_clause .= " OR ";
        }
        $where_clause .= "te_master.`zone` LIKE '%$zone%'";
    }

    $qry = "SELECT $engineer_master.*, $te_master.`te_name`, `latest_recommended_site_master`.`r_submission_date`
            FROM $engineer_master
            LEFT JOIN $te_master ON $engineer_master.`te_code` = $te_master.`te_code`
            LEFT JOIN (
                SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date`
                FROM $recommended_site_master
                GROUP BY `r_engineer_id`
            ) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id`
            WHERE $where_clause
            ORDER BY $engineer_master.`reg_date` DESC";

}

//exit();
$sql = mysqli_query($conn,$qry);

$output .= '"Name","Mobile","Email","Point","Branch","Birthday","Anniversary","LINKED TE Code","LINKED TE Name","Address","Pin","State","City","Zone","App Access Status","Status By TE","Reg Datetime","Activity Status","Login Status","Device type","App Version","Image"';

$output .="\n";
// Get Records from the table

while ($row1 = mysqli_fetch_assoc($sql)) {
$the_eid = $row1["eid"];
$the_e_name = $row1["e_name"] ? str_replace('"', '""', trim($row1["e_name"])) : "";
$the_e_mobile = $row1["e_mobile"];
$the_te_code = $row1["te_code"];
$the_te_name = $row1["te_name"] ? str_replace('"', '""', trim($row1["te_name"])) : "";
$the_e_email = $row1["e_email"];
$the_e_dob = $row1["e_dob"] ? trim($row1["e_dob"]) : "";
if($the_e_dob!=""){
$the_e_dob = date("Y-m-d",strtotime($the_e_dob));	
}
$the_e_dom = $row1["e_dom"] ? trim($row1["e_dom"]) : "";
if($the_e_dom!=""){
$the_e_dom = date("Y-m-d",strtotime($the_e_dom));	
}
$the_e_address = $row1["e_address"] ? str_replace('"', '""', trim($row1["e_address"])) : "";
$the_e_pin = $row1["e_pin"];
$the_e_state = $row1["e_state"];
$the_e_city_town = $row1["e_city_town"];
$the_e_zone = $row1["e_zone"];

$the_e_status = $row1["status"] ? trim($row1["status"]) : "";
$the_status_by_te = $row1["status_by_te"] ? trim($row1["status_by_te"]) : "";

$the_e_points = $row1["e_points"] ? trim($row1["e_points"]) : "";
$the_branch_code_selected = $row1["branch_code"] ? trim($row1["branch_code"]) : "";
if($the_branch_code_selected!=""){
$the_branch_code_selected = get_branch_names_by_ids($conn,$the_branch_code_selected);
}else{
$the_branch_code_selected = "";	
}
$the_reg_date_time = $row1["reg_date"] ? trim($row1["reg_date"]) : "";
$r_submission_date = $row1["r_submission_date"] ? trim($row1["r_submission_date"]) : "";
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

if($r_submission_date!=""){
$the_date_time_stamp = strtotime($r_submission_date);
	if($the_date_time_stamp>=$date_before_twelve_month_stamp){
	$e_sts_text = "Site Recommendation within last one year";
	}else{
	$e_sts_text = "Site Recommendation not done within last one year";
	}
}else{
$e_sts_text = "Site Recommendation not done within last one year";	
}
	
$device_type = $row1["device_type"];
$the_app_version = $row1["app_version"] ? trim($row1["app_version"]) : "";
$device_id = $row1["device_id"];
if($device_id!='') $logged_in='Y';
else 				$logged_in='N';	

$output .= '"'.$the_e_name.'","'.$the_e_mobile.'","'.$the_e_email.'","'.$the_e_points.'","'.$the_branch_code_selected.'","'.$the_e_dob.'","'.$the_e_dom.'","'.$the_te_code.'","'.$the_te_name.'","'.$the_e_address.'","'.$the_e_pin.'","'.$the_e_state.'","'.$the_e_city_town.'","'.$the_e_zone.'","'.$the_e_status.
'","'.$the_status_by_te.'","'.$the_reg_date_time.'","'.$e_sts_text.'","'.$logged_in.'","'.$device_type.'","'.$the_app_version.'","'.$the_e_profile_image_url.'"';
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
}

mysqli_close($conn);
?>