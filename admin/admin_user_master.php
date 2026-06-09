<?php
include "web_check.php";
include "star_connection.php";
$admin_master = "admin_master";
$selected_menu_for_user = "selected_menu_for_user";
function get_selected_user_menus_by_uid($conn,$uid){
$qpnmvl = "Not set";
$submsg = '';
$nmsvalarr = array();
$menu_master = "menu_master";
$selected_menu_for_user = "selected_menu_for_user";
$uid = $uid ? trim($uid) : "" ;
	if($uid!=""){
	$sql1 = "select `menu_name` from $menu_master where `menu_id` in(select `menu_id` from $selected_menu_for_user where `user_id`='$uid') ";
	$res1 = mysqli_query($conn,$sql1);
	$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
	$the_menu_name_sho = trim($row1["menu_name"]);
	$nmsvalarr[] = $the_menu_name_sho;
	}
	$qpnmvl = implode(",",$nmsvalarr);
	}	 
	}
return $qpnmvl;
}
$add_page_name = "add_edit_admin_user_master.php";
$page_name = "admin_user_master.php";
$add_menu_access_page_name="admin_user_wise_menu_accessibility.php";

if(isset($_GET["submsg"]) && @$_GET["submsg"]!=''){
$submsg = $_GET["submsg"];
}


if(isset($_GET["dlt_admin_id"]) && @$_GET["dlt_admin_id"]!=''){
 $dlt_admin_id = $_GET["dlt_admin_id"];
$news_img_sql = "delete from $admin_master where `id`='$dlt_admin_id' and `user_type`='MANAGER'";
$news_img_res = mysqli_query($conn,$news_img_sql);
$news_img_sql2 = "delete from $selected_menu_for_user where `user_id`='$dlt_admin_id'";
$news_img_res2 = mysqli_query($conn,$news_img_sql2);
$submsg2 = 'The user details successfully deleted.';
header("location:".$page_name."?submsg=".$submsg2);
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

$pgsql = "select `id` from $admin_master where `user_type`='MANAGER'";
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
.branchCodeEachField{
	display:block;
	width:200px;
	word-wrap: break-word;
	font-size:11px;
	cursor:pointer;
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
                          <h2>Admin User Master (<?php echo $total_pgres;?>)&nbsp;&nbsp;<a href="<?php echo $add_page_name;?>" class="btn bg-red waves-effe">Add&nbsp;New&nbsp;User</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="text-align: left;font-size: 12px;width: 246px;display: inline-block;" id="success_msg" ><?php echo $submsg; ?></span>
                          
                          </h2>
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
                                            <th>Username</th>
                                            <th>Password</th>
                                            <th>Status</th>
                                            <th>Zone</th>
                                            <th>Selected&nbsp;Menus</th>
                                            <th style="width:200px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Username</th>
                                            <th>Password</th>
                                            <th>Status</th>
                                            <th>Zone</th>
                                            <th>Selected&nbsp;Menus</th>
                                            <th style="width:200px;">Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<?php
$sql1 = "select * from $admin_master where `user_type`='MANAGER' order by `user_name` asc limit $start_from,$limit";
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$the_admin_id = $row1["id"];
		$the_user_name = $row1["user_name"];
		$the_password = $row1["password"];
		$the_status = $row1["status"];
		$the_data_show_type = $row1["data_show_type"];
		$menuname_for_the_user = get_selected_user_menus_by_uid($conn,$the_admin_id);	
?>
<tr >
<td><?php echo $the_user_name;?></td>
<td><?php echo $the_password;?></td>
<td><?php echo $the_status;?></td>
<td><?php echo $the_data_show_type;?></td>
<td><?php echo $menuname_for_the_user;?></td>
<td style="width:200px;">
<a href="<?php echo $add_page_name;?>?edt_admin_id=<?php echo $the_admin_id;?>" class="btn bg-red waves-effe">Edit</a>
<a href="javascript:void(0);" class="btn bg-red waves-effe dlt_admin_cls" style="margin-left:8px;" dlt_admin_id="<?php echo $the_admin_id;?>">Delete</a><span style="height: 10px; padding: 20px; padding-bottom: 20px;"><a href="<?php echo $add_menu_access_page_name;?>?edt_admin_id=<?php echo $the_admin_id;?>" class="btn bg-red waves-effe">Access</a></span>
</td>
</tr>

<?php
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="5">No admin user found.</td>
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
    jQuery("#success_msg").html("");
},15000);

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

jQuery(document).on('click', '.dlt_admin_cls', function(event){
var dlt_admin_id = jQuery(this).attr("dlt_admin_id");
if(dlt_admin_id!=''){
var r = confirm("Do you want to delete the user details?");
if (r == true) {
window.location = '<?php echo $page_name;?>?dlt_admin_id='+dlt_admin_id+'&paged=<?php echo $page;?>';
} else {
return false;
}
}
}); 

});
</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>