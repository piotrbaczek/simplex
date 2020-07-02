<?php

/**
 * Generating table rows with data-dane for
 * displaying as tooltips
 *
 * @version 1.0
 * @author PETTER
 * @deprecated
 */
class DivisionCoefficient {

	/**
	 * None equals to situation where denominator is equal 0 or negative
	 * @static
	 */
	const none = '-';

	/**
	 * Numerator of the fraction
	 * @var Fraction or const
	 */
	private $numerator;

	/**
	 * Denominator of the fraction
	 * @var Fraction or const
	 */
	private $denominator;

	/**
	 * Result of division. const if const present
	 * @var Fraction or const
	 */
	private $result;

	/**
	 * Constructor
	 * @param Fraction $n
	 * @param Fraction $d
	 */
	public function __construct($n = DivisionCoefficient::none, $d = DivisionCoefficient::none) {
		$this->numerator = $n;
		$this->denominator = $d;
		$this->reduction();
	}

	/**
	 * Measures the result of the division. Const if const present.
	 */
	private function reduction() {
		if ($this->numerator == DivisionCoefficient::none && $this->denominator == DivisionCoefficient::none) {
			$this->result = DivisionCoefficient::none;
		} else {
			$this->result = clone $this->numerator;
			$this->result->divide($this->denominator);
		}
	}

	/**
	 * Returns numerator of the fraction
	 * @return Fraction or const
	 */
	public function getNumerator() {
		return $this->numerator;
	}

	/**
	 * Returns denomiator of the fraction
	 * @return Fraction or const
	 */
	public function getDenominator() {
		return $this->denominator;
	}

	/**
	 * Returns result of the fraction
	 * @return Fraction or const
	 */
	public function getResult() {
		return $this->result;
	}

	/**
	 * Outputs Division as formed HTML <td>
	 * @return String
	 */
	public function __toString() {
		return '<td data-dane="dc,' . $this->numerator . ',' . $this->denominator . ',' . $this->result . '">' . $this->result . '</td>';
	}

}

?>
