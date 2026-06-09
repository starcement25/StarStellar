<?php
include "web_check.php";
include "star_connection.php";
$home_screen_slider_for_engineer = "home_screen_slider_for_engineer";
$te_master = "te_master";
$supported_mime_type = array("image/jpeg","image/png","image/jpg");
$img_dir = "../e_slider/";
$default_image_name = "profile.png";
$image_url_prefix = $server_url."e_slider/";
$new_qry_string_filtered = "";
$msg_txt = "";
$add_page_name = "home_screen_slider.php";
$page_name = "home_screen_slider.php";

$the_data_show_type = $_SESSION["start_stellar_data_show_type"];
$the_access_user_type = $_SESSION["start_stellar_user_type"];
if($the_access_user_type=="MANAGER"){
	$data_show_type = $the_data_show_type;
}else{
	$data_show_type = "ALL";
}

if(isset($_GET["msg_txt"]) && @$_GET["msg_txt"]!=""){
	$msg_txt = $_GET["msg_txt"];
}
if(isset($_GET["the_dlt_sid"]) && @$_GET["the_dlt_sid"]!=""){
$the_dlt_sid = $_GET["the_dlt_sid"] ? trim($_GET["the_dlt_sid"]) : "";
if($the_dlt_sid!=""){
		$sqlsldck3 = "select `slider_image` from $home_screen_slider_for_engineer where `id`='$the_dlt_sid'";
		$ressldck3 = mysqli_query($conn,$sqlsldck3);
		$totressldck3 = mysqli_num_rows($ressldck3);
		if($totressldck3>0){
		$rowsldck3=mysqli_fetch_assoc($ressldck3);
		$old_slider_image_name3 = $rowsldck3["slider_image"] ? trim($rowsldck3["slider_image"]) : "";
		}else{
		$old_slider_image_name3 = "";
		}
		$sqldel = "delete from $home_screen_slider_for_engineer where `id`='$the_dlt_sid'";
		$resdel = mysqli_query($conn,$sqldel);
		if($old_slider_image_name3!=""){
		if(file_exists($img_dir.$old_slider_image_name3)){
		unlink($img_dir.$old_slider_image_name3);
		}
		}
}

$msg_txt = "Slider successfully deleted.";
header("location:".$page_name."?msg_txt=".$msg_txt);
}
if(@$_POST["edit_slider_btn"]=="Update"){
	$slidr_hid_id = $_POST["slidr_hid_id"] ? addslashes(trim($_POST["slidr_hid_id"])) : "";
	$add_sl_slider_category = $_POST["sl_slider_category"] ? addslashes(trim($_POST["sl_slider_category"])) : "FESTIVE";
	$add_sld_title = $_POST["sld_title"] ? addslashes(trim($_POST["sld_title"])) : "";
	$add_sld_des = $_POST["sld_des"] ? addslashes(trim($_POST["sld_des"])) : "";
	$add_sldr_img_file_name = $_FILES['sldr_img_file']['name'];
	
	if($add_sld_title==""){
		$msg_txt = "Please enter slider title.";
	}else if($add_sld_des==""){
		$msg_txt = "Please enter slider description.";
	}else{
		if($add_sldr_img_file_name!=""){
		$add_sldr_img_file_type = $_FILES['sldr_img_file']['type'];
		/*if(!in_array($add_sldr_img_file_type,$supported_mime_type)){
		$msg_txt = "Please select a image.";
		}else{*/
			$sqlsldck2 = "select `slider_image` from $home_screen_slider_for_engineer where `id`='$slidr_hid_id'";
			$ressldck2 = mysqli_query($conn,$sqlsldck2);
			$totressldck2 = mysqli_num_rows($ressldck2);
			if($totressldck2>0){
				$rowsldck2=mysqli_fetch_assoc($ressldck2);
				$old_slider_image_name = $rowsldck2["slider_image"] ? trim($rowsldck2["slider_image"]) : "";
			}else{
				$old_slider_image_name = "";
			}
			
			
			$add_sldr_img_file_tmp_name = $_FILES['sldr_img_file']['tmp_name'];
			$unid = uniqid();
			$img_file_name = str_replace(" ","_",$add_sldr_img_file_name);
			$img_file_name = str_replace("  ","_",$img_file_name);
			$img_file_name = str_replace("'","_",$img_file_name);
			$img_file_name = str_replace('"',"_",$img_file_name);
			$img_file_name = str_replace('-',"_",$img_file_name);
			$ad_new_name = "slider_img_".$unid."_".$img_file_name;
			$target_file = $img_dir.$ad_new_name;
			$upload_the_ad_file = move_uploaded_file($add_sldr_img_file_tmp_name,$target_file);
			if($upload_the_ad_file){
				if($old_slider_image_name!=""){
					if(file_exists($img_dir.$old_slider_image_name)){
						unlink($img_dir.$old_slider_image_name);
					}
				}
$sqlsldupd = "update $home_screen_slider_for_engineer set `slider_header_text`='$add_sld_title',`slider_description_text`='$add_sld_des',`slider_image`='$ad_new_name',`slider_category`='$add_sl_slider_category' where `id`='$slidr_hid_id'";
$ressldupd = mysqli_query($conn,$sqlsldupd);
				$msg_txt = "Slider successfully updated.";
				header("location:".$page_name."?msg_txt=".$msg_txt);
			}else{
$sqlsldupd = "update $home_screen_slider_for_engineer set `slider_header_text`='$add_sld_title',`slider_description_text`='$add_sld_des',`slider_category`='$add_sl_slider_category' where `id`='$slidr_hid_id'";
$ressldupd = mysqli_query($conn,$sqlsldupd);
$msg_txt = "Slider successfully updated. But failed to upload image.";
header("location:".$page_name."?msg_txt=".$msg_txt);
			}
		
		/*}*/
		
		}else{
		$sqlsldupd = "update $home_screen_slider_for_engineer set `slider_header_text`='$add_sld_title',`slider_description_text`='$add_sld_des',`slider_category`='$add_sl_slider_category' where `id`='$slidr_hid_id'";
		$ressldupd = mysqli_query($conn,$sqlsldupd);
		$msg_txt = "Slider successfully updated.";
		header("location:".$page_name."?msg_txt=".$msg_txt);
		}		
}	
}

