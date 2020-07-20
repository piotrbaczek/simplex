<?php

namespace pbaczek\simplex\tests\Unit\Printer;

use pbaczek\simplex\Equation;
use pbaczek\simplex\Equation\Sign\LessOrEqual;
use pbaczek\simplex\EquationsCollection;
use pbaczek\simplex\Fraction;
use pbaczek\simplex\FractionsCollection;
use pbaczek\simplex\Simplex;
use pbaczek\simplex\Simplex\Solver\Number;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Class ConsolePrinterTest
 * @package pbaczek\simplex\tests\Unit\Printer
 */
class ConsolePrinterTest extends TestCase
{
    /** @var Simplex $simplex */
    private $simplex;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $equationsCollection = new EquationsCollection();

        $firstEquationVariables = new FractionsCollection();
        $firstEquationVariables->add(new Fraction(2));
        $firstEquationVariables->add(new Fraction(5));
        $firstEquation = new Equation($firstEquationVariables, new LessOrEqual(), new Fraction(30));

        $secondEquationVariables = new FractionsCollection();
        $secondEquationVariables->add(new Fraction(2));
        $secondEquationVariables->add(new Fraction(3));
        $secondEquation = new Equation($secondEquationVariables, new LessOrEqual(), new Fraction(26));

        $thirdEquationVariables = new FractionsCollection();
        $thirdEquationVariables->add(new Fraction(0));
        $thirdEquationVariables->add(new Fraction(3));
        $thirdEquation = new Equation($thirdEquationVariables, new LessOrEqual(), new Fraction(15));

        $equationsCollection->add($firstEquation);
        $equationsCollection->add($secondEquation);
        $equationsCollection->add($thirdEquation);
        $solver = new Number($equationsCollection, new FractionsCollection([new Fraction(2), new Fraction(6)]), true);

        $this->simplex = new Simplex($solver);
        $this->simplex->solve();
    }

    /**
     * Tests basic printing of solution using standard printer
     * @throws Simplex\Exceptions\Printer\InvalidPrinterException
     */
    public function testBasicPrinting(): void
    {
        $this->assertNotEmpty($this->simplex->print(Simplex\Printer\ConsolePrinter::class));
    }

    /**
     * Tests that printer must extend PrinterAbstract class
     * @throws Simplex\Exceptions\Printer\InvalidPrinterException
     */
    public function testAssertPrinterMustExtendPrinterAbstract(): void
    {
        $this->expectException(Simplex\Exceptions\Printer\InvalidPrinterException::class);
        $this->assertNotEmpty($this->simplex->print(stdClass::class));
    }
}