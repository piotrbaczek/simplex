<?php

namespace pbaczek\simplex\tests\Solver;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver;
use pbaczek\simplex\Solver\Equation;
use pbaczek\simplex\Solver\Exceptions\ProblemInvalidException;
use PHPUnit\Framework\TestCase;
use pbaczek\simplex\Solver\Solution\FractionsCollection;
use pbaczek\simplex\Solver\Engines\SimplexIntegerSolutionEngine;
use pbaczek\simplex\Solver\Dictionaries\Sign;

class ProblemTest extends TestCase
{
    /**
     * @throws ProblemInvalidException
     */
    public function testBasicThesisExample(): void
    {
        $solver = new Solver();
        $problem = new Solver\Problem();

        $problem
            ->calculateMaximum()
            ->setObjectiveFunction(new Equation([new Fraction(2), new Fraction(6)]))
            ->addEquation(new Equation([new Fraction(2), new Fraction(5)]), Sign::LEQ, new Fraction(30))
            ->addEquation(new Equation([new Fraction(2), new Fraction(3)]), Sign::LEQ, new Fraction(26))
            ->addEquation(new Equation([new Fraction(0), new Fraction(3)]), Sign::LEQ, new Fraction(15));

        $solver
            ->setSimplexEngine(new SimplexIntegerSolutionEngine())
            ->setProblem($problem);

        $this->assertEquals($problem, $solver->getProblem());
        $this->assertTrue($problem->isFunctionMaximized());

        $solution = $solver->solve();

        $expectedSolution = new Solver\Solution(
            new FractionsCollection(
                [
                    new Fraction(5, 2),
                    new Fraction(5)
                ]
            ),
            new Fraction(35)
        );

        //$this->assertEquals($expectedSolution, $solution);

        $this->assertInstanceOf(Solver\Solution::class, $solution);
    }
}