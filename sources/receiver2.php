<?php

include '../classes/TextareaProcesser.class.php';
include '../classes/Fraction.class.php';
include '../classes/SimplexTableu.class.php';
include '../classes/Signs.class.php';
include '../classes/Simplex2.class.php';
include '../classes/activity.class.php';
$ss = activity::isactivated2('../activity/active.xml') == 'true' ? true : false;
if ($ss) {
	//----------------------------------------------------------------------------
	$_POST['textarea'] = '2x1+5x2<=30
2x1+3x2<=26
0x1+3x2<=15';
	$_POST['targetfunction'] = '2x1+6x2';
	$_POST['funct'] = 'true';
	$_POST['gomorryf'] = 'false';
	$tp = new TextareaProcesser(
			!isset($_POST['textarea']) ? NULL : $_POST['textarea'], !isset($_POST['targetfunction']) ? NULL : $_POST['targetfunction'], !isset($_POST['funct']) ? NULL : $_POST['funct'], !isset($_POST['gomorryf']) ? NULL : $_POST['gomorryf']
	);
//echo '<pre>';
//print_r($_POST);
//print_r($tp->getBoundaries());
//print_r($tp->getVariables());
//print_r($tp->getSigns());
//print_r($tp->getGomorry());
//print_r($tp->getMaxMin());
//echo '</pre>';
	if ($tp->isCorrect()) {
		$simplex2 = new Simplex2($tp->getVariables(), $tp->getBoundaries(), $tp->getSigns(), $tp->getTargetfunction(), $tp->getMaxMin(), $tp->getGomorry());
		$simplex2->printProblem();
		$simplex2->printSolution();
//		$simplex->Solve($tp->getVariables(), $tp->getBoundaries(), $tp->getSigns(), $tp->getTargetfunction(), $tp->getMaxMin(), $tp->getGomorry());
//		echo '<div style="width:60%;height:100%;float:left;">';
//		$simplex->printSolution();
//		$simplex->printValuePair();
//		$simplex->printResult();
//		echo '</div><div style="width:40%;float:left">';
//		$simplex->getjsonData($tp->getVariables(), $tp->getBoundaries(), $tp->getTargetfunction(), $tp->getSigns());
//		echo '</div><div style="width:1000px;clear:both;"></div>';
	} else {
		TextareaProcesser::errormessage('Puste dane lub złe dane. Proszę poprawić treść wpisanego zadania.');
	}
} else {
	echo '<script>';
	echo '$(document).ready(function(){';
	echo '$(\'#tabs\').remove();';
	echo '$(\'#header\').after(\'' . activity::errormessage2('Strona została wyłączona przez administratora.<br/>Prosimy spróbować później.<br/>Powodzenia na egzaminie!') . '\');';
	echo '});';
	echo '</script>';
}
?>

