<?php

include '../classes/CSVReader.class.php';
include '../classes/Simplex.class.php';
include '../classes/SimplexTableu.class.php';
include '../classes/Fraction.class.php';
include '../classes/Processer.class.php';
include '../classes/activity.class.php';
include '../classes/Signs.class.php';
include '../classes/DivisionCoefficient.class.php';
$ss = activity::isactivated2('../activity/active.xml') == 'true' ? true : false;
if ($ss) {
	//----------------------------------------------------------------------------
	$adres = '../download/' . $_POST['filename'] . '.csv';
	$plik = new Processer($adres);
	unlink($adres);
	try {
		$simplex = new Simplex($plik->getVariables(), $plik->getBoundaries(), $plik->getSigns(), $plik->getTargetfunction(), $plik->getMinMax(), $plik->getGomorry());
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