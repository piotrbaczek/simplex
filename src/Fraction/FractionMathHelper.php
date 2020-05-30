<?php

namespace pbaczek\simplex\Fraction;

use pbaczek\simplex\Helpers\Math;

/**
 * Class FractionMathHelper
 * @package pbaczek\simplex\fraction
 */
trait FractionMathHelper
{
    /**
     * Reduce numerator and denominator
     */
    protected function reduction(): void
    {
        if ($this->getNumerator() == 0) {
            $this->setDenominatorWithoutReduction(1);
            return;
        }

        if (abs($this->getNumerator()) == 1 || $this->getDenominator() == 1) {
            return;
        }

        $hcd = Math::highestCommonDivisor($this->getNumerator(), $this->getDenominator());
        $this->setNumeratorWithoutReduction($this->getNumerator() / $hcd);
        $this->setDenominatorWithoutReduction($this->getDenominator() / $hcd);
    }
}