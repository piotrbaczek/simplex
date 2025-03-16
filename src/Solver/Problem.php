<?php

namespace pbaczek\simplex\Solver;

use Override;
use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Dictionaries\Sign;
use pbaczek\simplex\Solver\Interfaces\SimplexProblemInterface;
use pbaczek\simplex\Solver\Problem\ProblemEquation;
use pbaczek\simplex\Solver\Problem\ProblemEquationsCollection;

class Problem implements SimplexProblemInterface
{
    private bool $isFunctionMaximized;
    private Equation $objectiveFunction;

    private ProblemEquationsCollection $problemEquations;

    public function __construct()
    {
        $this->problemEquations = new ProblemEquationsCollection();
    }

    #[Override] public function validate(): bool
    {
        // TODO: Implement validate() method.
        return true;
    }

    #[Override] public function getErrors(): array
    {
        // TODO: Implement getErrors() method.
        return [];
    }

    public function calculateMaximum(): static
    {
        $this->isFunctionMaximized = true;

        return $this;
    }

    public function calculateMinimum(): static
    {
        $this->isFunctionMaximized = false;

        return $this;
    }

    public function isFunctionMaximized(): bool
    {
        return $this->isFunctionMaximized;
    }

    public function setObjectiveFunction(Equation $equation): static
    {
        $this->objectiveFunction = $equation;

        return $this;
    }

    public function getObjectiveFunction(): Equation
    {
        return $this->objectiveFunction;
    }

    public function addEquation(Equation $equation, Sign $sign, Fraction $limit): static
    {
        $this->problemEquations->add(new ProblemEquation($equation, $sign, $limit));

        return $this;
    }

    public function getProblemEquations(): ProblemEquationsCollection
    {
        return $this->problemEquations;
    }
}