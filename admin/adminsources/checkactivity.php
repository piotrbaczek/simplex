<?php
session_start();
include '../../classes/activity.class.php';
$json=Array();
echo activity::isactivated2('../../activity/active.xml');
?>