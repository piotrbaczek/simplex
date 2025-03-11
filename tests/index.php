<?php

use tests\RandomClass;

include_once 'RandomClass.php';

$class = new RandomClass();
try {
    $class->go();
} catch (Exception $ex) {
    echo $ex->getMessage();
}
