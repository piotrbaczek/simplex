<?php

namespace pbaczek\simplex;

/**
 * Class MFraction
 * @package pbaczek\simplex
 */
class MFraction extends Fraction
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
    public function __construct(int $numerator, int $denominator, int $mNumerator, int $mDenominator)
    {
        parent::__construct($numerator, $denominator);
        $this->mNumerator = $mNumerator;
        $this->mDenominator = $mDenominator;
    }
}