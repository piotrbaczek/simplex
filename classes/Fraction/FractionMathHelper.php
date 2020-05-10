<?php

namespace pbaczek\simplex\fraction;

/**
 * Class FractionMathHelper
 * @package pbaczek\simplex\fraction
 */
trait FractionMathHelper
{
    protected function reduction()
    {
        if ($this->numerator == 0) {
            $this->denominator = 1;
            return;
        }

        if (abs($this->numerator) == 1 || $this->denominator == 1) {
            return;
        }

        $hcd = $this->highestCommonDivisor($this->numerator, $this->denominator);
        $this->numerator /= $hcd;
        $this->denominator /= $hcd;
    }

    /**
     * Finds hcd (Highest Common Division) of two Integers
     * @static
     * @param Integer $a
     * @param Integer $b
     * @return Integer
     */
    protected static function highestCommonDivisor(int $a, int $b)
    {
        $a = abs($a);
        while ($a != $b) {
            if ($a > $b) {
                $a = $a - $b;
                continue;
            }

            $b = $b - $a;
        }
        return $a;
    }

    /**
     * Expands the Fraction (just the regular part)
     * $num * Fraction
     * @param int $number
     */
    protected function expansion(int $number)
    {
        $this->numerator *= $number;
        $this->denominator *= $number;
    }
}