<?php

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename=problem_' . date('Y-m-d') . '.csv');
include '../classes/CSVGenerator.class.php';
if (isset($_GET['funct']) && isset($_GET['gomorryf']) && isset($_GET['targetfunction']) && isset($_GET['textarea'])) {
	$csvGenerator = new CSVGenerator($_GET['funct'], $_GET['gomorryf'], $_GET['targetfunction'], $_GET['textarea']);
	echo $csvGenerator;
} else {
	echo 'ERROR: Brak poprawnych danych' . print_r($_GET) . ' ;';
}


