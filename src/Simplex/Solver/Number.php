<?php

namespace pbaczek\simplex\Simplex\Solver;

/**
 * Class Number
 * @package pbaczek\simplex\Simplex\Solver
 */
class Number extends SolverAbstract
{
    /**
     * @inheritDoc
     * @return void
     */
    protected function validate(): void
    {
//        if ($this->variables->count() === 0) {
//            throw new InvalidArgumentException('variables count can\'t be 0');
//        }
//
//        if ($this->boundaries->count() === 0) {
//            throw new InvalidArgumentException('boundaries count can\'t be 0');
//        }
//
//        if ($this->signs->count() === 0) {
//            throw new InvalidArgumentException('signs count can\'t be 0');
//        }
    }

    /**
     * Solve the problem
     * @return void
     */
    protected function run(): void
    {
        // TODO: Implement run() method.
    }
}