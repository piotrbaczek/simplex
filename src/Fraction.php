<?php

namespace pbaczek\simplex;

use InvalidArgumentException;
use pbaczek\simplex\Fraction\FractionMathHelper;
use pbaczek\simplex\Fraction\RealPartDivider;

/**
 * Class Fraction
 * @package pbaczek\simplex
 */
class Fraction extends FractionAbstract
{
    use FractionMathHelper, RealPartDivider;

    /**
     * Checks if fraction is equal to zero
     * @return bool
     */
    public function equalsZero(): bool
    {
        return $this->getNumerator() === 0;
    }

    /**
     * @inheritDoc
     * @param Fraction $fractionAbstract
     * @return void
     */
    public function add($fractionAbstract): void
    {
        if ($fractionAbstract instanceof self === false) {
            throw new InvalidArgumentException('Only same class allowed');
        }

        $this->setNumeratorWithoutReduction($this->getNumerator() * $fractionAbstract->getDenominator() + $this->getDenominator() * $fractionAbstract->getNumerator());
        $this->setDenominatorWithoutReduction($this->getDenominator() * $fractionAbstract->getDenominator());
        $this->reduction();
    }

    /**
     * @inheritDoc
     * @param Fraction $fractionAbstract
     * @return void
     */
    public function subtract($fractionAbstract): void
    {
        if ($fractionAbstract instanceof self === false) {
            throw new InvalidArgumentException('Only same class allowed');
        }

        $this->setNumeratorWithoutReduction($this->getNumerator() * $fractionAbstract->getDenominator() - $this->getDenominator() * $fractionAbstract->getNumerator());
        $this->setDenominatorWithoutReduction($this->getDenominator() * $fractionAbstract->getDenominator());
        $this->reduction();
    }

    /**
     * @inheritDoc
     * @param Fraction $fractionAbstract
     * @return void
     */
    public function divide($fractionAbstract): void
    {
        if ($fractionAbstract instanceof self === false) {
            throw new InvalidArgumentException('Only same class allowed');
        }

        $this->divideRealPart($fractionAbstract);

        $this->reduction();
    }

    /**
     * @inheritDoc
     * @param Fraction $fractionAbstract
     * @return void
     */
    public function multiply($fractionAbstract): void
    {
        if ($fractionAbstract instanceof self === false) {
            throw new InvalidArgumentException('Only same class allowed');
        }

        $this->setNumeratorWithoutReduction($this->getNumerator() * $fractionAbstract->getNumerator());
        $this->setDenominatorWithoutReduction($this->getDenominator() * $fractionAbstract->getDenominator());
        $this->reduction();
    }
}