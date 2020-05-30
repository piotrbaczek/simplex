<?php

namespace pbaczek\simplex\Tests\Unit;

use pbaczek\simplex\MFraction;
use PHPUnit\Framework\TestCase;

/**
 * Class MFractionTest
 * @package pbaczek\simplex\Tests\Unit
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