<?php	
//-----------------------------Definition of setup attributes--------------------------//
define("SERVER","localhost");
define("USER","starsaat_dnsprod");
define("PASSWORD","dnsprod1234#");
$nick_name = "START";
$linksetup=mysqli_connect(SERVER,USER,PASSWORD) or die("Setup Database Connection Error.");
mysqli_select_db("starsaat_acednsproduct",$linksetup) or die("could not connect the setup database");
$sqlproductdetails="SELECT no_of_filter,col1,col2,col3,col4,branch_wise_product,branch_wise_mrp,uom_wise_mrp,branch_wise_cl_stk,
state_wise_mrp,focus_product FROM product_details WHERE nick_name='".$nick_name."'";
$rsproductdetails=mysqli_query($conn,$sqlproductdetails,$linksetup);
$rowproductdetails=mysqli_fetch_array($rsproductdetails);
$no_of_filter=$rowproductdetails['no_of_filter'];
$col1=$rowproductdetails['col1'];
$col2=$rowproductdetails['col2'];
$col3=$rowproductdetails['col3'];
$col4=$rowproductdetails['col4'];
$branch_wise_product=$rowproductdetails['branch_wise_product'];
$branch_wise_mrp=$rowproductdetails['branch_wise_mrp'];
$uom_wise_mrp=$rowproductdetails['uom_wise_mrp'];
$branch_wise_cl_stk=$rowproductdetails['branch_wise_cl_stk'];
$state_wise_mrp=$rowproductdetails['state_wise_mrp'];
$focus_product=$rowproductdetails['focus_product'];

$sqlorderformdetails="SELECT mrp,TD,sale_rate,TD_type,sale_rate_input_dropdown,credit_limit,cl_stk,sale,
VAT,VAT_details,amount,TD_calc,TD_trans_type,VAT_calc_on,instruction,premium,tagged_distributor_for_order  FROM order_form_details WHERE nick_name='".$nick_name."'";
$rsorderformdetails=mysqli_query($conn,$sqlorderformdetails,$linksetup);
$roworderformdetails=mysqli_fetch_array($rsorderformdetails);
$TD=$roworderformdetails['TD'];
$TD_type=$roworderformdetails['TD_type'];
$sale_rate=$roworderformdetails['sale_rate'];
$sale_rate_input_dropdown=$roworderformdetails['sale_rate_input_dropdown'];
$mrp=$roworderformdetails['mrp'];
$credit_limit=$roworderformdetails['credit_limit'];
$cl_stk=$roworderformdetails['cl_stk'];
$sale=$roworderformdetails['sale'];
$VAT=$roworderformdetails['VAT'];
$VAT_details=$roworderformdetails['VAT_details'];
$amount=$roworderformdetails['amount'];
$TD_calc=$roworderformdetails['TD_calc'];
$TD_trans_type=$roworderformdetails['TD_trans_type'];
$VAT_calc_on=$roworderformdetails['VAT_calc_on'];
$instruction=$roworderformdetails['instruction'];
$premium=$roworderformdetails['premium'];
$tagged_distributor_for_order = $roworderformdetails['tagged_distributor_for_order'];
$sqluserdetails="SELECT no_of_licensed_users,vertical_fields,employeewise_hierarchy,vertical_branch_relation,providing_code,
email_hierarchywise,need_DCR,branch_vertical_operation_wise_email,ace_integration,
customer_employee_mapping,modified_customer_emp_route,tour_plan_daywise FROM user_details WHERE nick_name='".$nick_name."'";
$rsuserdetails=mysqli_query($conn,$sqluserdetails,$linksetup);
$rowuserdetails=mysqli_fetch_array($rsuserdetails);
$no_of_licensed_users=$rowuserdetails['no_of_licensed_users'];
$vertical_fields=$rowuserdetails['vertical_fields'];
$vertical_branch_relation=$rowuserdetails['vertical_branch_relation'];
$employeewise_hierarchy=$rowuserdetails['employeewise_hierarchy'];
$providing_code=$rowuserdetails['providing_code'];
$email_hierarchywise=$rowuserdetails['email_hierarchywise'];
$need_DCR=$rowuserdetails['need_DCR'];
$branch_vertical_operation_wise_email=$rowuserdetails['branch_vertical_operation_wise_email'];
$ace_integration=$rowuserdetails['ace_integration'];
$customer_employee_mapping=$rowuserdetails['customer_employee_mapping'];
$modified_customer_emp_route=$rowuserdetails['modified_customer_emp_route'];
$tour_plan_daywise=$rowuserdetails['tour_plan_daywise'];

