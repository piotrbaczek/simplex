<?php

namespace pbaczek\simplex;

use pbaczek\simplex\Fraction\Dictionaries\Sign;
use pbaczek\simplex\Fraction\MFractionMathHelper;

/**
 * Class MFraction
 * @package pbaczek\simplex
 */
class MFraction extends FractionAbstract
{
    use MFractionMathHelper;

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
        $this->validateDenominator($mDenominator);

        $this->mNumerator = $mNumerator;
        $this->mDenominator = $mDenominator;

        parent::__construct($numerator, $denominator);
    }

    /**
     * @param int $mNumerator
     * @return $this
     */
    public function setMNumerator(int $mNumerator): self
    {
        $this->mNumerator = $mNumerator;
        return $this;
    }

    /**
     * @param int $mDenominator
     * @return $this
     */
    public function setMDenominator(int $mDenominator): self
    {
        $this->mDenominator = $mDenominator;
        return $this;
    }

    /**
     * Set numerator without triggering reduction
     * @param int $mNumerator
     * @return $this
     */
    protected function setMNumeratorWithoutReduction(int $mNumerator): self
    {
        $this->mNumerator = $mNumerator;
        return $this;
    }

    /**
     * Set denominator without triggering reduction
     * @param int $mDenominator
     * @return $this
     */
    protected function setMDenominatorWithoutReduction(int $mDenominator): self
    {
        $this->validateDenominator($mDenominator);
        $this->mDenominator = $mDenominator;
        return $this;
    }

    /**
     * @return int
     */
    public function getMNumerator(): int
    {
        return $this->mNumerator;
    }

    /**
     * @return int
     */
    public function getMDenominator(): int
    {
        return $this->mDenominator;
    }

    /**
     * Returns true when FractionAbstract equals Zero
     * @return bool
     */
    public function equalsZero(): bool
    {
        return $this->getNumerator() === 0 && $this->mNumerator === 0;
    }

    /**
     * Return float value of FractionAbstract
     * @return float
     */
    public function getRealValue(): float
    {
        if ($this->mNumerator !== 0) {
            return $this->getSign() === Sign::NON_NEGATIVE ? PHP_INT_MAX : PHP_INT_MIN;
        }

        return parent::getRealValue();
    }

    /**
     * Return string of FractionAbstract
     * @return string
     */
    public function __toString(): string
    {
        $realPart = parent::__toString();

        if ($this->mNumerator === 0) {
            return $realPart;
        }

        return $realPart . ($this->getSign() === Sign::NON_NEGATIVE ? '+' : '-') . $this->mNumerator . '/' . $this->mDenominator;
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
}