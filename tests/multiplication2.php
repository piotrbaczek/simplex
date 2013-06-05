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

$a = 1;
$b = 7;
$c = $a / $b;
//----------------------
echo $c;
echo '<br/>';
$c += 1;
echo $c;
echo '<br/>';
$c -=1;
echo $c;
echo '<br/>';
$c *=$b;
echo $c;
echo '<br/>';
echo DEBUG_TIME_END($start);
//-----------------------
echo '<hr>';
//-----------------------
DEBUG_TIME_START($start);
include '../classes/Fraction.class.php';
$fraction = new Fraction($a, $b);
echo $fraction->toString();
echo '<br/>';
$fraction->add(1);
echo $fraction->toString();
echo '<br/>';
$fraction->substract(1);
echo $fraction->toString();
echo '<br/>';
$fraction->multiply($b);
echo $fraction->toString();
echo '<br/>';
echo DEBUG_TIME_END($start);
?>
