<?php

/**
 * Encapsulates Multidimensional-array into SimplexTableau class
 * Private class of Simplex class
 * @author PETTER
 * @version 1.1
 * @deprecated
 */
class SimplexTableau {

	private $mainRow = -1;
	private $mainCol = -1;
	private $index = 1;
	private $gomoryTable = false;
	private $array;
	private $divisionArray = Array();

	/**
	 * Creates multidimensional Array rows * cols
	 * @param Integer $rows
	 * @param Integer $cols
	 */
	public function __construct($rows = 1, $cols = 1) {
		for ($i = 0; $i < $cols; $i++) {
			for ($j = 0; $j < $rows; $j++) {
				$this->array[$i][$j] = new Fraction(0);
			}
		}
		for ($i = 0; $i < $this->getRows() - 1; $i++) {
			$this->divisionArray[$i] = new DivisionCoefficient();
		}
	}

	/**
	 * returns number of rows
	 * @return Integer
	 */
	public function getRows() {
		return count($this->array);
	}

	/**
	 * Returns number of Columns
	 * @return Integer
	 */
	public function getCols() {
		return count($this->array[0]);
	}

	/**
	 * Setter for Simplex method pivot Row
	 * @param type Integer
	 */
	public function setMainRow($mainRow) {
		$this->mainRow = $mainRow;
	}

	/**
	 * Setter for Simplex method pivot Column
	 * @param Integer $mainCol
	 */
	public function setMainCol($mainCol) {
		$this->mainCol = $mainCol;
	}

	/**
	 * Setter for SimplexTableau cell
	 * @param Integer $rowNum
	 * @param Integer $colNum
	 * @param Fraction $value
	 * @throws Exception
	 */
	public function setValue($rowNum, $colNum, Fraction $value) {
		if ($rowNum >= $this->getRows() || $colNum >= $this->getCols()) {
			throw new Exception('SimplexTableu (' . __FUNCTION__ . '): Incorrect index of Array: [' . $rowNum . ',' . $colNum . ']');
		} else {
			$this->array[$rowNum][$colNum] = $value;
		}
	}

	/**
	 * Setter for Current index
	 * @param int $index
	 */
	public function setIndex($index) {
		$this->index = (int) $index;
	}

	/**
	 * Returns cell value of Matrix[$rowNum][$colNum]
	 * @param int $rowNum
	 * @param int $colNum
	 * @return type
	 * @throws Exception
	 */
	public function getElement($rowNum, $colNum) {
		if ($rowNum >= $this->getRows() || $colNum >= $this->getCols() || $rowNum < 0 || $colNum < 0) {
			throw new Exception('SimplexTableu (' . __FUNCTION__ . '): Incorrect index of Array: [' . $rowNum . ',' . $colNum . ']');
		} else {
			return $this->array[$rowNum][$colNum];
		}
	}

	/**
	 * Returns raw array
	 * @return array
	 */
	public function getArray() {
		return $this->array;
	}

	/**
	 * Getter for index
	 * @return int
	 */
	public function getIndex() {
		return $this->index;
	}

	/**
	 * Returns index with minimal negative value of last row
	 * @see Simplex
	 * @return int
	 */
	public function findBaseCol() {
		$startv = new Fraction(PHP_INT_MAX);
		$starti = -1;
		for ($i = 0; $i < $this->getRows() - 1; $i++) {
			$element = clone $this->getElement($i, $this->getCols() - 1);
			if (Fraction::equalsZero($element)) {
				continue;
			} elseif ($startv->compare($element) && Fraction::isNegative($element)) {
				$starti = $i;
				$startv = $element;
			}
		}
		return $starti;
	}

	/**
	 * Returns row number with minimal positive fraction of P0/aij
	 * @see Simplex.class.php
	 * @param int $p
	 * @return int
	 */
	public function findBaseRow($p) {
		$startv = new Fraction(PHP_INT_MAX);
		$starti = -1;
		for ($i = 0; $i < $this->getCols() - 1; $i++) {
			$s = clone $this->getElement($this->getRows() - 1, $i);
			$n = clone $this->getElement($p, $i);
			if (Fraction::equalsZero($n) || Fraction::isNegative($n)) {
				$this->divisionArray[$i] = new DivisionCoefficient();
				continue;
			} elseif (Fraction::isNegative($n)) {
				$this->divisionArray[$i] = new DivisionCoefficient(clone $s, clone $n);
				$s->divide($n);
			} else {
				$this->divisionArray[$i] = new DivisionCoefficient(clone $s, clone $n);
				$s->divide($n);
				if (!$s->compare($startv) && !Fraction::isNegative($s) && !Fraction::equal($startv, $s)) {
					$starti = $i;
					$startv = $s;
				}
			}
		}
		return $starti;
	}

	/**
	 * Getter for MainRow
	 * @return int
	 */
	public function getMainRow() {
		return $this->mainRow;
	}

	/**
	 * Getter for MainCol
	 * @return int
	 */
	public function getMainCol() {
		return $this->mainCol;
	}

	/**
	 * Swaps flag of Gomory Tableau
	 */
	public function swapGomory() {
		$this->gomoryTable = ($this->gomoryTable ? false : true);
	}

	/**
	 * Returns Gomory Tableau flag
	 * True if Gomory, false otherwise
	 * @return boolean
	 */
	public function isGomory() {
		return $this->gomoryTable;
	}

	/**
	 * Prints array as basic, simple HTML <array>
	 */
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

	/**
	 * Returns array of Divisions
	 * @see DivisionCoefficient.class.php
	 * @return array
	 */
	public function getDivisionArray() {
		return $this->divisionArray;
	}

}

?>
