<?php
include "web_check.php";
include "star_connection.php";
$gift_master = "gift_master";
$en_status_arr = array("ACTIVE","INACTIVE");
$featured_status_arr = array("NO","YES");
$supported_mime_type = array("image/jpeg","image/png","image/jpg");
$img_dir = "../gift_pic/";
$default_image_name = "profile.png";
$image_url_prefix = $server_url."gift_pic/";
$new_qry_string_filtered = "";
$msg_txt = "";
$add_page_name = "gift_master.php";
$page_name = "gift_master.php";
if(isset($_GET["msg_txt"]) && @$_GET["msg_txt"]!=""){
	$msg_txt = $_GET["msg_txt"];
}
if(isset($_GET["the_dlt_gid"]) && @$_GET["the_dlt_gid"]!=""){
$the_dlt_gid = $_GET["the_dlt_gid"] ? trim($_GET["the_dlt_gid"]) : "";
if($the_dlt_gid!=""){
		$sqlsldck3 = "select `gift_image` from $gift_master where `id`='$the_dlt_gid'";
		$ressldck3 = mysqli_query($conn,$sqlsldck3);
		$totressldck3 = mysqli_num_rows($ressldck3);
		if($totressldck3>0){
		$rowsldck3=mysqli_fetch_assoc($ressldck3);
		$old_gift_image_name3 = $rowsldck3["gift_image"] ? trim($rowsldck3["gift_image"]) : "";
		}else{
		$old_gift_image_name3 = "";
		}
		$sqldel = "delete from $gift_master where `id`='$the_dlt_gid'";
		$resdel = mysqli_query($conn,$sqldel);
		if($old_gift_image_name3!=""){
		if(file_exists($img_dir.$old_gift_image_name3)){
		unlink($img_dir.$old_gift_image_name3);
		}
		}
}

$msg_txt = "Gift successfully deleted.";
header("location:".$page_name."?msg_txt=".$msg_txt);
}
if(@$_POST["edit_gft_btn"]=="Update"){
	$gft_hid_id = $_POST["gft_hid_id"] ? addslashes(trim($_POST["gft_hid_id"])) : "";
	$add_gft_title = $_POST["gft_title"] ? addslashes(trim($_POST["gft_title"])) : "";
	$add_gft_des = $_POST["gft_des"] ? addslashes(trim($_POST["gft_des"])) : "";
	$add_gft_point = $_POST["gft_point"] ? addslashes(trim($_POST["gft_point"])) : "";
	$add_gft_img_file_name = $_FILES['gft_img_file']['name'];
	if($add_gft_title==""){
		$msg_txt = "Please enter gift title.";
	}else if($add_gft_des==""){
		$msg_txt = "Please enter gift description.";
	}else if($add_gft_point==""){
		$msg_txt = "Please enter gift point.";
	}else{
		if($add_gft_img_file_name!=""){
		$add_gft_img_file_type = $_FILES['gft_img_file']['type'];
		if(!in_array($add_gft_img_file_type,$supported_mime_type)){
		$msg_txt = "Please select a image.";
		}else{
			$sqlsldck2 = "select `gift_image` from $gift_master where `id`='$gft_hid_id'";
			$ressldck2 = mysqli_query($conn,$sqlsldck2);
			$totressldck2 = mysqli_num_rows($ressldck2);
			if($totressldck2>0){
				$rowsldck2=mysqli_fetch_assoc($ressldck2);
				$old_gift_image_name = $rowsldck2["gift_image"] ? trim($rowsldck2["gift_image"]) : "";
			}else{
				$old_gift_image_name = "";
			}
			
			
			$add_gft_img_file_tmp_name = $_FILES['gft_img_file']['tmp_name'];
			$unid = uniqid();
			$img_file_name = str_replace(" ","_",$add_gft_img_file_name);
			$img_file_name = str_replace("  ","_",$img_file_name);
			$img_file_name = str_replace("'","_",$img_file_name);
			$img_file_name = str_replace('"',"_",$img_file_name);
			$img_file_name = str_replace('-',"_",$img_file_name);
			$ad_new_name = "gift_img_".$unid."_".$img_file_name;
			$target_file = $img_dir.$ad_new_name;
			$upload_the_ad_file = move_uploaded_file($add_gft_img_file_tmp_name,$target_file);
			if($upload_the_ad_file){
				if($old_gift_image_name!=""){
					if(file_exists($img_dir.$old_gift_image_name)){
						unlink($img_dir.$old_gift_image_name);
					}
				}
$sqlsldupd = "update $gift_master set `gift_title`='$add_gft_title',`description`='$add_gft_des',`point_require`='$add_gft_point',`gift_image`='$ad_new_name' where `id`='$gft_hid_id'";
$ressldupd = mysqli_query($conn,$sqlsldupd);
				$msg_txt = "Gift successfully updated.";
				header("location:".$page_name."?msg_txt=".$msg_txt);
			}else{
$sqlsldupd = "update $gift_master set `gift_title`='$add_gft_title',`description`='$add_gft_des',`point_require`='$add_gft_point' where `id`='$gft_hid_id'";
$ressldupd = mysqli_query($conn,$sqlsldupd);
$msg_txt = "Gift successfully updated. But failed to upload image.";
header("location:".$page_name."?msg_txt=".$msg_txt);
			}
		
		}
		
		}else{
		$sqlsldupd = "update $gift_master set `gift_title`='$add_gft_title',`description`='$add_gft_des',`point_require`='$add_gft_point' where `id`='$gft_hid_id'";
$ressldupd = mysqli_query($conn,$sqlsldupd);
$msg_txt = "Gift successfully updated.";
header("location:".$page_name."?msg_txt=".$msg_txt);
		}		
}	
}

