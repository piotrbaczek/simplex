<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include '../classes/dbconn.class.php';
$baza = dbconn::instance();
if (empty($_POST['range']) || empty($_POST['version']) || empty($_POST['opinion'])) {
    $json = array('result' => 0);
} else {
    $stmt = "INSERT INTO feedback (`ocena`, `version`, `opinion`, `date`, `ip`) VALUES ('" . $baza->escape($_POST['range']) . "','" . $baza->escape($_POST['version']) . "','" . $baza->escape($_POST['opinion']) . "',NOW(),'" . $_SERVER['REMOTE_ADDR'] . "')";
    $json = array('result' => $baza->insert($stmt));
}

echo json_encode($json);
?>
