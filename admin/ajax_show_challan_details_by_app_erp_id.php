<?php
set_time_limit(0);
include "star_connection.php";
$res_msg= array();
$t_apperpdo = "T_APPERPDO";
$t_dochallan = "T_DOCHALLAN";
$apporder_no = $_REQUEST["apporder_no"] ? trim($_REQUEST["apporder_no"]) : "";
$erporder_no = $_REQUEST["erporder_no"] ? trim($_REQUEST["erporder_no"]) : "";
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Challan Details</title>
<link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.js"></script>
</head>

<body>
<div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>App&nbsp;Order&nbsp;No</th>
                                            <th>ERP&nbsp;No</th>
                                            <th>ERP&nbsp;Date</th>
                                            <th>Challan&nbsp;No</th>
                                            <th>Challan&nbsp;Date</th>
                                            <th>Product&nbsp;Name</th>
                                            <th>qty&nbsp;(MT)</th>
                                            <th>Challan&nbsp;qty&nbsp;(MT)</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                      <tr>
                                            <th>App&nbsp;Order&nbsp;No</th>
                                            <th>ERP&nbsp;No</th>
                                            <th>ERP&nbsp;Date</th>
                                            <th>Challan&nbsp;No</th>
                                            <th>Challan&nbsp;Date</th>
                                            <th>Product&nbsp;Name</th>
                                            <th>qty&nbsp;(MT)</th>
                                            <th>Challan&nbsp;qty&nbsp;(MT)</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>

<?php
if($apporder_no!="" || $erporder_no!=""){
	if($apporder_no!="" && $erporder_no!=""){
		$sql1 = "select * from $t_dochallan where `APPORDERNO`='".addslashes($apporder_no)."' and `ERPORDERNO`='".addslashes($erporder_no)."'";
	}else if($apporder_no!="" && $erporder_no==""){
		$sql1 = "select * from $t_dochallan where `APPORDERNO`='".addslashes($apporder_no)."'";
	}else if($apporder_no=="" && $erporder_no!=""){
		$sql1 = "select * from $t_dochallan where `ERPORDERNO`='".addslashes($erporder_no)."'";
	}else{
	$sql1 = "";	
	}
if($sql1!=""){
$res1 = mysqli_query($conn,$sql1);
$totres1 = mysqli_num_rows($res1);
if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
		$apporder_no_chtl = $row1["APPORDERNO"];
		$erporder_no_chtl = $row1["ERPORDERNO"];
		$erporder_date_chtl = $row1["ERPORDERDT"];
		$challanno_chtl = $row1["CHALLANNO"];
		$challandt_chtl = $row1["CHALLANDT"];
		$prod_code_chtl= $row1["prod_code"];
		$dns_prod_code_chtl= $row1["dns_prod_code"];
		$prod_display_name_chtl= $row1["prod_display_name"];
		$prod_qty_chtl = $row1["QTY"];
		$challanqty_chtl = $row1["CHALLANQTY"];
		
?>
<tr>
<td><?php echo $apporder_no_chtl;?></td>
<td><?php echo $erporder_no_chtl;?></td>
<td><?php echo $erporder_date_chtl;?></td>
<td><?php echo $challanno_chtl;?></td>
<td><?php echo $challandt_chtl;?></td>
<td><?php echo $prod_display_name_chtl;?></td>
<td><?php echo $prod_qty_chtl;?></td>
<td><?php echo $challanqty_chtl;?></td>
</tr>

<?php
$the_sl_no++;
	}
}else{
?>
<tr>
<td style="text-align:center" colspan="8">No data found.</td>
</tr>
<?php
}
}else{
?>
<tr>
<td style="text-align:center" colspan="8">something went wrong.</td>
</tr>
<?php	
}
}else{
	?>
<tr>
<td style="text-align:center" colspan="8">something went wrong.</td>
</tr>
<?php
}
?>
</tbody>
</table>
</div>
</body>
</html>
<?php
mysqli_close($conn);
?>