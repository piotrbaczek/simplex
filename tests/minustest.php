<?php
include '../classes/Fraction.class.php';
$a=new Fraction2(2,3,-2,3);
$a->_increment();
echo $a->toString();
if(Fraction2::isNegative($a)){
	echo 'jest ujemny';
}
?>
