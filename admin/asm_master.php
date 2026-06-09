<?php
include "web_check.php";
include "star_connection.php";
$asm_master = "asm_master";
$supported_mime_type = array("image/jpeg","image/png","image/jpg");
$img_dir = "../gift_pic/";
$default_image_name = "profile.png";
$image_url_prefix = $server_url."gift_pic/";
$new_qry_string_filtered = "";
$msg_txt = "";
$csv_mimetypes = array(
    'text/csv',
    'application/csv',
	'application/x-csv',
    'text/comma-separated-values',
	'text/x-comma-separated-values',
	'text/tab-separated-values',
    'application/excel',
	'application/vnd.ms-excel'
);
$add_page_name = "edit_asm_master.php";
$page_name = "asm_master.php";
if(isset($_GET["msg_txt"]) && @$_GET["msg_txt"]!=""){
	$msg_txt = $_GET["msg_txt"];
}

if(@$_POST["bulk_upload_btn"]=="Import"){
$astn_upload_csv_file_name = $_FILES["upload_csv_file"]["name"];
$astn_upload_csv_file_tmp_name = $_FILES["upload_csv_file"]["tmp_name"];
$astn_upload_csv_file_type = $_FILES["upload_csv_file"]["type"];
	if($astn_upload_csv_file_name==''){
		$submsg = 'Please select a csv file.';
		$res_colour = 2;
	}else if(!in_array($astn_upload_csv_file_type, $csv_mimetypes)){
		$submsg = "Please select a csv file.";
		$res_colour = 2;
	}else{
		
		if(($handle = fopen($astn_upload_csv_file_tmp_name , "r")) !== FALSE) 
	{
		$data1 = fgetcsv($handle, 1000, ",");	
		$countscsvrow = 0;
		$countscsvrowupd = 0;
	while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){
$thecsv_branch_code = $data[0];
$thecsv_branch_name = $data[1];
$thecsv_asm_name = $data[2];
$thecsv_ph_no = $data[3];
$thecsv_email = $data[4];

$thecsv_branch_code = $thecsv_branch_code ? addslashes(trim($thecsv_branch_code)) : "";
$thecsv_branch_name = $thecsv_branch_name ? addslashes(trim($thecsv_branch_name)) : "";
$thecsv_asm_name = $thecsv_asm_name ? addslashes(trim($thecsv_asm_name)) : "";
$thecsv_ph_no = $thecsv_ph_no ? addslashes(trim($thecsv_ph_no)) : "";
$thecsv_email = $thecsv_email ? addslashes(trim($thecsv_email)) : "";

if($thecsv_branch_code!="" && $thecsv_email!=""){
	$sql2ckin = "select `branch_code` from $asm_master where `branch_code`='$thecsv_branch_code'";
	$res2ckin = mysqli_query($conn,$sql2ckin);
	$totres2ckin = mysqli_num_rows($res2ckin);
	if($totres2ckin>0){
	$sql5produp = "update $asm_master set `branch`='$thecsv_branch_name',`asm_name`='$thecsv_asm_name',`ph_no`='$thecsv_ph_no',`email`='$thecsv_email' where `branch_code`='$thecsv_branch_code'";
	$res5produp = mysqli_query($conn,$sql5produp);
	$countscsvrowupd++;
	}else{
	$sql5prodin = "insert into $asm_master (`branch_code`,`branch`,`asm_name`,`ph_no`,`email`) values ('$thecsv_branch_code','$thecsv_branch_name','$thecsv_asm_name','$thecsv_ph_no','$thecsv_email')";
	$res5prodin = mysqli_query($conn,$sql5prodin);
	if($res5prodin){			
	$countscsvrow++;
	}
	}	

}
	}
	fclose($handle);
	}

	$submsg = "ASM details successfully inserted: ".$countscsvrow." and updated: ".$countscsvrowupd." .";
		}	
	
	}


if(isset($_GET["dlt_asmid"]) && @$_GET["dlt_asmid"]!=""){
$dlt_asmid = $_GET["dlt_asmid"] ? trim($_GET["dlt_asmid"]) : "";
if($dlt_asmid!=""){		
$sqldel = "delete from $asm_master where `asm_id`='$dlt_asmid'";
$resdel = mysqli_query($conn,$sqldel);
}
$msg_txt = "ASM successfully deleted.";
header("location:".$page_name."?msg_txt=".$msg_txt);
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

$pgsql = "select `asm_id` from $asm_master ";
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
                          <h2>ASM Master (<?php echo $total_pgres;?>)&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo $add_page_name;?>" class="btn bg-red waves-effe">Add&nbsp;New&nbsp;ASM</a></h2>

                       <br>
                         <div class="col-lg-10 col-md-12 col-sm-12 col-xs-12" style="text-align:right">
                        <form action="" method="POST" enctype="multipart/form-data" name="the_upload_frm" class="the_upload_frm" id="the_upload_frm" style="display:inline-block;">
                        <input type="file" id="upload_csv_file" name="upload_csv_file" style="display:inline-block;width: 100px; font-size:16px;">
                        <input type="submit" class="btn bg-red waves-effect bulk_upload_btn" name="bulk_upload_btn" value="Import" style="display:inline-block;" />
                            </form>
                            </div>
                            
                               <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12" style="text-align:right">
                    
                          <a href="export_asm_master.php" class="btn bg-red waves-effe">Export</a>
                         </a>
                         </div>
                            
                          <span style="text-align: left;font-size: 12px;width: 246px;display: inline-block;" id="success_msg" ><?php echo $submsg; ?></span>
                          
                     
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
                                            <th>Branch Code</th>
                                            <th>Branch Name</th>
                                            <th>ASN Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Branch Code</th>
                                            <th>Branch Name</th>
                                            <th>ASN Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select * from $asm_master order by `branch_code` asc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$the_asm_id = $row1["asm_id"];
		$the_branch_code = $row1["branch_code"];
		$the_branch_name = $row1["branch"];
		$the_asm_name = $row1["asm_name"];
		$the_ph_no = $row1["ph_no"];
		$the_email = $row1["email"];
?>
<tr>
<td><?php echo $the_branch_code;?></td>
<td><?php echo $the_branch_name;?></td>
<td><?php echo $the_asm_name;?></td>
<td><?php echo $the_ph_no;?></td>
<td><?php echo $the_email;?></td>
<td>
<div>
<span class="adminActivityField2">
<a href="<?php echo $add_page_name;?>?edt_asm_id=<?php echo $the_asm_id;?>" class="btn bg-red waves-effe" style="margin-right:10px;">Edit</a>
<a href="javascript:void(0);" class="btn bg-red waves-effe delete_asm" dlt_asmid="<?php echo $the_asm_id;?>">Delete</a>
</span>
</div>
</td>
</tr>
<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="6">No data found.</td>
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

jQuery(".delete_asm").click(function(){
	var dlt_asmid = jQuery(this).attr("dlt_asmid");
	if(dlt_asmid!=""){
		var cb = confirm("Do you want to delete this ASM?");
		if(cb == true){
			window.location = "<?php echo $page_name;?>?dlt_asmid="+dlt_asmid;
		}
	}
});	



jQuery("form#the_upload_frm").submit(function(){
	var upload_csv_file = jQuery("#upload_csv_file").val();
	if(upload_csv_file=="" || upload_csv_file==null){
		alert("Please choose a csv file.");
		return false;
	}else{
		return true;
	}
});

setTimeout(function(){
    jQuery("#success_msg").html("");
},10000);	
	
});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>