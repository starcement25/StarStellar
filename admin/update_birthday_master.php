<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Asia/Kolkata');

include "web_check.php";
include "star_connection.php";

$message = "";
$status  = false;

// Fetch existing record
$sql    = "SELECT * FROM birthday_master LIMIT 1";
$result = mysqli_query($conn,$sql);
$data   = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $new_message = mysqli_real_escape_string($conn,$_POST['message']);
    $image_path  = $data['img']; // keep old image by default

    // Check if new image uploaded
    if (!empty($_FILES['img']['name'])) {

        $target_dir = "../img/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_ext = strtolower(pathinfo($_FILES["img"]["name"], PATHINFO_EXTENSION));
        $allowed  = array("jpg", "jpeg", "png", "gif");

        if (in_array($file_ext, $allowed)) {

            $new_file_name = "birthday_" . time() . "." . $file_ext;
            $target_file   = $target_dir . $new_file_name;

            if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {

                // Delete old image (if exists and inside img folder)
                if (!empty($data['img'])) {
                    $old_img = str_replace($server_url, "../", $data['img']);
                    if (file_exists($old_img)) {
                        unlink($old_img);
                    }
                }

                // Save full URL
                $image_path = $server_url . "img/" . $new_file_name;

            } else {
                $message = "Image Upload Failed!";
            }

        } else {
            $message = "Only JPG, JPEG, PNG, GIF allowed!";
        }
    }

    // Update query
    $update_sql = "UPDATE birthday_master 
                   SET message='$new_message', img='$image_path'";
    //echo"<pre>";print_r($update_sql);die;

    if (mysqli_query($conn,$update_sql)) {
        $status  = true;
        $message = "Updated Successfully!";
    } else {
        $message = "Update Failed! " . mysqli_error();
    }

    // Refresh updated data
    $result = mysqli_query($conn,"SELECT * FROM birthday_master LIMIT 1");
    $data   = mysqli_fetch_assoc($result);
}
?>

<?php include "web_header.php"; ?>

<section class="content">
<div class="container-fluid">
<div class="row clearfix">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<div class="card">

<div class="header">
    <h2>Update Birthday Wish (Admin Panel)</h2>
</div>

<div class="body">

<?php if($message != "") { ?>
    <div style="padding:10px; color:#fff; background-color:<?php echo $status ? 'green' : 'red'; ?>">
        <?php echo $message; ?>
    </div>
    <br>
<?php } ?>

<form method="POST" enctype="multipart/form-data">
 <div class="table-responsive">
    <div class="form-group">
        <label>Birthday Message:</label>
        <textarea class="form-control" style="border:1px solid #ccc;" name="message" rows="5" required>
<?php echo isset($data['message']) ? $data['message'] : ''; ?>
        </textarea>
    </div>

    <br>

    <div class="form-group">
        <label>Current Image:</label><br>
        <?php if(!empty($data['img'])) { ?>
            <img src="<?php echo $data['img']; ?>" width="200" style="border:1px solid #ccc; padding:5px;">
        <?php } else { ?>
            No Image Found
        <?php } ?>
    </div>

    <br>

    <div class="form-group">
        <label>Upload New Image:</label>
        <input type="file" name="img" accept="image/*" class="form-control">
    </div>

    <br>

    <button class="btn bg-red waves-effect" type="submit">
        Update
    </button>

</form>

</div>
</div>
</div>
</div>
</div>
</section>

<?php 
include "web_footer.php";
mysqli_close();
?>