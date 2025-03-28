<?php

namespace pbaczek\simplex;

use pbaczek\simplex\Solver\Exceptions\InvalidEngineException;
use pbaczek\simplex\Solver\Exceptions\ProblemInvalidException;
use pbaczek\simplex\Solver\Interfaces\SimplexEngineInterface;
use pbaczek\simplex\Solver\Interfaces\SimplexProblemInterface;
use pbaczek\simplex\Solver\Interfaces\SimplexSolutionInterface;

class Solver
{
    private SimplexProblemInterface $problem;
    private SimplexEngineInterface $simplexEngineInterface;

    public function setEngine(SimplexEngineInterface $simplexEngineInterface): static
    {
        $this->simplexEngineInterface = $simplexEngineInterface;

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

        $simplexEngine = $this->simplexEngineInterface;

        $simplexEngine->setProblem($this->problem);

        return $simplexEngine->solve();
    }
}