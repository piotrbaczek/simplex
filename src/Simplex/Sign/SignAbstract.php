<?php

namespace pbaczek\simplex\Simplex\Sign;

/**
 * Class SignAbstract
 * @package pbaczek\simplex\Simplex\Sign
 */
abstract class SignAbstract
{
    /**
     * Return sign
     * @return string
     */
    abstract public static function getSign(): string;
}