<?php

namespace pbaczek\simplex\Equation\Sign;

use pbaczek\simplex\Equation\Sign\Dictionary\SignCharacter;

/**
 * Class GreaterOrEqual
 * @package pbaczek\simplex\Simplex\Sign
 */
final class GreaterOrEqual extends SignAbstract
{
    /**
     * Return sign
     * @return string
     */
    public static function getSignCharacter(): string
    {
        return SignCharacter::GREATER_OR_EQUAL;
    }
}