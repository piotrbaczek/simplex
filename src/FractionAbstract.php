<?php

namespace pbaczek\simplex;

use pbaczek\simplex\Fraction\Dictionaries\Sign;
use pbaczek\simplex\Fraction\Exceptions\NegativeDenominatorException;
use pbaczek\simplex\Fraction\Exceptions\UnknownSign;
use pbaczek\simplex\Fraction\Exceptions\ZeroDenominatorException;
use ReflectionClass;
use ReflectionException;

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

    /** @var string $sign */
    private $sign;

    /**
     * FractionAbstract constructor.
     * @param int $numerator
     * @param int $denominator
     */
    public function __construct(int $numerator, int $denominator = 1)
    {
        $this->validateDenominator($denominator);

        $this->sign = $numerator >= 0 ? Sign::NON_NEGATIVE : Sign::NEGATIVE;

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
        return $this->sign;
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
     * @return FractionAbstract
     */
    protected function setNumeratorWithoutReduction(int $numerator): self
    {
        $this->numerator = $numerator;
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
     * @param string $sign
     * @return $this
     * @throws ReflectionException
     * @throws UnknownSign
     */
    public function setSign(string $sign): self
    {
        $signReflectionClass = new ReflectionClass(new Sign());

        if (in_array($sign, $signReflectionClass->getConstants()) === false) {
            throw new UnknownSign($sign);
        }

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
     * Returns true if fraction is negative
     * @return bool
     */
    public function isNegative(): bool
    {
        return $this->getSign() === Sign::NEGATIVE;
    }

    /**
     * Returns true if fraction is not negative
     * @return bool
     */
    public function isNonNegative(): bool
    {
        return $this->getSign() === Sign::NON_NEGATIVE;
    }

    /**
     * Validate denominator
     * @param int $denominator
     */
    private function validateDenominator(int $denominator): void
    {
        if ($denominator === 0) {
            throw new ZeroDenominatorException($denominator);
        }

        if ($denominator < 0) {
            throw new NegativeDenominatorException($denominator);
        }
    }

    /**
     * Returns true when FractionAbstract equals Zero
     * @return bool
     */
    abstract function equalsZero(): bool;

    /**
     * Return float value of FractionAbstract
     * @return float
     */
    abstract public function getRealValue(): float;

    /**
     * Return string of FractionAbstract
     * @return string
     */
    abstract public function __toString(): string;

    /**
     * Add
     * @param FractionAbstract $fractionAbstract
     */
    abstract public function add(FractionAbstract $fractionAbstract): void;

    /**
     * Subtract
     * @param FractionAbstract $fractionAbstract
     */
    abstract public function subtract(FractionAbstract $fractionAbstract): void;

    /**
     * Divide
     * @param FractionAbstract $fractionAbstract
     */
    abstract public function divide(FractionAbstract $fractionAbstract): void;

    /**
     * Multiply
     * @param FractionAbstract $fractionAbstract
     */
    abstract public function multiply(FractionAbstract $fractionAbstract): void;

    /**
     * Perform reduction of parameters
     * @return void
     */
    abstract protected function reduction(): void;
}