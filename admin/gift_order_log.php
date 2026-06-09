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
$srch_order_dtls = isset($_GET['srch_order_dtls']) ? $_GET['srch_order_dtls'] : '';
$old_status = isset($_GET['old_status']) ? $_GET['old_status'] : '';
$new_status = isset($_GET['new_status']) ? $_GET['new_status'] : '';
$sl_day_wise = isset($_GET['sl_day_wise']) ? $_GET['sl_day_wise'] : '';
$from_dt = isset($_GET['from_dt']) ? $_GET['from_dt'] : '';
$to_dt = isset($_GET['to_dt']) ? $_GET['to_dt'] : '';
$page_name = basename($_SERVER['PHP_SELF']);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20; // Items per page
$offset = ($page - 1) * $limit;

// Define order status options
$order_statuses = array(
    "PENDING",
    "ORDER PLACED", 
    "DELIVERED",
    "ACKNOWLEDGEMENT OF DELIVERY",
    "COMPLAINT/FEEDBACK",
    "UNDELIVERED",
    "REJECT"
);

// Build query conditions
$query_conditions = array();

// Search by order details
if (!empty($srch_order_dtls)) {
    $query_conditions[] = "(gol.g_order_id LIKE '%$srch_order_dtls%' OR 
                            gol.admin_action_id LIKE '%$srch_order_dtls%' OR
                            gol.comments LIKE '%$srch_order_dtls%' OR
                            go.user_id LIKE '%$srch_order_dtls%' OR
                            e.e_name LIKE '%$srch_order_dtls%' OR
                            a.user_name LIKE '%$srch_order_dtls%')";
}

// Filter by old status
if (!empty($old_status)) {
    $query_conditions[] = "gol.old_status = '$old_status'";
}

// Filter by new status
if (!empty($new_status)) {
    $query_conditions[] = "gol.new_status = '$new_status'";
}

// Date filters
if (!empty($sl_day_wise)) {
    if ($sl_day_wise == "Today") {
        $query_conditions[] = "DATE(gol.log_datetime) = CURDATE()";
    } elseif ($sl_day_wise == "Yesterday") {
        $query_conditions[] = "DATE(gol.log_datetime) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
    } elseif ($sl_day_wise == "Date_Range") {
        if (!empty($from_dt) && !empty($to_dt)) {
            $query_conditions[] = "DATE(gol.log_datetime) BETWEEN '$from_dt' AND '$to_dt'";
        } elseif (!empty($from_dt)) {
            $query_conditions[] = "DATE(gol.log_datetime) >= '$from_dt'";
        } elseif (!empty($to_dt)) {
            $query_conditions[] = "DATE(gol.log_datetime) <= '$to_dt'";
        }
    }
}

// Build WHERE clause
$where_clause = '';
if (!empty($query_conditions)) {
    $where_clause = " WHERE " . implode(" AND ", $query_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total 
                FROM gift_order_master_log gol
                LEFT JOIN gift_order_master go ON gol.g_order_id = go.g_order_id
                LEFT JOIN gift_master gm ON go.gift_id = gm.id
                LEFT JOIN engineer_master e ON go.user_id = e.eid
                LEFT JOIN admin_master a ON gol.admin_action_id = a.id
                " . $where_clause;
                
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

// Get log data with your specific query
$query = "SELECT gol.*, 
                 go.user_id,
                 go.gift_id,
                 go.order_id,
                 go.point_taken,
                 go.datetime,
                 gm.gift_title,
                 gm.point_require,
                 e.e_name,
                 a.user_name
          FROM gift_order_master_log gol
          LEFT JOIN gift_order_master go ON gol.g_order_id = go.g_order_id
          LEFT JOIN gift_master gm ON go.gift_id = gm.id
          LEFT JOIN engineer_master e ON go.user_id = e.eid
          LEFT JOIN admin_master a ON gol.admin_action_id = a.id
          " . $where_clause . " 
          ORDER BY gol.log_datetime DESC 
          LIMIT $offset, $limit";
          
$log_result = mysqli_query($conn, $query);

// Debug: Uncomment to see the query
// echo "<pre>Query: " . htmlspecialchars($query) . "</pre>";

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

.status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    display: inline-block;
    min-width: 100px;
}

.status-pending {
    background-color: #ffc107;
    color: #212529;
}

.status-order-placed {
    background-color: #17a2b8;
    color: white;
}

.status-delivered {
    background-color: #28a745;
    color: white;
}

.status-acknowledgement {
    background-color: #6f42c1;
    color: white;
}

.status-complaint {
    background-color: #fd7e14;
    color: white;
}

.status-undelivered {
    background-color: #dc3545;
    color: white;
}

.status-reject {
    background-color: #343a40;
    color: white;
}

.value-change-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 300px;
}

