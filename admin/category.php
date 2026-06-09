<?php
include "web_check.php";
include "star_connection.php";

set_time_limit(0);
ini_set('memory_limit', '-1');

$table_name = "categories";
$page_name  = "category.php";

$submsg = "";

/* ================= CSV IMPORT (OPTIMIZED) ================= */

if (@$_POST["bulk_upload_btn"] == "Import") {

    $fileName = $_FILES["upload_csv_file"]["name"];
    $fileTmp  = $_FILES["upload_csv_file"]["tmp_name"];

    if ($fileName == '') {
        $submsg = 'Please select a csv file.';
    } else {

        $ext = pathinfo($fileName, PATHINFO_EXTENSION);

        if (strtolower($ext) != 'csv') {
            $submsg = "Please upload only CSV file.";
        } else {

            if (($handle = fopen($fileTmp, "r")) !== FALSE) {

                mysqli_begin_transaction($conn);

                try {

                    fgetcsv($handle); // skip header

                    $insertCount = 0;
                    $updateCount = 0;

                    $batchSize = 500;
                    $insertData = [];

                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                        if (count($data) < 3) continue;

                        $id     = trim($data[0]);
                        $name   = trim($data[1]);
                        $status = trim($data[2]) !== '' ? (int)$data[2] : 1;

                        if (!$name) continue;

                        if ($id) {
                            // UPDATE (rename supported)
                            $stmt = $conn->prepare("UPDATE categories SET name=?, status=? WHERE id=?");
                            $stmt->bind_param("sii", $name, $status, $id);
                            $stmt->execute();
                            $updateCount++;
                        } else {
                            // BATCH INSERT
                            $nameEsc = mysqli_real_escape_string($conn, $name);
                            $insertData[] = "('$nameEsc', $status)";
                        }

                        // execute batch
                        if (count($insertData) == $batchSize) {
                            $sql = "INSERT INTO categories (name, status) VALUES " . implode(",", $insertData);
                            mysqli_query($conn, $sql);
                            $insertCount += count($insertData);
                            $insertData = [];
                        }
                    }

                    // remaining insert
                    if (!empty($insertData)) {
                        $sql = "INSERT INTO categories (name, status) VALUES " . implode(",", $insertData);
                        mysqli_query($conn, $sql);
                        $insertCount += count($insertData);
                    }

                    fclose($handle);
                    mysqli_commit($conn);

                    $submsg = "Inserted: $insertCount , Updated: $updateCount";

                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    $submsg = "Error: " . $e->getMessage();
                }
            }
        }
    }
}

/* ================= PAGINATION ================= */

$limit = 100;
$page  = isset($_GET['paged']) ? $_GET['paged'] : 1;

$pgres = mysqli_query($conn, "SELECT id FROM $table_name");
$total_pgres = mysqli_num_rows($pgres);

$start_from = ($page - 1) * $limit;

$prev = $page - 1;
$next = $page + 1;
$lastpage = ceil($total_pgres / $limit);
$lpm1 = $lastpage - 1;

include "web_header.php";
?>

<style>
.wrapper_scrl{
    overflow-x: auto;
    overflow-y: hidden;
    height: 15px;
}
.wrapper_scrl_div{
    height: 15px;
}
.table-responsive{
    margin-top:15px;
}
.table th{
    background:#f5f5f5;
    font-weight:600;
}
#success_msg{
    display:block;
    margin-top:10px;
    color:green;
}
</style>

<section class="content">
<div class="container-fluid">
<div class="block-header"></div>

<div class="card">

<div class="header">
    <h2>Categories Master (<?php echo $total_pgres;?>)</h2>

    <div class="row" style="margin-top:15px;">

        <div class="col-lg-8">
            <form method="POST" enctype="multipart/form-data" id="the_upload_frm"
                  style="display:flex; gap:10px; align-items:center;">
                
                <input type="file" name="upload_csv_file" id="upload_csv_file"
                       style="border:1px solid #ccc; padding:5px; border-radius:4px;">

                <input type="submit" name="bulk_upload_btn" value="Import"
                       class="btn bg-red waves-effect">
            </form>
        </div>

        <div class="col-lg-4" style="text-align:right;">
            <a href="export_categories.php" class="btn bg-red waves-effect">Export</a>
        </div>

    </div>

    <span id="success_msg"><?php echo $submsg; ?></span>
</div>

<div class="body">

<?php
echo olcPaging(4,$page_name,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged","");
?>

<div class="wrapper_scrl"><div class="wrapper_scrl_div"></div></div>

<div class="table-responsive tr_for_scroll">
<table class="table table-bordered table-striped table-hover table_for_scroll">

<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Status</th>
</tr>
</thead>

<tfoot>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Status</th>
</tr>
</tfoot>

<tbody>

<?php
$sql = "SELECT * FROM $table_name ORDER BY id DESC LIMIT $start_from,$limit";
$res = mysqli_query($conn,$sql);

if(mysqli_num_rows($res) > 0){
    while($row = mysqli_fetch_assoc($res)){
?>

<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['name']; ?></td>
    <td>
        <span class="label <?php echo $row['status'] ? 'bg-green' : 'bg-red'; ?>">
            <?php echo $row['status'] ? 'Active' : 'Inactive'; ?>
        </span>
    </td>
</tr>

<?php
    }
}else{
?>
<tr>
<td colspan="3" style="text-align:center;">No data found.</td>
</tr>
<?php } ?>

</tbody>
</table>
</div>

<?php
echo olcPaging(4,$page_name,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged","");
?>

</div>
</div>
</div>
</div>
</section>

<script>
jQuery(function(){

    jQuery("#the_upload_frm").submit(function(){
        if(!jQuery("#upload_csv_file").val()){
            alert("Please choose a CSV file.");
            return false;
        }
    });

    setTimeout(function(){
        jQuery("#success_msg").html("");
    },10000);

    var trWidth = jQuery(".tr_for_scroll").width();
    var tableWidth = jQuery(".table_for_scroll").width();

    jQuery(".wrapper_scrl").width(trWidth);
    jQuery(".wrapper_scrl_div").width(tableWidth);

    jQuery(".wrapper_scrl").scroll(function(){
        jQuery(".tr_for_scroll").scrollLeft(jQuery(this).scrollLeft());
    });

    jQuery(".tr_for_scroll").scroll(function(){
        jQuery(".wrapper_scrl").scrollLeft(jQuery(this).scrollLeft());
    });

});
</script>

<?php
include "web_footer.php";
mysqli_close($conn);
?>