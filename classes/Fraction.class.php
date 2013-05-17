<?php

class Fraction2 {

	private $numerator;
	private $denominator;
	private $mnumerator;
	private $mdenominator;

	public function __construct($numerator = 0, $denominator = 1, $mnumerator = 0, $mdenominator = 1) {
		settype($numerator, 'integer');
		settype($denominator, 'integer');
		settype($mnumerator, 'integer');
		settype($mdenominator, 'integer');
		if ($denominator == 0 || $mdenominator == 0) {
			throw new Exception('Denominator can\'t be a 0!');
		} else {
			$this->numerator = (int) $numerator;
			$this->denominator = (int) $denominator;
			$this->mnumerator = (int) $mnumerator;
			$this->mdenominator = (int) $mdenominator;
			$this->reduction();
		}
	}

	public function getNumerator() {
		return $this->numerator;
	}

	public function getDenominator() {
		return $this->denominator;
	}

	public function getMNumerator() {
		return $this->mnumerator;
	}

	public function getMDenominator() {
		return $this->mdenominator;
	}

	private function reduction() {
		if ($this->numerator == 0) {
			$this->denominator = 1;
		} elseif (( $this->numerator < 0 && $this->denominator < 0 ) || ( $this->denominator < 0 )) {
			$this->expansion(-1);
			$hcd = $this->highestCommonDivisor($this->numerator, $this->denominator);
			$this->numerator /= $hcd;
			$this->denominator /= $hcd;
		} else {
			$hcd = $this->highestCommonDivisor($this->numerator, $this->denominator);
			$this->numerator /= $hcd;
			$this->denominator /= $hcd;
		}

		if ($this->mnumerator == 0) {
			$this->mdenominator = 1;
		} elseif (( $this->mnumerator < 0 && $this->mdenominator < 0 ) || ( $this->mdenominator < 0 )) {
			$this->mexpansion(-1);
			$hcd = $this->highestCommonDivisor($this->mnumerator, $this->mdenominator);
			$this->mnumerator /= $hcd;
			$this->mdenominator /= $hcd;
		} else {
			$hcd = $this->highestCommonDivisor($this->mnumerator, $this->mdenominator);
			$this->mnumerator /= $hcd;
			$this->mdenominator /= $hcd;
		}
	}

	public function expansion($num) {
		$this->numerator *= $num;
		$this->denominator *= $num;
	}

	public function mexpansion($num) {
		$this->mnumerator *= $num;
		$this->mdenominator *= $num;
	}

	public function contraction($num) {
		$this->numerator /= $num;
		$this->denominator /= $num;
	}

	public function compare($param) {
		if ($param instanceof Fraction2) {
			$a = $this->numerator * $param->getDenominator();
			$b = $this->denominator * $param->getNumerator();
			if ($a > $b) {
				return true;
				//this fraction is bigger
			} else {
				return false;
				//param fraction is bigger
			}
		} elseif (is_numeric($param)) {
			$p = $this->realToFraction($param);
			$a = $this->numerator * $p[1];
			$b = $this->denominator * $p[0];
			if ($a > $b) {
				return true;
			} else {
				return false;
			}
		}
	}

	public static function isPositive($param) {
		if ($param instanceof Fraction2) {
			return $param->numerator > 0 ? true : false;
		} elseif (is_numeric($param)) {
			return $param > 0 ? true : false;
		}
	}

	public static function isNegative($param) {
		if ($param instanceof Fraction2) {
			return $param->numerator < 0 ? true : false;
		} elseif (is_numeric($param)) {
			return $param < 0 ? true : false;
		}
	}

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

	public function realToFraction($number) {
		$endOfNumber = $number - (int) $number;
		if ($endOfNumber != 0) {
			$mul = bcpow(10, strlen($endOfNumber) - 2);
			return array($number * $mul, $mul);
		} else {
			return array($number, 1);
		}
	}

	public function toString() {
		$string = '';
		if ($this->denominator == 1) {
			$string.=$this->numerator;
		} else {
			$string.=$this->numerator . '/' . $this->denominator;
		}
		if ($this->mnumerator == 0 && $this->mdenominator == 1) {
			
		} else {
			$string.=($this->mnumerator >= 0 ? '+' : '');
			$string.=($this->mdenominator == 1 ? $this->mnumerator . 'M' : $this->mnumerator . '/' . $this->mdenominator . 'M');
		}
		return $string;
	}

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

	public function isEqual($param) {
		if ($param instanceof Fraction2) {
			if ($param->getNumerator() == $this->numerator && $param->getDenominator() == $this->denominator) {
				return true;
			} else {
				return false;
			}
		} elseif (is_numeric($param)) {
			if ($this->getRealValue() == $param) {
				return true;
			} else {
				return false;
			}
		}
	}

	public function getImproperPart() {
		if (Fraction2::isPositive(new Fraction2($this->getNumerator(), $this->getDenominator()))) {
			while ($this->numerator >= $this->denominator) {
				$this->numerator-=$this->denominator;
			}
			$this->minusFraction();
		} elseif (Fraction2::isNegative(new Fraction2($this->getNumerator(), $this->getDenominator()))) {
			while ($this->numerator < -$this->denominator) {
				$this->numerator+=$this->denominator;
			}
			$this->add(1);
			$this->minusFraction();
		}
	}

	public function commonDenominator(&$fraction) {
		$lcm = $this->leastCommonMultiple($this->denominator, $fraction->denominator);
		$this->numerator = $this->numerator * ( $lcm / $this->denominator );
		$fraction->numerator = $fraction->numerator * ( $lcm / $fraction->denominator );
		$this->denominator = $fraction->denominator = $lcm;
	}

	private function leastCommonMultiple($a, $b) {
		return ( $a * $b ) / $this->highestCommonDivisor($a, $b);
	}

	public function getRealValue() {
		return ($this->mnumerator != 0 ? ($this->mnumerator > 0 ? PHP_INT_MAX : ~PHP_INT_MAX) : $this->numerator / $this->denominator);
	}

	public static function errormessage($message) {
		echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
	}

	public function minusFraction() {
		$this->numerator = -$this->numerator;
		$this->mnumerator = -$this->mnumerator;
	}

	public function add($param) {
		if (is_numeric($param)) {
			$s = new Fraction2($param);
			$this->add($s);
		} elseif ($param instanceof Fraction2) {
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

	public function substract($param) {
		if (is_numeric($param)) {
			$s = new Fraction2($param);
			$this->substract($s);
		} elseif ($param instanceof Fraction2) {
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

	public function multiply($param) {
		if (is_numeric($param)) {
			$s = new Fraction2($param);
			$this->multiply($s);
		} elseif ($param instanceof Fraction2) {
			$this->numerator*=$param->numerator;
			$this->denominator*=$param->denominator;
			$this->mnumerator*=$param->mnumerator;
			$this->mdenominator*=$param->mdenominator;
			$this->reduction();
		}
	}

	public function divide($param) {
		if (is_numeric($param)) {
			$s = new Fraction2($param);
			$this->divide($s);
		} elseif ($param instanceof Fraction2) {
			$this->numerator*=$param->denominator;
			$this->denominator*=$param->numerator;
			$this->mnumerator*=$param->mdenominator;
			$this->mdenominator*=$param->mnumerator;
			$this->reduction();
		} else {
			$this->errormessage('Must be a fraction or number!');
		}
	}

}

?>
