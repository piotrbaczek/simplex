<?php

namespace pbaczek\simplex\tests\Unit;

use pbaczek\simplex\Fraction;
use pbaczek\simplex\Sign;
use PHPUnit\Framework\TestCase;

/**
 * Class FractionTest
 * @package pbaczek\simplex\tests\Unit
 */
class FractionTest extends TestCase
{
    public function testBasic()
    {
        $fraction = new Fraction(1, 2);
        $this->assertEquals(1, $fraction->getNumerator());
        $this->assertEquals(2, $fraction->getDenominator());
        $this->assertEquals(Sign::NON_NEGATIVE, $fraction->getSign());
    }
}