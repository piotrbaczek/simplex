<?php

namespace pbaczek\simplex\Equation\Sign;

use pbaczek\simplex\Equation\Sign\Dictionary\SignCharacter;

/**
 * Class Equal
 * @package pbaczek\simplex\Simplex\Sign
 */
class Equal extends SignAbstract
{
    /**
     * Return sign
     * @return string
     */
    public static function getSignCharacter(): string
    {
        return SignCharacter::EQUAL;
    }
}