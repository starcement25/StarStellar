<?php
ini_set('memory_limit', '999M');
set_time_limit(0);
include "web_check.php";
include "star_connection.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Include header
include "web_header.php";

// Initialize variables
$srch_gift_dtls = isset($_GET['srch_gift_dtls']) ? $_GET['srch_gift_dtls'] : '';
$status_type = isset($_GET['status_type']) ? $_GET['status_type'] : '';
$sl_day_wise = isset($_GET['sl_day_wise']) ? $_GET['sl_day_wise'] : '';
$from_dt = isset($_GET['from_dt']) ? $_GET['from_dt'] : '';
$to_dt = isset($_GET['to_dt']) ? $_GET['to_dt'] : '';
$page_name = basename($_SERVER['PHP_SELF']);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20; // Items per page
$offset = ($page - 1) * $limit;

// Build query conditions
$query_conditions = array();

// Search by gift details or admin
if (!empty($srch_gift_dtls)) {
    $query_conditions[] = "(gift_id LIKE '%$srch_gift_dtls%' OR action_by LIKE '%$srch_gift_dtls%')";
}

// Filter by status type
if (!empty($status_type)) {
    $query_conditions[] = "status_type = '$status_type'";
}

// Date filters
if (!empty($sl_day_wise)) {
    if ($sl_day_wise == "Today") {
        $query_conditions[] = "DATE(action_time) = CURDATE()";
    } elseif ($sl_day_wise == "Yesterday") {
        $query_conditions[] = "DATE(action_time) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
    } elseif ($sl_day_wise == "Date_Range") {
        if (!empty($from_dt) && !empty($to_dt)) {
            $query_conditions[] = "DATE(action_time) BETWEEN '$from_dt' AND '$to_dt'";
        } elseif (!empty($from_dt)) {
            $query_conditions[] = "DATE(action_time) >= '$from_dt'";
        } elseif (!empty($to_dt)) {
            $query_conditions[] = "DATE(action_time) <= '$to_dt'";
        }
    }
}

// Build WHERE clause
$where_clause = '';
if (!empty($query_conditions)) {
    $where_clause = " WHERE " . implode(" AND ", $query_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM gift_master_status_log" . $where_clause;
$count_result = mysqli_query($conn, $count_query);
if ($count_result) {
    $count_row = mysqli_fetch_assoc($count_result);
    $total_records = $count_row['total'];
} else {
    $total_records = 0;
}

// Calculate pagination
$total_pages = ceil($total_records / $limit);
$prev_page = ($page > 1) ? $page - 1 : 1;
$next_page = ($page < $total_pages) ? $page + 1 : $total_pages;

// Get log data with gift details
$query = "SELECT gsl.*, 
                 gm.gift_title,
                 gm.gift_image,
                 gm.point_require
          FROM gift_master_status_log gsl
          LEFT JOIN gift_master gm ON gsl.gift_id = gm.id
          " . $where_clause . " 
          ORDER BY action_time DESC 
          LIMIT $offset, $limit";
          
$log_result = mysqli_query($conn, $query);

// Initialize serial number
$the_sl_no = ($page - 1) * $limit + 1;
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
    max-height: 500px;
    overflow-y: auto;
}

table {
    border-collapse: collapse;
    width: 100%;
}

th {
    background-color: #f2f2f2;
    position: relative;
    font-weight: bold;
}

th, td {
    padding: 10px;
    text-align: left;
    border: 1px solid #ddd;
    vertical-align: top;
}

tr:hover {
    background-color: #f5f5f5;
}

.status-type {
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: bold;
    text-align: center;
    display: inline-block;
    min-width: 80px;
}

.status-redeem {
    background-color: #28a745;
    color: white;
}

.status-featured {
    background-color: #ffc107;
    color: #212529;
}

.status-status {
    background-color: #17a2b8;
    color: white;
}

.value-change {
    padding: 6px 10px;
    border-radius: 6px;
    text-align: center;
    display: inline-block;
    min-width: 120px;
}

.old-value {
    background-color: #f8d7da;
    color: #721c24;
    text-decoration: line-through;
    margin-right: 5px;
}

.new-value {
    background-color: #d4edda;
    color: #155724;
    margin-left: 5px;
}

.arrow {
    color: #6c757d;
    font-weight: bold;
    margin: 0 5px;
}

.gift-info {
    max-width: 200px;
}

.gift-title {
    font-weight: bold;
    color: #007bff;
    margin-bottom: 5px;
    display: block;
}

.gift-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    margin-right: 10px;
    float: left;
}

