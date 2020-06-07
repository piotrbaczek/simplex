<?php

namespace pbaczek\simplex;

use InvalidArgumentException;
use pbaczek\simplex\Fraction\Dictionaries\Sign;
use pbaczek\simplex\Fraction\MFractionMathHelper;
use pbaczek\simplex\Fraction\RealPartDivider;

/**
 * Class MFraction
 * @package pbaczek\simplex
 */
class MFraction extends FractionAbstract
{
    use MFractionMathHelper, RealPartDivider;

    private const M_SIGN = 'M';

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
        $this->reduction();
        return $this;
    }

    /**
     * @param int $mDenominator
     * @return $this
     */
    public function setMDenominator(int $mDenominator): self
    {
        $this->mDenominator = $mDenominator;
        $this->reduction();
        return $this;
    }

    /**
     * Set M numerator without triggering reduction
     * @param int $mNumerator
     * @return $this
     */
    protected function setMNumeratorWithoutReduction(int $mNumerator): self
    {
        $this->mNumerator = $mNumerator;
        return $this;
    }

    /**
     * Set M denominator without triggering reduction
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
     * @inheritDoc
     * @return void
     */
    public function changeSign(): void
    {
        parent::changeSign();
        if ($this->mNumerator === 0) {
            return;
        }

        $this->mNumerator = -$this->mNumerator;
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
            return $this->mNumerator >= 0 ? PHP_INT_MAX : PHP_INT_MIN;
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

        if ($this->mDenominator === 1) {
            return $realPart . ($this->mNumerator >= 0 ? Sign::NON_NEGATIVE : '') . $this->mNumerator . self::M_SIGN;
        }

        return $realPart . ($this->mNumerator >= 0 ? Sign::NON_NEGATIVE : '') . $this->mNumerator . '/' . $this->mDenominator . self::M_SIGN;
    }

    /**
     * Add
     * @param MFraction $fractionAbstract
     */
    public function add($fractionAbstract): void
    {
        if ($fractionAbstract instanceof self === false) {
            throw new InvalidArgumentException('Only same class allowed');
        }

        $this->setNumeratorWithoutReduction($this->getNumerator() * $fractionAbstract->getDenominator() + $this->getDenominator() * $fractionAbstract->getNumerator());
        $this->setDenominatorWithoutReduction($this->getDenominator() * $fractionAbstract->getDenominator());

        $this->setMNumeratorWithoutReduction($this->getMNumerator() * $fractionAbstract->getMDenominator() + $this->getMDenominator() * $fractionAbstract->getMNumerator());
        $this->setMDenominatorWithoutReduction($this->getMDenominator() * $fractionAbstract->getMDenominator());

        $this->reduction();
    }

    /**
     * Subtract
     * @param MFraction $fractionAbstract
     */
    public function subtract($fractionAbstract): void
    {
        if ($fractionAbstract instanceof self === false) {
            throw new InvalidArgumentException('Only same class allowed');
        }

        $this->setNumeratorWithoutReduction($this->getNumerator() * $fractionAbstract->getDenominator() - $this->getDenominator() * $fractionAbstract->getNumerator());
        $this->setDenominatorWithoutReduction($this->getDenominator() * $fractionAbstract->getDenominator());

        $this->setMNumeratorWithoutReduction($this->getMNumerator() * $fractionAbstract->getMDenominator() - $this->getMDenominator() * $fractionAbstract->getMNumerator());
        $this->setMDenominatorWithoutReduction($this->getMDenominator() * $fractionAbstract->getMDenominator());

        $this->reduction();
    }

    /**
     * Divide
     * @param MFraction $fractionAbstract
     */
    public function divide($fractionAbstract): void
    {
        if ($fractionAbstract instanceof self === false) {
            throw new InvalidArgumentException('Only same class allowed');
        }

        $this->divideRealPart($fractionAbstract);
        $this->divideMPart($fractionAbstract);

        $this->reduction();
    }

    /**
     * Divide M Part of MFraction
     * @param MFraction $fractionAbstract
     */
    private function divideMPart(MFraction $fractionAbstract): void
    {
        $newMNumerator = $this->getMNumerator() * $fractionAbstract->getMDenominator();
        if ($newMNumerator < 0) {
            $this->changeSign();
        }

        $this->setMNumeratorWithoutReduction(abs($newMNumerator));

        $newMDenominator = $this->getMDenominator() * $fractionAbstract->getMNumerator();

        if ($newMDenominator < 0) {
            $this->changeSign();
        }

        $this->setMDenominatorWithoutReduction(abs($newMDenominator));
    }

    /**
     * Multiply
     * @param MFraction $fractionAbstract
     */
    public function multiply($fractionAbstract): void
    {
        if ($fractionAbstract instanceof self === false) {
            throw new InvalidArgumentException('Only same class allowed');
        }

        $this->setNumeratorWithoutReduction($this->getNumerator() * $fractionAbstract->getNumerator());
        $this->setDenominatorWithoutReduction($this->getDenominator() * $fractionAbstract->getDenominator());

        $this->setMNumeratorWithoutReduction($this->getMNumerator() * $fractionAbstract->getMNumerator());
        $this->setMDenominatorWithoutReduction($this->getMDenominator() * $fractionAbstract->getMDenominator());

        $this->reduction();
    }
}