$sqlmenudetails="SELECT `order`,route_plan,tour_exp,loyalty,stk_audit,delete_transaction,sauda_allocation,business_prospect, survey, collection, 
sauda_outstanding,check_in_out,TD_allocation_app FROM menu_details WHERE nick_name='".$nick_name."'";
$rsmenudetails=mysqli_query($conn,$sqlmenudetails,$linksetup);
$rowmenudetails=mysqli_fetch_array($rsmenudetails);
$route_plan=$rowmenudetails['route_plan'];
$tour_exp=$rowmenudetails['tour_exp'];
$loyalty=$rowmenudetails['loyalty'];
$stk_audit=$rowmenudetails['stk_audit'];
$delete_transaction=$rowmenudetails['delete_transaction'];
$sauda_allocation=$rowmenudetails['sauda_allocation'];
$business_prospect = $rowmenudetails['business_prospect'];
$survey=$rowmenudetails['survey'];
$collection=$rowmenudetails['collection'];
$sauda_outstanding=$rowmenudetails['sauda_outstanding'];
$check_in_out=$rowmenudetails['check_in_out'];
$order=$rowmenudetails['order'];
$TD_allocation_app=$rowmenudetails['TD_allocation_app'];

$sqlrouteplandetails="SELECT route_plan_flow,route_plan_access_period,route_customer_planning,distributor_route_planning FROM route_plan_details WHERE nick_name='".$nick_name."'";
$rsrouteplandetails=mysqli_query($conn,$sqlrouteplandetails,$linksetup);
$rowrouteplandetails=mysqli_fetch_array($rsrouteplandetails);
$route_plan_flow=$rowrouteplandetails['route_plan_flow'];
$route_plan_access_period=$rowrouteplandetails['route_plan_access_period'];
$route_customer_planning=$rowrouteplandetails['route_customer_planning'];
$distributor_route_planning=$rowrouteplandetails['distributor_route_planning'];

$sqlsaudadetails="SELECT sauda_allocation_carry_forward,sauda_depot_wise,sauda_rate_variable,sauda_rate_variable_value,
sauda_booked_through,sauda_valid_from FROM sauda_form_details WHERE nick_name='".$nick_name."'";
$rssaudadetails=mysqli_query($conn,$sqlsaudadetails,$linksetup);
$rowsaudadetails=mysqli_fetch_array($rssaudadetails);
$sauda_allocation_carry_forward=$rowsaudadetails['sauda_allocation_carry_forward'];
$sauda_depot_wise=$rowsaudadetails['sauda_depot_wise'];
$sauda_rate_variable=$rowsaudadetails['sauda_rate_variable'];
$sauda_rate_variable_value=$rowsaudadetails['sauda_rate_variable_value'];
$sauda_booked_through=$rowsaudadetails['sauda_booked_through'];
$sauda_valid_from=$rowsaudadetails['sauda_valid_from'];

