<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SimplexTableu
 *
 * @author PETTER
 */
include_once 'Fraction.class.php';

class SimplexTableu {

	private $mainRow = '';
	private $mainCol = '';
	private $array;

	public function __construct($rows = 1, $cols = 1) {
		for ($i = 0; $i < $cols; $i++) {
			for ($j = 0; $j < $rows; $j++) {
				$this->array[$i][$j] = new Fraction(0);
			}
		}
	}

	public function getRows() {
		return count($this->array);
	}

	public function getCols() {
		return count($this->array[0]);
	}

	public function setMainRow(Array $mainRow) {
		$this->mainRow = $mainRow;
	}

	public function setMainCol(Array $mainCol) {
		$this->mainCol = $mainCol;
	}

	public function setValue($rowNum, $colNum, Fraction $value) {
		if ($rowNum >= $this->getRows() || $colNum >= $this->getCols()) {
			throw new Exception('SimplexTableu: Incorrect index of Array: [' . $rowNum . ',' . $colNum . ']');
		} else {
			$this->array[$rowNum][$colNum] = $value;
		}
	}

	public function __toString() {
		$string = '';
		for ($i = 0; $i < $this->getCols(); $i++) {
			for ($j = 0; $j < $this->getRows(); $j++) {
				$string.=$this->array[$j][$i];
			}
			$string.='<br/>';
		}
		return $string;
	}

}

?>
