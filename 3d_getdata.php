<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include 'classes/TextareaProcesser.class.php';
include 'classes/Fraction.class.php';
include 'classes/Simplex.class.php';
include 'classes/activity.class.php';
$ss = activity::isactivated2('activity/active.xml') == 'true' ? true : false;
//----------------------------------------------------------------------------
$_POST['textarea']='1x1+0x2+0x3<=1000
0x1+1x2+0x3<=500
0x1+0x2+1x3<=1500
3x+6x2+2x3<=6750';
$_POST['targetfunction']='4x1+12x2+3x3';
$_POST['funct']='true';
$_POST['gomorryf']='false';
$tp = new TextareaProcesser($_POST['textarea'], $_POST['targetfunction'], $_POST['funct'], $_POST['gomorryf']);
//echo '<pre>';
//print_r($_POST);
//print_r($tp->getBoundaries());
//print_r($tp->getVariables());
//print_r($tp->getSigns());
//print_r($tp->getGomorry());
//print_r($tp->getMaxMin());
//echo '</pre>';
if ($ss) {
    $simplex = new Simplex();
    $simplex->Solve($tp->getVariables(), $tp->getBoundaries(), $tp->getSigns(), $tp->getTargetfunction(), $tp->getMaxMin(), $tp->getGomorry());
    $simplex->getjsonData($tp->getVariables(), $tp->getBoundaries(), $tp->getTargetfunction(), 1, $tp->getSigns());
}
?>
