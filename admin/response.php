<?php

include '../classes/login.class.php';
$login = login::validate($_POST['login'], $_POST['password']);
if ($login) {
	session_start();
	$_SESSION['admin'] = 'true';
	header('Location:admin.php');
} else {
	$_SESSION['admin'] = 'false';
	session_destroy();
	header('Location:index.php?error=1');
}
?>
