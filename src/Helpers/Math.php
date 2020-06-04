<?php

namespace pbaczek\simplex\Helpers;

/**
 * Class Math
 * @package pbaczek\simplex\Helpers
 */
final class Math
{
    /**
     * Finds GCD (Greatest Common Divisor) of two positive Integers
     * @static
     * @param Integer $a
     * @param Integer $b
     * @return Integer
     */
    public static function greatestCommonDivisor(int $a, int $b): int
    {
        return gmp_intval(gmp_gcd($a, $b));
    }
}