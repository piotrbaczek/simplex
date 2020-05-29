<?php

/**
 * Class storing numbers in Numerator/Denominator form,
 * which is for learning purposes more human-like and
 * provides greater understanding of Simplex method.
 * <p>
 * Fraction contains of two numerators and two denominators
 * which give numer like 1+1M
 * which is numerator/denominator (if mnumerator !=0) + mnumerator/mdenominator
 * @author Piotr Gołasz <pgolasz@gmail.com>
 * @version 1.0
 * @deprecated
 */
class Fraction implements Countable {

	/**
	 * Numerator of the Fraction
	 * @var Integer 
	 */
	private $numerator;

	/**
	 * Denominator of the Fraction
	 * @var Integer
	 */
	private $denominator;

	/**
	 * M - Numerator of the Fraction
	 * @var Integer
	 */
	private $mnumerator;

	/**
	 * M - Denominator of the Fraction
	 * @var Integer
	 */
	private $mdenominator;

	public function __construct($numerator = 0, $denominator = 1, $mnumerator = 0, $mdenominator = 1) {
		$numerator = $this->realToFraction($numerator);
		$denominator = $this->realToFraction($denominator);
		$this->numerator = (int) ( $numerator[0] * $denominator[1] );
		$this->denominator = (int) ( $denominator[0] * $numerator[1]);

		$mnumerator = $this->realToFraction($mnumerator);
		$mdenominator = $this->realToFraction($mdenominator);
		$this->mnumerator = (int) ( $mnumerator[0] * $mdenominator[1] );
		$this->mdenominator = (int) ( $mdenominator[0] * $mnumerator[1] );
		if ($this->denominator == 0 || $this->mdenominator == 0) {
			throw new Exception('Denominator can\'t be 0!');
		}
		$this->reduction();
	}

	/**
	 * Getter for Numerator
	 * @return Integer
	 */
	public function getNumerator() {
		return $this->numerator;
	}

	/**
	 * Getter for Denominator
	 * @return Integer
	 */
	public function getDenominator() {
		return $this->denominator;
	}

	/**
	 * Getter for M-Numerator
	 * @return Integer
	 */
	public function getMNumerator() {
		return $this->mnumerator;
	}

	/**
	 * Getter for M-Denominator
	 * @return Integer
	 */
	public function getMDenominator() {
		return $this->mdenominator;
	}

	private function reduction() {
		if ($this->denominator < 0) {
			$this->expansion(-1);
			$this->reduction();
		} elseif ($this->numerator == 0) {
			$this->denominator = 1;
		} elseif (abs($this->numerator) == 1 || abs($this->denominator) == 1) {
//do nothing - cannot reduce fraction with 1
		} elseif ($this->numerator < 0) {
			$this->numerator = abs($this->numerator);
			$hcd = $this->highestCommonDivisor($this->numerator, $this->denominator);
			$this->numerator /= $hcd;
			$this->denominator /= $hcd;
			$this->numerator*=-1;
		} else {
			$hcd = $this->highestCommonDivisor($this->numerator, $this->denominator);
			$this->numerator /= $hcd;
			$this->denominator /= $hcd;
		}
//----------------------------------
		if ($this->mdenominator < 0) {
			$this->mexpansion(-1);
			$this->reduction();
		} elseif ($this->mnumerator == 0) {
			$this->mdenominator = 1;
		} elseif (abs($this->mnumerator) == 1 || abs($this->mdenominator) == 1) {
//do nothing - cannot reduce fraction with 1
		} elseif ($this->mnumerator < 0) {
			$this->mnumerator = abs($this->mnumerator);
			$hcd = $this->highestCommonDivisor($this->mnumerator, $this->mdenominator);
			$this->mnumerator /= $hcd;
			$this->mdenominator /= $hcd;
			$this->mnumerator*=-1;
		} else {
			$hcd = $this->highestCommonDivisor($this->mnumerator, $this->mdenominator);
			$this->mnumerator /= $hcd;
			$this->mdenominator /= $hcd;
		}
	}

