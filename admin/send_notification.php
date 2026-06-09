<?php
include "web_check.php";
include "star_connection.php";
$changepassword = "changepassword";
$notification_message = "notification_message";
$branch_master = "branch_master";
$te_master = "te_master";
$engineer_master = "engineer_master";

function get_branch_name_by_id($bid){
$bnm = "";
$branch_master = "branch_master";
$bid = $bid ? trim($bid) : "" ;
	if($bid!=""){
	$sql1 = "select `branch_name` from $branch_master where `branch_code`='".$bid."' ";
	$res1 = mysqli_query($conn,$sql1);
	$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
	$row1=mysqli_fetch_assoc($res1);
	$bnm = trim($row1["branch_name"]);	
	}	 
	}
return $bnm;
}

function get_te_name_by_id($bid){
$bnm = "";
$te_master = "te_master";
$bid = $bid ? trim($bid) : "" ;
	if($bid!=""){
	$sql1 = "select `te_name` from $te_master where `te_code`='".$bid."' ";
	$res1 = mysqli_query($conn,$sql1);
	$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
	$row1=mysqli_fetch_assoc($res1);
	$bnm = trim($row1["te_name"]);	
	}	 
	}
return $bnm;
}

function get_engineer_name_by_id($bid){
$bnm = "";
$engineer_master = "engineer_master";
$bid = $bid ? trim($bid) : "" ;
	if($bid!=""){
	$sql1 = "select `e_name` from $engineer_master where `eid`='".$bid."' ";
	$res1 = mysqli_query($conn,$sql1);
	$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
	$row1=mysqli_fetch_assoc($res1);
	$bnm = trim($row1["e_name"]);	
	}	 
	}
return $bnm;
}


$img_dir = "noty_images/";
$server_url1 = "http://" . $_SERVER['SERVER_NAME']."/starstellar/";
$image_link_url = $server_url1."admin/noty_images/";
$mime_type_array = array("image/jpeg", "image/png","image/jpg");
$mime_type_array_pdf = array("application/pdf");
$n_dlt_msg = "";
$n_msg = "";
$submsg = "";
$add_page_name = "send_notification.php";
$page_name = "send_notification.php";

$sql2 = "select `branch_name`,`branch_code` from $branch_master order by `branch_name` asc";
$res2 = mysqli_query($conn,$sql2);
$totres2 = mysqli_num_rows($res2);

$sql4 = "select `te_code`,`te_name` from $te_master where acedns='Y' order by `te_name` asc";
$res4 = mysqli_query($conn,$sql4);
$totres4 = mysqli_num_rows($res4);

$sql6 = "select `eid`,`e_name`,`e_mobile` from $engineer_master where status='ACTIVE' order by `e_name` asc";
$res6 = mysqli_query($conn,$sql6);
$totres6 = mysqli_num_rows($res6);


if(isset($_GET["n_msg"]) and $_GET["n_msg"]!=""){
$n_msg = $_GET["n_msg"];
}else{
$n_msg = "";
}
if(isset($_GET["n_dlt_msg"]) and $_GET["n_dlt_msg"]!=""){
$n_dlt_msg = $_GET["n_dlt_msg"];
}else{
$n_dlt_msg = "";
}

