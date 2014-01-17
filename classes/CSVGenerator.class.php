<?php

/**
 * Description of CSVGenerator
 *
 * @author PETTER
 */
class CSVGenerator {

	private $function;
	private $gomorryFunction;
	private $targetFunction;
	private $textarea;

	public function __construct($function, $gomorry, $targetFunction, $textarea) {
		$this->function = $function;
		$this->gomorryFunction = $gomorry;
		$this->targetFunction = $targetFunction;
		$this->textarea = $textarea;
	}

	public function toArray() {
		$array = Array();
		if ($this->function == true) {
			$array[0][0] = 'max';
		} else {
			$array[0][0] = 'min';
		}
		if ($this->gomorryFunction == 'true') {
			$array[0][1] = 'true';
		} else {
			$array[0][1] = 'false';
		}
		$tf = preg_split('/x|\+|\-/', urldecode($this->targetFunction));
		$ArrayIndex = 2;
		for ($i = 0; $i < count($tf); $i+=2) {
			$array[0][$ArrayIndex] = $tf[$i];
		}
		$rows = explode("%0D%0A", $this->textarea);
		foreach ($rows as $key => $value) {
			$rows[$key] = urldecode($value);
		}
		foreach ($rows as $key => $value) {
			$row = preg_split('/=|<=|>=/', $value);
			$left = preg_split('/x|\+|\-/', $row[0]);
			for ($i = 0; $i < count($left); $i+=2) {
				$array[$key + 1][] = $left[$i];
			}
			if (strpos($value, '<=') !== false) {
				$array[$key + 1][] = '<=';
			} elseif (strpos($value, '>=') !== false) {
				$array[$key + 1][] = '>=';
			} else {
				$array[$key + 1][] = '=';
			}
			$array[$key + 1][] = $row[1];
		}
		return $array;
	}

	public static function outputCSV(Array $data) {
		$output = fopen("php://output", "w");
		foreach ($data as $row) {
			fputcsv($output, $row, ';');
		}
		fclose($output);
	}

}
