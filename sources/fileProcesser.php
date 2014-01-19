<?php

include '../classes/CSVReader.class.php';
include '../classes/Simplex.class.php';
include '../classes/SimplexTableau.class.php';
include '../classes/Fraction.class.php';
include '../classes/Processer.class.php';
include '../classes/activity.class.php';
include '../classes/Signs.class.php';
include '../classes/DivisionCoefficient.class.php';
include '../classes/Point.class.php';
include '../classes/TextareaProcesser.class.php';
$ss = activity::isactivated2('../activity/active.xml') == 'true' ? true : false;
$json = Array();
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Content-Type: application/json');
if ($ss) {
	//----------------------------------------------------------------------------
	try {
		if (!isset($_POST['filename'])) {
			throw new Exception('Name of the file cannot be empty!');
		}
		$adres = '../download/' . mysql_real_escape_string($_POST['filename']) . '.csv';
		$processer = new Processer($adres);
		unlink($adres);
		$simplex = new Simplex($processer->getVariables(), $processer->getBoundaries(), $processer->getSigns(), $processer->getTargetfunction(), $processer->getMinMax(), $processer->getGomorry());
		$json[0] = count($simplex->getTargetFunction());
		$json[1] = $simplex->getMaxRangeArray();
		$json[2] = $simplex->getMinRangeArray();
		$json[3] = $simplex->printProblem() . $simplex->printSolution() . $simplex->printValuePair() . $simplex->printResult();
		$json[4] = $simplex->getPrimaryGraphJson();
		$json[5] = $simplex->getSecondaryGraphJson();
		$json[6] = serialize($simplex);
		$json[7] = $processer->getTextareaData();
	} catch (Exception $e) {
		$json[0] = -2;
		$json[3] = TextareaProcesser::errormessage($e->getMessage());
	}
} else {
	$json[0] = -1;
	$json[3] = TextareaProcesser::errormessage('Strona została wyłączona przez administratora.<br/>Prosimy spróbować później.<br/>Powodzenia na egzaminie!');
}
echo json_encode($json);
?>