.gift-details {
    overflow: hidden;
}

.gift-points {
    color: #28a745;
    font-weight: bold;
    margin-top: 5px;
}

.add_top_bottom_padding {
    padding-top: 10px;
    padding-bottom: 10px;
}

.pagination {
    margin: 20px 0;
    text-align: center;
}

.pagination a {
    display: inline-block;
    padding: 8px 16px;
    margin: 0 2px;
    text-decoration: none;
    background-color: #007bff;
    color: white;
    border-radius: 4px;
}

.pagination a:hover {
    background-color: #0056b3;
}

.pagination .current {
    background-color: #28a745;
    padding: 8px 16px;
    margin: 0 2px;
    color: white;
    border-radius: 4px;
}

.json-data {
    font-family: monospace;
    font-size: 12px;
    background-color: #f8f9fa;
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    max-height: 200px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-break: break-all;
}

.status-active {
    background-color: #28a745;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.status-inactive {
    background-color: #dc3545;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.featured-yes {
    background-color: #ffc107;
    color: #212529;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.featured-no {
    background-color: #6c757d;
    color: white;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 12px;
}
</style>

<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <!-- <h2>Gift Status Master Log</h2> -->
        </div>
        
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>Gift Status Logs (<?php echo $total_records; ?> records)</h2>
                        <div class="row clearfix">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <input type="text" class="form-control" id="srch_gift_dtls" 
                                       value="<?php echo htmlspecialchars($srch_gift_dtls); ?>" 
                                       placeholder="Search Gift ID or Admin">
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <select class="form-control" id="status_type">
                                    <option value="">All Status Types</option>
                                    <option value="REDEEM" <?php if($status_type=="REDEEM") echo 'selected="selected"'; ?>>REDEEM</option>
                                    <option value="FEATURED" <?php if($status_type=="FEATURED") echo 'selected="selected"'; ?>>FEATURED</option>
                                    <option value="STATUS" <?php if($status_type=="STATUS") echo 'selected="selected"'; ?>>STATUS</option>
                                </select>
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <select class="form-control" id="sl_day_wise">
                                    <option value="">All Time</option>
                                    <option value="Today" <?php if($sl_day_wise=="Today") echo 'selected="selected"'; ?>>Today</option>
                                    <option value="Yesterday" <?php if($sl_day_wise=="Yesterday") echo 'selected="selected"'; ?>>Yesterday</option>
                                    <option value="Date_Range" <?php if($sl_day_wise=="Date_Range") echo 'selected="selected"'; ?>>Date Range</option>
                                </select>
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <input type="text" class="datepicker form-control" id="from_dt" 
                                       <?php if($sl_day_wise!="Date_Range") echo 'style="display:none;"'; ?> 
                                       value="<?php echo htmlspecialchars($from_dt); ?>" 
                                       placeholder="From date">
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <input type="text" class="datepicker form-control" id="to_dt" 
                                       <?php if($sl_day_wise!="Date_Range") echo 'style="display:none;"'; ?> 
                                       value="<?php echo htmlspecialchars($to_dt); ?>" 
                                       placeholder="To date">
                            </div>
                        </div>
                        
                        <div class="row clearfix">
                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <button type="button" class="btn btn-primary waves-effect srch_btn">
                                    <i class="material-icons">search</i> Search
                                </button>
                            </div>
                            
                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <button type="button" class="btn btn-default waves-effect srch_reset_btn">
                                    <i class="material-icons">refresh</i> Reset
                                </button>
                            </div>
                        </div>
                        
                        <span style="clear:both;display:block;"></span>
                    </div>
                    
                    <div class="body">
                        <!-- Pagination Top -->
                        <?php if ($total_pages > 1): ?>
                        <!-- <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="<?php echo $page_name; ?>?page=1<?php echo buildQueryString(array('page')); ?>">First</a>
                                <a href="<?php echo $page_name; ?>?page=<?php echo $prev_page; ?><?php echo buildQueryString(array('page')); ?>">Previous</a>
                            <?php endif; ?>
                            
                            <span class="current">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="<?php echo $page_name; ?>?page=<?php echo $next_page; ?><?php echo buildQueryString(array('page')); ?>">Next</a>
                                <a href="<?php echo $page_name; ?>?page=<?php echo $total_pages; ?><?php echo buildQueryString(array('page')); ?>">Last</a>
                            <?php endif; ?>
                        </div> -->
                        <?php endif; ?>
                        
                        <div class="table-responsive table-container">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">S.No</th>
                                        <th width="100">Log ID</th>
                                        <th width="100">Gift ID</th>
                                        <th>Gift Details</th>
                                        <th width="120">Status Type</th>
                                        <th>Value Change</th>
                                        <th width="120">Action By</th>
                                        <th width="180">Action Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($log_result && mysqli_num_rows($log_result) > 0) {
                                        while($row = mysqli_fetch_assoc($log_result)) { 
                                            // Determine CSS class based on status type
                                            $status_class = '';
                                            switch($row['status_type']) {
                                                case 'REDEEM':
                                                    $status_class = 'status-redeem';
                                                    break;
                                                case 'FEATURED':
                                                    $status_class = 'status-featured';
                                                    break;
                                                case 'STATUS':
                                                    $status_class = 'status-status';
                                                    break;
                                            }
                                            
                                            // Format old and new values
                                            $old_value_display = formatValue($row['old_value'], $row['status_type']);
                                            $new_value_display = formatValue($row['new_value'], $row['status_type']);
                                    ?>
                                    <tr>
                                        <td><?php echo $the_sl_no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['gift_id']); ?></td>
                                        <td class="gift-info">
                                            <div class="gift-details">
                                                <?php if (!empty($row['gift_image'])): ?>
                                                    <img src="https://starstellar.com/gift_pic/<?php echo htmlspecialchars($row['gift_image']); ?>" 
                                                         alt="Gift Image" 
                                                         class="gift-image"
                                                         onerror="this.src='images/default-gift.jpg'">
                                                <?php endif; ?>
                                                <span class="gift-title">
                                                    <?php echo !empty($row['gift_title']) ? htmlspecialchars(substr($row['gift_title'], 0, 50)) . (strlen($row['gift_title']) > 50 ? '...' : '') : 'Gift ID: ' . $row['gift_id']; ?>
                                                </span>
                                                <?php if (!empty($row['point_require'])): ?>
                                                    <div class="gift-points">
                                                        Points: <?php echo htmlspecialchars($row['point_require']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div style="clear:both;"></div>
                                        </td>
                                        <td>
                                            <span class="status-type <?php echo $status_class; ?>">
                                                <?php echo htmlspecialchars($row['status_type']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="value-change">
                                                <span class="old-value"><?php echo $old_value_display; ?></span>
                                                <span class="arrow">→</span>
                                                <span class="new-value"><?php echo $new_value_display; ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['action_by']); ?></td>
                                        <td><?php echo date('d-m-Y H:i:s', strtotime($row['action_time'])); ?></td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center; padding: 20px;">
                                            <h4>No status log data found</h4>
                                            <p>No gift status log entries match your search criteria.</p>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination Bottom -->
                        <?php if ($total_pages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="<?php echo $page_name; ?>?page=1<?php echo buildQueryString(array('page')); ?>">First</a>
                                <a href="<?php echo $page_name; ?>?page=<?php echo $prev_page; ?><?php echo buildQueryString(array('page')); ?>">Previous</a>
                            <?php endif; ?>
                            
                            <span class="current">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="<?php echo $page_name; ?>?page=<?php echo $next_page; ?><?php echo buildQueryString(array('page')); ?>">Next</a>
                                <a href="<?php echo $page_name; ?>?page=<?php echo $total_pages; ?><?php echo buildQueryString(array('page')); ?>">Last</a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <span style="display:block; clear:both;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
jQuery(function(){
    // Initialize date pickers
    if (typeof jQuery.fn.bootstrapMaterialDatePicker !== 'undefined') {
        jQuery('#from_dt').bootstrapMaterialDatePicker({ 
            weekStart: 0, 
            time: false,
            format: 'YYYY-MM-DD'
        });
        jQuery('#to_dt').bootstrapMaterialDatePicker({ 
            weekStart: 0, 
            time: false,
            format: 'YYYY-MM-DD'
        });
    }
    
    // Handle day-wise selection change
    jQuery("#sl_day_wise").change(function(){
        var sl_day_wise = jQuery(this).val();
        if(sl_day_wise == "Date_Range") {
            jQuery('#from_dt').show();
            jQuery('#to_dt').show();
            jQuery('#from_dt').val('');
            jQuery('#to_dt').val('');
        } else {
            jQuery('#from_dt').hide();
            jQuery('#to_dt').hide();
            jQuery('#from_dt').val('');
            jQuery('#to_dt').val('');
        }
    });
    
    // Handle search button click
    jQuery(".srch_btn").click(function(){
        var srch_gift_dtls = jQuery("#srch_gift_dtls").val();
        var status_type = jQuery("#status_type").val();
        var sl_day_wise = jQuery("#sl_day_wise").val();
        var from_dt = jQuery("#from_dt").val();
        var to_dt = jQuery("#to_dt").val();
        
        // Build query string
        var params = [];
        
        if(srch_gift_dtls != "") {
            params.push("srch_gift_dtls=" + encodeURIComponent(srch_gift_dtls));
        }
        
        if(status_type != "") {
            params.push("status_type=" + encodeURIComponent(status_type));
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
        
        var qstring = params.length > 0 ? "?" + params.join("&") : "";
        window.location = "<?php echo $page_name; ?>" + qstring;
    });
    
    // Handle reset button click
    jQuery(".srch_reset_btn").click(function(){
        window.location = "<?php echo $page_name; ?>";
    });
    
    // Handle Enter key in search field
    jQuery("#srch_gift_dtls").keypress(function(e){
        if(e.which == 13) {
            jQuery(".srch_btn").click();
        }
    });
});
</script>

<?php
// Helper function to format values based on status type
function formatValue($value, $status_type) {
    if ($value === null || $value === '') {
        return 'N/A';
    }
    
    switch($status_type) {
        case 'REDEEM':
            return $value == 'YES' ? 'YES' : 'NO';
            
        case 'FEATURED':
            return $value == 'YES' ? 'YES' : 'NO';
            
        case 'STATUS':
            return $value == 'ACTIVE' ? 'ACTIVE' : 'INACTIVE';
            
        default:
            return htmlspecialchars($value);
    }
}

// Helper function to build query string for pagination
function buildQueryString($exclude_params = array()) {
    $params = array();
    $allowed_params = array('srch_gift_dtls', 'status_type', 'sl_day_wise', 'from_dt', 'to_dt');
    
    foreach ($allowed_params as $param) {
        if (isset($_GET[$param]) && !empty($_GET[$param]) && !in_array($param, $exclude_params)) {
            $params[] = $param . '=' . urlencode($_GET[$param]);
        }
    }
    
    return !empty($params) ? '&' . implode('&', $params) : '';
}

include "web_footer.php";
if (isset($conn)) {
    mysqli_close($conn);
}
?>