if($_POST["snd_btn"]=="Send"){
$the_noty_title = $_POST["noty_title"] ? addslashes(trim($_POST["noty_title"])) : "";
	$the_noty_msg = $_POST["noty_msg"] ? addslashes(trim($_POST["noty_msg"])) : "";
	$the_radio_user_type = $_POST["radio_user_type"] ? $_POST["radio_user_type"] : "ALL";
	
	$the_sl_branch = $_POST["sl_branch"] ? $_POST["sl_branch"] : "";
	$the_sl_te_code = $_POST["sl_te"] ? $_POST["sl_te"] : "";
	$the_sl_eng = $_POST["sl_eng"] ? $_POST["sl_eng"] : "";
	
	$the_radio_file_type = $_POST["radio_file_type"] ? addslashes(trim($_POST["radio_file_type"])) : "NONE";
	if($the_radio_file_type=="IMAGE"){
$nimage_file_name = $_FILES["nimage_file"]["name"];
$nimage_file_type = $_FILES["nimage_file"]["type"];
$nimage_file_size = $_FILES["nimage_file"]["size"];
$nimage_file_tmp = $_FILES["nimage_file"]["tmp_name"];
	}else if($the_radio_file_type=="PDF"){
$nimage_file_name = $_FILES["npdf_file"]["name"];
$nimage_file_type = $_FILES["npdf_file"]["type"];
$nimage_file_size = $_FILES["npdf_file"]["size"];
$nimage_file_tmp = $_FILES["npdf_file"]["tmp_name"];		
	}else{
$nimage_file_name = "";
$nimage_file_type = "";
$nimage_file_size = "";
$nimage_file_tmp = "";	
	}
	if($the_radio_user_type=="BRANCH_WISE_TE" && $the_sl_branch==""){
		$n_msg = "Please select branch.";
	}else if($the_radio_user_type=="BRANCH_WISE_ENGINEER" && $the_sl_branch==""){
		$n_msg = "Please select branch.";
	}else if($the_radio_user_type=="SINGLE_TE" && $the_sl_te_code==""){
		$n_msg = "Please select a TE.";
	}else if($the_radio_user_type=="SINGLE_ENGINEER" && $the_sl_eng==""){
		$n_msg = "Please select an engineer.";
	}else if($the_noty_title==""){
		$n_msg = "Please enter title";
	}else if($the_noty_msg==""){
		$n_msg = "Please enter message";
	}else{
		$curr_date_time = date("Y-m-d H:i:s");
		if($the_radio_user_type=="SINGLE_TE"){
			$the_single_user_id = $the_sl_te_code;
		}else if($the_radio_user_type=="SINGLE_ENGINEER"){
			$the_single_user_id = $the_sl_eng;
		}else{
			$the_single_user_id = "";
		}
		if($the_radio_file_type=="IMAGE"){
				if($nimage_file_name!=""){
				if(!in_array($nimage_file_type,$mime_type_array)){
				$n_msg = 'Please select an image file(PNG,JPG).';
				}else{
				$nimage_file_name = str_replace(" ","_",$nimage_file_name);
				$nimage_file_name = str_replace("-","_",$nimage_file_name);		
				$new_file_name = "icon_".time()."_".$nimage_file_name;
				$file_up = move_uploaded_file($nimage_file_tmp, $img_dir.$new_file_name);
				$sql_in = "insert into $notification_message (`title`,`message`,`image_name`,`file_type`,`user_type`,`branch_code`,`single_user_id`,`date_time`) values('$the_noty_title','$the_noty_msg','$new_file_name','$the_radio_file_type','$the_radio_user_type','$the_sl_branch','$the_single_user_id','$curr_date_time')";
				$res_in = mysqli_query($conn,$sql_in);
				if($res_in){
				$new_gen_noty_id = mysqli_insert_id($conn);
				$n_msg = 'Notification sending process has been staretd successfully.';				
				}else{
				$n_msg = 'Something went wrong. Please try later.';
				}
				}
				}else{
				$n_msg = 'Please select image file.';
				}
		}else if($the_radio_file_type=="PDF"){
			if($nimage_file_name!=""){
			if(!in_array($nimage_file_type,$mime_type_array_pdf)){
			$n_msg = 'Please select an PDF file.';
			}else{
			$nimage_file_name = str_replace(" ","_",$nimage_file_name);
			$nimage_file_name = str_replace("-","_",$nimage_file_name);		
			$new_file_name = "pdf_".time()."_".$nimage_file_name;
			$file_up = move_uploaded_file($nimage_file_tmp, $img_dir.$new_file_name);
			$sql_in = "insert into $notification_message (`title`,`message`,`image_name`,`file_type`,`user_type`,`branch_code`,`single_user_id`,`date_time`) values('$the_noty_title','$the_noty_msg','$new_file_name','$the_radio_file_type','$the_radio_user_type','$the_sl_branch','$the_single_user_id','$curr_date_time')";
			$res_in = mysqli_query($conn,$sql_in);
			if($res_in){
			$new_gen_noty_id = mysqli_insert_id($conn);
			$n_msg = 'Notification sending process has been staretd successfully.';				
			}else{
			$n_msg = 'Something went wrong. Please try later.';
			}
			}
			}else{
			$n_msg = 'Please select PDF file.';
			}
		}else{
			$sql_in = "insert into $notification_message (`title`,`message`,`user_type`,`branch_code`,`single_user_id`,`date_time`) values('$the_noty_title','$the_noty_msg','$the_radio_user_type','$the_sl_branch','$the_single_user_id','$curr_date_time')";
			$res_in = mysqli_query($conn,$sql_in);
			if($res_in){
			$new_gen_noty_id = mysqli_insert_id($conn);
			$n_msg = 'Notification sending process has been staretd successfully.';
			}else{
			$n_msg = 'Something went wrong. Please try later.';
			}
		}
				
	}
}