.old-status-badge {
    background-color: #f8d7da;
    color: #721c24;
    padding: 6px 12px;
    border-radius: 4px;
    margin-right: 10px;
    min-width: 120px;
    text-align: center;
}

.new-status-badge {
    background-color: #d4edda;
    color: #155724;
    padding: 6px 12px;
    border-radius: 4px;
    margin-left: 10px;
    min-width: 120px;
    text-align: center;
}

.arrow-icon {
    font-size: 20px;
    color: #6c757d;
    margin: 0 5px;
}

.order-info {
    max-width: 250px;
}

.order-title {
    font-weight: bold;
    color: #007bff;
    margin-bottom: 5px;
    display: block;
}

.order-details {
    font-size: 12px;
    color: #6c757d;
    margin-top: 5px;
}

.order-details span {
    display: block;
    margin-bottom: 2px;
}

.comments-box {
    max-width: 200px;
    padding: 8px;
    background-color: #f8f9fa;
    border-radius: 4px;
    border-left: 3px solid #007bff;
    font-size: 12px;
    line-height: 1.4;
    max-height: 80px;
    overflow-y: auto;
    word-break: break-word;
}

.comments-box.empty {
    color: #6c757d;
    font-style: italic;
    border-left-color: #6c757d;
}

.admin-info {
    text-align: center;
}

.admin-name {
    font-weight: bold;
    color: #343a40;
    display: block;
}

.admin-id {
    font-size: 11px;
    color: #6c757d;
    display: block;
}

.datetime-cell {
    white-space: nowrap;
}

.date-part {
    display: block;
    font-weight: bold;
    color: #343a40;
}

