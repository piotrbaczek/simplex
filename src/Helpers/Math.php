<?php

namespace pbaczek\simplex\Helpers;

/**
 * Class Math
 * @package pbaczek\simplex\Helpers
 */
final class Math
{
    /**
     * Finds hcd (Highest Common Division) of two Integers
     * @static
     * @param Integer $a
     * @param Integer $b
     * @return Integer
     */
    public static function highestCommonDivisor(int $a, int $b): int
    {
        $a = abs($a);
        while ($a != $b) {
            if ($a > $b) {
                $a = $a - $b;
                continue;
            }

            $b = $b - $a;
        }
        return $a;
    }
}