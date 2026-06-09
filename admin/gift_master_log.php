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
$action_type = isset($_GET['action_type']) ? $_GET['action_type'] : '';
$sl_day_wise = isset($_GET['sl_day_wise']) ? $_GET['sl_day_wise'] : '';
$from_dt = isset($_GET['from_dt']) ? $_GET['from_dt'] : '';
$to_dt = isset($_GET['to_dt']) ? $_GET['to_dt'] : '';
$page_name = basename($_SERVER['PHP_SELF']);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20; // Items per page
$offset = ($page - 1) * $limit;

// Build query conditions
$query_conditions = array();

// Search by gift details
if (!empty($srch_gift_dtls)) {
    $query_conditions[] = "(log_data LIKE '%$srch_gift_dtls%' OR action_by LIKE '%$srch_gift_dtls%')";
}

// Filter by action type
if (!empty($action_type)) {
    $query_conditions[] = "action_type = '$action_type'";
}

// Date filters
if (!empty($sl_day_wise)) {
    if ($sl_day_wise == "Today") {
        $query_conditions[] = "DATE(action_at) = CURDATE()";
    } elseif ($sl_day_wise == "Yesterday") {
        $query_conditions[] = "DATE(action_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
    } elseif ($sl_day_wise == "Date_Range") {
        if (!empty($from_dt) && !empty($to_dt)) {
            $query_conditions[] = "DATE(action_at) BETWEEN '$from_dt' AND '$to_dt'";
        } elseif (!empty($from_dt)) {
            $query_conditions[] = "DATE(action_at) >= '$from_dt'";
        } elseif (!empty($to_dt)) {
            $query_conditions[] = "DATE(action_at) <= '$to_dt'";
        }
    }
}

// Build WHERE clause
$where_clause = '';
if (!empty($query_conditions)) {
    $where_clause = " WHERE " . implode(" AND ", $query_conditions);
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM gift_master_log" . $where_clause;
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

// Get log data
$query = "SELECT * FROM gift_master_log" . $where_clause . " ORDER BY action_at DESC LIMIT $offset, $limit";
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

.action-update {
    color: #28a745;
    font-weight: bold;
}

.action-delete {
    color: #dc3545;
    font-weight: bold;
}

.json-data {
    font-family: monospace;
    font-size: 12px;
    background-color: #f8f9fa;
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
    max-height: 150px;
    overflow-y: auto;
    white-space: pre-wrap;
    word-break: break-all;
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
</style>

<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <!-- <h2>Gift Master Log</h2> -->
        </div>
        
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>Gift Master Logs (<?php echo $total_records; ?> records)</h2>
                        <div class="row clearfix">
                            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <input type="text" class="form-control" id="srch_gift_dtls" 
                                       value="<?php echo htmlspecialchars($srch_gift_dtls); ?>" 
                                       placeholder="Search gift title, description or admin">
                            </div>
                            
                            <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12 add_top_bottom_padding">
                                <select class="form-control" id="action_type">
                                    <option value="">All Actions</option>
                                    <option value="UPDATE" <?php if($action_type=="UPDATE") echo 'selected="selected"'; ?>>UPDATE</option>
                                    <option value="DELETE" <?php if($action_type=="DELETE") echo 'selected="selected"'; ?>>DELETE</option>
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
                        
                        <div class="table-responsive table-container">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">S.No</th>
                                        <th width="100">Log ID</th>
                                        <th width="100">Gift ID</th>
                                        <th width="100">Action Type</th>
                                        <th>Gift Details</th>
                                        <th width="150">Action By</th>
                                        <th width="180">Action At</th>
                                        <!-- <th width="150">JSON Data</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($log_result && mysqli_num_rows($log_result) > 0) {
                                        while($row = mysqli_fetch_assoc($log_result)) { 
                                            $log_data = json_decode($row['log_data'], true);
                                            $action_class = strtolower($row['action_type']) == 'update' ? 'action-update' : 'action-delete';
                                    ?>
                                    <tr>
                                        <td><?php echo $the_sl_no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                                        <td><?php echo htmlspecialchars($row['gift_id']); ?></td>
                                        <td class="<?php echo $action_class; ?>">
                                            <?php echo htmlspecialchars($row['action_type']); ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($log_data)): ?>
                                                <strong>Title:</strong> <?php echo isset($log_data['gift_title']) ? htmlspecialchars(substr($log_data['gift_title'], 0, 100)) . (strlen($log_data['gift_title']) > 100 ? '...' : '') : 'N/A'; ?><br>
                                                <strong>Description:</strong> <?php echo isset($log_data['description']) ? htmlspecialchars(substr($log_data['description'], 0, 80)) . (strlen($log_data['description']) > 80 ? '...' : '') : 'N/A'; ?><br>
                                                <strong>Points:</strong> <?php echo isset($log_data['point_require']) ? htmlspecialchars($log_data['point_require']) : 'N/A'; ?><br>
                                                <strong>Type ID:</strong> <?php echo isset($log_data['gift_type_id']) ? htmlspecialchars($log_data['gift_type_id']) : 'N/A'; ?>
                                            <?php else: ?>
                                                No data available
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['action_by']); ?></td>
                                        <td><?php echo date('d-m-Y H:i:s', strtotime($row['action_at'])); ?></td>
                                        <!-- <td>
                                            <button type="button" class="btn btn-sm btn-info view-json-btn" 
                                                    data-json='<?php echo htmlspecialchars($row['log_data']); ?>'
                                                    data-toggle="modal" data-target="#jsonModal">
                                                View JSON
                                            </button>
                                        </td> -->
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="8" style="text-align:center; padding: 20px;">
                                            <h4>No log data found</h4>
                                            <p>No gift log entries match your search criteria.</p>
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

<!-- JSON View Modal -->
<div class="modal fade" id="jsonModal" tabindex="-1" role="dialog" aria-labelledby="jsonModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="jsonModalLabel">JSON Data</h4>
            </div>
            <div class="modal-body">
                <pre id="jsonContent" class="json-data"></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="copyToClipboard()">Copy JSON</button>
            </div>
        </div>
    </div>
</div>

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
        var action_type = jQuery("#action_type").val();
        var sl_day_wise = jQuery("#sl_day_wise").val();
        var from_dt = jQuery("#from_dt").val();
        var to_dt = jQuery("#to_dt").val();
        
        // Build query string
        var params = [];
        
        if(srch_gift_dtls != "") {
            params.push("srch_gift_dtls=" + encodeURIComponent(srch_gift_dtls));
        }
        
        if(action_type != "") {
            params.push("action_type=" + encodeURIComponent(action_type));
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
    
    // Handle JSON view button click
    jQuery(document).on('click', '.view-json-btn', function(){
        var jsonData = jQuery(this).data('json');
        try {
            var formattedJson = JSON.stringify(JSON.parse(jsonData), null, 4);
            jQuery('#jsonContent').text(formattedJson);
        } catch(e) {
            jQuery('#jsonContent').text(jsonData);
        }
    });
});

function copyToClipboard() {
    var copyText = document.getElementById("jsonContent");
    var textArea = document.createElement("textarea");
    textArea.value = copyText.textContent;
    document.body.appendChild(textArea);
    textArea.select();
    document.execCommand("Copy");
    textArea.remove();
    
    // Show notification
    alert("JSON copied to clipboard!");
}
</script>

<?php
// Helper function to build query string for pagination
function buildQueryString($exclude_params = array()) {
    $params = array();
    $allowed_params = array('srch_gift_dtls', 'action_type', 'sl_day_wise', 'from_dt', 'to_dt');
    
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