	/**
	 * Expands the Fraction (just the regular part)
	 * $num * Fraction
	 * @param Integer $num
	 */
	public function expansion($num) {
		$this->numerator *= $num;
		$this->denominator *= $num;
	}

	/**
	 * Expands the M-Part of the Fraction
	 * @param Integer $num
	 */
	public function mexpansion($num) {
		$this->mnumerator *= $num;
		$this->mdenominator *= $num;
	}

	/**
	 * Contracts Fraction
	 * Fraction/$num
	 * @param Integer $num
	 */
	public function contraction($num) {
		$this->numerator /= $num;
		$this->denominator /= $num;
	}

	/**
	 * Returns float of M-Part
	 * @return float
	 */
	public function getRealM() {
		return $this->mnumerator / $this->mdenominator;
	}

	/**
	 * Returns float of Real value of Fraction
	 * @return float
	 */
	public function getRealValue() {
		return $this->numerator / $this->denominator;
	}

	/**
	 * Compares two Fraction objects
	 * Returns true if Fraction is bigger than $param
	 * Returns false if $param is bigger than Fraction
	 * Does not return a value if both objects are equal
	 * @param Fraction or Integer $param
	 * @return boolean
	 */
	public function compare($param) {
		if ($param instanceof Fraction) {
			if ($this->getRealM() > $param->getRealM()) {
				return TRUE;
			} elseif ($this->getRealM() < $param->getRealM()) {
				return FALSE;
			} else {
				if ($this->getRealValue() > $param->getRealValue()) {
					return TRUE;
				} else {
					return FALSE;
				}
			}
		} elseif (is_numeric($param)) {
			$param = new Fraction($param);
			$this->compare($param);
		}
	}

