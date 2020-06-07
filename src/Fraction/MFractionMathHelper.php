<?php

namespace pbaczek\simplex\Fraction;

use pbaczek\simplex\Helpers\Math;

/**
 * Trait MFractionMathHelper
 * @package pbaczek\simplex\Fraction
 */
trait MFractionMathHelper
{
    /**
     * Reduce numerator and denominator
     * @return void
     */
    protected function reduction(): void
    {
        if ($this->getNumerator() == 0) {
            $this->setDenominatorWithoutReduction(1);
            $this->reduceMPart();
            return;
        }

        if (abs($this->getNumerator()) == 1 || $this->getDenominator() == 1) {
            $this->reduceMPart();
            return;
        }

        $greatestCommonDivisor = Math::greatestCommonDivisor($this->getNumerator(), $this->getDenominator());
        $this->setNumeratorWithoutReduction($this->getNumerator() / $greatestCommonDivisor);
        $this->setDenominatorWithoutReduction($this->getDenominator() / $greatestCommonDivisor);

        $this->reduceMPart();
    }

    /**
     * Reduce M Part of the MFraction
     * @return void
     */
    private function reduceMPart(): void
    {
        if ($this->getMNumerator() == 0) {
            $this->setMDenominatorWithoutReduction(1);
            return;
        }

        if (abs($this->getMNumerator()) == 1 || $this->getMDenominator() == 1) {
            return;
        }

        $greatestCommonDivisor = Math::greatestCommonDivisor($this->getMNumerator(), $this->getMDenominator());
        $this->setMNumeratorWithoutReduction($this->getMNumerator() / $greatestCommonDivisor);
        $this->setMDenominatorWithoutReduction($this->getMDenominator() / $greatestCommonDivisor);
    }
}