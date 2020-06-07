<?php

namespace pbaczek\simplex\Fraction;

use pbaczek\simplex\FractionAbstract;

/**
 * Trait RealPartDivider
 * @package pbaczek\simplex\Fraction
 */
trait RealPartDivider
{
    /**
     * Divide real part
     * @param FractionAbstract $fractionAbstract
     */
    private function divideRealPart($fractionAbstract)
    {
        $newNumerator = $this->getNumerator() * $fractionAbstract->getDenominator();
        if ($newNumerator < 0) {
            $this->changeSign();
        }

        $this->setNumeratorWithoutReduction(abs($newNumerator));

        $newDenominator = $this->getDenominator() * $fractionAbstract->getNumerator();

        if ($newDenominator < 0) {
            $this->changeSign();
        }

        $this->setDenominatorWithoutReduction(abs($newDenominator));
    }
}