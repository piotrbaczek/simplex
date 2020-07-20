<?php

namespace pbaczek\simplex;

use pbaczek\simplex\Fraction\Dictionaries\Sign;
use pbaczek\simplex\Fraction\Exceptions\NegativeDenominatorException;
use pbaczek\simplex\Fraction\Exceptions\ZeroDenominatorException;

/**
 * Class FractionAbstract
 * @package pbaczek\simplex
 */
abstract class FractionAbstract
{
    /** @var int $numerator */
    private $numerator;

    /** @var int $denominator */
    private $denominator;

    /**
     * FractionAbstract constructor.
     * @param int $numerator
     * @param int $denominator
     */
    public function __construct(int $numerator, int $denominator = 1)
    {
        $this->validateDenominator($denominator);

        $this->numerator = $numerator;
        $this->denominator = $denominator;
        $this->reduction();
    }

    /**
     * @return int
     */
    public function getNumerator(): int
    {
        return $this->numerator;
    }

    /**
     * @return int
     */
    public function getDenominator(): int
    {
        return $this->denominator;
    }

    /**
     * @return string
     */
    public function getSign(): string
    {
        return $this->numerator >= 0 ? Sign::NON_NEGATIVE : Sign::NEGATIVE;
    }

    /**
     * @param int $numerator
     * @return $this
     */
    public function setNumerator(int $numerator): self
    {
        $this->numerator = $numerator;
        $this->reduction();

        return $this;
    }

    /**
     * Set numerator without triggering reduction
     * @param int $numerator
     * @return $this
     */
    protected function setNumeratorWithoutReduction(int $numerator): self
    {
        $this->numerator = $numerator;
        return $this;
    }

    /**
     * @param int $denominator
     * @return $this
     */
    public function setDenominator(int $denominator): self
    {
        $this->validateDenominator($denominator);

        $this->denominator = $denominator;
        $this->reduction();

        return $this;
    }

    /**
     * Set denominator without triggering reduction
     * @param int $denominator
     * @return $this
     */
    protected function setDenominatorWithoutReduction(int $denominator)
    {
        $this->validateDenominator($denominator);
        $this->denominator = $denominator;
        return $this;
    }

    /**
     * Change sign of fraction
     * @return void
     */
    public function changeSign(): void
    {
        if ($this->equalsZero()) {
            return;
        }

        $this->numerator = -$this->numerator;
    }

    /**
     * Tests if Fraction is an Integer
     * @return boolean
     */
    public function isFraction()
    {
        return $this->getDenominator() != 1;
    }

    /**
     * Returns true if fraction is negative
     * @return bool
     */
    public function isNegative(): bool
    {
        return $this->numerator < 0;
    }

    /**
     * Returns true if fraction is not negative
     * @return bool
     */
    public function isNonNegative(): bool
    {
        return $this->numerator >= 0;
    }

    /**
     * Validate denominator
     * @param int $denominator
     */
    protected function validateDenominator(int $denominator): void
    {
        if ($denominator === 0) {
            throw new ZeroDenominatorException($denominator);
        }

        if ($denominator < 0) {
            throw new NegativeDenominatorException($denominator);
        }
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
        $numeratorPart = $this->getNumerator();

        if ($this->getDenominator() === 1) {
            return $numeratorPart;
        }

        return $numeratorPart . '/' . $this->getDenominator();
    }

    /**
     * Returns true when FractionAbstract equals Zero
     * @return bool
     */
    abstract public function equalsZero(): bool;

    /**
     * Add
     * @param FractionAbstract $fractionAbstract
     */
    abstract public function add($fractionAbstract): void;

    /**
     * Subtract
     * @param FractionAbstract $fractionAbstract
     */
    abstract public function subtract($fractionAbstract): void;

    /**
     * Divide
     * @param FractionAbstract $fractionAbstract
     */
    abstract public function divide($fractionAbstract): void;

    /**
     * Multiply
     * @param FractionAbstract $fractionAbstract
     */
    abstract public function multiply($fractionAbstract): void;

    /**
     * Perform reduction of parameters
     * @return void
     */
    abstract protected function reduction(): void;
}