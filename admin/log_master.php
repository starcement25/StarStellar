<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "web_check.php";
include "star_connection.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

/*---------PAGINATION RELATED CODE START----------*/
include "web_header.php";

// Initialize variables to avoid undefined errors
$srch_order_dtls = isset($_GET['srch_order_dtls']) ? $_GET['srch_order_dtls'] : '';
$sl_day_wise = isset($_GET['sl_day_wise']) ? $_GET['sl_day_wise'] : '';
$from_dt = isset($_GET['from_dt']) ? $_GET['from_dt'] : '';
$to_dt = isset($_GET['to_dt']) ? $_GET['to_dt'] : '';
$page_name = basename($_SERVER['PHP_SELF']);
$export_filtered_str = '';
$the_sl_no = 1;

// Initialize pagination variables (you'll need to set these properly)
$adjacents = isset($adjacents) ? $adjacents : 3;
$targetpage = isset($targetpage) ? $targetpage : '';
$limit = isset($limit) ? $limit : 25;
$page = isset($page) ? $page : 1;
$prev = isset($prev) ? $prev : 1;
$next = isset($next) ? $next : 2;
$lastpage = isset($lastpage) ? $lastpage : 1;
$lpm1 = isset($lpm1) ? $lpm1 : 0;
$new_qry_string_filtered = isset($new_qry_string_filtered) ? $new_qry_string_filtered : '';
$total_pgres = 0;

// Build your query based on search parameters
$query_conditions = array();

if (!empty($srch_order_dtls)) {
    // Add your search conditions here
}

if (!empty($sl_day_wise)) {
    if ($sl_day_wise == "Today") {
        $query_conditions[] = "DATE(action_at) = CURDATE()";
    } elseif ($sl_day_wise == "Yesterday") {
        $query_conditions[] = "DATE(action_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
    } elseif ($sl_day_wise == "Date_Range" && (!empty($from_dt) || !empty($to_dt))) {
        if (!empty($from_dt) && !empty($to_dt)) {
            $query_conditions[] = "DATE(action_at) BETWEEN '$from_dt' AND '$to_dt'";
        } elseif (!empty($from_dt)) {
            $query_conditions[] = "DATE(action_at) >= '$from_dt'";
        } elseif (!empty($to_dt)) {
            $query_conditions[] = "DATE(action_at) <= '$to_dt'";
        }
    }
}

