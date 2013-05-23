<?php
include '../classes/Fraction.class.php';
$a=new Fraction(2,3,-2,3);
$a->_increment();
echo $a->toString();
if(Fraction::isNegative($a)){
	echo 'jest ujemny';
}
?>
