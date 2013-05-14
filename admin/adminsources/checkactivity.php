<?php

session_start();
include '../../classes/activity.class.php';
echo activity::isactivated2('../../activity/active.xml');
?>