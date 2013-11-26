<?php

include '../classes/TextareaProcesser.class.php';
include '../classes/Fraction.class.php';
include '../classes/SimplexTableu.class.php';
include '../classes/Signs.class.php';
include '../classes/Simplex.class.php';
include '../classes/activity.class.php';
$ss = activity::isactivated2('../activity/active.xml') == 'true' ? true : false;
if ($ss) {
	//----------------------------------------------------------------------------
//	$_POST['textarea'] = '2x1+5x2<=30
//2x1+3x2<=26
//0x1+3x2<=15';
//	$_POST['targetfunction'] = '2x1+6x2';
//	$_POST['funct'] = 'true';
//	$_POST['gomorryf'] = 'false';
	$tp = new TextareaProcesser(
			!isset($_POST['textarea']) ? Array() : $_POST['textarea'], !isset($_POST['targetfunction']) ? Array() : $_POST['targetfunction'], !isset($_POST['funct']) ? Array() : $_POST['funct'], !isset($_POST['gomorryf']) ? Array() : $_POST['gomorryf']
	);
//echo '<pre>';
//print_r($_POST);
//print_r($tp->getBoundaries());
//print_r($tp->getVariables());
//print_r($tp->getSigns());
//print_r($tp->getGomorry());
//print_r($tp->getMaxMin());
//echo '</pre>';
	try {
		if ($tp->isCorrect()) {
			$simplex = new Simplex($tp->getVariables(), $tp->getBoundaries(), $tp->getSigns(), $tp->getTargetfunction(), $tp->getMaxMin(), $tp->getGomorry());
			echo '<div style="width:700px;height:100%;float:left;">';
			$simplex->printProblem();
			$simplex->printSolution();
			$simplex->printValuePair();
			$simplex->printResult();
			
			echo '</div>';
			echo '<div style="width:500px;float:right;">';
			$simplex->getJSON();
			echo '</div><div style="width:1000px;clear:both;">';
			echo '</div>';
		} else {
			TextareaProcesser::errormessage('Puste dane lub złe dane. Proszę poprawić treść wpisanego zadania.');
		}
	} catch (Exception $e) {
		echo $e->getMessage();
	}
} else {
	echo '<script>';
	echo '$(document).ready(function(){';
	echo '$(\'#tabs\').remove();';
	echo '$(\'#header\').after(\'' . TextareaProcesser::errormessage('Strona została wyłączona przez administratora.<br/>Prosimy spróbować później.<br/>Powodzenia na egzaminie!') . '\');';
	echo '});';
	echo '</script>';
}
?>

