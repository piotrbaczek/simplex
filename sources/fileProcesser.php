<?php

include '../classes/CSVReader.class.php';
include '../classes/Simplex.class.php';
include '../classes/Fraction.class.php';
include '../classes/Processer.class.php';
include '../classes/activity.class.php';
$ss = activity::isactivated2('../activity/active.xml') == 'true' ? true : false;
$adres = '../download/' . $_POST['filename'] . '.csv';
if (file_exists($adres)) {
	$plik = new Processer($adres);
	unlink($adres);
	if ($ss) {
		$simplex = new Simplex($tp->getVariables(), $tp->getBoundaries(), $tp->getSigns(), $tp->getTargetfunction(), $tp->getMaxMin(), $tp->getGomorry());
		echo '<div style="width:700px;height:100%;float:left;">';
		$simplex->printProblem();
		$simplex->printSolution();
		//$simplex2->testPrint();
		$simplex->printValuePair();
		$simplex->printResult();
		echo '</div>';
		echo '<div style="width:500px;float:right;">';
		$simplex->getJSON();
		echo '</div><div style="width:1000px;clear:both;">';
		echo '</div>';
	} else {
		echo '<script>';
		echo '$(document).ready(function(){';
		echo '$(\'#tabs\').remove();';
		echo '$(\'#header\').after(\'' . activity::errormessage2('Strona została wyłączona przez administratora.<br/>Prosimy spróbować później.<br/>Powodzenia na egzaminie!') . '\');';
		echo '});';
		echo '</script>';
	}
} else {
	Simplex::errormessage('Błąd ładowania pliku ' . $_POST['filename'] . '.');
}
?>