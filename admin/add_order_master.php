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
                        <h2>Bulk Upload Order Status CSV</h2>
                    </div>
                    <div class="body" style="padding:20px;">
                        <form action="upload_order_master.php" method="post" enctype="multipart/form-data" id="bulk_gift_master_add" class="send_notification_form">
                            <div class="form-group">
                                <label class="Bulk Gift Data">Csv File</label>
                                <div class="form-line">
                                    <input type="file" name="csv_file" id="csv_file" class="form-control" placeholder="" accept=".csv">
                                    <input type="text" name="csrf_token" hidden value="<?php echo $csrf_token; ?>">
                                </div>
                            </div>
                            <input type="submit" class="btn bg-red waves-effect snd_btn" value="Save">   
                           
                            <a href="" class="btn bg-red waves-effe" style="margin-left:20px;">Cancel</a>
                            <a href="csv_formats/status_upload.csv" download="upload_status_upload">Download Format</a>
                        </form>
                      <?php if (isset($_SESSION['uploaded_response'])) {
                            $data = $_SESSION['uploaded_response'];
                             unset($_SESSION['uploaded_response']);
                        ?>

                        <div style="border:1px solid #ccc;padding:10px;margin:10px 0;">
                            <b>Total:</b> <?php echo $data['total']; ?><br>
                            <b style="color:green;">Success:</b> <?php echo $data['success']; ?><br>
                            <b style="color:red;">Failed:</b> <?php echo $data['failed']; ?><br><br>

                            <?php if (!empty($data['errors'])) { ?>
                                <b style="color:red;">Failed Rows:</b>
                                <ul style="color:red;margin-top:5px;">
                                    <?php foreach ($data['errors'] as $err) { ?>
                                        <li><?php echo htmlspecialchars($err); ?></li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        </div>

                        <?php } ?>
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