<?php

namespace pbaczek\simplex\Equation\Sign;

/**
 * Class SignAbstract
 * @package pbaczek\simplex\Simplex\Sign
 */
abstract class SignAbstract
{
    /**
     * Return sign character
     * @return string
     */
    abstract public static function getSignCharacter(): string;
}