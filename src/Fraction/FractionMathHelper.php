<?php

namespace pbaczek\simplex\Fraction;

use pbaczek\simplex\Helpers\Math;

/**
 * Class FractionMathHelper
 * @package pbaczek\simplex\fraction
 * @property int $numerator
 * @property int $denominator
 */
trait FractionMathHelper
{
    /**
     * Reduce numerator and denominator
     */
    protected function reduction(): void
    {
        if ($this->numerator == 0) {
            $this->denominator = 1;
            return;
        }

        if (abs($this->numerator) == 1 || $this->denominator == 1) {
            return;
        }

        $hcd = Math::highestCommonDivisor($this->numerator, $this->denominator);
        $this->numerator /= $hcd;
        $this->denominator /= $hcd;
    }
}