<?php

namespace pbaczek\simplex;

use pbaczek\simplex\Solver\Exceptions\ProblemInvalidException;
use pbaczek\simplex\Solver\Interfaces\SimplexEngineInterface;
use pbaczek\simplex\Solver\Interfaces\SimplexProblemInterface;
use pbaczek\simplex\Solver\Interfaces\SimplexSolutionInterface;

class Solver
{
    private SimplexEngineInterface $simplexEngine;

    private SimplexProblemInterface $problem;

    public function setSimplexEngine(SimplexEngineInterface $simplexEngine): static
    {
        $this->simplexEngine = $simplexEngine;

        return $this;
    }

    public function setProblem(SimplexProblemInterface $problem): static
    {
        $this->problem = $problem;

        return $this;
    }

    public function getProblem(): SimplexProblemInterface
    {
        return $this->problem;
    }

    /**
     * @throws ProblemInvalidException
     */
    public function solve(): SimplexSolutionInterface
    {
        if (!$this->problem->validate()) {
            throw new ProblemInvalidException('Problem is invalid: ' . join(', ', $this->problem->getErrors()));
        }

        return $this->simplexEngine->solve();
    }
}