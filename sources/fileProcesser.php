<?php

include '../classes/CSVReader.class.php';
include '../classes/Simplex.class.php';
include '../classes/Fraction.class.php';
include '../classes/Processer.class.php';
include '../classes/activity.class.php';
$ss=activity::isactivated2('../activity/active.xml')=='true' ? true : false;
$adres = '../download/' . $_POST['filename'] . '.csv';
if (file_exists($adres)) {
	$plik = new Processer($adres);
	unlink($adres);
	if($ss){
		$simplex2 = new Simplex($plik->getVariables(), $plik->getBoundaries(), $plik->getSigns(), $plik->getTargetfunction(), $plik->getMinMax(), $plik->getGomorry());
		echo '<div style="width:60%;height:100%;float:left;">';
		$simplex2->printProblem();
		$simplex2->printSolution();
		//$simplex2->testPrint();
		$simplex2->printValuePair();
		$simplex2->printResult();
		echo '</div>';
		echo '<div style="width:40%;float:right">';
		$simplex2->getJSON();
		echo '</div><div style="width:1000px;clear:both;">';
		echo '</div>';
	}else{
		echo '<script>';
		echo '$(document).ready(function(){';
		echo '$(\'#tabs\').remove();';
		echo '$(\'#header\').after(\''.activity::errormessage2('Strona została wyłączona przez administratora.<br/>Prosimy spróbować później.<br/>Powodzenia na egzaminie!').'\');';
		echo '});';
		echo '</script>';
	}
} else {
	Simplex::errormessage('Błąd ładowania pliku ' . $_POST['filename'] . '.');
}
?>