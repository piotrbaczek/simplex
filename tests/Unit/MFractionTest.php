<?php

namespace pbaczek\simplex\tests\Unit;

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
}