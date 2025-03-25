<?php

namespace pbaczek\simplex\tests\Solver;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver;
use pbaczek\simplex\Solver\Equation;
use pbaczek\simplex\Solver\Exceptions\InvalidEngineException;
use pbaczek\simplex\Solver\Exceptions\ProblemInvalidException;
use PHPUnit\Framework\TestCase;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine;
use pbaczek\simplex\Solver\Dictionaries\Sign;

class ProblemTest extends TestCase
{
    /**
     * @throws ProblemInvalidException
     * @throws InvalidEngineException
     */
    public function testBasicThesisExample(): void
    {
        $problem = new Solver\Problem();

        $problem
            ->calculateMaximum()
            ->setObjectiveFunction(new Equation([new Fraction(2), new Fraction(6)]))
            ->addEquation(
                new Equation(
                    [
                        new Fraction(2),
                        new Fraction(5),
                    ]
                ),
                Sign::LEQ,
                new Fraction(30)
            )
            ->addEquation(
                new Equation(
                    [
                        new Fraction(2),
                        new Fraction(3),
                    ]
                ),
                Sign::LEQ,
                new Fraction(26)
            )
            ->addEquation(
                new Equation(
                    [
                        new Fraction(0),
                        new Fraction(3)
                    ]
                ),
                Sign::LEQ,
                new Fraction(15)
            );

        $solver = (new Solver())
            ->setEngine(new SimplexDefaultEngine())
            ->setProblem($problem);

        $this->assertEquals($problem, $solver->getProblem());
        $this->assertTrue($problem->isFunctionMaximized());

        $solution = $solver->solve();

        foreach ($solution->getSimplexTables()->getIterator() as $table) {
            echo $table;
        }

        $this->assertInstanceOf(Solver\Solution::class, $solution);

        $points = $solution->getPointCoordinates();

        foreach ($points as $pointIndex => $point) {
            echo sprintf('x%s = %s' . PHP_EOL, $pointIndex, $point->getValue());
        }

        $this->assertTrue($points->count() === 2);
        $this->assertEquals(new Fraction(5, 2), $points->offsetGet(0));
        $this->assertEquals(new Fraction(5), $points->offsetGet(1));
        // $this->assertEquals(35, $solution->getSolutionValue()->getValue());
    }
}