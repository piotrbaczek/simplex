<?php

namespace pbaczek\simplex\Tests\Unit;

use pbaczek\simplex\Fraction;
use pbaczek\simplex\Sign;
use PHPUnit\Framework\TestCase;

/**
 * Class FractionTest
 * @package pbaczek\simplex\Tests\Unit
 */
class FractionTest extends TestCase
{
    /**
     * Test basic functionality
     * @return void
     */
    public function testBasic(): void
    {
        $fraction = new Fraction(1, 2);
        $this->assertEquals(1, $fraction->getNumerator());
        $this->assertEquals(2, $fraction->getDenominator());
        $this->assertEquals(Sign::NON_NEGATIVE, $fraction->getSign());
        $this->assertEquals(0.5, $fraction->getRealValue());
        $this->assertTrue($fraction->isFraction());
    }

    /**
     * Test sign of a fraction changing
     */
    public function testChangingSign(): void
    {
        $fraction = new Fraction(1, 2);
        $fraction->changeSign();
        $this->assertEquals(Sign::NEGATIVE, $fraction->getSign());
        $fraction->changeSign();
        $this->assertEquals(Sign::NON_NEGATIVE, $fraction->getSign());
    }
}