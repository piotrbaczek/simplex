<?php

namespace pbaczek\simplex\Simplex\Sign;

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
    public static function getSign(): string
    {
        return '=';
    }
}