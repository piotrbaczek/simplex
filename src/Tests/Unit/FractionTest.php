<?php

namespace pbaczek\simplex\Tests\Unit;

use InvalidArgumentException;
use pbaczek\simplex\Fraction;
use pbaczek\simplex\Fraction\Dictionaries\Sign;
use pbaczek\simplex\Fraction\Exceptions\NegativeDenominatorException;
use pbaczek\simplex\Fraction\Exceptions\UnknownSign;
use pbaczek\simplex\Fraction\Exceptions\ZeroDenominatorException;
use pbaczek\simplex\MFraction;
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
        $this->expectExceptionMessage('SomeOtherSign');

        $this->fraction->setSign(Sign::NEGATIVE);
        $this->fraction->setSign('SomeOtherSign');
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
     * @return void
     */
    public function testSettingNegativeDenominator(): void
    {
        $this->expectException(NegativeDenominatorException::class);
        $this->expectExceptionMessage('-12');

        $this->fraction->setDenominator(-12);
    }

    /**
     * Test adding fractions - fractions, whole number and negative number
     * @return void
     */
    public function testAddingTwoFractions(): void
    {
        $first = clone $this->fraction;
        $second = new Fraction(1, 3);
        $first->add($second);
        $this->assertEquals('5/6', $first->__toString());

        $first->add(new Fraction(1));
        $this->assertEquals('11/6', $first->__toString());

        $first->add(new Fraction(-1));
        $this->assertEquals('5/6', $first->__toString());

        $first->add(new Fraction(-11, 6));
        $this->assertEquals('-1', $first->__toString());
    }

    /**
     * Defined functions for
     * @return array
     */
    public function definedFunctions(): array
    {
        return [
            [
                'add'
            ],
            [
                'subtract'
            ],
            [
                'multiply'
            ],
            [
                'divide'
            ]
        ];
    }

    /**
     * Test that only same type objects can be added
     * @dataProvider definedFunctions
     * @param string $function
     * @return void
     */
    public function testAllMathFunctionsWorkOnlyOnSameObject(string $function): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only same class allowed');

        $this->fraction->{$function}(new MFraction(2));
    }
}