if(@$_POST["add_slider_btn"]=="Save"){
	$add_sl_slider_category = $_POST["sl_slider_category"] ? addslashes(trim($_POST["sl_slider_category"])) : "FESTIVE";
	$add_sld_title = $_POST["sld_title"] ? addslashes(trim($_POST["sld_title"])) : "";
	$add_sld_des = $_POST["sld_des"] ? addslashes(trim($_POST["sld_des"])) : "";
	$add_sldr_img_file_name = $_FILES['sldr_img_file']['name'];
	if($add_sld_title==""){
		$msg_txt = "Please enter slider title.";
	}else if($add_sld_des==""){
		$msg_txt = "Please enter slider description.";
	}else if($add_sldr_img_file_name==""){
		$msg_txt = "Please select a image.";
	}else{
		$add_sldr_img_file_type = $_FILES['sldr_img_file']['type'];
		/*if(!in_array($add_sldr_img_file_type,$supported_mime_type)){
		$msg_txt = "Please select a image.";
		}else{*/
			$add_sldr_img_file_tmp_name = $_FILES['sldr_img_file']['tmp_name'];
			$unid = uniqid();
			$img_file_name = str_replace(" ","_",$add_sldr_img_file_name);
			$img_file_name = str_replace("  ","_",$img_file_name);
			$img_file_name = str_replace("'","_",$img_file_name);
			$img_file_name = str_replace('"',"_",$img_file_name);
			$img_file_name = str_replace('-',"_",$img_file_name);
			$ad_new_name = "slider_img_".$unid."_".$img_file_name;
			$target_file = $img_dir.$ad_new_name;
			$upload_the_ad_file = move_uploaded_file($add_sldr_img_file_tmp_name,$target_file);
			if($upload_the_ad_file){
				$sqlsldin = "insert into $home_screen_slider_for_engineer (`slider_header_text`,`slider_description_text`,`slider_image`,`slider_category`) values('$add_sld_title','$add_sld_des','$ad_new_name','$add_sl_slider_category')";
				$ressldin = mysqli_query($conn,$sqlsldin);
				$msg_txt = "Slider successfully saved.";
			}else{
				$msg_txt = "Failed to upload image.";
			}
		
		/*}*/		
}	
}

