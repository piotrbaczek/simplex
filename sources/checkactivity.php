<?php
session_start();
include '../classes/activity.class.php';
$json=Array();
//if($_SESSION['admin']=='true'){
	echo activity::isactivated2('../activity/active.xml');
//}
?>