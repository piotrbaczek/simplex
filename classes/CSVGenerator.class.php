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

	public function __toString() {
		$string = '';
		if ($this->function == true) {
			$string.= 'max;';
		} else {
			$string.='min;';
		}
		if ($this->gomorryFunction == 'true') {
			$string.='true;';
		} else {
			$string.='false;';
		}
		$tf = preg_split('/x|\+|\-/', urldecode($this->targetFunction));
		for ($i = 0; $i < count($tf); $i+=2) {
			$string.=$tf[$i] . ';';
		}
		$string.="\n";
		$rows = explode("%0D%0A", $this->textarea);
		foreach ($rows as $key => $value) {
			$rows[$key] = urldecode($value);
		}
		foreach ($rows as $key => $value) {
			$row = preg_split('/=|<=|>=/', $value);
			$left = preg_split('/x|\+|\-/', $row[0]);
			for ($i = 0; $i < count($left); $i+=2) {
				$string.=$left[$i] . ';';
			}
			$string.=$row[1] . ';';
			$string.="\n";
		}
		return $string;
	}

}
