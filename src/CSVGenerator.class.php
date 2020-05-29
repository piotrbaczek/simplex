<?php

/**
 * @example ../sources/generateCSV.php generating csv from input data
 * @author PETTER
 * @deprecated
 */
class CSVGenerator {

	/**
	 * True/False corresponding to maximization function
	 * @var String 
	 */
	private $function;

	/**
	 * 'true'/'false' corresponding to use of Gomory algorithm
	 * @var String
	 */
	private $gomorryFunction;

	/**
	 * Target function on Linear problem URI encoded
	 * @var String
	 */
	private $targetFunction;

	/**
	 * Multidimensional array with coefficients if Linear problem
	 * @var String
	 */
	private $textarea;

	public function __construct($function, $gomorry, $targetFunction, $textarea) {
		$this->function = $function;
		$this->gomorryFunction = $gomorry;
		$this->targetFunction = $targetFunction;
		$this->textarea = $textarea;
	}

	/**
	 * Creates Array to be outputed as CSV from input data
	 * @return Array
	 */
	public function toArray() {
		$array = Array();
		if ($this->function == 'true') {
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
			$ArrayIndex++;
		}
		$rows = explode("%0D%0A", $this->textarea);
		foreach ($rows as $key => $value) {
			$rows[$key] = urldecode($value);
		}
		$left = Array();
		foreach ($rows as $key => $value) {
			$row = preg_split('/=|<=|>=/', $value);
			preg_match_all("/[\-][\d]+[\/][\d]+[x][\d]+|[\d]+[\/][\d]+[x][\d]+|[\-][\d]+[x][\d]+|[\d]+[x][\d]+/", $row[0], $left);
			foreach ($left as $leftvalue) {
				foreach ($leftvalue as $morevalue) {
					$newvalue = preg_split('/x/', $morevalue);
					$array[$key + 1][] = $newvalue[0];
				}
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

	/**
	 * Puts data into PHP's fputcsv() outputing as CSV
	 * @param array $data
	 * @static
	 * @description 
	 */
	public static function outputCSV(Array $data) {
		$output = fopen("php://output", "w");
		foreach ($data as $row) {
			fputcsv($output, $row, ';');
		}
		fclose($output);
	}

}
