<?php
include "web_check.php";
include "star_connection.php";
$table_name = "employee_master";
$changepassword = "changepassword";
if(@isset($_GET["submsg"]) && $_GET["submsg"]!=""){
	$submsg =$_GET["submsg"];
}else{
	$submsg ="";
}
$add_page_name = "edit_dealer_list.php";
$page_name = "dealer_list.php";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

if(@$_POST["update"]=="Update"){
$upthe_theempid = $_POST["upthe_theempid"] ? addslashes(trim($_POST["upthe_theempid"])) : "";
$emp_phone = $_POST["emp_phone"] ? addslashes(trim($_POST["emp_phone"])) : "";
$the_pno = $_POST["the_pno"] ? trim($_POST["the_pno"]) : "1";
	if($emp_phone==''){
		$submsg = 'Please enter phone number.';
		$res_colour = 2;
	}else{
		
$sql8 = "select `emp_code` from $table_name where `phone_no`='$emp_phone' and `acedns`='Y' and `emp_code`!='$upthe_theempid'";
	$res8 = mysqli_query($conn,$sql8);
	$totres8 = mysqli_num_rows($res8);
	if($totres8>0){
	$submsg = 'Phone number already exist. Please use another number.';
	header("location:$add_page_name?theempid=$upthe_theempid&paged=$the_pno&submsg=".$submsg);
	}else{
		
$sql5 = "update $table_name set `phone_no`='$emp_phone' where `emp_code`='$upthe_theempid'";
$res5 = mysqli_query($conn,$sql5);
$submsg = 'Phone number successfully updated.';
header("location:$add_page_name?theempid=$upthe_theempid&paged=$the_pno&submsg=".$submsg);
	}

}		
	
	}

if(@isset($_GET["theempid"]) && $_GET["theempid"]!=""){
	$theempid = $_GET["theempid"] ? trim($_GET["theempid"]) : "";
	$sql8 = "select * from $table_name where `emp_code`='$theempid'";
	$res8 = mysqli_query($conn,$sql8);
	$totres8 = mysqli_num_rows($res8);
	if($totres8>0){
		$row8 = mysqli_fetch_assoc($res8);
		$dns_emp_code = $row8["dns_emp_code"];
		$emp_name = $row8["emp_name"];
		$acedns = $row8["acedns"];
		$phone_no = $row8["phone_no"];
		
	}else{
		$theempid = "";
		$dns_emp_code = "";
		$emp_name = "";
		$acedns = "";
		$phone_no = "";
	}	
}else{
	$theempid = "";
	$dns_emp_code = "";
	$emp_name = "";
	$acedns = "";
	$phone_no = "";	
}


include "web_header.php";
?>
<script type="text/javascript">
jQuery(function () {

	
});
</script>
<section class="content">
        <div class="container-fluid">
            <div class="block-header">
                
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                          <h2>Edit Dealer &nbsp;&nbsp;&nbsp; <?php if($submsg!=""){ echo $submsg;}?></h2>
                        </div>
                        <div class="body">
 <div class="table-responsive">
 <form action="" method="POST" enctype="multipart/form-data">
<div class="row clearfix" style="margin:0px;">
<div class="col-sm-12">
    <div class="form-group">
    <label for="dealer_id">Dealer ID : <?php echo $dns_emp_code;?></label>
    </div>
    <div class="form-group">
    <label for="dealer_name">Dealer Name : <?php echo $emp_name;?></label>
    </div>
     <div class="form-group">
    <label for="active_status">Active Status : <?php echo $acedns;?></label>
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <div class="form-line">
        <input type="text" name="emp_phone" class="form-control emp_phone" id="emp_phone" value="<?php echo $phone_no;?>"  />
        </div>
    </div>

<div class="form-group" style="text-align:center;">
       <?php if($theempid!=""){ ?>
    <input type="hidden" name="upthe_theempid" value="<?php echo $theempid;?>" />
    <input type="hidden" name="the_pno" value="<?php echo $page;?>" />
	<input type="submit" class="btn bg-red waves-effect srch_btn" name="update" style="margin-bottom:10px;" value="Update" />
	<?php } ?>
    <a href="<?php echo $page_name."?paged=".$page;?>" class="btn bg-red waves-effect" style="margin-left: 20px;margin-bottom:10px;">Back To Dealer List</a>
</div>    
</div>
</div>
</form>                   
                            </div>
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
	
	jQuery(".clemply").click(function(){
		var ancr_elmnt = jQuery(this);
		var clemplyid = ancr_elmnt.attr("clemplyid");
		if(clemplyid!=""){
			var theldrid = "ca_ldr_"+clemplyid;
			var for_loader = jQuery("#"+theldrid);
			for_loader.html(imgs);
			jQuery.ajax({
			url: 'ajax_clear_allocation_by_emp_id.php',
			type: 'post',
			dataType: "JSON",
			data: "clemplyid="+clemplyid,
			success: function(response){
			if(response.process_status=="YES"){
				ancr_elmnt.html("--");
			for_loader.html(done_img);
			setTimeout(function (){
			for_loader.html("");
			},3000);
			}else{
			for_loader.html("");
			alert(response.process_message);
			}
			}
			});
		}
	});
	
	
	
	jQuery(".srch_btn").click(function(){
		var srch_dlr_dtls = jQuery("#srch_dlr_dtls").val();
		var sl_dlr_actdat = jQuery("#sl_dlr_actdat").val();
		var sl_dlr_alocated_type = jQuery("#sl_dlr_alocated_type").val();		
		var qstring ="";
		var amp = "";
		if(srch_dlr_dtls!="" || sl_dlr_actdat!="" || sl_dlr_alocated_type!=""){
		if(srch_dlr_dtls!=""){
			if(qstring!=""){
				qstring = qstring+"&srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}else{
				qstring = qstring+"srch_dlr_dtls="+encodeURIComponent(srch_dlr_dtls);
			}
		}
		if(sl_dlr_actdat!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_dlr_actdat="+sl_dlr_actdat;
			}else{
				qstring = qstring+"sl_dlr_actdat="+sl_dlr_actdat;
			}
		}
		if(sl_dlr_alocated_type!=""){
			if(qstring!=""){
				qstring = qstring+"&sl_dlr_alocated_type="+sl_dlr_alocated_type;
			}else{
				qstring = qstring+"sl_dlr_alocated_type="+sl_dlr_alocated_type;
			}
		}
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "dealer_list.php"+qstring;
		}else{
			alert("Please select atleast one field to search.");
		}
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "dealer_list.php";
	});
});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>