<?php

namespace pbaczek\simplex\Tests\Unit;

use pbaczek\simplex\Fraction;
use pbaczek\simplex\Fraction\Dictionaries\Sign;
use pbaczek\simplex\Fraction\Exceptions\NegativeDenominatorException;
use pbaczek\simplex\Fraction\Exceptions\UnknownSign;
use pbaczek\simplex\Fraction\Exceptions\ZeroDenominatorException;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * Class FractionTest
 * @package pbaczek\simplex\Tests\Unit
 */
class FractionTest extends TestCase
{
    /** @var Fraction $fraction */
    private $fraction;

    /**
     * @inheritDoc
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->fraction = new Fraction(1, 2);
    }

    /**
     * Test basic functionality
     * @return void
     */
    public function testBasic(): void
    {
        $this->assertEquals(1, $this->fraction->getNumerator());
        $this->assertEquals(2, $this->fraction->getDenominator());
        $this->assertEquals(Sign::NON_NEGATIVE, $this->fraction->getSign());
        $this->assertEquals(0.5, $this->fraction->getRealValue());
        $this->assertTrue($this->fraction->isFraction());
    }

    /**
     * Test sign of a fraction changing
     * @return void
     */
    public function testChangingSign(): void
    {
        $this->fraction->changeSign();
        $this->assertEquals(Sign::NEGATIVE, $this->fraction->getSign());
        $this->fraction->changeSign();
        $this->assertEquals(Sign::NON_NEGATIVE, $this->fraction->getSign());
    }

    /**
     * Test that reduction of numerator, denominator is occuring
     * @return void
     */
    public function testFractionIsBeingReduced(): void
    {
        $fraction = new Fraction(5, 10);
        $this->assertEquals(1, $fraction->getNumerator());
        $this->assertEquals(2, $fraction->getDenominator());
    }

    /**
     * Setting invalid sign
     * @throws UnknownSign
     * @throws ReflectionException
     */
    public function testSettingInvalidSign(): void
    {
        $this->expectException(UnknownSign::class);
        $this->expectExceptionMessage('ANC');

        $this->fraction->setSign(Sign::NEGATIVE);
        $this->fraction->setSign('ANC');
    }

    /**
     * Test setting numerator runs reduction
     * @return void
     */
    public function testSettingNumerator(): void
    {
        $this->fraction->setNumerator(12);
        $this->assertEquals('6', $this->fraction->__toString());

        $this->fraction->setNumerator(0);
        $this->assertEquals('0', $this->fraction->__toString());

        $this->fraction = new Fraction(1, 2);
        $this->fraction->setNumerator(-12);
        $this->assertEquals('-6', $this->fraction->__toString());
    }

    /**
     * Test setting denominator only accepts positive integers
     * @return void
     */
    public function testSettingPositiveDenominator(): void
    {
        $this->fraction->setDenominator(6);
        $this->assertEquals('1/6', $this->fraction->__toString());
    }

    /**
     * Test setting denominator as Zero throws exception
     * @return void
     */
    public function testSettingZeroDenominator(): void
    {
        $this->expectException(ZeroDenominatorException::class);
        $this->expectExceptionMessage('0');

        $this->fraction->setDenominator(0);
    }

    /**
     * Test that setting negative denominator throws NegativeDenominatorException
     */
    public function testSettingNegativeDenominator(): void
    {
        $this->expectException(NegativeDenominatorException::class);
        $this->expectExceptionMessage('-12');

        $this->fraction->setDenominator(-12);
    }
}