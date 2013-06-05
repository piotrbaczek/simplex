<?php

// Insert this block of code at the very top of your page:
function DEBUG_TIME_START(&$start) {
	$time = microtime();
	$time = explode(" ", $time);
	$time = $time[1] + $time[0];
	$start = $time;
}

// Place this part at the very end of your page
function DEBUG_TIME_END($start) {
	$time = microtime();
	$time = explode(" ", $time);
	$time = $time[1] + $time[0];
	$finish = $time;
	$totaltime = ($finish - $start);
	return sprintf("This section took %.2f seconds to load.\n", $totaltime);
}

$start = 0;
DEBUG_TIME_START($start);

$c = 9876413;
$a = 5 / $c;
//----------------------
echo $a;
echo '<br/>';
$a = $a + 1;
echo $a;
echo '<br/>';
$a = $a - 1;
echo $a;
echo '<br/>';
$a = $a * $c;
echo $a;
echo '<br/>';
echo $a==5 ? 'tak' : 'nie';
echo '<br/>';
echo DEBUG_TIME_END($start);
//-----------------------
echo '<hr>';
//-----------------------
DEBUG_TIME_START($start);
include '../classes/Fraction.class.php';
$d = new Fraction(5, $c);
echo $d->toString();
echo '<br/>';
$d->add(1);
echo $d->toString();
echo '<br/>';
$d->substract(1);
echo $d->toString();
echo '<br/>';
$d->multiply($c);
echo $d->toString();
echo '<br/>';
echo $d->getRealValue()==5 ? 'tak' : 'nie';
echo '<br/>';
echo DEBUG_TIME_END($start);
?>
