<?php

namespace pbaczek\simplex\tests\Unit;

use InvalidArgumentException;
use pbaczek\simplex\Fraction;
use pbaczek\simplex\Fraction\Dictionaries\Sign;
use pbaczek\simplex\Fraction\Exceptions\NegativeDenominatorException;
use pbaczek\simplex\Fraction\Exceptions\ZeroDenominatorException;
use pbaczek\simplex\MFraction;
use PHPUnit\Framework\TestCase;

/**
 * Class MFractionTest
 * @package pbaczek\simplex\tests\Unit
 */
class MFractionTest extends TestCase
{
    /** @var MFraction $mFraction */
    private $mFraction;

    /**
     * @inheritDoc
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->mFraction = new MFraction(1, 2, 3, 5);
    }

    /**
     * Basic functionality test
     * @return void
     */
    public function testBasic(): void
    {
        $this->assertEquals(1, $this->mFraction->getNumerator());
        $this->assertEquals(2, $this->mFraction->getDenominator());
        $this->assertEquals(3, $this->mFraction->getMNumerator());
        $this->assertEquals(5, $this->mFraction->getMDenominator());
        $this->assertEquals(Sign::NON_NEGATIVE, $this->mFraction->getSign());
        $this->assertEquals(PHP_INT_MAX, $this->mFraction->getRealValue());
        $this->assertEquals('1/2+3/5M', $this->mFraction->__toString());
        $this->assertTrue($this->mFraction->isFraction());
    }

    /**
     * Test sign of a fraction changing
     * @return void
     */
    public function testChangingSign(): void
    {
        $this->mFraction->changeSign();
        $this->assertEquals(Sign::NEGATIVE, $this->mFraction->getSign());
        $this->mFraction->changeSign();
        $this->assertEquals(Sign::NON_NEGATIVE, $this->mFraction->getSign());

        $zero = new MFraction(0);
        $zero->changeSign();
        $this->assertEquals(Sign::NON_NEGATIVE, $zero->getSign());
    }

    /**
     * Test that both real part and M part are being reduced
     * @return void
     */
    public function testMFractionIsBeingReduced(): void
    {
        $mFraction = new MFraction(5, 10, 20, 30);
        $this->assertEquals(1, $mFraction->getNumerator());
        $this->assertEquals(2, $mFraction->getDenominator());
        $this->assertEquals(2, $mFraction->getMNumerator());
        $this->assertEquals(3, $mFraction->getMDenominator());
    }

    /**
     * Test after setting numerator, the MFraction is being reduced
     * @return void
     */
    public function testSettingMNumerator(): void
    {
        $this->mFraction->setMNumerator(15);
        $this->assertEquals('1/2+3M', $this->mFraction->__toString());

        $this->mFraction->setMNumerator(-15);
        $this->assertEquals('1/2-15M', $this->mFraction->__toString());
    }

    /**
     * Test after setting denominator, the MFraction is being reduced
     * @return void
     */
    public function testSettingMDenominator(): void
    {
        $this->mFraction->setMDenominator(6);
        $this->assertEquals('1/2+1/2M', $this->mFraction->__toString());
    }

    /**
     * Test that attempt to set MDenominator as 0 throws an Exception
     * @return void
     */
    public function testSettingZeroMDenominator(): void
    {
        $this->expectException(ZeroDenominatorException::class);
        $this->expectExceptionMessage('0');

        $this->mFraction->setMDenominator(0);
    }

    /**
     * Test that attempt to set negative MDenominator throws an Exception
     * @return void
     */
    public function testSettingNegativeMDenominator(): void
    {
        $this->expectException(NegativeDenominatorException::class);
        $this->expectExceptionMessage('-4');

        $this->mFraction->setMDenominator(-12);
    }

    /**
     * Tests adding two MFractions together works
     * @return void
     */
    public function testAddingTwoMFractions(): void
    {
        $first = clone $this->mFraction;
        $second = new MFraction(2, 4, -4, 5);
        $first->add($second);
        $this->assertEquals('1-1/5M', $first->__toString());
    }

    /**
     * Tests subtracting two MFractions works
     * @return void
     */
    public function testSubtractingTwoMFractions(): void
    {
        $first = clone $this->mFraction;
        $second = new MFraction(1, 2, 6, 5);
        $first->subtract($second);
        $this->assertEquals('0-3/5M', $first->__toString());
    }

    /**
     * Test multiplying two MFractions works
     * @return void
     */
    public function testMultiplyingTwoMFractions(): void
    {
        $first = clone $this->mFraction;
        $second = new MFraction(2, 1, -5, 3);
        $first->multiply($second);
        $this->assertEquals('1-1M', $first->__toString());
    }

    /**
     * Tests dividing two MFractions works
     * @return void
     */
    public function testDividingTwoMFractions(): void
    {
        $first = clone $this->mFraction;
        $second = new MFraction(3, 4, 9, 15);
        $first->divide($second);
        $this->assertEquals('2/3+1M', $first->__toString());
    }

    /**
     * Tests that real value is returned -> PHP_INT_MAX and PHP_INT_MIN when mNumerator present
     */
    public function testReturningRealValue(): void
    {
        $this->mFraction->setMNumerator(0);
        $this->assertEquals(0.5, $this->mFraction->getRealValue());

        $this->mFraction->setMNumerator(1);
        $this->assertEquals(PHP_INT_MAX, $this->mFraction->getRealValue());

        $this->mFraction->setMNumerator(-1);
        $this->assertEquals(PHP_INT_MIN, $this->mFraction->getRealValue());
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

        $this->mFraction->{$function}(new Fraction(2));
    }
}