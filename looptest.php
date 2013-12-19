<?php

//header('Content-Type: application/json');
$dataarray = [15, 26 / 3, 15, 13, 15];
$scopeArray = Array(1, 2, 4);

function getSecondaryGraphJson(Array $dataArray, Array $scopeArray) {
	$divider = 20;
	if (count($scopeArray) != 3) {
		throw new Exception('Scope array must be 3 dimensional.');
	}
	foreach ($scopeArray as $value) {
		if ($value > count($dataArray)) {
			throw new Exception($value . " exceeds scope of dataArray.");
		}
	}
	$json = Array();
	for ($i = 0; $i <= $dataArray[0]; $i+=($dataArray[0] / $divider)) {
		for ($j = 0; $j <= $dataArray[1]; $j+=($dataArray[1] / $divider)) {
			for ($k = 0; $k <= $dataArray[2]; $k+=($dataArray[2] / $divider)) {
				$json[] = Array(round($i, 2), round($j, 2), round($k, 2));
			}
		}
	}
	return $json;
}

echo json_encode(getSecondaryGraphJson($dataarray, $scopeArray));
