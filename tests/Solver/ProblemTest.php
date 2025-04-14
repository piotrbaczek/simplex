<?php

namespace pbaczek\simplex\tests\Solver;

use pbaczek\fraction\MFraction;
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
            ->setObjectiveFunction(new Equation([new MFraction(2), new MFraction(6)]))
            ->addEquation(
                new Equation(
                    [
                        new MFraction(2),
                        new MFraction(5),
                    ]
                ),
                Sign::LEQ,
                new MFraction(30)
            )
            ->addEquation(
                new Equation(
                    [
                        new MFraction(2),
                        new MFraction(3),
                    ]
                ),
                Sign::LEQ,
                new MFraction(26)
            )
            ->addEquation(
                new Equation(
                    [
                        new MFraction(0),
                        new MFraction(3)
                    ]
                ),
                Sign::LEQ,
                new MFraction(15)
            );

        $solver = (new Solver())
            ->setEngine(new SimplexDefaultEngine())
            ->setProblem($problem);

        $this->assertEquals($problem, $solver->getProblem());
        $this->assertTrue($problem->isFunctionMaximized());

        $solution = $solver->solve();

        /** @var SimplexDefaultEngine\SimplexTable $table */
        foreach ($solution->getSimplexTables() as $tableIndex => $table) {
            echo $table;

            $pivotHistory = $table->getPivotHistory()->offsetGet($tableIndex - 1);

            if (is_null($pivotHistory) === false) {
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
            echo sprintf('x%s = %s' . PHP_EOL, $pointIndex + 1, $point->getValue());
        }

        echo sprintf('Value = %s' . PHP_EOL, $solution->getSolutionValue());

        $this->assertTrue($points->count() === 2);
        $this->assertEquals(new MFraction(5, 2), $points->offsetGet(0));
        $this->assertEquals(new MFraction(5), $points->offsetGet(1));
        $this->assertEquals(35, $solution->getSolutionValue()->getValue());
    }

    /**
     * @throws ProblemInvalidException
     */
    public function testThreeDimensionalProblem(): void
    {
        $problem = new Solver\Problem();

        $problem->calculateMaximum()
            ->setObjectiveFunction(new Equation(
                    [
                        new MFraction(3),
                        new MFraction(4),
                        new MFraction(2)
                    ]
                )
            )
            ->addEquation(
                new Equation([new MFraction(1), new MFraction(2), new MFraction(3)]),
                Sign::LEQ,
                new MFraction(20)
            )
            ->addEquation(
                new Equation([new MFraction(1), new MFraction(1), new MFraction(1)]),
                Sign::LEQ,
                new MFraction(15)
            )
            ->addEquation(
                new Equation([new MFraction(3), new MFraction(2), new MFraction(1)]),
                Sign::LEQ,
                new MFraction(15)
            );

        $solver = (new Solver())
            ->setEngine(new SimplexDefaultEngine())
            ->setProblem($problem);

        $solution = $solver->solve();

        /** @var SimplexDefaultEngine\SimplexTable $table */
        foreach ($solution->getSimplexTables() as $tableIndex => $table) {
            echo $table;

            $pivotHistory = $table->getPivotHistory()->offsetGet($tableIndex - 1);

            if (is_null($pivotHistory) === false) {
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
            echo sprintf('x%s = %s' . PHP_EOL, $pointIndex + 1, $point->getValue());
        }

        echo sprintf('Value = %s' . PHP_EOL, $solution->getSolutionValue());

        $this->assertTrue($points->count() === 1);
        $this->assertEquals(new MFraction(15, 2), $points->offsetGet(1));
    }

    /**
     * @throws ProblemInvalidException
     */
    public function testUnboundedCase(): void
    {
        // $this->expectException(Solver\Exceptions\OutOfBoundsException::class);

        $problem = new Solver\Problem();

        $problem->calculateMaximum()
            ->setObjectiveFunction(
                new Equation(
                    [
                        new MFraction(2),
                        new MFraction(1)
                    ]
                )
            )
            ->addEquation(
                new Equation(
                    [
                        new MFraction(1),
                        new MFraction(1)
                    ]
                ),
                Sign::GEQ,
                new MFraction(1)
            );

        $solver = (new Solver())
            ->setEngine(new SimplexDefaultEngine())
            ->setProblem($problem);

        $solution = $solver->solve();

        /** @var SimplexDefaultEngine\SimplexTable $table */
        foreach ($solution->getSimplexTables() as $tableIndex => $table) {
            echo $table;

            $pivotHistory = $table->getPivotHistory()->offsetGet($tableIndex - 1);

            if (is_null($pivotHistory) === false) {
                echo sprintf(
                    'Pivot element [%s,%s] on ratio %s on value %s' . PHP_EOL,
                    $pivotHistory->getRowSearchResult()->getRowIndex(),
                    $pivotHistory->getColumnSearchResult()->getColumnIndex(),
                    $pivotHistory->getRowSearchResult()->getRatio(),
                    $pivotHistory->getColumnSearchResult()->getValue()
                );
            }
        }
    }
}