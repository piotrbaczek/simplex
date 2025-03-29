<?php

namespace pbaczek\simplex\tests\Solver;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver;
use pbaczek\simplex\Solver\Equation;
use pbaczek\simplex\Solver\Exceptions\InvalidEngineException;
use pbaczek\simplex\Solver\Exceptions\ProblemInvalidException;
use pbaczek\simplex\Solver\Solution;
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

        /** @var SimplexDefaultEngine\SimplexTable $table */
        foreach ($solution->getSimplexTables() as $table) {
            echo $table;

            /** @var SimplexDefaultEngine\SimplexTable\PivotHistory $pivotHistory */
            foreach ($table->getPivotHistory() as $pivotHistory) {
                echo sprintf(
                    'Pivot element [%s,%s] on ratio %s on value %s' . PHP_EOL,
                    $pivotHistory->getRowSearchResult()->getRowIndex(),
                    $pivotHistory->getColumnSearchResult()->getColumnIndex(),
                    $pivotHistory->getRowSearchResult()->getRatio(),
                    $pivotHistory->getColumnSearchResult()->getValue()
                );
            }
        }

        $this->assertInstanceOf(Solution::class, $solution);

        $points = $solution->getPointCoordinates();

        foreach ($points as $pointIndex => $point) {
            echo sprintf('x%s = %s' . PHP_EOL, $pointIndex, $point->getValue());
        }

        echo sprintf('Value = %s' . PHP_EOL, $solution->getSolutionValue());

        $this->assertTrue($points->count() === 2);
        $this->assertEquals(new Fraction(5, 2), $points->offsetGet(0));
        $this->assertEquals(new Fraction(5), $points->offsetGet(1));
        $this->assertEquals(35, $solution->getSolutionValue()->getValue());
    }

//    /**
//     * @throws ProblemInvalidException
//     */
//    public function testThreeDimensionalProblem(): void
//    {
//        $problem = new Solver\Problem();
//
//        $problem->calculateMaximum()
//            ->setObjectiveFunction(new Equation(
//                    [
//                        new Fraction(3),
//                        new Fraction(4),
//                        new Fraction(2)
//                    ]
//                )
//            )
//            ->addEquation(
//                new Equation([new Fraction(1), new Fraction(2), new Fraction(3)]),
//                Sign::LEQ,
//                new Fraction(20)
//            )
//            ->addEquation(
//                new Equation([new Fraction(1), new Fraction(1), new Fraction(1)]),
//                Sign::LEQ,
//                new Fraction(15)
//            )
//            ->addEquation(
//                new Equation([new Fraction(3), new Fraction(2), new Fraction(1)]),
//                Sign::LEQ,
//                new Fraction(15)
//            );
//
//        $solver = (new Solver())
//            ->setEngine(new SimplexDefaultEngine())
//            ->setProblem($problem);
//
//        $solution = $solver->solve();
//
//        foreach ($solution->getSimplexTables()->getIterator() as $table) {
//            echo $table;
//        }
//
//        $this->assertInstanceOf(Solution::class, $solution);
//
//        $points = $solution->getPointCoordinates();
//
//        foreach ($points as $pointIndex => $point) {
//            echo sprintf('x%s = %s' . PHP_EOL, $pointIndex, $point->getValue());
//        }
//
//        echo sprintf('Value = %s' . PHP_EOL, $solution->getSolutionValue());
//
//        $this->assertTrue($points->count() === 3);
//        $this->assertEquals(new Fraction(0), $points->offsetGet(0));
//        $this->assertEquals(new Fraction(15,2), $points->offsetGet(1));
//        $this->assertEquals(new Fraction(0), $points->offsetGet(2));
//    }
}