<?php

namespace pbaczek\simplex\tests\Unit;

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
        $this->mFraction = new MFraction(1, 2, 1, 2);
    }
}