<?php

namespace pbaczek\simplex\Solver;

use Override;
use pbaczek\simplex\Solver\Interfaces\SimplexProblemInterface;

class Problem implements SimplexProblemInterface
{
    private bool $isFunctionMaximized;

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

    public function setObjectiveFunction()
    {

    }
}