define("col1",$col1);
define("col2",$col2);
define("col3",$col3);
define("col4",$col4);
define("branch_wise_product",$branch_wise_product);
define("no_of_filter",$no_of_filter);
define("TD",$TD);
define("TD_type",$TD_type);
define("sale_rate",$sale_rate);
define("sale_rate_input_dropdown",$sale_rate_input_dropdown);
define("no_of_licensed_users",$no_of_licensed_users);
define("vertical_fields",$vertical_fields);
define("vertical_branch_relation",$vertical_branch_relation);
define("branch_vertical_operation_wise_email",$branch_vertical_operation_wise_email);
define("employeewise_hierarchy",$employeewise_hierarchy);
define("email_hierarchywise",$email_hierarchywise);
define("need_DCR",$need_DCR);
define("ace_integration",$ace_integration);
define("credit_limit",$credit_limit);
define("cl_stk",$cl_stk);
define("sale",$sale);
define("route_plan",$route_plan);
define("tour_exp",$tour_exp);
define("loyalty",$loyalty);
define("stk_audit",$stk_audit);
define("delete_transaction",$delete_transaction);
define("sauda_allocation",$sauda_allocation);
define("business_prospect",$business_prospect);
define("mrp",$mrp);
define("providing_code",$providing_code);
define("VAT",$VAT);
define("VAT_details",$VAT_details);
define("amount",$amount);
define("TD_calc",$TD_calc);
define("TD_trans_type",$TD_trans_type);
define("VAT_calc_on",$VAT_calc_on);
define("instruction",$instruction);
define("premium",$premium);
define("sauda_depot_wise",$sauda_depot_wise);
define("sauda_rate_variable",$sauda_rate_variable);
define("route_plan_flow",$route_plan_flow);
define("route_plan_access_period",$route_plan_access_period);	
define("branch_wise_mrp",$branch_wise_mrp);
define("uom_wise_mrp",$uom_wise_mrp);
define("branch_wise_cl_stk",$branch_wise_cl_stk);
define("survey",$survey);
define("customer_employee_mapping",$customer_employee_mapping);
define("collection",$collection);
define("sauda_outstanding",$sauda_outstanding);
define("modified_customer_emp_route",$modified_customer_emp_route);
define("check_in_out",$check_in_out);
define("order",$order);
define("distributor_route_planning",$distributor_route_planning);
define("tagged_distributor_for_order",$tagged_distributor_for_order);
define("modified_customer_emp_route",$modified_customer_emp_route);
define("state_wise_mrp",$state_wise_mrp);
define("focus_product",$focus_product);
define("TD_allocation_app",$TD_allocation_app);
define("tour_plan_daywise",$tour_plan_daywise);
//------------------------------------------------Define setup------------------------------------------------

$incremental_download=$_REQUEST['incremental_download'];
//$last_update_time='2014-06-06 13:40:25';
$last_update_time=$_REQUEST['last_update_time'];
$last_update_time=str_replace('€',' ',$last_update_time);

if($incremental_download=='yes')
{
$sqluserdetails="SELECT COUNT(user_id) AS total_users FROM user_details 
WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$rsuserdetails=mysqli_query($conn,$sqluserdetails);
$rowuserdetails=mysqli_fetch_array($rsuserdetails);
$usersetupcnt=$rowuserdetails['total_users'];

if($usersetupcnt >0)
{
define("user_details_download","yes");
}
else
{
define("user_details_download","no");
}

$sqlmenudetails="SELECT COUNT(menu_id) AS total_menus FROM menu_details 
WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$rsmenudetails=mysqli_query($conn,$sqlmenudetails);
$rowmenudetails=mysqli_fetch_array($rsmenudetails);
$menusetupcnt=$rowmenudetails['total_menus'];

if($menusetupcnt >0)
{
define("menu_details_download","yes");
}
else
{
define("menu_details_download","no");
}

$sqlorderformdetails="SELECT COUNT(order_form_id) AS total_order_form_details FROM order_form_details 
WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$rsorderformdetails=mysqli_query($conn,$sqlorderformdetails);
$roworderformdetails=mysqli_fetch_array($rsorderformdetails);
$orderformsetupcnt=$roworderformdetails['total_order_form_details'];

if($orderformsetupcnt >0)
{
define("order_form_details_download","yes");
}
else
{
define("order_form_details_download","no");
}

$sqlproductdetails="SELECT COUNT(product_id) AS total_product_details FROM product_details 
WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$rsproductdetails=mysqli_query($conn,$sqlproductdetails);
$rowproductdetails=mysqli_fetch_array($rsproductdetails);
$productsetupcnt=$rowproductdetails['total_product_details'];

if($productsetupcnt >0)
{
define("product_details_download","yes");
}
else
{
define("product_details_download","no");
}

$sqlrouteplandetails="SELECT COUNT(route_plan_id) AS total_route_plan_details FROM route_plan_details 
WHERE nick_name='".$nick_name."' AND UNIX_TIMESTAMP(download_time) > UNIX_TIMESTAMP('".$last_update_time."')";
$rsrouteplandetails=mysqli_query($conn,$sqlrouteplandetails);
$rowrouteplandetails=mysqli_fetch_array($rsrouteplandetails);
$routeplansetupcnt=$rowrouteplandetails['total_route_plan_details'];
if($routeplansetupcnt >0)
{
define("route_plan_details_download","yes");
}
else
{
define("route_plan_details_download","no");
}				

}
//------------------------------------------------End of Define stup-------------------------------------------

mysqli_close($conn$linksetup);
?>