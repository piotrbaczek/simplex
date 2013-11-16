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

class SimplexTableu {

	private $mainRow = -1;
	private $mainCol = -1;
	private $index = 0;
	private $gomoryTable = false;
	private $array;
	private $divisionArray;

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

	public function setMainRow($mainRow) {
		$this->mainRow = $mainRow;
	}

	public function setMainCol($mainCol) {
		$this->mainCol = $mainCol;
	}

	public function setValue($rowNum, $colNum, Fraction $value) {
		if ($rowNum >= $this->getRows() || $colNum >= $this->getCols()) {
			throw new Exception('SimplexTableu (' . __FUNCTION__ . '): Incorrect index of Array: [' . $rowNum . ',' . $colNum . ']');
		} else {
			$this->array[$rowNum][$colNum] = $value;
		}
	}

	public function setIndex($index) {
		$this->index = (int) $index;
	}

	public function getElement($rowNum, $colNum) {
		if ($rowNum >= $this->getRows() || $colNum >= $this->getCols()) {
			throw new Exception('SimplexTableu (' . __FUNCTION__ . '): Incorrect index of Array: [' . $rowNum . ',' . $colNum . ']');
		} else {
			return $this->array[$rowNum][$colNum];
		}
	}

	public function getArray() {
		return $this->array;
	}

	public function getIndex() {
		return $this->index;
	}

	public function findBaseCol() {
		$startv = new Fraction(PHP_INT_MAX);
		$starti = -1;
		for ($i = 0; $i < $this->getRows() - 1; $i++) {
			if (Fraction::equalsZero($this->getElement($i, $this->getCols() - 1))) {
				continue;
			} elseif ($startv->compare($this->getElement($i, $this->getCols() - 1)) && Fraction::isNegative($this->getElement($i, $this->getCols() - 1))) {
				$starti = $i;
				$startv = clone $this->getElement($i, $this->getCols() - 1);
			}
		}
		return $starti;
	}

	public function findBaseRow($p) {
		$startv = new Fraction(PHP_INT_MAX);
		$starti = -1;
		for ($i = 0; $i < $this->getCols() - 1; $i++) {
			$s = clone $this->getElement($this->getRows() - 1, $i);
			$n = clone $this->getElement($p, $i);
			if (Fraction::equalsZero($n) || Fraction::isNegative($n)) {
				$this->divisionArray[$i] = new Fraction(0,1,1,1);
				continue;
			} else {
				$s->divide($n);
				$this->divisionArray[$i] = clone $s;
				if (!$s->compare($startv) && Fraction::isPositive($s)) {
					$starti = $i;
					$startv = $s;
				}
			}
		}
		return $starti;
	}

	public function getMainRow() {
		return $this->mainRow;
	}

	public function getMainCol() {
		return $this->mainCol;
	}

	public function swapGomory() {
		$this->gomoryTable = ($this->gomoryTable == true ? false : true);
	}

	public function isGomory() {
		return $this->gomoryTable;
	}

	public function printArray() {
		echo '<table>';
		for ($i = 0; $i < $this->getCols(); $i++) {
			echo '<tr>';
			for ($j = 0; $j < $this->getRows(); $j++) {
				echo '<td>' . $this->getElement($j, $i) . '</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	}

	public function getDivisionArray() {
		return $this->divisionArray;
	}

}

?>