if(@$_POST["add_gft_btn"]=="Save"){
	$add_gft_title = $_POST["gft_title"] ? addslashes(trim($_POST["gft_title"])) : "";
	$add_gft_des = $_POST["gft_des"] ? addslashes(trim($_POST["gft_des"])) : "";
	$add_gft_point = $_POST["gft_point"] ? addslashes(trim($_POST["gft_point"])) : "";
	$add_gft_img_file_name = $_FILES['gft_img_file']['name'];
	if($add_gft_title==""){
		$msg_txt = "Please enter gift title.";
	}else if($add_gft_des==""){
		$msg_txt = "Please enter gift description.";
	}else if($add_gft_point==""){
		$msg_txt = "Please enter gift point.";
	}else if($add_gft_img_file_name==""){
		$msg_txt = "Please select a image.";
	}else{
		$add_gft_img_file_type = $_FILES['gft_img_file']['type'];
		if(!in_array($add_gft_img_file_type,$supported_mime_type)){
		$msg_txt = "Please select a image.";
		}else{
			$add_gft_img_file_tmp_name = $_FILES['gft_img_file']['tmp_name'];
			$unid = uniqid();
			$img_file_name = str_replace(" ","_",$add_gft_img_file_name);
			$img_file_name = str_replace("  ","_",$img_file_name);
			$img_file_name = str_replace("'","_",$img_file_name);
			$img_file_name = str_replace('"',"_",$img_file_name);
			$img_file_name = str_replace('-',"_",$img_file_name);
			$ad_new_name = "gift_img_".$unid."_".$img_file_name;
			$target_file = $img_dir.$ad_new_name;
			$upload_the_ad_file = move_uploaded_file($add_gft_img_file_tmp_name,$target_file);
			if($upload_the_ad_file){
				$sqlsldin = "insert into $gift_master (`gift_title`,`description`,`gift_image`,`point_require`) values('$add_gft_title','$add_gft_des','$ad_new_name','$add_gft_point')";
				$ressldin = mysqli_query($conn,$sqlsldin);
				$msg_txt = "Gift successfully saved.";
			}else{
				$msg_txt = "Failed to upload image.";
			}
		
		}		
}	
}

