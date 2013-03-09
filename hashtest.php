<?php

function testAlgos() {
    $algos = hash_algos();
    $word = "This will be crypted by all different algoritms";
    $results = array();
    foreach ($algos as $algo) {
        $time = microtime(1);
        $data = hash($algo, $word, false);
        $results["" . (microtime(1) - $time)][] = "$algo (hex)";
    }
    foreach ($algos as $algo) {
        $time = microtime(1);
        $data = hash($algo, $word, true);
        $results["" . (microtime(1) - $time)][] = "$algo (raw)";
    }
    ksort($results);
    foreach ($results as $time => &$algos) {
        echo $time . "\n";
        sort($algos);
        foreach ($algos as $algo)
            echo "\t" . $algo . "\n";
    }
}

testAlgos();
?>