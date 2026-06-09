<?php
	include "web_check.php";
	ini_set('memory_limit', '999M');
	set_time_limit(0);
	include "star_connection.php";
	$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
	$the_access_user_type = $_SESSION["start_stellar_user_type"];
	if($the_access_user_type=="MANAGER")
	{
		$data_show_type = $the_data_show_type;
	}
	else
	{
		$data_show_type = "ALL";
	}
	$trn_te_code = $_GET["trn_te_code"] ? addslashes(trim($_GET["trn_te_code"])) : "";
	$sl_en_id = $_GET["sl_en_id"] ? addslashes(trim($_GET["sl_en_id"])) : "";
	$srch_eng_te_site_dtls = $_GET["srch_eng_te_site_dtls"] ? addslashes(trim($_GET["srch_eng_te_site_dtls"])) : "";
	$sl_ord_sts = $_GET["sl_ord_sts"] ? addslashes(trim($_GET["sl_ord_sts"])) : "";
	$sl_day_wise = $_GET["sl_day_wise"] ? addslashes(trim($_GET["sl_day_wise"])) : "";
	$from_dt = $_GET["from_dt"] ? addslashes(trim($_GET["from_dt"])) : "";
	$to_dt = $_GET["to_dt"] ? addslashes(trim($_GET["to_dt"])) : "";

	startCreatOrderMasterCsvfile($conn,$trn_te_code,$sl_en_id,$srch_eng_te_site_dtls,$sl_ord_sts,$sl_day_wise,$from_dt,$to_dt);

	function get_branch_names_by_ids($conn,$bids)
	{
		$pnms = "";
		$nmsarr = array();
		$selected_pids_arr = array();
		$branch_master = "branch_master";
		$bids = $bids ? trim($bids) : "" ;
		if($bids!="")
		{
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


	function startCreatOrderMasterCsvfile($conn,$trn_te_code,$sl_en_id,$srch_eng_te_site_dtls,$sl_ord_sts,$sl_day_wise,$from_dt,$to_dt)
	{
		$gift_master = "gift_master";
		$gift_order_master = "gift_order_master";
		$te_master = "te_master";
		$engineer_master = "engineer_master";
		$curr_date = date("jS_M_Y_h_m_s_A");
		$the_file_name = "order_master_".$curr_date.".csv";
		$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
		$the_access_user_type = $_SESSION["start_stellar_user_type"];
		if($the_access_user_type=="MANAGER")
		{
			$data_show_type = $the_data_show_type;
		}
		else
		{
			$data_show_type = "ALL";
		}
		$output = "";

		$current_date = date("Y-m-d");
		$yesterday_date = date('Y-m-d',strtotime("-1 days"));
		$frm_hrs = "00:00:00";
		$to_hrs = "23:59:59";

		$search_array = array("trn_te_code"=>$trn_te_code,"sl_en_id"=>$sl_en_id,"srch_eng_te_site_dtls"=>$srch_eng_te_site_dtls,"sl_ord_sts"=>$sl_ord_sts,"data_show_type"=>$data_show_type,"daywise"=>array("sl_day_wise"=>$sl_day_wise,"from_dt"=>$from_dt,"to_dt"=>$to_dt));
		foreach($search_array as $search_array_key=>$search_array_val)
		{
			if($search_array_key=="sl_ord_sts")
			{
				if($search_array_val!='')
				{
					if(trim($whr_str)!="")
					{
						$aand = " and";
					}
					else
					{
						$aand = "";
					}
					
					$whr_str .= "$aand $gift_order_master.`status`='$search_array_val' ";
					
					$new_qry_string_filtered .= "&sl_ord_sts=".$search_array_val;
					if($export_filtered_str!="")
					{
						$export_filtered_str .= "&sl_ord_sts=".$search_array_val;
					}
					else
					{
						$export_filtered_str .= "&sl_ord_sts=".$search_array_val;
					}	
					
				}	
			}
			else if($search_array_key=="data_show_type")
			{
				if($search_array_val!='')
				{
					if(trim($whr_str)!="")
					{
						$aand = " and";
					}
					else
					{
						$aand = "";
					}
					/*if($search_array_val=='NE'){
						$whr_str .= "$aand ($te_master.`zone` like '%A%' or $te_master.`zone` like '%B%' or $te_master.`zone` like '%C%' ) ";
					}else if($search_array_val=='OSNE'){
						$whr_str .= "$aand ($te_master.`zone` like '%D%' or $te_master.`zone` like '%E%' ) ";
					}*/
					if($search_array_val!='ALL')
					{
						$whr_str .= "$aand $te_master.`zone` like '%".$search_array_val."%' ";
					}
					
				}
			}
			else if($search_array_key=="trn_te_code")
			{
				if($search_array_val!='')
				{
					if(trim($whr_str)!="")
					{
						$aand = " and";
					}
					else
					{
						$aand = "";
					}
					$whr_str .= "$aand $engineer_master.`te_code`='$search_array_val' ";	
					$new_qry_string_filtered .= "&trn_te_code=".$search_array_val;
					if($export_filtered_str!="")
					{
						$export_filtered_str .= "&trn_te_code=".$search_array_val;
					}
					else
					{
						$export_filtered_str .= "&trn_te_code=".$search_array_val;
					}		
				}		
			}
			else if($search_array_key=="sl_en_id")
			{
				if($search_array_val!='')
				{
					if(trim($whr_str)!="")
					{
						$aand = " and";
					}
					else
					{
						$aand = "";
					}
					$whr_str .= "$aand $gift_order_master.`user_id`='$search_array_val' ";	
					$new_qry_string_filtered .= "&sl_en_id=".$search_array_val;
					if($export_filtered_str!="")
					{
						$export_filtered_str .= "&sl_en_id=".$search_array_val;
					}
					else
					{
						$export_filtered_str .= "&sl_en_id=".$search_array_val;
					}	
				}		
			}
			else if($search_array_key=="srch_eng_te_site_dtls")
			{
				if($search_array_val!='')
				{
					if(trim($whr_str)!="")
					{
						$aand = " and";
					}
					else
					{
						$aand = "";
					}
					
					$whr_str .= "$aand ($engineer_master.`e_name` like '%$search_array_val%' or $engineer_master.`te_code` like '%$search_array_val%' or $engineer_master.`e_mobile` like '%$search_array_val%' or $te_master.`te_name` like '%$search_array_val%' or $te_master.`te_mobile_no` like '%$search_array_val%' or $gift_master.`gift_title` like '%$search_array_val%' or $gift_order_master.`g_order_id` like '%$search_array_val%' or $gift_order_master.`address` like '%$search_array_val%' or $gift_order_master.`city` like '%$search_array_val%' or $gift_order_master.`pin` like '%$search_array_val%' or $gift_order_master.`state` like '%$search_array_val%' )";
					
					$new_qry_string_filtered .= "&srch_eng_te_site_dtls=".$search_array_val;
					if($export_filtered_str!="")
					{
						$export_filtered_str .= "&srch_eng_te_site_dtls=".$search_array_val;
					}
					else
					{
						$export_filtered_str .= "&srch_eng_te_site_dtls=".$search_array_val;
					}	
				}		
			}
			else if($search_array_key=="daywise")
			{
				$the_sl_day_wise = $search_array_val["sl_day_wise"];
				$the_from_dt = $search_array_val["from_dt"];
				$the_to_dt = $search_array_val["to_dt"];
				if(trim($whr_str)!="")
				{
					$aand = " and";
				}
				else
				{
					$aand = "";
				}
				if($the_sl_day_wise=="Date_Range")
				{
					$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
					if($export_filtered_str!="")
					{
						$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
					}
					else
					{
						$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
					}
					if($the_from_dt!="" && $the_to_dt!="")
					{
						$whr_str .= "$aand $gift_order_master.`datetime` between '".$the_from_dt." ".$frm_hrs."' and '".$the_to_dt." ".$to_hrs."' ";
						$new_qry_string_filtered .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
						if($export_filtered_str!="")
						{
							$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
						}
						else
						{
							$export_filtered_str .= "&from_dt=".$the_from_dt."&to_dt=".$the_to_dt;
						}
					}
					elseif($the_from_dt!="" && $the_to_dt=="")
					{
						$whr_str .= "$aand $gift_order_master.`datetime` >= '".$the_from_dt." ".$frm_hrs."' ";
						$new_qry_string_filtered .= "&from_dt=".$the_from_dt;
						if($export_filtered_str!="")
						{
							$export_filtered_str .= "&from_dt=".$the_from_dt;
						}
						else
						{
							$export_filtered_str .= "&from_dt=".$the_from_dt;
						}
					}
					elseif($the_from_dt=="" && $the_to_dt!="")
					{
						$whr_str .= "$aand $gift_order_master.`datetime` <= '".$the_to_dt." ".$to_hrs."' ";
						$new_qry_string_filtered .= "&to_dt=".$the_to_dt;
						if($export_filtered_str!="")
						{
							$export_filtered_str .= "&to_dt=".$the_to_dt;
						}
						else
						{
							$export_filtered_str .= "&to_dt=".$the_to_dt;
						}
					}
				}
				else
				{
					if($the_sl_day_wise=="Today")
					{
						$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
						if($export_filtered_str!="")
						{
							$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
						}
						else
						{
							$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
						}
						$whr_str .= "$aand $gift_order_master.`datetime` between '".$current_date." ".$frm_hrs."' and '".$current_date." ".$to_hrs."' ";
					}
					else if($the_sl_day_wise=="Yesterday")
					{
						$new_qry_string_filtered .= "&sl_day_wise=".$the_sl_day_wise;
						if($export_filtered_str!="")
						{
							$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
						}
						else
						{
							$export_filtered_str .= "&sl_day_wise=".$the_sl_day_wise;
						}
						$whr_str .= "$aand $gift_order_master.`datetime` between '".$yesterday_date." ".$frm_hrs."' and '".$yesterday_date." ".$to_hrs."' ";
					
					}
				}
			}
		}


		if($whr_str!="")
		{
			$new_whr_str = " where ".$whr_str;
		}
		else
		{
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

		/*if($data_show_type=='NE'){
		$qry = "select $gift_order_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no`,$gift_master.`gift_title`,$gift_master.`gift_image` from $gift_order_master left join $engineer_master on $gift_order_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join $gift_master on $gift_order_master.`gift_id`=$gift_master.`id` where ($te_master.`zone` like '%A%' or $te_master.`zone` like '%B%' or $te_master.`zone` like '%C%' ) order by $gift_order_master.`g_order_id` desc";
		}else if($data_show_type=='OSNE'){
		$qry = "select $gift_order_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no`,$gift_master.`gift_title`,$gift_master.`gift_image` from $gift_order_master left join $engineer_master on $gift_order_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join $gift_master on $gift_order_master.`gift_id`=$gift_master.`id` where ($te_master.`zone` like '%D%' or $te_master.`zone` like '%E%' ) order by $gift_order_master.`g_order_id` desc";
		}else{
		$qry = "select $gift_order_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no`,$gift_master.`gift_title`,$gift_master.`gift_image` from $gift_order_master left join $engineer_master on $gift_order_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join $gift_master on $gift_order_master.`gift_id`=$gift_master.`id` $new_whr_str order by $gift_order_master.`g_order_id` desc";
		}*/

		if($data_show_type=="ALL")
		{
			$qry = "select $gift_order_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`branch_code`,$te_master.`te_mobile_no`,$gift_master.`gift_title`,$gift_master.`gift_image` from $gift_order_master left join $engineer_master on $gift_order_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join $gift_master on $gift_order_master.`gift_id`=$gift_master.`id` $new_whr_str order by $gift_order_master.`g_order_id` desc";
		} 
		else 
		{

			$where_clause = "";
			$zones = explode(",", $data_show_type); // Assuming $data_show_type is a comma-separated string of zones

			foreach ($zones as $zone) 
			{
				$zone = trim($zone);
				if ($where_clause != "") 
				{
					$where_clause .= " OR ";
				}
				$where_clause .= "te_master.`zone` LIKE '%$zone%'";
			}

			if ($where_clause != "") 
			{
				$where_clause = "WHERE " . $where_clause;
			}

			$qry = "SELECT gift_order_master.*, engineer_master.`e_name`, engineer_master.`e_mobile`, engineer_master.`te_code`, te_master.`te_name`, te_master.`branch_code`, te_master.`te_mobile_no`, gift_master.`gift_title`, gift_master.`gift_image` 
			FROM gift_order_master LEFT JOIN engineer_master ON gift_order_master.`user_id` = engineer_master.`eid` LEFT JOIN te_master ON engineer_master.`te_code` = te_master.`te_code` LEFT JOIN gift_master ON gift_order_master.`gift_id` = gift_master.`id` $where_clause ORDER BY gift_order_master.`g_order_id` DESC";

		}


		// $qry = "select $gift_order_master.*,$engineer_master.`e_name`,$engineer_master.`e_mobile`,$engineer_master.`te_code`,$te_master.`te_name`,$te_master.`te_mobile_no`,$gift_master.`gift_title`,$gift_master.`gift_image` from $gift_order_master left join $engineer_master on $gift_order_master.`user_id`=$engineer_master.`eid` left join $te_master on $engineer_master.`te_code`=$te_master.`te_code` left join $gift_master on $gift_order_master.`gift_id`=$gift_master.`id` $new_whr_str order by $gift_order_master.`g_order_id` desc";
		//echo $qry;

		$sql = mysqli_query($conn,$qry);

		$output .= '"Order Id","Old Order Id","Amazon Order Id","Amazon Order Link","Item","Ordered On","Order Received","Product Point","TDS","Point Reedem","Delivery Date","Status","Engineer Name","Engineer Phn No","Engineer Email","City","Pin","State","Address","TE Code","TE Name","TE Branch Code","TE Branch Name","Remarks"';

		$output .="\n";
		// Get Records from the table

		while ($row1 = mysqli_fetch_assoc($sql)) 
		{
			$the_g_order_id = $row1["g_order_id"];		
			$order_id = $row1["order_id"];		
					
			$r_te_code = $row1["te_code"] ? trim($row1["te_code"]) : "";
			$r_te_name = $row1["te_name"] ? trim($row1["te_name"]) : "";

			$r_te_branch_name = "";
			$r_te_branch_code = $row1["branch_code"] ? trim($row1["branch_code"]) : "";
			if($r_te_branch_code!="")
			{
				$r_te_branch_name = get_branch_names_by_ids($conn,$r_te_branch_code);
			}


			$r_engineer_id = $row1["user_id"] ? trim($row1["user_id"]) : "";
			$r_engineer_name = $row1["e_name"] ? trim($row1["e_name"]) : "";
			$r_engineer_mobile = $row1["e_mobile"] ? trim($row1["e_mobile"]) : "";
			$r_engineer_email = $row1["user_email"] ? trim($row1["user_email"]) : "N\A";
			$r_point_taken = $row1["point_taken"] ? trim($row1["point_taken"]) : "0";
			$tds = $row1["tds"] ? trim($row1["tds"]) : "";
			$product_point = $row1["product_point"] ? trim($row1["product_point"]) : "";
			$r_te_name = $row1["te_name"] ? trim($row1["te_name"]) : "";
			$r_te_mobile_no = $row1["te_mobile_no"] ? trim($row1["te_mobile_no"]) : "";
			$r_gift_id = $row1["gift_id"] ? trim($row1["gift_id"]) : "";
			$r_city = $row1["city"] ? trim($row1["city"]) : "";
			$r_pin = $row1["pin"] ? trim($row1["pin"]) : "";
			$r_state = $row1["state"] ? trim($row1["state"]) : "";
			$r_address = $row1["address"] ? str_replace('"', '""', trim($row1["address"])) : "";
			$r_status = $row1["status"] ? trim($row1["status"]) : "";
			$r_datetime = $row1["datetime"] ? trim($row1["datetime"]) : "";
			$r_delivery_date = $row1["delivery_date"] ? trim($row1["delivery_date"]) : "";
			$r_gift_title = $row1["gift_title"] ? trim($row1["gift_title"]) : "";
			$r_gift_title = preg_replace('/[^\x20-\x7E]/', '', $r_gift_title);

			$remarks = $row1["remarks"] ? trim($row1["remarks"]) : "";
			$pending_reason = $row1["pending_reason"] ? trim($row1["pending_reason"]) : "";
			if($r_status == 'PENDING'){
				$remarks = $pending_reason;
			}
			

			// Escape any internal double quotes for CSV safety
			$r_gift_title = str_replace('"', '""', $r_gift_title);
			$r_amazon_order_id  = $row1["amazon_order_id"] ? trim($row1["amazon_order_id"]) : "";
			$r_amazon_order_link  = $row1["amazon_order_link"] ? trim($row1["amazon_order_link"]) : "";
			$is_order_received  = $row1["is_order_received"] ? trim($row1["is_order_received"]) : "NO";	

			if($r_datetime!="")
			{
				$r_datetime = date("d-m-Y",strtotime($r_datetime));
			}
			if($r_delivery_date!="")
			{
				$r_delivery_date = date("d-m-Y",strtotime($r_delivery_date));
			}
			// ----------- Setting: number of ACTIVATION days / start date rule -----------
			$acknowledgement_rule_start_date = get_value_by_setting_key($conn, "acknowledgement_rule_start_date");
			if ($acknowledgement_rule_start_date == "") {
				$acknowledgement_rule_start_date = '2025-11-01'; // fallback date
			}
			$old_order_id='';
			$current_date = date('Y-m-d');
				 if (strtotime($r_datetime) <= strtotime($acknowledgement_rule_start_date)) {
					$old_order_id=$the_g_order_id;
				 }
			$output .= '"'.$order_id.'","'.$old_order_id.'","'.$r_amazon_order_id.'","'.$r_amazon_order_link.'","'.$r_gift_title.'","'.$r_datetime.'","'.$is_order_received.'","'.$product_point.'","'.$tds.'","'.$r_point_taken.'","'.$r_delivery_date.'","'.$r_status.'","'.$r_engineer_name.'","'.$r_engineer_mobile.'","'.$r_engineer_email.'","'.$r_city.'","'.$r_pin.'","'.$r_state.'","'.$r_address.'","'.$r_te_code.'","'.$r_te_name.'","'.$r_te_branch_code.'","'.$r_te_branch_name.'","'.$remarks.'"';
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