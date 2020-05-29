<?php

namespace pbaczek\simplex;

use pbaczek\simplex\Exceptions\NegativeDenominatorException;
use pbaczek\simplex\Exceptions\ZeroDenominatorException;
use pbaczek\simplex\Fraction\FractionMathHelper;

/**
 * Class Fraction
 * @package pbaczek\simplex
 */
class Fraction
{
    use FractionMathHelper;

    /** @var int $numerator */
    private $numerator;

    /** @var int $denominator */
    private $denominator;

    /** @var string $sign */
    private $sign;

    /**
     * Fraction constructor.
     * @param int $numerator
     * @param int $denominator
     */
    public function __construct(int $numerator, int $denominator)
    {
        if ($denominator === 0) {
            throw new ZeroDenominatorException();
        }

        if ($denominator < 0) {
            throw new NegativeDenominatorException();
        }

        $this->sign = $numerator >= 0 ? Sign::NON_NEGATIVE : Sign::NEGATIVE;

        $this->numerator = $numerator;
        $this->denominator = abs($denominator);
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
        return $this->sign;
    }

    /**
     * @param int $numerator
     * @return $this
     */
    public function setNumerator(int $numerator): self
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
        $this->denominator = $denominator;
        return $this;
    }

    /**
     * @param string $sign
     * @return $this
     */
    public function setSign(string $sign): self
    {
        $this->sign = $sign;
        return $this;
    }

    /**
     * Change sign of fraction
     * @return void
     */
    public function changeSign(): void
    {
        if ($this->sign === Sign::NEGATIVE) {
            $this->sign = Sign::NON_NEGATIVE;
            return;
        }

        $this->sign = Sign::NEGATIVE;
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
     * Add Fraction
     * @param Fraction $number
     * @return void
     */
    public function add(Fraction $number): void
    {
        $this->numerator = $this->numerator * $number->getDenominator() + $this->denominator * $number->getNumerator();
        $this->denominator = $this->denominator * $number->getDenominator();
        $this->reduction();
    }

    /**
     * Subtract fraction
     * @param Fraction $number
     * @return void
     */
    public function subtract(Fraction $number): void
    {
        $this->numerator = $this->numerator * $number->getDenominator() - $this->denominator * $number->getNumerator();
        $this->denominator = $this->denominator * $number->getDenominator();
        $this->reduction();
    }

    /**
     * Multiple by fraction
     * @param Fraction $number
     * @return void
     */
    public function multiply(Fraction $number): void
    {
        $this->numerator *= $number->getNumerator();
        $this->denominator *= $number->getDenominator();
        $this->reduction();
    }

    /**
     * Divide by fraction
     * @param Fraction $number
     */
    public function divide(Fraction $number): void
    {
        $this->numerator *= $number->getDenominator();
        $this->denominator *= $number->getNumerator();
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
        return $this->getSign() . $this->getNumerator() . '/' . $this->getDenominator();
    }
}