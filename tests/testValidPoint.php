<?php

include '../classes/Point.class.php';
include '../classes/Simplex.class.php';
include '../classes/SimplexTableu.class.php';
include '../classes/DivisionCoefficient.class.php';
include '../classes/Fraction.class.php';
include '../classes/Signs.class.php';
include '../classes/TextareaProcesser.class.php';

$_POST['textarea'] = '1x1+0x2+0x3<=1000
0x1+1x2+0x3<=500
0x1+0x2+1x3<=1500
3x1+6x2+2x3<=6750';
$_POST['targetfunction'] = '4x1+12x2+3x3';
$_POST['funct'] = 'true';
$_POST['gomorryf'] = 'false';
$tp = new TextareaProcesser(
		!isset($_POST['textarea']) ? Array() : $_POST['textarea'], !isset($_POST['targetfunction']) ? Array() : $_POST['targetfunction'], !isset($_POST['funct']) ? Array() : $_POST['funct'], !isset($_POST['gomorryf']) ? Array() : $_POST['gomorryf']
);
if ($tp->isCorrect()) {
	$simplex = new Simplex($tp->getVariables(), $tp->getBoundaries(), $tp->getSigns(), $tp->getTargetfunction(), $tp->getMaxMin(), $tp->getGomorry());
	$point1 = new Point(count($simplex->getMaxRangeArray()));
	$point1->setPointDimension(0, 1001);
	$point1->setPointDimension(1, 26);
	$point1->setPointDimension(2, 16);
	//print_r($point1->toArray());
	if ($simplex->isValidPoint($point1)) {
		echo 'jest';
	} else {
		echo 'nie jest';
	}
}

