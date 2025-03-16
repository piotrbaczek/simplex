<?php

namespace pbaczek\simplex;

use pbaczek\simplex\Solver\Exceptions\InvalidEngineException;
use pbaczek\simplex\Solver\Exceptions\ProblemInvalidException;
use pbaczek\simplex\Solver\Interfaces\SimplexEngineInterface;
use pbaczek\simplex\Solver\Interfaces\SimplexProblemInterface;
use pbaczek\simplex\Solver\Interfaces\SimplexSolutionInterface;

class Solver
{
    private SimplexEngineInterface $simplexEngine;

    private SimplexProblemInterface $problem;
    private string $simplexEngineClassName;

    public function setSimplexEngineClassName(string $class): static
    {
        $this->simplexEngineClassName = $class;

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
     * @throws InvalidEngineException
     */
    public function solve(): SimplexSolutionInterface
    {
        if (!$this->problem->validate()) {
            throw new ProblemInvalidException('Problem is invalid: ' . join(', ', $this->problem->getErrors()));
        }

        $engine = new $this->simplexEngineClassName();

        if (!$engine instanceof SimplexEngineInterface) {
            throw new InvalidEngineException(sprintf('Engine %s must implement %s interface.', $this->simplexEngineClassName, SimplexEngineInterface::class));
        }

        $this->simplexEngine = $engine;

        $this->simplexEngine->setProblem($this->problem);
        return $this->simplexEngine->solve();
    }
}