<?php
include "web_check.php";
include "star_connection.php";
$branch_master = "branch_master";
$branch_schemes_pdf = "branch_schemes_PDF";
$brsql = "select `branch_code`,`branch_name` from $branch_master where `acedns`='Y' order by `branch_name` asc";
$brres = mysqli_query($conn,$brsql);
$total_brres = mysqli_num_rows($brres);
$curr_date = date("Y-m-d");
$new_qry_string_filtered = "";
$astn_branch_code = $_GET["astn_branch_code"] ? addslashes(trim($_GET["astn_branch_code"])) : "";
$astn_scheme_sts = $_GET["astn_scheme_sts"] ? addslashes(trim($_GET["astn_scheme_sts"])) : "";
$whr_str = "";
$search_array = array("astn_branch_code"=>$astn_branch_code,"astn_scheme_sts"=>$astn_scheme_sts);
foreach($search_array as $search_array_key=>$search_array_val){
	if($search_array_key=="astn_branch_code"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
$whr_str .= "$aand $branch_schemes_pdf.`branch_code` = '$search_array_val' ";
			$new_qry_string_filtered .= "&astn_branch_code=".$search_array_val;
		}
	}else if($search_array_key=="astn_scheme_sts"){
		if($search_array_val!=''){
			if(trim($whr_str)!=""){
				$aand = " and";
			}else{
				$aand = "";
			}
			if($search_array_val=='Active'){
$whr_str .= "$aand CURDATE() between $branch_schemes_pdf.`start_date` and $branch_schemes_pdf.`end_date` ";
			}else if($search_array_val=='Inactive'){
$whr_str .= "$aand  CURDATE() not between $branch_schemes_pdf.`start_date` and $branch_schemes_pdf.`end_date` ";
			}
			$new_qry_string_filtered .= "&astn_branch_code=".$search_array_val;
		}
	}
}

if($whr_str!=""){
	$new_whr_str = "where ".$whr_str;
}else{
	$new_whr_str ="";
}
$add_page_name = "add_new_schemes.php";
$page_name = "schemes.php";
$cnt = 0;
$countrow = 1;
/*---------PAGINATION RELATED CODE START----------*/

$adjacents = 4;
$targetpage = $page_name;
$limit = "100";
$page = $_GET['paged'] ? $_GET['paged'] : 1;

/*---------PAGINATION RELATED CODE END----------*/

/*---------PAGINATION RELATED CODE START----------*/

