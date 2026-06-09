<?php
$file_name = basename($_SERVER['PHP_SELF']);
if($file_name==''){
	$file_name = "index.php";
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Welcome To Star Stellar Dashboard</title>
    <!-- Favicon-->
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
   
    
    <!-- Bootstrap Material Datetime Picker Css -->
<link href="plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />

    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Morris Chart Css-->
    <link href="plugins/morrisjs/morris.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">

    <!-- AdminBSB Themes. You can choose a theme from css/themes instead of get all themes -->
    <link href="css/themes/all-themes.css" rel="stylesheet" />
<!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>
    
    <script src="js/jquery.colorbox.js"></script>
     <link href="css/colorbox.css" rel="stylesheet" />
     
     <link rel="stylesheet" type="text/css" href="plugins/chosen/chosen.css">
<script type="text/javascript" src="plugins/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="js/jquery.form.js"></script>
<script src="js/jquery-ui.js"></script>
<link href="css/jquery-ui.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery.form.js"></script>
</head>

<body class="theme-red">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <div class="overlay"></div>
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand" href="main.php">Star Stellar - Admin Panel</a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                
            </div>
        </div>
    </nav>
    <!-- #Top Bar -->
    <section>
        <!-- Left Sidebar -->
        <aside id="leftsidebar" class="sidebar">
            <!-- User Info -->
            <div class="user-info">
                <div class="image">
                    <img src="images/user.png" width="48" height="48" alt="User" />
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Welcome 
					<?php if(trim($_SESSION["start_stellar_admin_name"])!=""){
						$name_login = trim($_SESSION["start_stellar_admin_name"]);
						$data_show_type_login = trim($_SESSION["start_stellar_data_show_type"]);
						$name_login = ucwords(strtolower($name_login));
						echo $name_login." (".$data_show_type_login.")";
					}?>
                    </div>
                    <div class="email">Star Stellar</div>
                    <div class="btn-group user-helper-dropdown">
                        <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="web_logout.php"><i class="material-icons">input</i>Sign Out</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- #User Info -->
            <!-- Menu -->
            <div class="menu">
                <ul class="list">
                    <li class="header">MAIN NAVIGATION</li>
                    
                    <li <?php if($file_name=="main.php"){ echo 'class="active"';}?>>
                        <a href="main.php">
                            <i class="material-icons">home</i>
                            <span>Home</span>
                        </a>
                    </li>
                    <?php
                    if($_SESSION["start_stellar_user_type"]=="ADMIN"){ ?>
                    
                    <li <?php if($file_name=="dashboard_with_respect_to_te.php"){ echo 'class="active"';}?>>
                        <a href="dashboard_with_respect_to_te.php">
                            <i class="material-icons">business</i>
                            <span>Dashboard for TE</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="te_master.php"){ echo 'class="active"';}?>>
                        <a href="te_master.php">
                            <i class="material-icons">account_circle</i>
                            <span>TE Master</span>
                        </a>                        
                    </li>
                    <li <?php if($file_name=="add_edit_te.php"){ echo 'class="active"';}?> style="display:none;">
                        <a href="add_edit_te.php">
                            <i class="material-icons">view_list</i>
                            <span>Add/Edit TE</span>
                        </a>                        
                    </li>
                    <li <?php if($file_name=="engineer_master.php"){ echo 'class="active"';}?>>
                        <a href="engineer_master.php">
                            <i class="material-icons">account_circle</i>
                            <span>Engineer Master</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="edit_engineer_master.php"){ echo 'class="active"';}?> style="display:none;">
                        <a href="edit_engineer_master.php">
                            <i class="material-icons">view_list</i>
                            <span>Edit Engineer</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="asm_master.php"){ echo 'class="active"';}?>>
                        <a href="asm_master.php">
                            <i class="material-icons">view_list</i>
                            <span>ASM Master</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="edit_asm_master.php"){ echo 'class="active"';}?> style="display:none;">
                        <a href="edit_asm_master.php">
                            <i class="material-icons">view_list</i>
                            <span>Edit ASM</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="recomended_site_master.php"){ echo 'class="active"';}?>>
                        <a href="recomended_site_master.php">
                            <i class="material-icons">web</i>
                            <span>Recommended Site Master</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="recommended_site_asm_activity_log.php"){ echo 'class="active"';}?>>
                        <a href="recommended_site_asm_activity_log.php">
                            <i class="material-icons">web</i>
                            <span>ASM Activity Log</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="order_master.php"){ echo 'class="active"';}?>>
                        <a href="order_master.php">
                            <i class="material-icons">reorder</i>
                            <span>Order Master</span>
                        </a>                        
                    </li>
                    <li <?php if($file_name=="support_master.php"){ echo 'class="active"';}?>>
                        <a href="support_master.php">
                            <i class="material-icons">reorder</i>
                            <span>Support Master</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="transfer_engineer_from_te_to_te.php"){ echo 'class="active"';}?>>
                        <a href="transfer_engineer_from_te_to_te.php">
                            <i class="material-icons">account_circle</i>
                            <span>Transfer Engineer</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="ledger_master.php"){ echo 'class="active"';}?>>
                        <a href="ledger_master.php">
                            <i class="material-icons">reorder</i>
                            <span>Ledger Master</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="add_points_for_engineer.php"){ echo 'class="active"';}?>>
                        <a href="add_points_for_engineer.php">
                            <i class="material-icons">account_circle</i>
                            <span>Add/Deduct Points For Engineers</span>
                        </a>                        
                    </li>
                    
                    
                    <li <?php if($file_name=="gift_master.php"){ echo 'class="active"';}?>>
                        <a href="gift_master.php">
                            <i class="material-icons">card_giftcard</i>
                            <span>Gift Master</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="stellar_setting.php"){ echo 'class="active"';}?>>
                        <a href="stellar_setting.php">
                            <i class="material-icons">settings_applications</i>
                            <span>Setting</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="home_screen_slider.php"){ echo 'class="active"';}?>>
                        <a href="home_screen_slider.php">
                            <i class="material-icons">view_list</i>
                            <span>Home Screen Slider</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="send_notification.php"){ echo 'class="active"';}?>>
                        <a href="send_notification.php">
                            <i class="material-icons">notification_important</i>
                            <span>Notification</span>
                        </a>                        
                    </li>
                    
                    <li <?php if($file_name=="admin_user_master.php"){ echo 'class="active"';}?>>
                        <a href="admin_user_master.php">
                            <i class="material-icons">view_list</i>
                            <span>Admin User Master</span>
                        </a>                        
                    </li>
					<li <?php if($file_name=="admin_user_wise_menu_accessibility.php"){ echo 'class="active"';}?> style="display:none;">
                        <a href="admin_user_wise_menu_accessibility.php">
                            <i class="material-icons">view_list</i>
                            <span>Admin User Wise menu access</span>
                        </a>                        
                    </li>
                    
                   <li <?php if($file_name=="add_edit_admin_user_master.php"){ echo 'class="active"';}?> style="display:none;">
                        <a href="add_edit_admin_user_master.php">
                            <i class="material-icons">view_list</i>
                            <span>Edit Admin User Master</span>
                        </a>                        
                    </li>
                    
                    
                    <li <?php if($file_name=="branch_master.php"){ echo 'class="active"';}?>>
                        <a href="branch_master.php">
                            <i class="material-icons">business</i>
                            <span>Branch Master</span>
                        </a>                        
                    </li>
                    <li <?php if($file_name=="db_backup_details.php"){ echo 'class="active"';}?>>
                        <a href="db_backup_details.php">
                            <i class="material-icons">layers</i>
                            <span>DB Backup Details</span>
                        </a>
                    </li>
                    
                     
                    
                    
                    
                    
                    
                    
                    
                    
						 
					<?php }if($_SESSION["start_stellar_user_type"]=="MANAGER"){ 
					$the_admin_id = $_SESSION["start_stellar_admin"];
					$selected_menu_for_user = "selected_menu_for_user";
					$menu_master = "menu_master";
					$sql1 = "select * from $menu_master where `menu_id` in(select `menu_id` from $selected_menu_for_user where `user_id`='$the_admin_id') order by `menu_id` asc";
	$res1 = mysqli_query($conn,$sql1);
	$totres1 = mysqli_num_rows($res1);
	if($totres1>0){
	while($row1=mysqli_fetch_assoc($res1)){
	$the_menu_name_sho = trim($row1["menu_name"]);
	$the_material_icon_sho = trim($row1["material_icon"]);
	$the_menu_admin_page_sho = trim($row1["menu_admin_page"]);
	
	?>
        <li <?php if($file_name==$the_menu_admin_page_sho){ echo 'class="active"';}?>>
        <a href="<?php echo $the_menu_admin_page_sho;?>">
        <i class="material-icons"><?php echo $the_material_icon_sho;?></i>
        <span><?php echo $the_menu_name_sho;?></span>
        </a>                        
        </li>
	
	<?php
	if($the_menu_name_sho=="Engineer Master"){
		?>
            <li <?php if($file_name=="edit_engineer_master.php"){ echo 'class="active"';}?> style="display:none;">
            <a href="edit_engineer_master.php">
            <i class="material-icons">view_list</i>
            <span>Edit Engineer</span>
            </a>                        
            </li>
                    
	
	<?php
	}
	if($the_menu_name_sho=="TE Master"){
		?>
<li <?php if($file_name=="add_edit_te.php"){ echo 'class="active"';}?> style="display:none;">
<a href="add_edit_te.php">
<i class="material-icons">view_list</i>
<span>Add/Edit TE</span>
</a>                        
</li>
<?php
	}
	}
	}	
					
					?> 
                    
                    
                     <?php }else{ ?>
                    
                    
                  <?php
					}
					?>
                    
                </ul>
            </div>
            <!-- #Menu -->
            <!-- Footer -->
            <div class="legal">
                <div class="copyright">
                    &copy; 2017 - 2018 <a href="javascript:void(0);">Forcepower infotech Pvt Ltd</a>.
                </div>
                <div class="version">
                    <b>Version: </b> 1.0.0
                </div>
            </div>
            <!-- #Footer -->
        </aside>
        <!-- #END# Left Sidebar -->
      
    </section>
