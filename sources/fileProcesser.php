<?php
include '../classes/CSVReader.class.php';
include '../classes/Simplex.class.php';
include '../classes/SimplexTableu.class.php';
include '../classes/Fraction.class.php';
include '../classes/Processer.class.php';
include '../classes/activity.class.php';
include '../classes/Signs.class.php';
include '../classes/DivisionCoefficient.class.php';
include '../classes/Point.class.php';
$ss = activity::isactivated2('../activity/active.xml') == 'true' ? true : false;
$json = Array();
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Content-Type: application/json');
if ($ss) {
	//----------------------------------------------------------------------------
	$adres = '../download/' . $_POST['filename'] . '.csv';
	$plik = new Processer($adres);
	unlink($adres);
	try {
		$simplex = new Simplex($plik->getVariables(), $plik->getBoundaries(), $plik->getSigns(), $plik->getTargetfunction(), $plik->getMinMax(), $plik->getGomorry());
		$json[0] = count($simplex->getTargetFunction());
		$json[1] = $simplex->getTargetFunction();
		$json[2] = $simplex->printProblem() . $simplex->printSolution() . $simplex->printValuePair() . $simplex->printResult();
		$json[3] = $simplex->getPrimaryGraphJson();
		$json[4] = $simplex->getSecondaryGraphJson();
	} catch (Exception $e) {
		$json[0] = -2;
		$json[2] = TextareaProcesser::errormessage($e->getMessage());
	}
} else {
	$json[0] = -1;
	$json[2] = TextareaProcesser::errormessage('Strona została wyłączona przez administratora.<br/>Prosimy spróbować później.<br/>Powodzenia na egzaminie!');
}
echo \json_encode($json);
?>