if(isset($_GET["sedtid"]) && @$_GET["sedtid"]!=""){
$the_sedtid = $_GET["sedtid"] ? addslashes(trim($_GET["sedtid"])) : "";
$sqlsldck = "select * from $home_screen_slider_for_engineer where `id`='$the_sedtid'";
$ressldck = mysqli_query($conn,$sqlsldck);
$totressldck = mysqli_num_rows($ressldck);
if($totressldck>0){
	$rowsldck=mysqli_fetch_assoc($ressldck);
	$selected_slider_title = $rowsldck["slider_header_text"];
	$selected_slider_description = $rowsldck["slider_description_text"];
	$selected_slider_category = $rowsldck["slider_category"];
	$selected_slider_image = $rowsldck["slider_image"] ? trim($rowsldck["slider_image"]) : "";
	if($selected_slider_image!=""){
	if(file_exists($img_dir.$selected_slider_image)){
	$selected_slider_image_url = $image_url_prefix.$selected_slider_image;
	}else{
	$selected_slider_image_url = "";
	}
	}else{
	$selected_slider_image_url = "";
	}
}else{
	$the_sedtid = "";
	$selected_slider_title = "";
	$selected_slider_description = "";
	$selected_slider_category = "FESTIVE";
	$selected_slider_image_url = "";
}
}else{
	$the_sedtid = "";
	$selected_slider_title = "";
	$selected_slider_description = "";
	$selected_slider_category = "FESTIVE";
	$selected_slider_image_url = "";
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

$pgsql = "select `id` from $home_screen_slider_for_engineer order by `id` asc";
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
                          <h2>Home Screen Slider (<?php echo $total_pgres;?>)&nbsp;&nbsp; <span class="shomsg"><?php if($msg_txt!=""){ echo $msg_txt;}?></span>
                          <?php /*?><a href="export_dealer.php?get_type=loggedin" class="btn bg-red waves-effe">Export&nbsp;loggedin&nbsp;dealer</a> &nbsp; <a href="export_dealer.php?get_type=notloggedin" class="btn bg-red waves-effe">Export&nbsp;not&nbsp;loggedin&nbsp;dealer</a><?php */?>
                          </h2>
                            <div class="row clearfix">
                            <form name="hss_form" id="hss_form" enctype="multipart/form-data" method="POST">
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
    <label class="blbl">Slider Category</label>
    <select name="sl_slider_category" class="form-control sl_slider_category" id="sl_slider_category">
    <option value="FESTIVE" <?php if($selected_slider_category=="FESTIVE"){ ?> selected="selected" <?php } ?>>FESTIVE</option>
    <option value="GIFT" <?php if($selected_slider_category=="GIFT"){ ?> selected="selected" <?php } ?>>GIFT</option>
    </select>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
    <label class="blbl">Slider Title</label>
<textarea class="form-control" id="sld_title" name="sld_title" placeholder="Enter Slider Title" style="width:100%;height:50px;"><?php echo $selected_slider_title;?></textarea>
    </div>
   <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding">
   <label class="blbl">Description</label>
<textarea class="form-control"  id="sld_des" name="sld_des" style="width:100%;height:50px;" placeholder="Enter Slider Description"><?php echo $selected_slider_description;?></textarea>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding" style="text-align:left !important;">
    <label class="blbl">Image</label>
    <?php
    if($the_sedtid!=""){
		if($selected_slider_image_url!=""){
	?>
    <img src="<?php echo $selected_slider_image_url;?>" class="selected_prfl_img" />
    <?php }} ?>
    <input type="file" name="sldr_img_file" id="sldr_img_file" />
    
    </div>
    <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
    <label class="blbl"></label>
    <?php
    if($the_sedtid!=""){
	?>
    <input type="submit" class="btn bg-red waves-effect slider_form_btn" name="edit_slider_btn" id="edit_slider_btn" value="Update" > <br />
    <input type="hidden" name="slidr_hid_id" value="<?php echo $the_sedtid;?>" />
    <a href="<?php echo $page_name;?>" class="btn bg-red waves-effect" style="margin-top:20px;">Refresh</a>
    <?php }else {?>
    <input type="submit" class="btn bg-red waves-effect slider_form_btn" name="add_slider_btn" id="add_slider_btn" value="Save" >
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
                                            <th>Slider Image</th>
                                            <th>Category</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Slider Image</th>
                                            <th>Category</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select * from $home_screen_slider_for_engineer order by `id` desc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$the_sid = $row1["id"];
		$the_slider_title = $row1["slider_header_text"];
		$the_slider_description = $row1["slider_description_text"];
		$the_slider_category = $row1["slider_category"];
		
		$the_slider_image = $row1["slider_image"] ? trim($row1["slider_image"]) : "";
		if($the_slider_image!=""){
			if(file_exists($img_dir.$the_slider_image)){
				$the_slider_image_url = $image_url_prefix.$the_slider_image;
			}else{
				$the_slider_image_url = "";
			}
		}else{
			$the_slider_image_url = "";
		}
?>
<tr>
<td>
<?php
if($the_slider_image_url!=""){
?>
<img src="<?php echo $the_slider_image_url;?>" class="prfl_img" />
<?php
}else{
	echo "Not set yet";
}
?>
</td>
<td><?php echo $the_slider_category;?></td>
<td><?php echo $the_slider_title;?></td>
<td><?php echo $the_slider_description;?></td>
<td>
<?php
if($the_sedtid==$the_sid){
	
}else{
?>

<div>
<span class="adminActivityField2">
	<?php if(!in_array($_SESSION["menu_id"],$ediit_inactive_menu_array)){?>
<a href="<?php echo $add_page_name;?>?sedtid=<?php echo $the_sid;?>" class="btn bg-red waves-effe" style="margin-right:10px;">Edit</a>
	<?php }?>
	<?php if(strtoupper($the_access_user_type)=="ADMIN"){?>
<a href="javascript:void(0);" class="btn bg-red waves-effe delete_slider" the_dlt_sid="<?php echo $the_sid;?>">Delete</a>
	<?php }?>
</span>
</div>

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

jQuery(".sdlt_btn").click(function(){
	var the_dlt_sid = jQuery(this).attr("the_dlt_sid");
	if(the_dlt_sid!=""){
		var cb = confirm("Do you want to delete this slider?");
		if(cb == true){
			window.location = "<?php echo $page_name;?>?the_dlt_sid="+the_dlt_sid;
		}
	}
});

jQuery("form#hss_form").submit(function(){
	var sld_title = jQuery.trim(jQuery("#sld_title").val());
	var sld_des = jQuery.trim(jQuery("#sld_des").val());
	var sldr_img_file = jQuery.trim(jQuery("#sldr_img_file").val());
	var slider_form_btn = jQuery.trim(jQuery(".slider_form_btn").val());
	if(sld_title==""){
		alert("Please enter slider title.");
		jQuery("#sld_title").focus();
		return false;
	}else if(sld_des==""){
		alert("Please enter slider description.");
		jQuery("#sld_des").focus();
		return false;
	}else{
		if(slider_form_btn=="Save" && sldr_img_file==""){
			alert("Please select a slider image.");
			jQuery("#sldr_img_file").focus();
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