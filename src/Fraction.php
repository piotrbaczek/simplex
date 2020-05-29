<?php

namespace pbaczek\simplex;

/**
 * Class Fraction
 * @package pbaczek\simplex
 */
class Fraction extends FractionAbstract
{
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
        $this->setNumerator($this->getNumerator() * $fractionAbstract->getDenominator() + $this->getDenominator() * $fractionAbstract->getNumerator());
        $this->setDenominator($this->getDenominator() * $fractionAbstract->getDenominator());
        $this->reduction();
    }

    /**
     * @inheritDoc
     * @param FractionAbstract $fractionAbstract
     * @return void
     */
    public function subtract(FractionAbstract $fractionAbstract): void
    {
        $this->setNumerator($this->getNumerator() * $fractionAbstract->getDenominator() - $this->getDenominator() * $fractionAbstract->getNumerator());
        $this->setDenominator($this->getDenominator() * $fractionAbstract->getDenominator());
        $this->reduction();
    }

    /**
     * @inheritDoc
     * @param FractionAbstract $fractionAbstract
     * @return void
     */
    public function divide(FractionAbstract $fractionAbstract): void
    {
        $this->setNumerator($this->getNumerator() * $fractionAbstract->getDenominator());
        $this->setDenominator($this->getDenominator() * $fractionAbstract->getDenominator());
        $this->reduction();
    }

    /**
     * @inheritDoc
     * @param FractionAbstract $fractionAbstract
     * @return void
     */
    public function multiply(FractionAbstract $fractionAbstract): void
    {
        $this->setNumerator($this->getNumerator() * $fractionAbstract->getNumerator());
        $this->setDenominator($this->getDenominator() * $fractionAbstract->getDenominator());
        $this->reduction();
    }

    /**
     * Get real value
     * @return float
     */
    public function getRealValue(): float
    {
        return round($this->getNumerator() / $this->getDenominator(), 2);
    }

    /**
     * Print object
     * @return string
     */
    public function __toString(): string
    {
        $numeratorPart = $this->isNegative() ? $this->getSign() . $this->getNumerator() : $this->getNumerator();

        if ($this->getDenominator() === 1) {
            return $numeratorPart;
        }

        return $numeratorPart . '/' . $this->getDenominator();
    }
}