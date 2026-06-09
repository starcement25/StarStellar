<?php
	include "web_check.php";
	include "star_connection.php";
	$engineer_master = "engineer_master";
	$te_master = "te_master";
	$gift_order_master = "gift_order_master";
	$recommended_site_master = "recommended_site_master";
	$support_master = "support_master";
	$recommended_site_count_arr = array("PENDING"=>0,"APPROVED"=>0,"REJECTED"=>0);
	$gift_order_count_arr = array("PENDING"=>0,"DELIVERED"=>0);
	$support_count_arr = array("PENDING"=>0,"RESOLVE"=>0);
	$date_before_twelve_month = date('Y-m-d H:i:s',strtotime("-12 month"));

	$date_before_three_month = date('Y-m-d H:i:s',strtotime("-3 month"));
	$date_before_six_month = date('Y-m-d H:i:s',strtotime("-6 month"));
	$date_before_three_month_stamp = strtotime($date_before_three_month);
	$date_before_six_month_stamp = strtotime($date_before_six_month);


	$total_engineer_count = 0;

	$total_site_recomended_count = 0;
	$total_site_approved_count = 0;
	$total_site_pending_count = 0;
	$total_site_rejected_count = 0;
	$total_engineer_gift_redeemd_count = 0;
	$total_gift_delivered_to_engineer_count = 0;
	$total_query_raised_count = 0;
	$total_query_solved_count = 0;
	$total_active_linked_engineer_count = 0;
	$total_semi_active_linked_engineer_count = 0;
	$total_inactive_linked_engineer_count = 0;

	$total_pending_order_count = 0;
	$total_delivered_order_count = 0;

	$total_te_count = 0;

	$pgsql2 = "select count(`eid`) as `total_engineer_count` from $engineer_master";
	$pgres2 = mysqli_query($conn,$pgsql2);
	$total_pgres2 = mysqli_num_rows($pgres2);
	if($total_pgres2>0)
	{
		$row12=mysqli_fetch_assoc($pgres2);
		$total_engineer_count = $row12["total_engineer_count"];
	}

	$pgsqlrecom1 = "SELECT `r_status`,count(`r_status`) as `tot_r_status` FROM $recommended_site_master group by `r_status`";
	$pgresrecom1 = mysqli_query($conn,$pgsqlrecom1);
	$total_pgresrecom1 = mysqli_num_rows($pgresrecom1);
	if($total_pgresrecom1>0)
	{
		while($rowrecom1=mysqli_fetch_assoc($pgresrecom1))
		{
			$recomsts = $rowrecom1["r_status"] ? trim($rowrecom1["r_status"]) : "";
			$recomtot_r_status = $rowrecom1["tot_r_status"] ? trim($rowrecom1["tot_r_status"]) : 0;
			if($recomsts!="")
			{
				$recommended_site_count_arr[$recomsts] = $recomtot_r_status;
			}
		}
	}

	$total_site_recomended_count = $recommended_site_count_arr["PENDING"] + $recommended_site_count_arr["APPROVED"] + $recommended_site_count_arr["REJECTED"];

	$pgsqlgords = "SELECT `status`,count(`status`) as `ord_status_cnt` FROM $gift_order_master group by `status`";
	$pgresgords = mysqli_query($conn,$pgsqlgords);
	$total_pgresgords = mysqli_num_rows($pgresgords);
	if($total_pgresgords>0)
	{
		while($rowgords=mysqli_fetch_assoc($pgresgords))
		{
			$gords_status = $rowgords["status"] ? trim($rowgords["status"]) : "";
			$gords_status_cnt = $rowgords["ord_status_cnt"] ? trim($rowgords["ord_status_cnt"]) : 0;
			if($gords_status!="")
			{
				$gift_order_count_arr[$gords_status] = $gords_status_cnt;
			}
		}
	}

	$total_engineer_gift_redeemd_count = $gift_order_count_arr["PENDING"] + $gift_order_count_arr["DELIVERED"];



	$sqlActive = "select count($engineer_master.`eid`) as `tot_active_engineer` from $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`>='$date_before_twelve_month' and $engineer_master.`status_by_te`='APPROVED'";
	$resActive = mysqli_query($conn,$sqlActive);
	$totresActive = mysqli_num_rows($resActive);
	if($totresActive>0)
	{
		$rowActive=mysqli_fetch_assoc($resActive);
		$total_active_linked_engineer_count = $rowActive["tot_active_engineer"];
	}

	$sqlSemiActive = "select count($engineer_master.`eid`) as `tot_semi_active_engineer` from $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where `latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_three_month' and `latest_recommended_site_master`.`r_submission_date`>='$date_before_six_month'";
	$resSemiActive = mysqli_query($conn,$sqlSemiActive);
	$totresSemiActive = mysqli_num_rows($resSemiActive);
	if($totresSemiActive>0)
	{
		$rowSemiActive=mysqli_fetch_assoc($resSemiActive);
		$total_semi_active_linked_engineer_count = $rowSemiActive["tot_semi_active_engineer"];
	}

	$sqlInActive = "select count($engineer_master.`eid`) as `tot_inactive_engineer` from $engineer_master left join (SELECT `r_engineer_id`, MAX(`r_submission_date`) AS `r_submission_date` FROM $recommended_site_master GROUP BY `r_engineer_id`) AS `latest_recommended_site_master` ON $engineer_master.`eid` = `latest_recommended_site_master`.`r_engineer_id` where `latest_recommended_site_master`.`r_submission_date` is null or (`latest_recommended_site_master`.`r_submission_date` is not null and `latest_recommended_site_master`.`r_submission_date`<'$date_before_six_month')";
	$resInActive = mysqli_query($conn,$sqlInActive);
	$totresInActive = mysqli_num_rows($resInActive);
	if($totresInActive>0)
	{
		$rowInActive=mysqli_fetch_assoc($resInActive);
		$total_inactive_linked_engineer_count = $rowInActive["tot_inactive_engineer"];
	}

	$pgsqlsupp = "SELECT `status`,count(`status`) as `supp_status_cnt` FROM $support_master group by `status`";
	$pgressupp = mysqli_query($conn,$pgsqlsupp);
	$total_pgressupp = mysqli_num_rows($pgressupp);
	if($total_pgressupp>0)
	{
		while($rowsupp=mysqli_fetch_assoc($pgressupp))
		{
			$supp_status = $rowsupp["status"] ? trim($rowsupp["status"]) : "";
			$supp_status_cnt = $rowsupp["supp_status_cnt"] ? trim($rowsupp["supp_status_cnt"]) : 0;
			if($supp_status!="")
			{
				$support_count_arr[$supp_status] = $supp_status_cnt;
			}
		}
	}
	$total_query_raised_count = $support_count_arr["PENDING"] + $support_count_arr["RESOLVE"];
	$total_query_solved_count = $support_count_arr["RESOLVE"];

	include "web_header.php";
