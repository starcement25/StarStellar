<?php
session_start();
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
if(!isset($_SESSION["start_stellar_admin"])){
	header("location:index.php");
}
?>