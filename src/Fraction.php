<?php

namespace pbaczek\simplex;

use InvalidArgumentException;
use pbaczek\simplex\Fraction\Dictionaries\Sign;
use pbaczek\simplex\Fraction\FractionMathHelper;

/**
 * Class Fraction
 * @package pbaczek\simplex
 */
class Fraction extends FractionAbstract
{
    use FractionMathHelper;

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
     * @param FractionAbstract $fractionAbstract
     * @return void
     */
    public function add(FractionAbstract $fractionAbstract): void
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
     * @param FractionAbstract $fractionAbstract
     * @return void
     */
    public function subtract(FractionAbstract $fractionAbstract): void
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
     * @param FractionAbstract $fractionAbstract
     * @return void
     */
    public function divide(FractionAbstract $fractionAbstract): void
    {
        if ($fractionAbstract instanceof self === false) {
            throw new InvalidArgumentException('Only same class allowed');
        }

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

        $this->reduction();
    }

    /**
     * @inheritDoc
     * @param FractionAbstract $fractionAbstract
     * @return void
     */
    public function multiply(FractionAbstract $fractionAbstract): void
    {
        if ($fractionAbstract instanceof self === false) {
            throw new InvalidArgumentException('Only same class allowed');
        }

        $this->setNumeratorWithoutReduction($this->getNumerator() * $fractionAbstract->getNumerator());
        $this->setDenominatorWithoutReduction($this->getDenominator() * $fractionAbstract->getDenominator());
        $this->reduction();
    }
}