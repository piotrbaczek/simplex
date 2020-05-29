<?php

namespace pbaczek\simplex\Simplex\Sign;

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
    public static function getSign(): string
    {
        return '<=';
    }
}