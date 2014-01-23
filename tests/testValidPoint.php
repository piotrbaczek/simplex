<?php

include '../classes/Point.class.php';
include '../classes/Simplex.class.php';
include '../classes/SimplexTableu.class.php';
include '../classes/DivisionCoefficient.class.php';
include '../classes/Fraction.class.php';
include '../classes/Signs.class.php';
include '../classes/TextareaProcesser.class.php';

	$_POST['textarea'] = '2x1+5x2<=30
2x1+3x2<=26
0x1+3x2<=15';
	$_POST['targetfunction'] = '2x1+6x2';
	$_POST['funct'] = 'true';
	$_POST['gomorryf'] = 'false';
$tp = new TextareaProcesser(
		!isset($_POST['textarea']) ? Array() : $_POST['textarea'], !isset($_POST['targetfunction']) ? Array() : $_POST['targetfunction'], !isset($_POST['funct']) ? Array() : $_POST['funct'], !isset($_POST['gomorryf']) ? Array() : $_POST['gomorryf']
);
if ($tp->isCorrect()) {
	$simplex = new Simplex($tp->getVariables(), $tp->getBoundaries(), $tp->getSigns(), $tp->getTargetfunction(), $tp->getMaxMin(), $tp->getGomorry());
	$point1 = new Point(count($simplex->getMaxRangeArray()));
	$point1->setPointDimension(0, 8);
	$point1->setPointDimension(1, 2);
	$point1->setPointDimension(4, 4);
	print_r($point1->toArray());
	print_r($simplex->getMaxRangeArray());
	////echo $simplex->getRedrawData([1, 2, 4], [3234]);
	if ($simplex->isValidPoint($point1)) {
		echo 'jest';
	} else {
		echo 'nie jest';
	}
}

