<?php

include '../classes/login.class.php';
if (new login($_POST['login'], $_POST['password'])) {
    session_start();
    $_SESSION['admin'] = 'true';
    header('Location:admin.php');
} else {
    header('Location:index.php?error=1');
}
?>
