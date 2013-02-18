<?php

include '../classes/CSVReader.class.php';
include '../classes/Simplex.class.php';
include '../classes/Ulamek.class.php';
include '../classes/Processer.class.php';
include '../classes/activity.class.php';
$sa=activity::isactivated2('../activity/active.xml')=='true' ? true : false;

$adres = '../download/' . $_POST['filename'] . '.csv';
//$adres = '../upload/Book1.csv';
if (file_exists($adres)) {
	$plik = new Processer($adres);
	unlink($adres);
	if($sa){
		$ss = new Simplex();
		$ss->Solve($plik->getVariables(), $plik->getBoundaries(), $plik->getSigns(), $plik->getTargetFunction(), $plik->getMinMax(), $plik->getGomorry());
		echo '<div style="width:40%;float:left;">';
		$ss->testprint();
		$ss->printValuePair();
		$ss->printResult();
		echo '</div><div style="width:60%;float:right">';
		echo '<div style="margin:0px auto;">';
		$ss->getjsonData($plik->getVariables(), $plik->getBoundaries(),$plik->getTargetfunction(),2);
		echo '</div></div><div style="width:1000px;clear:both;"></div>';
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