$where_clause = '';
if (!empty($query_conditions)) {
    $where_clause = " WHERE " . implode(" AND ", $query_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM engineer_point_action_log" . $where_clause;
$count_result = mysqli_query($conn, $count_query);
if ($count_result) {
    $count_row = mysqli_fetch_assoc($count_result);
    $total_pgres = $count_row['total'];
} else {
    $total_pgres = 0;
}

// Add pagination to query
$offset = ($page - 1) * $limit;
$query = "SELECT * FROM engineer_point_action_log" . $where_clause . " ORDER BY action_at DESC LIMIT $offset, $limit";
$log = mysqli_query($conn, $query);

// Check for query error
if (!$log) {
    echo "Database error: " . mysqli_error($conn);
    $log = false;
}
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
.each_mk_cncl_span{
    display:block;
    width:150px;
    margin-bottom: 7px;
    margin-top: 7px;
}

.table-container {
    height: 400px; /* Set the height of the container to limit the height of the table */
    overflow-y: auto; /* Enable vertical scrolling */
}

table {
    border-collapse: collapse;
    width: 100%;
}

th {
    background-color: #ddd;
    position: relative; /* Changed from sticky for better compatibility */
}

th,
td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

/* CSS classes for action types */
.add {
    color: green;
    font-weight: bold;
}
.deduct {
    color: red;
    font-weight: bold;
}
.reset {
    color: blue;
    font-weight: bold;
}
</style>

<section class="content">
    <div class="container-fluid">
        <div class="block-header"></div>
        <!-- Basic Examples -->
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>Log Add/Deduct Points For Engineers(<?php echo $total_pgres;?>) &nbsp;&nbsp;&nbsp;<span class="rpt_loader"></span></h2>
                        <div class="row clearfix">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <input type="text" class="form-control" id="srch_order_dtls" value="<?php echo htmlspecialchars($srch_order_dtls);?>" placeholder="Search Order Details">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <select class="form-control" id="sl_day_wise">
                                    <option value="">Select Day-Wise</option>
                                    <option value="Today" <?php if($sl_day_wise=="Today") echo 'selected="selected"'; ?>>Today</option>
                                    <option value="Yesterday" <?php if($sl_day_wise=="Yesterday") echo 'selected="selected"'; ?>>Yesterday</option>
                                    <option value="Date_Range" <?php if($sl_day_wise=="Date_Range") echo 'selected="selected"'; ?>>Date Range</option>
                                </select>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <input type="text" class="datepicker form-control" id="from_dt" <?php if($sl_day_wise!="Date_Range") echo 'style="display:none;"'; ?> value="<?php echo htmlspecialchars($from_dt);?>" placeholder="Choose from date">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <input type="text" class="datepicker form-control" id="to_dt" <?php if($sl_day_wise!="Date_Range") echo 'style="display:none;"'; ?> value="<?php echo htmlspecialchars($to_dt);?>" placeholder="Choose to date">
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <button type="button" class="btn bg-red waves-effect srch_btn">Search</button>
                            </div>
                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <button type="button" class="btn bg-red waves-effect srch_reset_btn">Reset</button>
                            </div>
                        </div>
                        <span style="clear:both;display:block;"></span>
                    </div>
                    <div class="body">
                        <?php 
                        if (function_exists('olcPaging')) {
                            echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
                        }
                        ?>
                        <span style="display:block; clear:both;"></span>
                        
                        <div class="table-wrap">
                            <div class="table-responsive table-container">  
                                <table class="table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Engineer</th>
                                            <th>Action</th>
                                            <!-- <th>Product</th> -->
                                            <th>Old</th>
                                            <th>Change</th>
                                            <th>New</th>
                                            <th>Admin</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if ($log && mysqli_num_rows($log) > 0) {
                                            while($r = mysqli_fetch_assoc($log)) { 
                                                $action_class = strtolower($r['action_type']);
                                        ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($r['id']); ?></td>
                                            <td><?php echo htmlspecialchars($r['engineer_id']); ?></td>
                                            <td class="<?php echo htmlspecialchars($action_class); ?>">
                                                <?php echo htmlspecialchars($r['action_type']); ?>
                                            </td>
                                            <!-- <td><?php echo htmlspecialchars($r['product']); ?></td> -->
                                            <td><?php echo htmlspecialchars($r['old_point']); ?></td>
                                            <td><?php echo htmlspecialchars($r['change_point']); ?></td>
                                            <td><?php echo htmlspecialchars($r['new_point']); ?></td>
                                            <td><?php echo htmlspecialchars($r['action_by']); ?></td>
                                            <td><?php echo htmlspecialchars($r['action_at']); ?></td>
                                        </tr>
                                        <?php 
                                            }
                                        } else {
                                        ?>
                                        <tr>
                                            <td style="text-align:center" colspan="9">No data found.</td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <?php 
                        if (function_exists('olcPaging')) {
                            echo olcPaging($adjacents,$targetpage,$limit,$page,$prev,$next,$lastpage,$lpm1,"paged",$new_qry_string_filtered);
                        }
                        ?>
                        <span style="display:block; clear:both;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
jQuery(function(){
    // Initialize variables
    var imgs = '<img src="images/ajax-loader.gif"/>';
    var done_img = '<img src="images/success_tick.png"/>';
    
    // Initialize date pickers if they exist
    if (jQuery('#from_dt').length && typeof jQuery.fn.bootstrapMaterialDatePicker !== 'undefined') {
        jQuery('#from_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
        jQuery('#to_dt').bootstrapMaterialDatePicker({ weekStart : 0, time: false });
    }
    
    // Handle day-wise selection change
    jQuery("#sl_day_wise").change(function(){
        var sl_day_wise = jQuery(this).val();
        if(sl_day_wise == "") {
            jQuery('#from_dt').hide();
            jQuery('#to_dt').hide();
            jQuery('#from_dt').val("");
            jQuery('#to_dt').val("");
        } else {
            if(sl_day_wise == "Date_Range") {
                jQuery('#from_dt').show();
                jQuery('#to_dt').show();
                jQuery('#from_dt').val("");
                jQuery('#to_dt').val("");
            } else {
                jQuery('#from_dt').hide();
                jQuery('#to_dt').hide();
                jQuery('#from_dt').val("");
                jQuery('#to_dt').val("");
            }
        }
    });
    
    // Handle search button click
    jQuery(".srch_btn").click(function(){
        var srch_order_dtls = jQuery("#srch_order_dtls").val();
        var sl_day_wise = jQuery("#sl_day_wise").val();
        var from_dt = jQuery("#from_dt").val();
        var to_dt = jQuery("#to_dt").val();
        var qstring = "";
        
        // Build query string
        var params = [];
        
        if(srch_order_dtls != "") {
            params.push("srch_order_dtls=" + encodeURIComponent(srch_order_dtls));
        }
        
        if(sl_day_wise != "") {
            params.push("sl_day_wise=" + encodeURIComponent(sl_day_wise));
            
            if(sl_day_wise == "Date_Range") {
                if(from_dt != "") {
                    params.push("from_dt=" + encodeURIComponent(from_dt));
                }
                if(to_dt != "") {
                    params.push("to_dt=" + encodeURIComponent(to_dt));
                }
            }
        }
        
        if(params.length > 0) {
            qstring = "?" + params.join("&");
        }
        
        window.location = "<?php echo $page_name; ?>" + qstring;
    });
    
    // Handle reset button click
    jQuery(".srch_reset_btn").click(function(){
        window.location = "<?php echo $page_name; ?>";
    });
    
    // Clear update messages after timeout
    setTimeout(function(){
        jQuery(".ord_upd_msg").html("");
    }, 8000);
});
</script>

<?php
include "web_footer.php";
// Use mysqli_close instead of mysql_close
if (isset($conn)) {
    mysqli_close($conn);
}
?>