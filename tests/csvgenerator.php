<?php

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=file.csv");
header("Pragma: no-cache");
header("Expires: 0");
include '../classes/CSVGenerator.class.php';

$data = Array();
$data[0][0] = "max";
$data[0][1] = "min";
$data[1][0] = 2;

CSVGenerator::outputCSV($data);