.time-part {
    display: block;
    font-size: 11px;
    color: #6c757d;
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

.no-data {
    text-align: center;
    padding: 40px;
    color: #6c757d;
}

.no-data i {
    font-size: 48px;
    margin-bottom: 15px;
    color: #dee2e6;
}

.filter-row {
    background-color: #f8f9fa;
    border-radius: 5px;
    padding: 15px;
    margin-bottom: 20px;
}

.engineer-info {
    text-align: center;
}

.engineer-name {
    font-weight: bold;
    color: #343a40;
    display: block;
}

.engineer-id {
    font-size: 11px;
    color: #6c757d;
    display: block;
}

.points-info {
    text-align: center;
}

.points-taken {
    font-weight: bold;
    color: #dc3545;
    display: block;
}

.points-required {
    font-size: 11px;
    color: #28a745;
    display: block;
}
</style>

<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <!-- <h2>Gift Order Master Log</h2> -->
        </div>
        
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>Gift Order Status Logs (<?php echo $total_records; ?> records)</h2>
                        <div class="filter-row">
                            <div class="row clearfix">
                                <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
                                    <input type="text" class="form-control" id="srch_order_dtls" 
                                           value="<?php echo htmlspecialchars($srch_order_dtls); ?>" 
                                           placeholder="Search Order ID, Admin, Engineer...">
                                </div>
                                
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                    <select class="form-control" id="old_status">
                                        <option value="">Old Status (All)</option>
                                        <?php foreach ($order_statuses as $status): ?>
                                            <option value="<?php echo $status; ?>" 
                                                <?php if($old_status==$status) echo 'selected="selected"'; ?>>
                                                <?php echo $status; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                    <select class="form-control" id="new_status">
                                        <option value="">New Status (All)</option>
                                        <?php foreach ($order_statuses as $status): ?>
                                            <option value="<?php echo $status; ?>" 
                                                <?php if($new_status==$status) echo 'selected="selected"'; ?>>
                                                <?php echo $status; ?>
                                            </option>
                                        <?php endforeach; ?>
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
                                        <th width="100">Order ID</th>
                                        <th>Gift Details</th>
                                        <th>Engineer Info</th>
                                        <th>Points</th>
                                        <th>Status Change</th>
                                        <th width="150">Admin Action</th>
                                        <th>Comments</th>
                                        <th width="150">Log Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($log_result && mysqli_num_rows($log_result) > 0) {
                                        while($row = mysqli_fetch_assoc($log_result)) { 
                                            // Get CSS classes for status badges
                                            $old_status_class = getStatusClass($row['old_status']);
                                            $new_status_class = getStatusClass($row['new_status']);
                                    ?>
                                    <tr>
                                        <td><?php echo $the_sl_no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td>
                                            <strong>#<?php echo htmlspecialchars($row['order_id']); ?></strong>
                                        </td>
                                        <td class="order-info">
                                            <span class="order-title">
                                                <?php echo !empty($row['gift_title']) ? htmlspecialchars(substr($row['gift_title'], 0, 40)) . (strlen($row['gift_title']) > 40 ? '...' : '') : 'Gift ID: ' . $row['gift_id']; ?>
                                            </span>
                                            <div class="order-details">
                                                <?php if (!empty($row['gift_id'])): ?>
                                                    <span><strong>Gift ID:</strong> <?php echo htmlspecialchars($row['gift_id']); ?></span>
                                                <?php endif; ?>
                                                <?php if (!empty($row['datetime'])): ?>
                                                    <span><strong>Order Date:</strong> <?php echo date('d-m-Y', strtotime($row['datetime'])); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="engineer-info">
                                            <?php if (!empty($row['e_name'])): ?>
                                                <span class="engineer-name"><?php echo htmlspecialchars($row['e_name']); ?></span>
                                                <span class="engineer-id">ID: <?php echo htmlspecialchars($row['user_id']); ?></span>
                                            <?php else: ?>
                                                <span class="engineer-id">ID: <?php echo htmlspecialchars($row['user_id']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="points-info">
                                            <?php if (!empty($row['point_taken'])): ?>
                                                <span class="points-taken">Taken: <?php echo htmlspecialchars($row['point_taken']); ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($row['point_require'])): ?>
                                                <span class="points-required">Required: <?php echo htmlspecialchars($row['point_require']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="value-change-container">
                                                <div class="old-status-badge">
                                                    <small>From:</small><br>
                                                    <span class="status-badge <?php echo $old_status_class; ?>">
                                                        <?php echo htmlspecialchars($row['old_status']); ?>
                                                    </span>
                                                </div>
                                                <span class="arrow-icon">→</span>
                                                <div class="new-status-badge">
                                                    <small>To:</small><br>
                                                    <span class="status-badge <?php echo $new_status_class; ?>">
                                                        <?php echo htmlspecialchars($row['new_status']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="admin-info">
                                            <?php if (!empty($row['user_name'])): ?>
                                                <span class="admin-name"><?php echo htmlspecialchars($row['user_name']); ?></span>
                                                <span class="admin-id">ID: <?php echo htmlspecialchars($row['admin_action_id']); ?></span>
                                            <?php else: ?>
                                                <span class="admin-id">ID: <?php echo htmlspecialchars($row['admin_action_id']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="comments-box <?php echo empty($row['comments']) ? 'empty' : ''; ?>">
                                                <?php 
                                                if (!empty($row['comments'])) {
                                                    echo htmlspecialchars($row['comments']);
                                                } else {
                                                    echo 'No comments';
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td class="datetime-cell">
                                            <span class="date-part">
                                                <?php echo date('d-m-Y', strtotime($row['log_datetime'])); ?>
                                            </span>
                                            <span class="time-part">
                                                <?php echo date('h:i:s A', strtotime($row['log_datetime'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="10">
                                            <div class="no-data">
                                                <i class="material-icons">assignment</i>
                                                <h4>No order log data found</h4>
                                                <p>No gift order status log entries match your search criteria.</p>
                                            </div>
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
        var srch_order_dtls = jQuery("#srch_order_dtls").val();
        var old_status = jQuery("#old_status").val();
        var new_status = jQuery("#new_status").val();
        var sl_day_wise = jQuery("#sl_day_wise").val();
        var from_dt = jQuery("#from_dt").val();
        var to_dt = jQuery("#to_dt").val();
        
        // Build query string
        var params = [];
        
        if(srch_order_dtls != "") {
            params.push("srch_order_dtls=" + encodeURIComponent(srch_order_dtls));
        }
        
        if(old_status != "") {
            params.push("old_status=" + encodeURIComponent(old_status));
        }
        
        if(new_status != "") {
            params.push("new_status=" + encodeURIComponent(new_status));
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
    jQuery("#srch_order_dtls").keypress(function(e){
        if(e.which == 13) {
            jQuery(".srch_btn").click();
        }
    });
});
</script>

<?php
// Helper function to get CSS class for status badge
function getStatusClass($status) {
    $status = strtolower(str_replace(' ', '-', $status));
    
    switch($status) {
        case 'pending':
            return 'status-pending';
        case 'order-placed':
            return 'status-order-placed';
        case 'delivered':
            return 'status-delivered';
        case 'acknowledgement-of-delivery':
            return 'status-acknowledgement';
        case 'complaint/feedback':
            return 'status-complaint';
        case 'undelivered':
            return 'status-undelivered';
        case 'reject':
            return 'status-reject';
        default:
            return 'status-pending';
    }
}

// Helper function to build query string for pagination
function buildQueryString($exclude_params = array()) {
    $params = array();
    $allowed_params = array('srch_order_dtls', 'old_status', 'new_status', 'sl_day_wise', 'from_dt', 'to_dt');
    
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