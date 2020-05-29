<?php

namespace pbaczek\simplex\Simplex\Solver;

use InvalidArgumentException;

/**
 * Class Dantzig
 * @package pbaczek\simplex\Simplex\Solver
 */
class Dantzig extends SolverAbstract
{
    protected function solve(): void
    {
        // TODO: Implement solve() method.
    }

    protected function validate(): void
    {
        if ($this->variables->count() === 0) {
            throw new InvalidArgumentException('variables count can\'t be 0');
        }

        if ($this->boundaries->count() === 0) {
            throw new InvalidArgumentException('boundaries count can\'t be 0');
        }

        if ($this->signs->count() === 0) {
            throw new InvalidArgumentException('signs count can\'t be 0');
        }
    }
}