$pgsql = "select $branch_schemes_pdf.`branch_code` from $branch_schemes_pdf left join $branch_master on $branch_schemes_pdf.`branch_code`=$branch_master.`branch_code` $new_whr_str";
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
.add_top_bottom_padding2{
padding: 5px 8px;	
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
                          <h2>Schemes (<?php echo $total_pgres;?>)&nbsp;&nbsp;&nbsp;&nbsp;
                          <a href="add_new_schemes.php" class="btn bg-red waves-effe">ADD NEW SCHEME</a>
                          <?php /*?><a href="export_dealer.php?get_type=loggedin" class="btn bg-red waves-effe">Export&nbsp;loggedin&nbsp;dealer</a> &nbsp; <a href="export_dealer.php?get_type=notloggedin" class="btn bg-red waves-effe">Export&nbsp;not&nbsp;loggedin&nbsp;dealer</a><?php */?>
                          </h2>
                            <div class="row clearfix">
    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 add_top_bottom_padding2">
<select class="form-control" id="astn_branch_code" name="astn_branch_code" style="padding-left:2px;" data-placeholder="Select Branch Name">
<option value="">Select Branch Name</option>
<?php
if($total_brres>0){
	while($brrow=mysqli_fetch_assoc($brres)){
		$the_br_code = $brrow["branch_code"];
		$the_br_name = $brrow["branch_name"];?>
        <option value="<?php echo $the_br_code;?>" <?php if($the_br_code==$astn_branch_code){ ?> selected="selected" <?php } ?>><?php echo $the_br_name." (".$the_br_code.")";?></option>
		<?php
	}
	
}
?>

</select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">

<select class="form-control sl_scheme_sts" id="sl_scheme_sts" name="sl_scheme_sts">
<option value="">All</option>
<option value="Active" <?php if($astn_scheme_sts=="Active"){ ?> selected="selected" <?php }?> >Active</option>
<option value="Inactive" <?php if($astn_scheme_sts=="Inactive"){ ?> selected="selected" <?php }?> >Inactive</option>
</select>

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
                                            <th>Branch&nbsp;Name</th>
                                            <th>PDF&nbsp;File</th>
                                            <th>Start&nbsp;Date</th>
                                            <th>End&nbsp;Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Branch&nbsp;Name</th>
                                            <th>PDF&nbsp;File</th>
                                            <th>Start&nbsp;Date</th>
                                            <th>End&nbsp;Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$the_PDF_file_name_arr = array();
$the_PDF_file_link_arr = array();
$file_dir = "../schemes/";
$file_url_prefix = "http://starsaathi.com/schemes/";
$sql1 = "select $branch_schemes_pdf.*,$branch_master.`branch_name` from $branch_schemes_pdf left join $branch_master on $branch_schemes_pdf.`branch_code`=$branch_master.`branch_code` $new_whr_str order by $branch_schemes_pdf.`download_time` desc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$the_PDF_file_name_arr = array();
		$the_PDF_file_link_arr = array();
		$the_sl_no = $row1["sl_no"];
		$branch_code = $row1["branch_code"];
		$branch_name = $row1["branch_name"];
		$PDF_file_name = $row1["PDF_file_name"] ? trim($row1["PDF_file_name"]) : "";
		if($PDF_file_name!=""){
			$the_PDF_file_name_arr = explode("|",$PDF_file_name);
				if($the_PDF_file_name_arr>0){
					foreach($the_PDF_file_name_arr as $the_PDF_file_name_val){
					if(file_exists(($file_dir.$the_PDF_file_name_val))){
					$the_PDF_file_link_arr[] = $file_url_prefix.$the_PDF_file_name_val;
					}
					}
				}
		}
		$start_date = $row1["start_date"];
		$end_date = $row1["end_date"];
		$acedns = $row1["acedns"];
		$download_time = $row1["download_time"];
?>
<tr>
<td><?php echo $branch_name." (".$branch_code.")";?></td>
<td><?php 
if(count($the_PDF_file_link_arr)>0){
	
foreach($the_PDF_file_link_arr as $the_PDF_file_link_arr_val){
?>
<a class='btn bg-red waves-effect show_pdf_scheme' href="<?php echo $the_PDF_file_link_arr_val;?>" rel="group<?php echo $the_sl_no;?>">View</a>
<?php
}
}else{
	echo "NONE";
}
?></td>
<td>
<input type="text" class="form-control dp_start_dt " id="dp_start_dt_<?php echo $the_sl_no;?>" value="<?php echo $start_date;?>" readonly="readonly" placeholder="Choose start date"></td>
<td>
<input type="text" class="form-control dp_end_dt " id="dp_end_dt_<?php echo $the_sl_no;?>" value="<?php echo $end_date;?>" readonly="readonly" placeholder="Choose end date">
</td>
<td>
<a href="javascript:void(0);" class="btn bg-red waves-effect update_sl_no" style="display:inline-block;margin-right:5px;float:left;" the_sl_id="<?php echo $the_sl_no;?>">Update</a>
<span id="upd_ldr_<?php echo $the_sl_no;?>" style="display:inline-block;width:30px;height:30px;float:left;"></span>
<span style="display:block;clear:both;"></span>
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
jQuery('#astn_branch_code').chosen({width:"100%",no_results_text:'Oops, no branch found!',search_contains: true});	
jQuery(".show_pdf_scheme").colorbox({iframe:true, width:"90%", height:"95%"});	

jQuery(".dp_start_dt").datepicker({
changeMonth: true,
changeYear: true,
dateFormat: 'yy-mm-dd'
});
jQuery(".dp_end_dt").datepicker({
changeMonth: true,
changeYear: true,
dateFormat: 'yy-mm-dd'
});

 jQuery(".update_sl_no").click(function(){
	var the_sl_id = jQuery(this).attr("the_sl_id");
	if(the_sl_id!=""){
		var dp_start_dt = jQuery("#dp_start_dt_"+the_sl_id).val();
        var dp_end_dt = jQuery("#dp_end_dt_"+the_sl_id).val();
		
		var img1 = '<img src="images/ajax-loader.gif">';
var ldr_elmnt =jQuery("#upd_ldr_"+the_sl_id);
ldr_elmnt.html(img1);
jQuery.ajax({
url: 'ajax_update_scheme_date.php',
type: 'post',
dataType: 'json',
data: "the_sl_id="+the_sl_id+"&dp_start_dt="+dp_start_dt+"&dp_end_dt="+dp_end_dt,
success: function(response){
if(response.process_sts=="YES"){
ldr_elmnt.html("");
}else{
ldr_elmnt.html("");
}			
},
timeout : 0
});
		
	}
});

	jQuery(".srch_btn").click(function(){
		var astn_branch_code = jQuery("#astn_branch_code").val();
		var sl_scheme_sts = jQuery("#sl_scheme_sts").val();
		
		var qstring ="";
		var amp = "";
		
		if(astn_branch_code!=""){
			if(qstring!=""){
				qstring = qstring+"&astn_branch_code="+encodeURIComponent(astn_branch_code);
			}else{
				qstring = qstring+"astn_branch_code="+encodeURIComponent(astn_branch_code);
			}
		}
		
		if(sl_scheme_sts!=""){
			if(qstring!=""){
				qstring = qstring+"&astn_scheme_sts="+encodeURIComponent(sl_scheme_sts);
			}else{
				qstring = qstring+"astn_scheme_sts="+encodeURIComponent(sl_scheme_sts);
			}
		}
		
		  if(qstring!=""){
			 qstring = "?"+qstring; 
		  }
		window.location = "schemes.php"+qstring;
		
	});
	
	jQuery(".srch_reset_btn").click(function(){
		window.location = "schemes.php";
	});
});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>