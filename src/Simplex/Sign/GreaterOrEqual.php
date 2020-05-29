<?php

namespace pbaczek\simplex\Simplex\Sign;

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
    public static function getSign(): string
    {
        return '>=';
    }
}