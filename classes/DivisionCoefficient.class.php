<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DivisionCoefficient
 *
 * @author PETTER
 */
class DivisionCoefficient {

	//put your code here
	const none = '-';

	private $numerator;
	private $denominator;
	private $result;

	public function __construct($n = DivisionCoefficient::none, $d = DivisionCoefficient::none) {
		$this->numerator = $n;
		$this->denominator = $d;
		$this->reduction();
	}

	private function reduction() {
		if ($this->numerator == DivisionCoefficient::none && $this->denominator == DivisionCoefficient::none) {
			$this->result = DivisionCoefficient::none;
		} elseif ($this->denominator instanceof Fraction && $this->denominator->getRealValue() == 0) {
			$this->result = new Fraction(0, 1, 1, 1);
		} else {
			$this->result = clone $this->numerator;
			$this->result->divide($this->denominator);
		}
	}

	public function getNumerator() {
		return $this->numerator;
	}

	public function getDenominator() {
		return $this->denominator;
	}

	public function getResult() {
		return $this->result;
	}

	public function __toString() {
		return '<td data-dane="dc,' . $this->numerator . ',' . $this->denominator . ',' . $this->result . '">' . $this->result . '</td>';
	}

}

?>
