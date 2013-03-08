<?php
session_start();
include '../../classes/activity.class.php';
$json=Array();
if($_SESSION['admin']=='true'){
	activity::toggleactivity('../../activity/active.xml');
}else{
	header('Location:../index.php?error=1');
}
?>