<?php
include '../classes/TextareaProcesser.class.php';
include '../classes/Fraction.class.php';
include '../classes/Simplex.class.php';
include '../classes/activity.class.php';
$ss=activity::isactivated2('../activity/active.xml')=='true' ? true : false;
//----------------------------------------------------------------------------
//$_POST['textarea']='2x1+5x2<=30						2x1+3x2<=26						0x1+3x2<=15';
//$_POST['targetfunction']='2x1+6x2';
//$_POST['funct']='true';
//$_POST['gomorryf']='false';
$tp=new TextareaProcesser($_POST['textarea'],$_POST['targetfunction'],$_POST['funct'],$_POST['gomorryf']);
//echo '<pre>';
//print_r($_POST);
//print_r($tp->getBoundaries());
//print_r($tp->getVariables());
//print_r($tp->getSigns());
//print_r($tp->getGomorry());
//print_r($tp->getMaxMin());
//echo '</pre>';
if($ss){
	$simplex=new Simplex();
	$simplex->Solve($tp->getVariables(),$tp->getBoundaries(),$tp->getSigns(),$tp->getTargetfunction(),$tp->getMaxMin(),$tp->getGomorry());
	echo '<div style="width:40%;float:left;">';
	$simplex->testprint();
	$simplex->printValuePair();
	$simplex->printResult();
	echo '</div><div style="width:60%;float:right">';
	$simplex->getjsonData($tp->getVariables(), $tp->getBoundaries(),$tp->getTargetfunction(),1);
	echo '</div><div style="width:1000px;clear:both;"></div>';
}else{
	echo '<script>';
	echo '$(document).ready(function(){';
	echo '$(\'#tabs\').remove();';
	echo '$(\'#header\').after(\''.activity::errormessage2('Strona została wyłączona przez administratora.<br/>Prosimy spróbować później.<br/>Powodzenia na egzaminie!').'\');';
	echo '});';
	echo '</script>';
	
}
?>