	/**
	 * Checks if two fractions are equal to As Fractions without M part.
	 * @param Fraction $fraction1
	 * @param Fraction $fraction2
	 * @return boolean
	 */
	public static function equal(Fraction $fraction1, Fraction $fraction2) {
		if ($fraction1->getRealValue() == $fraction2->getRealValue()) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Positivity test of the Fraction
	 * Takes into consideration M values as Infinity
	 * Returns true if positive
	 * Returns false if negative
	 * Does not return value if Fraction equals zero
	 * @see equalsZero()
	 * @see isNegative()
	 * @param Fraction or Integer $param
	 * @return boolean
	 */
	public static function isPositive($param) {
		if ($param instanceof Fraction) {
			if ($param->numerator > 0) {
				if ($param->mnumerator >= 0) {
					return true;
				} else {
					return false;
				}
			} else {
				if ($param->mnumerator > 0) {
					return true;
				} else {
					return false;
				}
			}
		} elseif (is_numeric($param)) {
			return $param > 0 ? true : false;
		}
	}

	/**
	 * Test for negativity of a Fraction
	 * Returns true if fraction is negative
	 * Returns false if fraction is positive
	 * @see isPositive()
	 * @param Fraction $param
	 * @return boolean
	 */
	public static function isNegative($param) {
		if ($param instanceof Fraction) {
			if ($param->numerator < 0) {
				if ($param->mnumerator <= 0) {
					return true;
				} else {
					return false;
				}
			} else {
				if ($param->mnumerator < 0) {
					return true;
				} else {
					return false;
				}
			}
		} elseif (is_numeric($param)) {
			return $param < 0 ? true : false;
		}
	}

	/**
	 * Tests if Fraction is equal zero (Numerator and M-Numerator are both zeros).
	 * @param Fraction $param
	 * @return boolean
	 */
	public static function equalsZero($param) {
		return $param->numerator == 0 && $param->mnumerator == 0 ? true : false;
	}

	/**
	 * Tests if Fraction is an Integer
	 * @param Fraction $param
	 * @return boolean
	 */
	public static function isFraction($param) {
		return $param->denominator != 1 ? true : false;
	}

	/**
	 * Reverses a Fraction N/D into D/N
	 */
	public function reverse() {
		$sign = 1;
		$numerator = $this->numerator;
		$denominator = $this->denominator;
		if ($this->numerator < 0) {
			$sign = -1;
			$this->numerator*=$sign;
		}
		$this->numerator = $denominator;
		$this->denominator = $numerator;
		if ($sign == -1) {
			$this->numerator*=$sign;
		}
		$this->reduction();
	}

	/**
	 * Saves number as Scientific notation
	 * @param Float $number
	 * @return Array
	 */
	public function realToFraction($number) {
		$endOfNumber = $number - (int) $number;
		if ($endOfNumber != 0) {
			$mul = bcpow(10, strlen($endOfNumber) - 2);
			return array($number * $mul, $mul);
		} else {
			return array($number, 1);
		}
	}

	/**
	 * outputs Fraction as Integer if Integer
	 * and string n/d+mn/md if fraction
	 * @return String
	 */
	public function __toString() {
		$string = '';
		$equalszero = false;
		if ($this->numerator == 0) {
			//Fraction equals 0
			$equalszero = true;
		} elseif ($this->denominator == 1) {
			//Fraction is an integer
			$string.=$this->numerator;
		} else {
			//It's a Fraction
			$string.=($this->numerator . '/' . $this->denominator);
		}
		if ($this->mnumerator == 0) {
			if ($equalszero) {
				$string.='0';
			}
		} elseif ($this->mdenominator == 1) {
			if (!$equalszero) {
				$string.=($this->mnumerator >= 0 ? '+' : '');
			}
			$string.=$this->mnumerator;
			$string.='M';
		} else {
			$string.=($this->mnumerator >= 0 ? '+' : '');
			$string.=($this->mdenominator == 1 ? $this->mnumerator . 'M' : $this->mnumerator . '/' . $this->mdenominator . 'M');
		}
		return $string;
	}

	/**
	 * Finds hcd (Highest Common Division) of two Integers
	 * @static
	 * @param Integer $a
	 * @param Integer $b
	 * @return Integer
	 */
	public static function highestCommonDivisor($a, $b) {
		$a = abs($a);
		while ($a != $b) {
			if ($a > $b) {
				$a = $a - $b;
			} else {
				$b = $b - $a;
			}
		}
		return $a;
	}

	/**
	 * Test for equality of two Fractions
	 * @see compare();
	 * @param Fraction $param
	 * @return type
	 */
	public function isEqual($param) {
		if ($param instanceof Fraction) {
			return ($param->getNumerator() == $this->numerator && $param->getDenominator() == $this->denominator) ? true : false;
		} elseif (is_numeric($param)) {
			return ($this->getValue() == $param) ? true : false;
		}
	}

	/**
	 * Returns Positive side of a Fraction
	 * used in generating cuts in Gomory's Cutting Plane Method
	 * 
	 * @example
	 * ImproperPart(1/8)=1/8
	 * ImproperPart(-1/8)=7/8
	 * ImproperPart(1)=0
	 */
	public function getImproperPart() {
		if (Fraction::isPositive(new Fraction($this->getNumerator(), $this->getDenominator()))) {
			while ($this->numerator >= $this->denominator) {
				$this->numerator-=$this->denominator;
			}
		} elseif (Fraction::isNegative(new Fraction($this->getNumerator(), $this->getDenominator()))) {
			while ($this->numerator < -$this->denominator) {
				$this->numerator+=$this->denominator;
			}
			$this->add(1);
		}
	}

	/**
	 * 
	 * @param Fraction $fraction
	 */
	public function commonDenominator(&$fraction) {
		$lcm = $this->leastCommonMultiple($this->denominator, $fraction->denominator);
		$this->numerator = $this->numerator * ( $lcm / $this->denominator );
		$fraction->numerator = $fraction->numerator * ( $lcm / $fraction->denominator );
		$this->denominator = $fraction->denominator = $lcm;
	}

	private function leastCommonMultiple($a, $b) {
		return ( $a * $b ) / $this->highestCommonDivisor($a, $b);
	}

	/**
	 * Returns Integer representing value of the fraction
	 * Returns + or - PHP_INT_MAX if M-Part present
	 * @return Integer
	 */
	public function getValue() {
		return ($this->mnumerator != 0 ? ($this->mnumerator > 0 ? PHP_INT_MAX : -PHP_INT_MAX) : $this->numerator / $this->denominator);
	}

	public static function errormessage($message) {
		echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
	}

	/**
	 * Multiplying Fraction by -1
	 */
	public function minusFraction() {
		$this->numerator = -$this->numerator;
		$this->mnumerator = -$this->mnumerator;
	}

	/**
	 * Simple adding
	 * @param Fraction or Integer $param
	 */
	public function add($param) {
		if (is_numeric($param)) {
			$s = new Fraction($param);
			$this->add($s);
		} elseif ($param instanceof Fraction) {
			$denominator = $this->denominator * $param->denominator;
			$numerator = $this->numerator * $param->denominator + $this->denominator * $param->numerator;
			$this->numerator = $numerator;
			$this->denominator = $denominator;
			$mdenominator = $this->mdenominator * $param->mdenominator;
			$mnumerator = $this->mnumerator * $param->mdenominator + $this->mdenominator * $param->mnumerator;
			$this->mnumerator = $mnumerator;
			$this->mdenominator = $mdenominator;
			$this->reduction();
		}
	}

	/**
	 * Simple substraction
	 * @param Fraction or Integer $param
	 */
	public function substract($param) {
		if (is_numeric($param)) {
			$s = new Fraction($param);
			$this->substract($s);
		} elseif ($param instanceof Fraction) {
			$denominator = $this->denominator * $param->denominator;
			$numerator = $this->numerator * $param->denominator - $this->denominator * $param->numerator;
			$this->numerator = $numerator;
			$this->denominator = $denominator;
			$mdenominator = $this->mdenominator * $param->mdenominator;
			$mnumerator = $this->mnumerator * $param->mdenominator - $this->mdenominator * $param->mnumerator;
			$this->mnumerator = $mnumerator;
			$this->mdenominator = $mdenominator;
			$this->reduction();
		}
	}

	/**
	 * Simple multiplication
	 * @param Fraction or Integer $param
	 */
	public function multiply($param) {
		if (is_numeric($param)) {
			$s = new Fraction($param);
			$this->multiply($s);
		} elseif ($param instanceof Fraction) {
			$this->numerator*=$param->numerator;
			$this->denominator*=$param->denominator;
			$this->mnumerator*=$param->numerator;
			$this->mdenominator*=$param->denominator;
			$this->reduction();
		}
	}

	/**
	 * Simple division
	 * @param Fraction or Integer $param
	 */
	public function divide($param) {
		if (is_numeric($param)) {
			$s = new Fraction($param);
			$this->divide($s);
		} elseif ($param instanceof Fraction) {
			$this->numerator*=$param->denominator;
			$this->denominator*=$param->numerator;
			$this->mnumerator*=$param->denominator;
			$this->mdenominator*=$param->numerator;
			$this->reduction();
		} else {
			$this->errormessage('Must be a fraction or number!');
		}
	}

	/**
	 * Removes M-Part
	 * @param Fraction $param
	 */
	public static function removeM($param) {
		$param->mnumerator = 0;
		$param->mdenominator = 1;
	}

	/**
	 * Incrementation by 1
	 */
	public function increment() {
		$this->add(new Fraction(1));
	}

	/**
	 * Test for M-Part 
	 * @param Fraction $param
	 * @return boolean
	 */
	public static function hasM($param) {
		return $param->getMNumerator() == 0 ? false : true;
	}

	/**
	 * Tests if Fraction is an integer
	 * @return boolean
	 */
	public function isInteger() {
		return is_integer($this->numerator / $this->denominator);
	}

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return 1;
    }
}

?>
