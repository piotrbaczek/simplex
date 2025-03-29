<?php

namespace pbaczek\simplex\tests\Solver\Engines\SimplexDefaultEngine;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;
use pbaczek\simplex\Solver\Equation;
use PHPUnit\Framework\TestCase;

class SimplexTableTest extends TestCase
{
    public function testCloning()
    {
        $simplexTable = new SimplexTable();
        $simplexTable->setResourcesAtPoint(new Equation([new Fraction(1)]));
        $simplexTable->setObjectiveFunctionAtPoint(new Equation([new Fraction(2)]));

        $this->assertTrue($simplexTable->getResourcesAtPoint()->offsetGet(0)->equals(new Fraction(1)));
        $this->assertTrue($simplexTable->getObjectiveFunctionAtPoint()->offsetGet(0)->equals(new Fraction(2)));

        $cloned = clone $simplexTable;
        $this->assertTrue($cloned->getResourcesAtPoint()->offsetGet(0)->equals(new Fraction(0)));
        $this->assertTrue($cloned->getObjectiveFunctionAtPoint()->offsetGet(0)->equals(new Fraction(0)));
    }
}