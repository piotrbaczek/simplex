<?php

include '../classes/TextareaProcesser.class.php';
include '../classes/Fraction.class.php';
include '../classes/SimplexTableu.class.php';
include '../classes/Signs.class.php';
include '../classes/Simplex.class.php';
include '../classes/activity.class.php';
include '../classes/DivisionCoefficient.class.php';
include '../classes/RandomColor.class.php';
$ss = activity::isactivated2('../activity/active.xml') == 'true' ? true : false;
$json = Array();
if ($ss) {
	//header for correct json recognition
	header('Content-Type: application/json');
//----------------------------------------------------------------------------
	$_POST['textarea'] = '2x1+5x2<=30
2x1+3x2<=26
0x1+3x2<=15';
	$_POST['targetfunction'] = '2x1+6x2';
	$_POST['funct'] = 'true';
	$_POST['gomorryf'] = 'false';
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
			$json[0] = count($tp->getTargetfunction());
			$json[1] = $simplex->getJSON();
			$json[2] = $simplex->printProblem() . $simplex->printSolution() . $simplex->printValuePair() . $simplex->printResult();
		} else {
//$json[0].=TextareaProcesser::errormessage('Puste dane lub złe dane. Proszę poprawić treść wpisanego zadania.');
		}
	} catch (Exception $e) {
//$json[0].=TextareaProcesser::errormessage($e->getMessage());
	}
} else {
//	$json[0].='<script>';
//	$json[0].='$(document).ready(function(){';
//	$json[0].='$(\'#tabs\').remove();';
//	$json[0].='$(\'#header\').after(\'' . TextareaProcesser::errormessage('Strona została wyłączona przez administratora.<br/>Prosimy spróbować później.<br/>Powodzenia na egzaminie!') . '\');';
//	$json[0].='});';
//	$json[0].='</script>';
}
echo json_encode($json);
?>

