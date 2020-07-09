<?php

namespace pbaczek\simplex\Equation\Sign;

use pbaczek\simplex\Equation\Sign\Dictionary\SignCharacter;

/**
 * Class LessOrEqual
 * @package pbaczek\simplex\Simplex\Sign
 */
final class LessOrEqual extends SignAbstract
{
    /**
     * Return sign
     * @return string
     */
    public static function getSignCharacter(): string
    {
        return SignCharacter::LESS_OR_EQUAL;
    }
}