<?php

include '../classes/Simplex.class.php';
include '../classes/SimplexTableu.class.php';
include '../classes/Fraction.class.php';
include '../classes/DivisionCoefficient.class.php';
include '../classes/Signs.class.php';
include '../classes/Point.class.php';

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Content-Type: application/json');

if (isset($_POST['object']) && isset($_POST['dimensions']) && isset($_POST['values'])) {
	$simplex = unserialize($_POST['object']);
	echo $simplex->getRedrawData($_POST['dimensions'], $_POST['values']);
}
