<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "web_check.php";
include "star_connection.php";

$app_version = "app_version";
$server_url1 = "http://" . $_SERVER['SERVER_NAME'] . "/";
$app_setting_key_arr = array("app_down_time" => "ACTIVE", "app_downtime_message" => "");

$carry_forward_process_arr = array("ACTIVE", "INACTIVE");
$reg_btn_option_arr = array("YES", "NO");

$sql_check_app_downtime = "SELECT * FROM app_version WHERE `app_down_time`='ACTIVE'";
//echo $sql_check_app_downtime;
$res_check_app_downtime = mysqli_query($conn, $sql_check_app_downtime);
$totres_check_app_downtime = mysqli_num_rows($res_check_app_downtime);
//echo $totres_check_app_downtime;
if ($totres_check_app_downtime > 0) {
    // get value
    $row_app_down_time = mysqli_fetch_assoc($res_check_app_downtime);
    $app_setting_key_arr["app_down_time"] = $row_app_down_time["app_down_time"];
    $app_setting_key_arr["app_downtime_message"] = $row_app_down_time["message"];
} else {
    $app_setting_key_arr["app_down_time"] = "INACTIVE";
    $app_setting_key_arr["app_downtime_message"] = "";
}

$submsg = "";
$add_page_name = "starstellar_app_setting_V2.php";
$page_name = "starstellar_app_setting_V2.php";

include "web_header.php";
?>


<style>
    .estarix_cls {
        color: #F00;
        margin-left: 5px;
    }

    .span_clear {
        clear: both;
        display: block;
    }

    .att_radius {
        text-align: center;
        width: 100%;
        height: 30px;
    }

    .att_late_count_with_respect_to_one_absent {
        text-align: center;
        width: 100%;
        height: 30px;
    }

    .att_send_leave_request_to_this_mail {
        text-align: left;
        width: 100%;
        height: 30px;
        padding-left: 5px;
        padding-top: 5px;
    }

    .setting_table tbody tr th,
    .setting_table tbody tr td {
        width: 33%;
    }

    .setting_table tbody tr td span.small_msg {
        font-size: 11px;
        font-weight: bold;
    }

    .setting_table tbody tr td p {
        margin-bottom: 4px;
    }

    .the_float_btn {
        float: left;
    }

    .the_float_msg {
        float: left;
        margin-left: 10px;
        width: 30px;
        height: 30px;
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
                        <h2>App Setting</h2>
                    </div>
                    <div class="body" style="padding:20px;">

                        <table class="table table-bordered table-striped table-hover setting_table">
                            <thead>
                                <tr>
                                    <th>Settings&nbsp;Name</th>
                                    <th>Value</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <p>APP Downtime</p>
                                        <span class="small_msg"></span>
                                    </td>
                                    <td>
                                        <?php
                                        $fetched_app_downtime = $app_setting_key_arr["app_down_time"];
                                        ?>
                                        <select class="form-control str_carry_forward_process" id="str_carry_forward_process">
                                            <?php
                                            foreach ($carry_forward_process_arr as $carry_forward_process_arr_val) {
                                            ?>
                                                <option value="<?php echo $carry_forward_process_arr_val; ?>" <?php if ($carry_forward_process_arr_val == $fetched_app_downtime) { ?> selected="selected" <?php } ?>><?php echo $carry_forward_process_arr_val; ?></option>
                                            <?php } ?>
                                        </select>

                                    </td>
                                    <td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_str_carry_forward_btn the_float_btn">Update</a>
                                        <span class="str_carry_forward_msg_ldr the_float_msg"></span>
                                        <span class="span_clear"></span>

                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <p>APP Downtime Message</p>
                                        <span class="small_msg">End user will see this message if "APP Downtime" status is "ACTIVE"</span>
                                    </td>
                                    <td>
                                        <?php
                                        $fetched_carry_forward_process_message = $app_setting_key_arr["app_downtime_message"];
                                        ?>
                                        <textarea class="form-control str_carry_forward_process_message" id="str_carry_forward_process_message" rows="3"><?php echo $fetched_carry_forward_process_message; ?></textarea>
                                    </td>
                                    <td><a href="javascript:void(0);" class="btn bg-red waves-effect upd_str_carry_forward_message_btn the_float_btn">Update</a>
                                        <span class="str_carry_forward_message_msg_ldr the_float_msg"></span>
                                        <span class="span_clear"></span>

                                    </td>
                                </tr>

                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
        <!-- #END# Basic Examples -->
    </div>
</section>
<script type="text/javascript">
    jQuery(function() {
        var img = '<img style="min-width:16px;width:16px;height:16px;margin:0 auto;" src="images/ajax-loader.gif">';
        var success_tik = '<img style="min-width:16px;width:16px;height:16px;margin:0 auto;" src="images/success_tick.png">';

        jQuery(".upd_str_carry_forward_btn").click(function() {
            var str_carry_forward_process = jQuery.trim(jQuery("#str_carry_forward_process").val());
            jQuery(".str_carry_forward_msg_ldr").html(img);
            jQuery.ajax({
                url: 'https://starstellar.com/update_app_version.php?query=app_down_time&value=' + str_carry_forward_process,
                type: 'get',
                dataType: "json",
                success: function(response) {
                    if (response.process_sts == "YES") {
                        jQuery(".str_carry_forward_msg_ldr").html(success_tik);
                        setTimeout(function() {
                            jQuery(".str_carry_forward_msg_ldr").html("");
                        }, 5000);
                    } else {
                        jQuery(".str_carry_forward_msg_ldr").html("");
                    }
                }
            });
        });

        jQuery(".upd_str_carry_forward_message_btn").click(function() {
            var str_carry_forward_process_message = jQuery.trim(jQuery("#str_carry_forward_process_message").val());
            jQuery(".str_carry_forward_message_msg_ldr").html(img);
            jQuery.ajax({
                url: 'https://starstellar.com/update_app_version.php?query=message&value=' + str_carry_forward_process_message,
                type: 'get',
                dataType: "json",
                success: function(response) {
                    if (response.process_sts == "YES") {
                        jQuery(".str_carry_forward_message_msg_ldr").html(success_tik);
                        setTimeout(function() {
                            jQuery(".str_carry_forward_message_msg_ldr").html("");
                        }, 5000);
                    } else {
                        jQuery(".str_carry_forward_message_msg_ldr").html("");
                    }
                }
            });
        });


    });
</script>

<?php
include "web_footer.php";
mysqli_close($con);
?>