<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & E_NOTICE & E_DEPRECATED);
if(isset($_SESSION["start_stellar_admin"])){
header("location:main.php");
}
include "star_connection.php";
$table_name = "admin_master";
$msg = "";
if(@$_POST["login"]=="Login"){
	$uname = $_POST["username"] ? addslashes(trim($_POST["username"])) : "";
	$password = $_POST["password"] ? addslashes(trim($_POST["password"])) : "";
	if($uname!="" && $password!=""){
		$sql1 = "select * from $table_name where `user_name`='".$uname."' and `password`='".$password."' and `status`='ACTIVE'";
		$res1 = mysqli_query($conn,$sql1);
		$totres1 = mysqli_num_rows($res1);
		if($totres1>0){
			$row1 = mysqli_fetch_assoc($res1);
			$admin_id = $row1["id"];
			$admin_name = $row1["user_name"];
			$user_type = $row1["user_type"];
			$data_show_type = $row1["data_show_type"];
			$_SESSION["start_stellar_admin"]=$admin_id;
			$_SESSION["start_stellar_admin_name"]=$admin_name;
			$_SESSION["start_stellar_user_type"]=$user_type;
			$_SESSION["start_stellar_data_show_type"]=$data_show_type;
			header("location:main.php");		
		}else{
			$msg = "Wrong credential.";
		}		
	}else{
		$msg = "Please enter username and password.";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Sign In | Star Stellar</title>
<!-- custom-theme -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="css/font-awesome.css" rel="stylesheet">
<link href="css/style3.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<style>
.condition_class{
padding-left: 92px;
}
@media only screen and (max-width: 600px) {
  .condition_class{
padding-left: 15px !important;
}
}
</style>
</head>
<body class="bg-agileinfo">
<div class="container">
<div class="row">
<div class="col-sm-6 col-md-6 col-lg-6"><img src="images/star.png" width="300" ></div>
<div class="col-sm-6 col-md-6 col-lg-6 condition_class"><img src="images/logo.png" width="300" ></div>
</div>
</div>
	
	
    <div class="container-w3l">
			<div class="image">
				<div class="agileits-img">
				</div>
			</div>
			<div class="main-head">
				<form action="" method="post" class="form-1">
					<h2>LOGIN</h2>
					<p class="field">
						<input type="text" name="username" placeholder="Username" required>
						<i class="icon-user icon-large"></i>
					</p>
						<p class="field">
							<input type="password" name="password" placeholder="Password" required>
							<i class="icon-lock icon-large"></i>
					   </p>
					<p class="submit">
						<button type="submit" name="login" value="Login"><i class="icon-arrow-right icon-large"></i></button>
					</p>
				</form>
			</div>	
			<div class="clearfix"></div>
	</div>	
	
    </body>
</html>