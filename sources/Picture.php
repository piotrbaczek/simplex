<?php
include '../classes/Picture.class.php';
include '../classes/Fraction.class.php';
include '../classes/DivisionCoefficient.class.php';
header("Content-type: image/png");
$img=new Picture(isset($_GET['a']) ? $_GET['a'] : 'g', isset($_GET['b']) ? $_GET['b'] : 1, isset($_GET['c']) ? $_GET['c'] : 1, isset($_GET['d']) ? $_GET['d'] : 1, isset($_GET['e']) ? $_GET['e'] : 1);
?>