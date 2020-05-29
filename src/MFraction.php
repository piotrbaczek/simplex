<?php

namespace pbaczek\simplex;

/**
 * Class MFraction
 * @package pbaczek\simplex
 */
class MFraction extends FractionAbstract
{
    public function __construct(int $numerator, int $denominator)
    {
        parent::__construct($numerator, $denominator);
    }
}