if(isset($_GET["gedtid"]) && @$_GET["gedtid"]!=""){
$the_gedtid = $_GET["gedtid"] ? addslashes(trim($_GET["gedtid"])) : "";
$sqlsldck = "select * from $gift_master where `id`='$the_gedtid'";
$ressldck = mysqli_query($conn,$sqlsldck);
$totressldck = mysqli_num_rows($ressldck);
if($totressldck>0){
	$rowsldck=mysqli_fetch_assoc($ressldck);
$selected_gift_title = $rowsldck["gift_title"];
$selected_gift_description = $rowsldck["description"];
$selected_gift_point_require = $rowsldck["point_require"] ? trim($rowsldck["point_require"]) : "";
$selected_gift_image = $rowsldck["gift_image"] ? trim($rowsldck["gift_image"]) : "";
	if($selected_gift_image!=""){
	if(file_exists($img_dir.$selected_gift_image)){
	$selected_gift_image_url = $image_url_prefix.$selected_gift_image;
	}else{
	$selected_gift_image_url = "";
	}
	}else{
	$selected_gift_image_url = "";
	}
}else{
	$the_gedtid = "";
	$selected_gift_title = "";
	$selected_gift_description = "";
	$selected_gift_point_require = "";
	$selected_gift_image_url = "";
}
}else{
	$the_gedtid = "";
	$selected_gift_title = "";
	$selected_gift_description = "";
	$selected_gift_point_require = "";
	$selected_gift_image_url = "";
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

$pgsql = "select `id` from $gift_master ";
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
.adminActivityField{
	display:block;
	width:100px;
	margin-bottom:8px;
}
.adminActivityField3{
	display:block;
	width:100px;
	height:20px;
}
.adminActivityField2{
	display:block;
	width:150px;
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
                          <h2>Gift Master (<?php echo $total_pgres;?>)&nbsp;&nbsp; <a href="export_gift_master.php" class="btn bg-red waves-effe">Export&nbsp;Gift&nbsp;Master</a> &nbsp; &nbsp;<span class="shomsg"><?php if($msg_txt!=""){ echo $msg_txt;}?></span>
                          
                          </h2>
                            <div class="row clearfix">
                            <form name="hss_form" id="hss_form" enctype="multipart/form-data" method="POST">
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
    <label class="blbl">Gift Title</label>
<textarea class="form-control" id="gft_title" name="gft_title" placeholder="Enter Gift Title" style="width:100%;height:50px;"><?php echo $selected_gift_title;?></textarea>
    </div>
   <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
   <label class="blbl">Description</label>
<textarea class="form-control"  id="gft_des" name="gft_des" style="width:100%;height:50px;" placeholder="Enter Gift Description"><?php echo $selected_gift_description;?></textarea>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding" style="text-align:left !important;">
    <label class="blbl">Gift Point</label>
    <input type="text" name="gft_point" class="form-control" id="gft_point" value="<?php echo $selected_gift_point_require;?>" />
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding" style="text-align:left !important;">
    <label class="blbl">Image</label>
    <?php
    if($the_gedtid!=""){
		if($selected_gift_image_url!=""){
	?>
    <img src="<?php echo $selected_gift_image_url;?>" class="selected_prfl_img" />
    <?php }} ?>
    <input type="file" name="gft_img_file" id="gft_img_file" />
    
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <label class="blbl"></label>
    <?php
    if($the_gedtid!=""){
	?>
    <input type="submit" class="btn bg-red waves-effect gft_form_btn" name="edit_gft_btn" id="edit_gft_btn" value="Update" > <br />
    <input type="hidden" name="gft_hid_id" value="<?php echo $the_gedtid;?>" />
    <a href="<?php echo $page_name;?>" class="btn bg-red waves-effect" style="margin-top:20px;">Refresh</a>
    <?php }else {?>
    <input type="submit" class="btn bg-red waves-effect gft_form_btn" name="add_gft_btn" id="add_gft_btn" value="Save" >
    <?php } ?>

    </div>    
        </form>
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
                                            <th>Gift Image</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Point</th>
                                            <th>Status</th>
                                            <th>Featured</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Gift Image</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Point</th>
                                            <th>Status</th>
                                            <th>Featured</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select * from $gift_master order by ABS(`point_require`) asc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$the_gid = $row1["id"];
		$the_gift_title = $row1["gift_title"];
		$the_gift_description = $row1["description"];
		$the_gift_point_require = $row1["point_require"] ? trim($row1["point_require"]) : "";
		$the_gift_status = $row1["status"];
		$the_featured_status = $row1["featured"];
		$the_gift_image = $row1["gift_image"] ? trim($row1["gift_image"]) : "";
		if($the_gift_image!=""){
			if(file_exists($img_dir.$the_gift_image)){
				$the_gift_image_url = $image_url_prefix.$the_gift_image;
			}else{
				$the_gift_image_url = "";
			}
		}else{
			$the_gift_image_url = "";
		}
?>
<tr>
<td>
<?php
if($the_gift_image_url!=""){
?>
<img src="<?php echo $the_gift_image_url;?>" class="prfl_img" />
<?php
}else{
	echo "Not set yet";
}
?>
</td>
<td><?php echo $the_gift_title;?></td>
<td><?php echo $the_gift_description;?></td>
<td><?php echo $the_gift_point_require;?></td>
<td>
<div>
<span class="adminActivityField">
<select class="form-control sl_eng_sts" id="sl_eng_sts_<?php echo $the_gid;?>" eng_id="<?php echo $the_gid;?>">
<?php
if(count($en_status_arr)>0){
	foreach($en_status_arr as $en_status_arr_val){ ?>
<option value="<?php echo $en_status_arr_val;?>" <?php if($en_status_arr_val==$the_gift_status){?> selected="selected" <?php } ?>><?php echo $en_status_arr_val;?></option>
	<?php }
}
?>
</select>
</span>
<span class="adminActivityField3" id="sl_eng_sts_ldr_<?php echo $the_gid;?>"></span>
</div>
</td>
<td>
<div>
<span class="adminActivityField">
<select class="form-control sl_featured_sts" id="sl_featured_sts_<?php echo $the_gid;?>" gft_id="<?php echo $the_gid;?>">
<?php
if(count($featured_status_arr)>0){
	foreach($featured_status_arr as $featured_status_arr_val){ ?>
<option value="<?php echo $featured_status_arr_val;?>" <?php if($featured_status_arr_val==$the_featured_status){?> selected="selected" <?php } ?>><?php echo $featured_status_arr_val;?></option>
	<?php }
}
?>
</select>
</span>
<span class="adminActivityField3" id="sl_featured_sts_ldr_<?php echo $the_gid;?>"></span>
</div>
</td>
<td>
<div>
<?php
if($the_gedtid==$the_gid){
	
}else{
?>
<a href="<?php echo $page_name;?>?gedtid=<?php echo $the_gid;?>" class="btn bg-red waves-effect" style="margin-right:20px;">Edit</a>
<?php /*?><a href="javascript:void(0);" the_dlt_gid="<?php echo $the_gid;?>" class="btn bg-red waves-effect gdlt_btn">Delete</a><?php */?>
<?php	
}
?>
</div>
</td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="7">No data found.</td>
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

jQuery(".gdlt_btn").click(function(){
	var the_dlt_gid = jQuery(this).attr("the_dlt_gid");
	if(the_dlt_gid!=""){
		var cb = confirm("Do you want to delete this gift?");
		if(cb == true){
			window.location = "<?php echo $page_name;?>?the_dlt_gid="+the_dlt_gid;
		}
	}
});


jQuery("#gft_point").keydown(function(event) {
// Allow only backspace and delete
if ( event.keyCode == 46 || event.keyCode == 8  || event.keyCode == 9 ) {
// let it happen, don't do anything
}
else {
// Ensure that it is a number and stop the keypress
if ((event.keyCode > 47 && event.keyCode < 58) || (event.keyCode > 95 && event.keyCode < 106) || event.keyCode == 110 || event.keyCode == 190 ) {

}else{
event.preventDefault(); 
}
}
});

jQuery('.sl_eng_sts').change(function(){
		var eng_sts = jQuery(this).val();
		var eng_id = jQuery(this).attr("eng_id");
		if(eng_sts!='' && eng_id!=''){
			var ldr_elmnt = jQuery("#sl_eng_sts_ldr_"+eng_id);
				ldr_elmnt.html(imgs);
				jQuery.ajax({
				url: 'ajax_update_gift_status.php',
				type: 'post',
				dataType: 'json',
				data: "eng_id="+eng_id+"&eng_sts="+eng_sts,
				success: function(response){				
				if(response.process_sts=="YES"){					
					ldr_elmnt.html(done_img);
					setTimeout(function(){
						ldr_elmnt.html("");
					},6000);
				}else{
					ldr_elmnt.html("");				
				}						
				}
				});
		}
	
	});

jQuery('.sl_featured_sts').change(function(){
		var eng_sts = jQuery(this).val();
		var eng_id = jQuery(this).attr("gft_id");
		if(eng_sts!='' && eng_id!=''){
			var ldr_elmnt = jQuery("#sl_featured_sts_ldr_"+eng_id);
				ldr_elmnt.html(imgs);
				jQuery.ajax({
				url: 'ajax_update_gift_featured_status.php',
				type: 'post',
				dataType: 'json',
				data: "eng_id="+eng_id+"&eng_sts="+eng_sts,
				success: function(response){				
				if(response.process_sts=="YES"){					
					ldr_elmnt.html(done_img);
					setTimeout(function(){
						ldr_elmnt.html("");
					},6000);
				}else{
					ldr_elmnt.html("");				
				}						
				}
				});
		}
	
	});



jQuery("form#hss_form").submit(function(){
	var gft_title = jQuery.trim(jQuery("#gft_title").val());
	var gft_des = jQuery.trim(jQuery("#gft_des").val());
	var gft_point = jQuery.trim(jQuery("#gft_point").val());
	var gft_img_file = jQuery.trim(jQuery("#gft_img_file").val());
	var gft_form_btn = jQuery.trim(jQuery(".gft_form_btn").val());
	if(gft_title==""){
		alert("Please enter gift title.");
		jQuery("#gft_title").focus();
		return false;
	}else if(gft_des==""){
		alert("Please enter gift description.");
		jQuery("#gft_des").focus();
		return false;
	}else if(gft_point==""){
		alert("Please enter gift point.");
		jQuery("#gft_point").focus();
		return false;
	}else{
		if(gft_form_btn=="Save" && gft_img_file==""){
			alert("Please select a gift image.");
			jQuery("#gft_img_file").focus();
			return false;
		}else{
			return true;
		}
	}
});
	
	
});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>