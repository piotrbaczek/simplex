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

	private $mainRow = -1;
	private $mainCol = -1;
	private $index = 0;
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

	public function checkTargetFunction() {
		for ($i = 0; $i < $this->getRows() - 1; $i++) {
			if (Fraction::isNegative($this->getElement($i, $this->getCols() - 1))) {
				return false;
			}
		}
		return true;
	}

	public function findBaseCol() {
		$startv = new Fraction(100000);
		$starti = -1;
		for ($i = 0; $i < $this->getRows() - 1; $i++) {
			if ($this->getElement($i, $this->getCols() - 1)->getNumerator() == 0) {
				continue;
			} elseif ($startv->compare($this->getElement($i, $this->getCols() - 1)->getNumerator()) && Fraction::isNegative($this->getElement($i, $this->getCols() - 1)->getNumerator())) {
				$starti = $i;
				$startv = clone $this->getElement($i, $this->getCols() - 1);
			}
		}
		return $starti;
	}

	public function findBaseRow($p) {
		$startv = new Fraction(100000);
		$starti = -1;
		for ($i = 0; $i < $this->getCols() - 1; $i++) {
			$s = clone $this->getElement($this->getRows() - 1, $i);
			$n = clone $this->getElement($p, $i);
			if ($n->getNumerator() == 0) {
				continue;
			} else {
				$s->divide($n);
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

}

?>