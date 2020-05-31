<?php

namespace pbaczek\simplex;

/**
 * Class MFraction
 * @package pbaczek\simplex
 */
class MFraction extends FractionAbstract
{
    /** @var int $mNumerator */
    private $mNumerator;

    /** @var int $mDenominator */
    private $mDenominator;

    /**
     * MFraction constructor.
     * @param int $numerator
     * @param int $denominator
     * @param int $mNumerator
     * @param int $mDenominator
     */
    public function __construct(int $numerator, int $denominator = 1, int $mNumerator = 0, int $mDenominator = 1)
    {
        $this->mNumerator = $mNumerator;
        $this->mDenominator = $mDenominator;

        $this->validateDenominator($mDenominator);
        parent::__construct($numerator, $denominator);
    }

    /**
     * Returns true when FractionAbstract equals Zero
     * @return bool
     */
    public function equalsZero(): bool
    {
        // TODO: Implement equalsZero() method.
    }

    /**
     * Return float value of FractionAbstract
     * @return float
     */
    public function getRealValue(): float
    {
        // TODO: Implement getRealValue() method.
    }

    /**
     * Return string of FractionAbstract
     * @return string
     */
    public function __toString(): string
    {
        // TODO: Implement __toString() method.
    }

    /**
     * Add
     * @param FractionAbstract $fractionAbstract
     */
    public function add(FractionAbstract $fractionAbstract): void
    {
        // TODO: Implement add() method.
    }

    /**
     * Subtract
     * @param FractionAbstract $fractionAbstract
     */
    public function subtract(FractionAbstract $fractionAbstract): void
    {
        // TODO: Implement subtract() method.
    }

    /**
     * Divide
     * @param FractionAbstract $fractionAbstract
     */
    public function divide(FractionAbstract $fractionAbstract): void
    {
        // TODO: Implement divide() method.
    }

    /**
     * Multiply
     * @param FractionAbstract $fractionAbstract
     */
    public function multiply(FractionAbstract $fractionAbstract): void
    {
        // TODO: Implement multiply() method.
    }

    /**
     * Perform reduction of parameters
     * @return void
     */
    protected function reduction(): void
    {
        // TODO: Implement reduction() method.
    }
}