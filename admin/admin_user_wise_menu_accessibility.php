<?php
include "web_check.php";
include "star_connection.php";
$admin_master = "admin_master";
$menu_master = "menu_master";
$selected_menu_for_user = "selected_menu_for_user";
$edt_admin_id = $_GET["edt_admin_id"];
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/starstellar/";
$image_link_url = $server_url1."admin/noty_images/";
$mime_type_array = array("image/jpeg", "image/png","image/jpg");
$mime_type_array_pdf = array("application/pdf");
$n_dlt_msg = "";
$n_msg = "";
$submsg = "";
$add_page_name = "add_edit_admin_user_master.php";
$page_name = "add_edit_admin_user_master.php";
$back_to_page = "admin_user_master.php";
$status_arr = array("ACTIVE","DEACTIVE");

$pg_sts_arr = array();
$pg_sts_arr[] = array("key_val"=>"ACTIVE","title_val"=>"ACTIVE");
$pg_sts_arr[] = array("key_val"=>"INACTIVE","title_val"=>"INACTIVE");

$new_qry_string_filtered = "";
$srch_dlr_dtls = $_GET["srch_dlr_dtls"] ? addslashes(trim($_GET["srch_dlr_dtls"])) : "";
$sl_status = $_GET["sl_status"] ? addslashes(trim($_GET["sl_status"])) : "";
$whr_str = "";

if($whr_str!=""){
	$new_whr_str = $whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "admin_user_wise_menu_accessibility.php";
$page_name = "admin_user_wise_menu_accessibility.php";
$back_to_page = "admin_user_master.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/
$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;
/*---------PAGINATION RELATED CODE END----------*/
/*---------PAGINATION RELATED CODE START----------*/


$sqlckfm = "select SMU.`menu_id` from $selected_menu_for_user SMU, $menu_master MM 
			where SMU.`user_id`='$edt_admin_id' AND SMU.menu_id=MM.menu_id ORDER BY MM.menu_name ASC";
$pgres = mysqli_query($conn,$sqlckfm);
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
.dlr_prfl_img{
	width:150px;
}
.bwps_sel{
	width:150px;
}
.os_ldr{
	position:absolute;
	right:5px;
	top:5px;
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
                          <h2>Menu wise Edit Access(<?php echo $total_pgres;?>)&nbsp;&nbsp;<!--<a href="export_sp_destination.php?get_type=all" class="btn bg-red waves-effe">Export&nbsp;all</a>-->
                         
                          </h2>
                            <!--div class="row clearfix">
    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 add_top_bottom_padding">
<input type="text" class="form-control" id="srch_dlr_dtls" value="<?php echo $srch_dlr_dtls;?>" placeholder="Search Dealer Details">
    </div>
     <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
     <select class="form-control" id="sl_status">
    <option value="">Status</option>
    <option value="ACTIVE" <?php if($sl_status=="ACTIVE"){?> selected="selected" <?php } ?>>ACTIVE</option>
    <option value="INACTIVE" <?php if($sl_status=="INACTIVE"){?> selected="selected" <?php } ?>>INACTIVE</option>
    </select>
    </div>
   
    
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_btn" >Search</button>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <button type="button" class="btn bg-red waves-effect srch_reset_btn" >Reset</button>
    </div>
        
    </div-->
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
                                            <th> Menu&nbsp;Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Menu&nbsp;Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php

$sql1 = "select SMU.`menu_id`,SMU.`user_id`,SMU.`edit_access`,MM.`menu_name` from $selected_menu_for_user SMU, $menu_master MM 
			where SMU.`user_id`='$edt_admin_id' AND SMU.menu_id=MM.menu_id ORDER BY MM.menu_name ASC";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$menu_id = $row1["menu_id"];
		$menu_name = $row1["menu_name"];
		$edit_access = $row1["edit_access"];
		if($edit_access=='') $edit_access='INACTIVE';
		
?>
<tr>
<td><?php echo $menu_name;?></td>
<td >

<select class="form-control cwts_sel" id="cwts_sel_<?php echo $edt_admin_id;?>_<?php echo $menu_id;?>" the_customer_code="<?php echo $edt_admin_id;?>" menu_id="<?php echo $menu_id;?>" style="width: 100px;">
<?php
if(count($pg_sts_arr)>0){
	foreach($pg_sts_arr as $pg_sts_arr_val){
		$the_key_val = $pg_sts_arr_val["key_val"];
		$the_title_val = $pg_sts_arr_val["title_val"]; ?>
        <option value="<?php echo $the_key_val;?>" <?php if($the_key_val==$edit_access){?> selected="selected" <?php } ?>><?php echo $the_title_val;?></option>
        <?php
	}
	
}

?>
</select>


<span class="os_ldr" id="ca_ldr_<?php echo $edt_admin_id;?>_<?php echo $menu_id;?>"></span></td>


</tr>
<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="3">No data found.</td>
</tr>
<?php
}
?>
</tbody>
</table>
	 <a href="<?php echo $back_to_page;?>" class="btn bg-red waves-effe" style="margin-left:20px;">Back&nbsp;To&nbsp;Admin&nbsp;User&nbsp;Master</a>
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
	
	jQuery(".cwts_sel").change(function(){
		var ancr_elmnt = jQuery(this);
		var the_status = ancr_elmnt.val();
		var the_customer_code = ancr_elmnt.attr("the_customer_code");
		var the_menu_id = ancr_elmnt.attr("menu_id");
		//alert(the_customer_code+the_status);
		if(the_customer_code!=""){
			var for_loader = jQuery("#ca_ldr_"+the_customer_code+"_"+the_menu_id);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_menu_access_update.php',
			type: 'post',
			dataType: "JSON",
			data: "the_user_id="+the_customer_code+"&the_status="+the_status+"&menu_id="+the_menu_id,
			success: function(response){
				//alert(response.process_status);
			if(response.process_sts=="YES"){
			for_loader.html(done_img);
			setTimeout(function (){
			for_loader.html("");
			},3000);
			}else{
			for_loader.html("");
			alert(response.process_msg);
			}
			}
			});
		}
	});
	
	jQuery(".srch_btn").click(function(){
		var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();
		var sl_status = jQuery("#sl_status").val();	
		var qstring ="";
		var amp = "";
		if(srch_dlr_dtls!="" || sl_status!=""  ){
		if(srch_dlr_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}else{
				qstring = qstring+"srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}
		}
		if(sl_status!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_status="+encodeURIComponent(sl_status);
			}else{
				qstring = qstring+"sl_status="+encodeURIComponent(sl_status);
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
mysql_close();
?>