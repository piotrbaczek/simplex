<?php

namespace Solver;

use pbaczek\simplex\Solver;
use pbaczek\simplex\Solver\Exceptions\ProblemInvalidException;
use PHPUnit\Framework\TestCase;

class ProblemTest extends TestCase
{
    /**
     * @throws ProblemInvalidException
     */
    public function testSomething()
    {
        $solver = new Solver();
        $problem = new Solver\Problem();
        $problem->calculateMaximum();
        $solver->setProblem($problem)
            ->setSimplexEngine(new Solver\Engines\SimplexIntegerSolutionEngine());

        $this->assertEquals($problem, $solver->getProblem());
        $this->assertTrue($problem->isFunctionMaximized());

        $solution = $solver->solve();

        $this->assertInstanceOf(Solver\Solution::class, $solution);
    }
}