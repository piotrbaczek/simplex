<?php

header('Content-Type: text/csv; charset=utf-8;');
header('Content-Disposition: attachment; filename=problem_' . date('Y-m-d') . '.csv');
header("Pragma: no-cache");
header("Expires: 0");
include '../classes/CSVGenerator.class.php';
if (isset($_GET['funct']) && isset($_GET['gomorryf']) && isset($_GET['targetfunction']) && isset($_GET['textarea'])) {
	$csvGenerator = new CSVGenerator($_GET['funct'], $_GET['gomorryf'], $_GET['targetfunction'], $_GET['textarea']);
	echo CSVGenerator::outputCSV($csvGenerator->toArray());
} else {
	echo 'ERROR: Brak poprawnych danych' . print_r($_GET) . ' ;';
}


