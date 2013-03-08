<?php

class Fraction2 {

    private $numerator;
    private $denominator;

    public function __construct($numerator = 1, $denominator = 1) {
        if (is_numeric(trim($numerator)) && is_numeric(trim($denominator))) {
            if ($denominator == 0) {
                throw new Exception('Denominator can\'t be 0!');
            } else {
                $this->numerator = trim($numerator);
                $this->denominator = trim($denominator);
                $this->reduction();
            }
        }
    }

    public function getNumerator() {
        return $this->numerator;
    }

    public function getDenominator() {
        return $this->denominator;
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
    }

    public function expansion($num) {
        $this->numerator *= $num;
        $this->denominator *= $num;
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
        return $this->denominator == 1 ? $this->numerator : $this->numerator . '/' . $this->denominator;
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
            if ($this->numerator > $param) {
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
            //echo 'dodatnie';
        } elseif (Fraction2::isNegative(new Fraction2($this->getNumerator(), $this->getDenominator()))) {
            //echo 'ujemne, bo '.$this->numerator.'/'.$this->denominator.'<br/>';
            while ($this->numerator < -$this->denominator) {
                $this->numerator+=$this->denominator;
                //echo 'ujemne, bo '.$this->numerator.'/'.$this->denominator.'<br/>';
            }
            //echo 'ujemne, bo '.$this->numerator.'/'.$this->denominator.'<br/>';
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
        return $this->numerator / $this->denominator;
    }

    public static function errormessage($message) {
        echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
    }

    public function minusFraction() {
        $this->numerator = -$this->numerator;
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
            $this->reduction();
        } else {
            $this->errormessage('Must be a fraction or number!');
        }
    }

}

//$s1=new Fraction2(+0);
//echo $s1->toString();
?>