if(@$_GET["dlt_noty_id"] && $_GET["dlt_noty_id"]!=''){
 $dlt_noty_id = $_GET["dlt_noty_id"];
$sql8 = "select `id`,`image_name` from $notification_message where `id`='$dlt_noty_id'";
$res8 = mysqli_query($conn,$sql8);
$totres8 = mysqli_num_rows($res8);
if($totres8>0){
	$row8 = mysqli_fetch_assoc($res8);
	$the_image_name_dlt = $row8["image_name"] ? trim($row8["image_name"]) : "";
$news_img_sql = "delete from $notification_message where `id`='$dlt_noty_id'";
$news_img_res = mysqli_query($conn,$news_img_sql);
if($the_image_name_dlt!=""){
	if(file_exists($img_dir.$the_image_name_dlt)){
		unlink($img_dir.$the_image_name_dlt);
	}
}
$n_dlt_msg = 'The notification details successfully deleted.';
}else{
$n_dlt_msg = "";
}
header("location:".$page_name."?n_dlt_msg=".$n_dlt_msg);
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

$pgsql = "select `id` from $notification_message";
$pgres = mysqli_query($conn,$pgsql);
$total_pgres = mysqli_num_rows($pgres);
$start_from = (($page-1)*$limit);
$prev = $page - 1;							//previous page is page - 1
$next = $page + 1;							//next page is page + 1
$lastpage = ceil($total_pgres/$limit);   //lastpage is = total pages / items per page, rounded up.
$lpm1 = $lastpage - 1;

/*---------PAGINATION RELATED CODE START----------*/

include "web_header.php";
if($new_gen_noty_id!=""){

?>
<script type="text/javascript">
jQuery(function(){
var xhr2;
function call_bg_noty(the_noty_id){
xhr2 = jQuery.ajax({
url: 'ajax_call_bg_noty.php',
type: 'post',
dataType: 'json',
data: "the_noty_id="+the_noty_id,
success: function(response){			
},
timeout : 0
});
setTimeout(function(){
if(xhr2 && xhr2.readystate != 4){
xhr2.abort();
}
},5000);
}
	var the_noty_id = "<?php echo $new_gen_noty_id;?>";
	call_bg_noty(the_noty_id);
});
</script>
<?php 

} ?>
<style>
.estarix_cls{
	color:#F00;
	margin-left:5px;
}
.title_err_cls,.msg_err_cls,.img_err_cls,.pdf_err_cls,.branch_err_cls,.te_err_cls,.eng_err_cls{
	color:#F00;
	margin-left:5px;
}
.noty_curr_count,.noty_count_loader,.show_noty_sending_count{
	float:left;
	margin-left:5px;
}
.noty_curr_count{
	width:35px;
}
.noty_count_loader{
	width:24px;
}
.show_noty_sending_count{
	width:20px;
}
.show_noty_sending_count img{
	width:100%;;
}
.clear_class{
	clear:both;
	display:block;
}
.n_image_upload_section,.n_pdf_upload_section,.n_branch_section,.n_te_section,.n_eng_section{
	display:none;
}
.cls_ubnms{
	width:100%;
	display:block;
	color:#F00;
	font-weight:bold;
	margin-top:5px;
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
                          <h2>Notification</h2>
                        </div>
                        <div class="body" style="padding:20px;">
<form action="" method="post" enctype="multipart/form-data" id="send_notification_form" class="send_notification_form">
<div class="form-group">
<label class="Select File">Select User Type :</label>
<div style=" text-align:left;padding-top:10px;">
<input name="radio_user_type" id="radio_user_type_all" value="ALL" checked="checked" type="radio">
<label for="radio_user_type_all">ALL TE & ENGINEERS</label>
<input name="radio_user_type" id="radio_user_type_engineer" value="ENGINEER" type="radio">
<label for="radio_user_type_engineer">ALL ENGINEERS</label>
<input name="radio_user_type"  id="radio_user_type_te" value="TE" type="radio">
<label for="radio_user_type_te">ALL TE</label>
<input name="radio_user_type"  id="radio_user_type_branch_wise_te" value="BRANCH_WISE_TE" type="radio">
<label for="radio_user_type_branch_wise_te">BRANCH WISE TE</label>
<input name="radio_user_type"  id="radio_user_type_branch_wise_engineer" value="BRANCH_WISE_ENGINEER" type="radio">
<label for="radio_user_type_branch_wise_engineer">BRANCH WISE ENGINEER</label>
<input name="radio_user_type"  id="radio_user_type_single_te" value="SINGLE_TE" type="radio">
<label for="radio_user_type_single_te">SINGLE TE</label>
<input name="radio_user_type"  id="radio_user_type_single_engineer" value="SINGLE_ENGINEER" type="radio">
<label for="radio_user_type_single_engineer">SINGLE ENGINEER</label>

</div>
</div>

<div class="form-group n_branch_section">
<label class="Select Branch">Select Branch<span class="estarix_cls">*</span><span class="branch_err_cls"></span></label>
<select class="form-control" id="sl_branch" name="sl_branch" data-placeholder="Choose Branch Name...">
<option value="">Select Branch</option>
<?php
if($totres2>0){
	while($row2=mysqli_fetch_assoc($res2)){
		$the_branch_code = trim($row2["branch_code"]);
		$the_branch_name = trim($row2["branch_name"]);
		if($the_branch_code!="" && $the_branch_name!=""){ ?>
			<option value="<?php echo $the_branch_code;?>"><?php echo $the_branch_name." (".$the_branch_code.")";?></option>
	<?php	}
	}
}
?>
</select>
</div>

<div class="form-group n_te_section">
<label class="Select TE">Select TE<span class="estarix_cls">*</span><span class="te_err_cls"></span></label>
<select class="form-control" id="sl_te" name="sl_te" data-placeholder="Choose TE Name...">
<option value="">Select TE</option>
<?php
if($totres4>0){
	while($row4=mysqli_fetch_assoc($res4)){
		$the_te_code = trim($row4["te_code"]);
		$the_te_name = trim($row4["te_name"]);
		if($the_te_code!="" && $the_te_name!=""){ ?>
			<option value="<?php echo $the_te_code;?>"><?php echo $the_te_name." (".$the_te_code.")";?></option>
	<?php	}
	}
}
?>
</select>
</div>

<div class="form-group n_eng_section">
<label class="Select Engineer">Select Engineer<span class="estarix_cls">*</span><span class="eng_err_cls"></span></label>
<select class="form-control" id="sl_eng" name="sl_eng" data-placeholder="Choose Engineer Name...">
<option value="">Select Engineer</option>
<?php
if($totres6>0){
	while($row6=mysqli_fetch_assoc($res6)){
		$the_eid = trim($row6["eid"]);
		$the_e_name = trim($row6["e_name"]);
		$the_e_mobile = trim($row6["e_mobile"]);
		if($the_eid!="" && $the_e_name!=""){ ?>
			<option value="<?php echo $the_eid;?>"><?php echo $the_e_name." (".$the_e_mobile.")";?></option>
	<?php	}
	}
}
?>
</select>
</div>

<div class="form-group">
<label class="Enter Title">Enter Title <span class="estarix_cls">*</span><span class="title_err_cls"></span></label>
<div class="form-line">
<input type="text" name="noty_title"  id="noty_title" class="form-control"  />
</div>
</div>
<div class="form-group">
<label class="form-label">Enter Message<span class="estarix_cls">*</span><span class="msg_err_cls"></span></label>
<div class="form-line">
<textarea name="noty_msg"  id="noty_msg"  cols="30" rows="2" class="form-control no-resize"></textarea>
</div>
</div>

<div class="form-group">
<label class="Select File">Select File</label>
<div class="demo-radio-button">
<input name="radio_file_type" id="radio_file_type_none" value="NONE" checked="checked" type="radio">
<label for="radio_file_type_none">NONE</label>
<input name="radio_file_type" id="radio_file_type_image" value="IMAGE" type="radio">
<label for="radio_file_type_image">IMAGE</label>
<input name="radio_file_type"  id="radio_file_type_pdf" value="PDF" type="radio">
<label for="radio_file_type_pdf">PDF</label>
</div>
</div>
<div class="form-group n_image_upload_section">
<label class="Select Image">Select Image<span class="estarix_cls">*</span><span class="img_err_cls"></span></label>
<input type="file" class="form-control" id="nimage_file" name="nimage_file" placeholder="Select Image file">
</div>
<div class="form-group n_pdf_upload_section">
<label class="Select PDF">Select PDF<span class="estarix_cls">*</span><span class="pdf_err_cls"></span></label>
<input type="file" class="form-control" id="npdf_file" name="npdf_file" placeholder="Select PDF file">
</div>

<input type="submit" class="btn btn-primary waves-effect snd_btn" name="snd_btn" id="snd_btn1"  value="Send" />   
<span class="loaddr_msg" id="loaddr_msg"><?php if($n_msg!=""){ echo $n_msg;}?></span>
</form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Examples -->
            <!-- Exportable Table -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                        <h2>Notification List (<?php echo $total_pgres;?>)&nbsp;&nbsp;&nbsp;<span class="loaddr_dlt_msg" id="loaddr_dlt_msg"><?php if($n_dlt_msg!=""){ echo $n_dlt_msg;}?></span></h2>
                        </div>
                        <div class="body" style="padding:20px;">
                        <?php
echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
?>
<span style="display:block; clear:both;"></span>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                        <th>Title</th>
                                        <th>Message </th>
                                        <th>Image/PDF</th>
                                        <th>User Type</th>
                                        <th style="text-align:center;">Count</th>
                                        <th>Status</th>
                                        <th>Date Time</th>
                                        <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                        <th>Title</th>
                                        <th>Message </th>
                                        <th>Image/PDF</th>
                                        <th>User Type</th>
                                        <th style="text-align:center;">Count</th>
                                        <th>Status</th>
                                        <th>Date Time</th>
                                        <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select * from $notification_message order by `id` desc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$n_id = $row1["id"];
		$title = $row1["title"];
		$message = $row1["message"];
		$file_type = $row1["file_type"];
		$image_name = $row1["image_name"] ? trim($row1["image_name"]) : "";
		if($image_name!=""){
			if(file_exists($img_dir.$image_name)){
			$m_image_link = $image_link_url.$image_name;
			}else{
			$m_image_link ="";
			}
		}else{
			$m_image_link ="";
		}
		
        $sending_count = $row1["sending_count"];
		$sending_status = $row1["status"];
		$user_type = $row1["user_type"];
		$date_time = $row1["date_time"];
		
		$the_selected_branch_code = $row1["branch_code"] ? trim($row1["branch_code"]) : "";
		$the_single_user_id = $row1["single_user_id"] ? trim($row1["single_user_id"]) : "";
?>
<tr>
<td><?php echo $title;?></td>
<td><?php echo $message;?></td>
<td><?php
if($file_type=="IMAGE"){
if($m_image_link!=""){ ?>
	<a class='show_image_pdf_scheme_inline' href="<?php echo $m_image_link;?>"><img src="<?php echo $m_image_link; ?>" style="width:50px;" /></a>
<?php }else{
	echo "NONE";
}
}else if($file_type=="PDF"){ 
if($m_image_link!=""){ ?>
	<a class='btn bg-red waves-effect show_image_pdf_scheme' href="<?php echo $m_image_link;?>">View PDF</a>
<?php }else{
	echo "NONE";
}}else{
echo "NONE";	
}
?></td>
<td>
<?php
echo $user_type;
?>
<span class="cls_ubnms">
<?php
if($user_type=="BRANCH_WISE_TE" || $user_type=="BRANCH_WISE_ENGINEER"){
	$selected_branch_name = get_branch_name_by_id($the_selected_branch_code);
	echo $selected_branch_name." (".$the_selected_branch_code.")";
}else if($user_type=="SINGLE_TE"){
	$selected_te_name = get_te_name_by_id($the_single_user_id);
	echo $selected_te_name." (".$the_single_user_id.")";
}else if($user_type=="SINGLE_ENGINEER"){
	$selected_engineer_name = get_engineer_name_by_id($the_single_user_id);
	echo $selected_engineer_name;
}
?>
</span>
</td>
<td style="text-align:center;"><?php 
if($sending_status=="END"){
	echo $sending_count;
}else{
	?>
<span id="noty_curr_count_<?php echo $n_id;?>" class="noty_curr_count"><?php echo $sending_count;?></span>
<a href="javascript:void(0);" class="show_noty_sending_count" id="show_noty_sending_count_btn_<?php echo $n_id;?>" the_noty_id="<?php echo $n_id;?>"><img src="images/refresh_btn.png" /></a>
<span id="noty_count_loader_<?php echo $n_id;?>" class="noty_count_loader"></span>
<span class="clear_class"></span>
<?php 
}
?></td>
<td><?php 
if($sending_status=="END"){
echo $sending_status;
}else{ ?>
<span id="noty_curr_sts_<?php echo $n_id;?>"><?php echo $sending_status;?></span>
<?php }
?></td>
<td><?php echo $date_time;?></td>
<td><a href="javascript:void(0);" class="allbtns2 dlt_noty_cls" dlt_noty_id="<?php echo $n_id;?>"><img class="ved_img" src="images/delete.png" style=" width:32px;"></a></td>
</tr>
<?php
}

}else{
?>
<tr>
<td style="text-align:center" colspan="9">No notification found.</td>
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
            <!-- #END# Exportable Table -->
        </div>
    </section>
<script type="text/javascript">
jQuery(function(){
jQuery(".show_image_pdf_scheme").colorbox({iframe:true, width:"90%", height:"95%"});
jQuery(".show_image_pdf_scheme_inline").colorbox();

jQuery('#sl_branch').chosen({width:"100%",no_results_text:'Oops, no branch found!'});
jQuery('#sl_te').chosen({width:"100%",no_results_text:'Oops, no TE found!'});
jQuery('#sl_eng').chosen({width:"100%",no_results_text:'Oops, no engineer found!'});

setTimeout(function(){
	jQuery("#loaddr_msg").html("");
	jQuery("#loaddr_dlt_msg").html("");
},8000);

jQuery(document).on('click', '.dlt_noty_cls', function(event){
var dlt_noty_id = jQuery(this).attr("dlt_noty_id");
if(dlt_noty_id!=''){
	var r = confirm("Do you want to delete the notification details?");
	if (r == true) {
	window.location = '<?php echo $page_name;?>?dlt_noty_id='+dlt_noty_id+'&paged=<?php echo $page;?>';
	} else {
	return false;
	}
}
}); 

jQuery('input[name="radio_user_type"]').change(function(){
var radio_user_type_val = jQuery(this).val();
if(radio_user_type_val=="BRANCH_WISE_TE" || radio_user_type_val=="BRANCH_WISE_ENGINEER"){
	jQuery(".n_branch_section").show();
	jQuery(".n_te_section").hide();
	jQuery(".n_eng_section").hide();
}else if(radio_user_type_val=="SINGLE_TE"){
	jQuery(".n_branch_section").hide();
	jQuery(".n_te_section").show();
	jQuery(".n_eng_section").hide();
}else if(radio_user_type_val=="SINGLE_ENGINEER"){
	jQuery(".n_branch_section").hide();
	jQuery(".n_te_section").hide();
	jQuery(".n_eng_section").show();
}else{
	jQuery(".n_branch_section").hide();
	jQuery(".n_te_section").hide();
	jQuery(".n_eng_section").hide();
}

});

jQuery('input[name="radio_file_type"]').change(function(){
        var curr_f_type_val = jQuery(this).val();
		if(curr_f_type_val=="IMAGE"){
			jQuery(".n_image_upload_section").show();
			jQuery(".n_pdf_upload_section").hide();
		}else if(curr_f_type_val=="PDF"){
			jQuery(".n_pdf_upload_section").show();
			jQuery(".n_image_upload_section").hide();
		}else{
			jQuery(".n_image_upload_section").hide();
			jQuery(".n_pdf_upload_section").hide();
		}
    });

jQuery(".show_noty_sending_count").click(function(){
	var the_n_id = jQuery.trim(jQuery(this).attr("the_noty_id"));
	if(the_n_id!=""){
		var n_count_elmnt = jQuery("#noty_curr_count_"+the_n_id);
		var n_loader_elmnt = jQuery("#noty_count_loader_"+the_n_id);
		var show_noty_sending_count_btn = jQuery("#show_noty_sending_count_btn_"+the_n_id);
		var noty_curr_sts = jQuery("#noty_curr_sts_"+the_n_id);
		
		var img = '<img src="images/ajax-loader.gif">';
		jQuery(n_loader_elmnt).html(img);
	jQuery.ajax({
		url: 'ajax_show_noty_sending_count_and_status_by_id.php',
		type: 'post',
		dataType: 'json',
		data: "the_n_id="+the_n_id,
		success: function(response){
			if(response.process_sts=="YES"){
				jQuery(n_loader_elmnt).html("");
				var n_cr_sts = response.n_cr_sts;
				var n_cr_cnt = response.n_cr_cnt;
				jQuery(n_count_elmnt).html(n_cr_cnt);
				jQuery(noty_curr_sts).html(n_cr_sts);
				if(n_cr_sts=="END"){
					show_noty_sending_count_btn.hide();
					n_count_elmnt.css("float","none");
				}
			}else{
				jQuery(n_loader_elmnt).html("");
				alert(response.process_msg);
			}
			
		},
		timeout : 0
		});
	}
});



jQuery("form#send_notification_form").submit(function(){
	var radio_user_type = jQuery("input[name='radio_user_type']:checked").val();
	var noty_title = jQuery.trim(jQuery("#noty_title").val());
	var noty_msg = jQuery.trim(jQuery("#noty_msg").val());
	var radio_file_type = jQuery("input[name='radio_file_type']:checked").val();
	var sl_branch = jQuery("#sl_branch").val();
	var sl_te = jQuery("#sl_te").val();
	var sl_eng = jQuery("#sl_eng").val();
	var nimage_file = jQuery.trim(jQuery("#nimage_file").val());
	var npdf_file = jQuery.trim(jQuery("#npdf_file").val());
	
	if(radio_user_type=="BRANCH_WISE_TE" && sl_branch==""){
		jQuery(".branch_err_cls").html("Please select branch.");
		jQuery("#sl_branch").focus();
		setTimeout(function(){
			jQuery(".branch_err_cls").html("");
		},10000);
		return false;
	}else if(radio_user_type=="BRANCH_WISE_ENGINEER" && sl_branch==""){
		jQuery(".branch_err_cls").html("Please select branch.");
		jQuery("#sl_branch").focus();
		setTimeout(function(){
			jQuery(".branch_err_cls").html("");
		},10000);
		return false;
	}else if(radio_user_type=="SINGLE_TE" && sl_te==""){
		jQuery(".te_err_cls").html("Please select a TE.");
		jQuery("#sl_te").focus();
		setTimeout(function(){
			jQuery(".te_err_cls").html("");
		},10000);
		return false;
	}else if(radio_user_type=="SINGLE_ENGINEER" && sl_eng==""){
		jQuery(".eng_err_cls").html("Please select an engineer.");
		jQuery("#sl_eng").focus();
		setTimeout(function(){
			jQuery(".eng_err_cls").html("");
		},10000);
		return false;
	}else if(noty_title==""){
		jQuery(".title_err_cls").html("Please enter title.");
		jQuery("#noty_title").focus();
		setTimeout(function(){
			jQuery(".title_err_cls").html("");
		},10000);
		return false;
	}else if(noty_msg==""){
		jQuery(".msg_err_cls").html("Please enter message.");
		jQuery("#noty_msg").focus();
		setTimeout(function(){
			jQuery(".msg_err_cls").html("");
		},10000);
		return false;
	}else if(radio_file_type=="IMAGE" && nimage_file==""){
		jQuery(".img_err_cls").html("Please select image file.");
		jQuery("#nimage_file").focus();
		setTimeout(function(){
			jQuery(".img_err_cls").html("");
		},10000);
		return false;
	}else if(radio_file_type=="PDF" && npdf_file==""){
		jQuery(".pdf_err_cls").html("Please select PDF file.");
		jQuery("#npdf_file").focus();
		setTimeout(function(){
			jQuery(".pdf_err_cls").html("");
		},10000);
		return false;
	}else{
		return true;
	}
});



});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>