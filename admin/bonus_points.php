<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "web_check.php";
include "star_connection.php";
include "web_header.php";
$product_master = "product_master";


$sql_prod1 = "select * from $product_master order by `prod_id` asc";
$res_prod1 = mysqli_query($conn, $sql_prod1);
$totres_prod1 = mysqli_num_rows($res_prod1);

?>

<style>
    .span_clear{
	clear:both;
	display:block;
}
.more_than_bags_input,.bonus_points_input{
	text-align:center;
	width:100%;
	height:30px;
}
</style>

<table>
<tr>
    <td>
        Product Name<br/>
        <select id="sel_prod_id1" class="sel_prod_id1" autocomplete="off">
            <option value="">Select product to update bonus point</option>
            <?php
            if($totres_prod1 > 0){
                while($row_prod1 = mysqli_fetch_assoc($res_prod1)){
                    $the_prod_id = $row_prod1["prod_id"];
                    $the_prod_name = $row_prod1["prod_name"];
                    ?>
                    <option value="<?php echo $the_prod_id;?>"><?php echo $the_prod_name;?></option>
                    <?php
                }
            }
            ?>
        </select>
    </td>
</tr>

<tr>
    <th>More Than Bags</th>
    <th>Bonus Points</th>
</tr>
<tr>
    <td>
        <input type="text" class="more_than_bags_input" id="more_than_bags_input" autocomplete="off" />
    </td>
    <td>
        <input type="text" class="bonus_points_input" id="bonus_points_input" autocomplete="off" />
    </td>
    <td>
        <a href="javascript:void(0);" class="btn bg-red waves-effect upd_more_than_bags_btn1">Update</a>
        <span class="more_than_bags_msg_ldr"></span>
        <span class="span_clear"></span>
    </td>
</tr>
</table>

<script type="text/javascript">

jQuery(".upd_more_than_bags_btn1").click(function () {
    var sel_prod_id1 = jQuery("#sel_prod_id1").val();
    var more_than_bags = encodeURIComponent(jQuery.trim(jQuery("#the_prod_point1").val()));
    var bonus_points = encodeURIComponent(jQuery.trim(jQuery("#bonus_points_input").val()));
    var more_than_bags_loader_elmnt = jQuery(".sel_prod_point_msg_ldr1");

    if (sel_prod_id1 !== "") {
        more_than_bags_loader_elmnt.html(imgs);

        jQuery.ajax({
            url: "ajax_update_another_set.php", 
            type: 'post',
            dataType: "json",
            data: {
                sel_prod_id1: sel_prod_id1,
                more_than_bags: more_than_bags,
                bonus_points: bonus_points
            },
            success: function (response) {
                if (response.process_sts === "YES") {
                    more_than_bags_loader_elmnt.html(done_img);
                    setTimeout(function () {
                        more_than_bags_loader_elmnt.html("");
                    }, 5000);
                } else {
                    more_than_bags_loader_elmnt.html("");
                }
            }
        });
    } else {
        more_than_bags_loader_elmnt.html("Please select product.");
        setTimeout(function () {
            more_than_bags_loader_elmnt.html("");
        }, 5000);
    }
});

// for more than bags and bonus points

jQuery(".upd_more_than_bags_btn1").click(function () {
    var sel_prod_id1 = jQuery("#sel_prod_id1").val();
    var more_than_bags = encodeURIComponent(jQuery.trim(jQuery("#the_prod_point1").val()));
    var bonus_points = encodeURIComponent(jQuery.trim(jQuery("#bonus_points_input").val()));
    var more_than_bags_loader_elmnt = jQuery(".sel_prod_point_msg_ldr1");

    if (sel_prod_id1 !== "") {
        more_than_bags_loader_elmnt.html(imgs);

        jQuery.ajax({
            url: "ajax_update_another_set_bonus.php", // Replace with the correct API URL
            type: 'post',
            dataType: "json",
            data: {
                sel_prod_id1: sel_prod_id1,
                more_than_bags: more_than_bags,
                bonus_points: bonus_points
            },
            success: function (response) {
                if (response.process_sts === "YES") {
                    more_than_bags_loader_elmnt.html(done_img);
                    setTimeout(function () {
                        more_than_bags_loader_elmnt.html("");
                    }, 5000);
                } else {
                    more_than_bags_loader_elmnt.html("");
                }
            }
        });
    } else {
        more_than_bags_loader_elmnt.html("Please select product.");
        setTimeout(function () {
            more_than_bags_loader_elmnt.html("");
        }, 5000);
    }
});

</script>
<?php
include "web_footer.php";
mysqli_close($conn);
?>