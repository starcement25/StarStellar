<?php
	include "web_check.php";
	include "star_connection.php";

	include "web_header.php";
    session_start();

    $csrf_token = bin2hex(openssl_random_pseudo_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;

?>
<section class="content">
    <div class="container-fluid">
        <div class="block-header"></div>
        <!-- Basic Examples -->
        <div class="row clearfix">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="card">
                    <div class="header">
                        <h2>Bulk Upload/Update Gift</h2>
                    </div>
                    <div class="body" style="padding:20px;">
                        <form action="upload_gift_master.php" method="post" enctype="multipart/form-data" id="bulk_gift_master_add" class="send_notification_form">
                            <div class="form-group">
                                <label class="Bulk Gift Data">Csv File</label>
                                <div class="form-line">
                                    <input type="file" name="csv_file" id="csv_file" class="form-control" placeholder="" accept=".csv">
                                    <input type="text" name="csrf_token" hidden value="<?php echo $csrf_token; ?>">
                                </div>
                            </div>
                            <input type="submit" class="btn bg-red waves-effect snd_btn" value="Save">   
                            <a href="gift_master.php" class="btn bg-red waves-effe" style="margin-left:20px;">Back&nbsp;To&nbsp;Gift&nbsp;Master</a>
                            <a href="" class="btn bg-red waves-effe" style="margin-left:20px;">Cancel</a>
                            <a href="csv_formats/gift_uploading_format.csv" download="catalogue-format">Download Format</a>
                        </form>
                        <br>
                        <p style="color:blue;">Note: In CSV, enter only the image file name. Upload the matching image files in ZIP format.</p>
                        <form action="upload_image_master.php" method="post" enctype="multipart/form-data" id="bulk_gift_master_add" class="send_notification_form">
                            <div class="form-group">
                                <label class="Bulk Gift Data">ZIP File</label>
                                <div class="form-line">
                                    <input type="file" name="zip_file" id="zip_file" class="form-control" placeholder="" accept=".zip">
                                    <input type="text" name="csrf_token" hidden value="<?php echo $csrf_token; ?>">
                                </div>
                            </div>
                            <input type="submit" class="btn bg-red waves-effect snd_btn" value="Save">   
                            
                            <a href="" class="btn bg-red waves-effe" style="margin-left:20px;">Cancel</a>
                           
                        </form>
                        <?php 
                            if (isset($_SESSION['errors']) || isset($_SESSION['uploaded_response']))
                            {
                                if (isset($_SESSION['errors'])) {
                                    echo "<br>".$_SESSION['errors'];
                                    unset($_SESSION['errors']);
                                }

                                if (isset($_SESSION['uploaded_response'])) {
                                    echo "<br>".$_SESSION['uploaded_response'];
                                    unset($_SESSION['uploaded_response']);
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
</section>
<?php
	include "web_footer.php";
?>




<script>

</script>
