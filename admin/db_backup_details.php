<?php
include "web_check.php";
include "star_connection.php";
$db_backup_files = "db_backup_files";
$dir_name = "dbup/";
$new_qry_string_filtered = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$whr_str = "";
$msg_txt = "";
$search_array = array("srch_dlr_dtls"=>$srch_dlr_dtls);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="srch_dlr_dtls"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand having `thedate`='$search_array_val' ";
			$new_qry_string_filtered .= "&srch_dlr_dtls=".$search_array_val;
		}
	}
}

if($whr_str!=""){
	/*$new_whr_str = "where ".$whr_str;*/
	$new_whr_str = " ".$whr_str;
}else{
	$new_whr_str ="";
}
if(isset($_GET["msg_txt"]) && @$_GET["msg_txt"]!=""){
	$msg_txt = $_GET["msg_txt"];
}
$page = $_GET['paged'] ? $_GET['paged'] : 1;
$add_page_name = "db_backup_details.php";
$page_name = "db_backup_details.php";

if(isset($_GET["dlt_dbbkid"]) && @$_GET["dlt_dbbkid"]!=""){
$dlt_dbbkid = $_GET["dlt_dbbkid"] ? trim($_GET["dlt_dbbkid"]) : "";
if($dlt_dbbkid!=""){
		$sqlsldck3 = "select `backup_file_name` from $db_backup_files where `id`='$dlt_dbbkid'";
		$ressldck3 = mysqli_query($conn,$sqlsldck3);
		$totressldck3 = mysqli_num_rows($ressldck3);
		if($totressldck3>0){
		$rowsldck3=mysqli_fetch_assoc($ressldck3);
		$old_backup_file_name = $rowsldck3["backup_file_name"] ? trim($rowsldck3["backup_file_name"]) : "";
		}else{
		$old_backup_file_name = "";
		}
		$sqldel = "delete from $db_backup_files where `id`='$dlt_dbbkid'";
		$resdel = mysqli_query($conn,$sqldel);
		if($old_backup_file_name!=""){
		if(file_exists($dir_name.$old_backup_file_name)){
		unlink($dir_name.$old_backup_file_name);
		}
		}
}

$msg_txt = "DB successfully deleted.";
header("location:".$page_name."?paged=".$page."&msg_txt=".$msg_txt);
}



$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select `id`,DATE_FORMAT(`date_time`,'%Y-%m-%d') as `thedate` from $db_backup_files $new_whr_str";
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
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                          <h2>DB Backup Details (<?php echo $total_pgres;?>)&nbsp;&nbsp;
                          <?php /*?><a href="export_dealer.php?get_type=loggedin" class="btn bg-red waves-effe">Export&nbsp;loggedin&nbsp;dealer</a> &nbsp; <a href="export_dealer.php?get_type=notloggedin" class="btn bg-red waves-effe">Export&nbsp;not&nbsp;loggedin&nbsp;dealer</a><?php */?>
                          
                          &nbsp; &nbsp;<span class="shomsg"><?php if($msg_txt!=""){ echo $msg_txt;}?></span>
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" readonly="readonly" placeholder="Select Date">
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
 <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>DB&nbsp;File&nbsp;Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                       <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th>DB&nbsp;File&nbsp;Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select *,DATE_FORMAT(`date_time`,'%Y-%m-%d') as `thedate` from $db_backup_files $new_whr_str order by `date_time` desc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$the_id = $row1["id"];
		$the_backup_file_name = $row1["backup_file_name"];
		$the_date_time = $row1["thedate"];
?>
<tr>
<td><?php echo $the_id;?></td>
<td><?php echo $the_date_time;?></td>
<td><?php echo $the_backup_file_name;?></td>
<td>
<?php
if(file_exists($dir_name.$the_backup_file_name)){
?>
<a href="download_the_db.php?thefilename=<?php echo $the_backup_file_name;?>" class="btn bg-red waves-effect">Download</a>

<a href="javascript:void(0);" class="btn bg-red waves-effe delete_dbbk" style="margin-left:10px;" dlt_dbbkid="<?php echo $the_id;?>">Delete</a>

<?php
}
?>
</td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="4">No data found.</td>
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
},5000);	
	jQuery('#srch_dlr_dtls').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
	
	jQuery(".srch_btn").click(function(){
		var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();
		var qstring ="";
		var amp = "";
		if(srch_dlr_dtls!=""){
		if(srch_dlr_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}else{
				qstring = qstring+"srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}
		}
		
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "<?php echo $page_name;?>"+qstring;
		}else{
			alert("Please select date to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "<?php echo $page_name;?>";
	});


jQuery(".delete_dbbk").click(function(){
	var dlt_dbbkid = jQuery(this).attr("dlt_dbbkid");
	if(dlt_dbbkid!=""){
		var cb = confirm("Do you want to delete this file?");
		if(cb == true){
			window.location = "<?php echo $page_name;?>?paged=<?php echo $page;?>&dlt_dbbkid="+dlt_dbbkid;
		}
	}
});


});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>