?>


<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <h2>DASHBOARD</h2>
        </div>
    	<!-- Widgets -->
		<div class="row clearfix">            
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<!-- <a href="engineer_master.php" style="text-decoration:none;cursor:pointer;"> -->
				<a href="engineer_master_updated.php" style="text-decoration:none;cursor:pointer;">
					<div class="info-box bg-light-green hover-expand-effect" style="cursor:pointer;"> 
						<div class="icon">
							<i class="material-icons">link</i>
						</div>
						<div class="content">
							<div class="text" style="margin-top: 0px;">TOTAL LINKED ENGINEER</div>
							<div class="number count-to" data-from="0" data-to="<?php echo $total_engineer_count;?>" data-speed="1000" data-fresh-interval="20"></div>
						</div>
					</div>
				</a>
			</div>                


			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<!-- <a href="engineer_master.php?sl_activity_status=ACTIVE&sl_status_by_te=APPROVED" style="text-decoration:none;cursor:pointer;"> -->
				<a href="engineer_master_updated.php?sl_activity_status=ACTIVE&sl_status_by_te=APPROVED" style="text-decoration:none;cursor:pointer;">
					<div class="info-box bg-light-green hover-expand-effect" style="cursor:pointer;">                  
						<div class="icon">
							<i class="material-icons">link</i>
						</div>
						<div class="content">
							<div class="text" style="margin-top: 0px;">ACTIVE ENGINEER</div>
							<div class="number count-to" data-from="0" data-to="<?php echo $total_active_linked_engineer_count;?>" data-speed="1000" data-fresh-interval="20"></div>
						</div>
					</div>
				</a>
			</div>
			<!--<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<a href="engineer_master.php?sl_activity_status=SEMI_ACTIVE" style="text-decoration:none;cursor:pointer;">
					<div class="info-box bg-light-green hover-expand-effect" style="cursor:pointer;">                  
						<div class="icon">
							<i class="material-icons">link</i>
						</div>
						<div class="content">
							<div class="text" style="margin-top: 0px;">SEMIACTIVE LINKED ENGINEER</div>
							<div class="number count-to" data-from="0" data-to="<?php echo $total_semi_active_linked_engineer_count;?>" data-speed="1000" data-fresh-interval="20"></div>
						</div>
					</div>
				</a>
			</div>   -->             
			<!--<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<a href="engineer_master.php?sl_activity_status=INACTIVE" style="text-decoration:none;cursor:pointer;">
					<div class="info-box bg-light-green hover-expand-effect" style="cursor:pointer;">                  
						<div class="icon">
							<i class="material-icons">link</i>
						</div>
						<div class="content">
							<div class="text" style="margin-top: 0px;">INACTIVE LINKED ENGINEER</div>
							<div class="number count-to" data-from="0" data-to="<?php echo $total_inactive_linked_engineer_count;?>" data-speed="1000" data-fresh-interval="20"></div>
						</div>
					</div>
				</a>
			</div>-->
		</div>

		<div class="row clearfix">                
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<a href="recomended_site_master.php" style="text-decoration:none;cursor:pointer;">
					<div class="info-box bg-blue hover-expand-effect" style="cursor:pointer;">                  
						<div class="icon">
							<i class="material-icons">web</i>
						</div>
						<div class="content">
							<div class="text" style="margin-top: 0px;">TOTAL SITES RECOMMENDED</div>
							<div class="number count-to" data-from="0" data-to="<?php echo $total_site_recomended_count;?>" data-speed="1000" data-fresh-interval="20"></div>
						</div>
					</div>
				</a>
			</div>                
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<a href="recomended_site_master.php?sl_ord_sts=APPROVED" style="text-decoration:none;cursor:pointer;">
					<div class="info-box bg-blue hover-expand-effect" style="cursor:pointer;">                  
						<div class="icon">
							<i class="material-icons">web</i>
						</div>
						<div class="content">
							<div class="text" style="margin-top: 0px;">TOTAL SITES APPROVED</div>
							<div class="number count-to" data-from="0" data-to="<?php echo $recommended_site_count_arr["APPROVED"];?>" data-speed="1000" data-fresh-interval="20"></div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<a href="recomended_site_master.php?sl_ord_sts=PENDING" style="text-decoration:none;cursor:pointer;">
					<div class="info-box bg-blue hover-expand-effect" style="cursor:pointer;">                  
						<div class="icon">
							<i class="material-icons">web</i>
						</div>
						<div class="content">
							<div class="text" style="margin-top: 0px;">TOTAL SITES PENDING</div>
							<div class="number count-to" data-from="0" data-to="<?php echo $recommended_site_count_arr["PENDING"];?>" data-speed="1000" data-fresh-interval="20"></div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<a href="recomended_site_master.php?sl_ord_sts=REJECTED" style="text-decoration:none;cursor:pointer;">
					<div class="info-box bg-blue hover-expand-effect" style="cursor:pointer;">                  
						<div class="icon">
							<i class="material-icons">web</i>
						</div>
						<div class="content">
							<div class="text" style="margin-top: 0px;">SITES REJECTED</div>
							<div class="number count-to" data-from="0" data-to="<?php echo $recommended_site_count_arr["REJECTED"];?>" data-speed="1000" data-fresh-interval="20"></div>
						</div>
					</div>
				</a>
			</div>
		</div>

		<div class="row clearfix">            
						
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<a href="order_master.php" style="text-decoration:none;cursor:pointer;">
					<div class="info-box bg-pink hover-expand-effect" style="cursor:pointer;">                  
						<div class="icon">
							<i class="material-icons">redeem</i>
						</div>
						<div class="content">
							<div class="text" style="margin-top: 0px;">TOTAL ENGINEERS GIFT REDEEM</div>
							<div class="number count-to" data-from="0" data-to="<?php echo $total_engineer_gift_redeemd_count;?>" data-speed="1000" data-fresh-interval="20"></div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<a href="order_master.php?sl_ord_sts=DELIVERED" style="text-decoration:none;cursor:pointer;">
					<div class="info-box bg-pink hover-expand-effect" style="cursor:pointer;">                  
						<div class="icon">
							<i class="material-icons">card_giftcard</i>
						</div>
						<div class="content">
							<div class="text" style="margin-top: 0px;">GIFT DELIVERED</div>
							<div class="number count-to" data-from="0" data-to="<?php echo $gift_order_count_arr["DELIVERED"];?>" data-speed="1000" data-fresh-interval="20"></div>
						</div>
					</div>
				</a>
			</div>              

			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<a href="support_master.php" style="text-decoration:none;cursor:pointer;">
					<div class="info-box bg-pink hover-expand-effect" style="cursor:pointer;">                  
						<div class="icon">
							<i class="material-icons">feedback</i>
						</div>
						<div class="content">
							<div class="text" style="margin-top: 0px;">QUERY RAISED</div>
							<div class="number count-to" data-from="0" data-to="<?php echo $total_query_raised_count;?>" data-speed="1000" data-fresh-interval="20"></div>
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<a href="support_master.php?sl_supp_status=RESOLVE" style="text-decoration:none;cursor:pointer;">
					<div class="info-box bg-pink hover-expand-effect" style="cursor:pointer;">                  
						<div class="icon">
							<i class="material-icons">assignment_turned_in</i>
						</div>
						<div class="content">
							<div class="text" style="margin-top: 0px;">QUERY SOLVED</div>
							<div class="number count-to" data-from="0" data-to="<?php echo $total_query_solved_count;?>" data-speed="1000" data-fresh-interval="20"></div>
						</div>
					</div>
				</a>
			</div>
		</div>
		<!-- #END# Widgets -->
           

            
    </div>
</section>
    

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>

<?php
	include "web_footer.php";
	mysqli_close($conn);
?>