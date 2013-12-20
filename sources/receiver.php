<?php

include '../classes/TextareaProcesser.class.php';
include '../classes/Fraction.class.php';
include '../classes/SimplexTableu.class.php';
include '../classes/Signs.class.php';
include '../classes/Simplex.class.php';
include '../classes/activity.class.php';
include '../classes/DivisionCoefficient.class.php';
$ss = activity::isactivated2('../activity/active.xml') ? true : false;
$json = Array();
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Content-Type: application/json');
if ($ss) {
//	$_POST['textarea'] = '1x1+0x2+0x3<=1000
//0x1+1x2+0x3<=500
//0x1+0x2+1x3<=1500
//3x1+6x2+2x3<=6750';
//	$_POST['targetfunction'] = '4x1+12x2+3x3';
//	$_POST['funct'] = 'true';
//	$_POST['gomorryf'] = 'false';
//--------------------------------
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
			$json[0] = count($simplex->getTargetFunction());
			$json[1] = $simplex->getMaxRangeArray();
			$json[2] = $simplex->printProblem() . $simplex->printSolution() . $simplex->printValuePair() . $simplex->printResult();
			$json[3] = $simplex->getPrimaryGraphJson();
			$json[4] = $simplex->getSecondaryGraphJson();
		} else {
			$json[0] = -2;
			$json[2] = TextareaProcesser::errormessage('Puste dane lub złe dane. Proszę poprawić treść wpisanego zadania.');
		}
	} catch (Exception $e) {
		$json[0] = -2;
		$json[2] = TextareaProcesser::errormessage($e->getMessage());
	}
} else {
	$json[0] = -1;
	$json[2] = TextareaProcesser::errormessage('Strona została wyłączona przez administratora.<br/>Prosimy spróbować później.<br/>Powodzenia na egzaminie!');
}
echo json_encode($json);
?>

