<?php

namespace pbaczek\simplex;

use pbaczek\simplex\Simplex\Solver\SolverAbstract;

/**
 * Class Simplex
 * @package pbaczek\simplex
 */
class Simplex
{
    /** @var SolverAbstract $solver */
    private $solver;

    /**
     * Simplex constructor.
     * @param SolverAbstract $solverAbstract
     */
    public function __construct(SolverAbstract $solverAbstract)
    {
        $this->solver = $solverAbstract;
    }

    /**
     * Solve equation
     * @return void
     */
    public function run(): void
    {
        $this->solver->run();
    }
}