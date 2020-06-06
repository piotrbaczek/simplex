<?php

namespace pbaczek\simplex\tests\Unit;

use pbaczek\simplex\Fraction\Dictionaries\Sign;
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

    public function testBasic(): void
    {
        $this->assertEquals(1, $this->mFraction->getNumerator());
        $this->assertEquals(2, $this->mFraction->getDenominator());
        $this->assertEquals(3, $this->mFraction->getMNumerator());
        $this->assertEquals(5, $this->mFraction->getMDenominator());
        $this->assertEquals(Sign::NON_NEGATIVE, $this->mFraction->getSign());
        $this->assertEquals(PHP_INT_MAX, $this->mFraction->getRealValue());
        $this->assertEquals('1/2+3/5M', $this->mFraction->__toString());
        $this->mFraction->changeSign();
        $this->assertEquals('-1/2-3/5M', $this->mFraction->__toString());
        $this->assertTrue($this->mFraction->isFraction());
    }
}