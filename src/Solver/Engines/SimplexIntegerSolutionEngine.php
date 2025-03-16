<?php

namespace pbaczek\simplex\Solver\Engines;

use Override;
use pbaczek\simplex\Solver\Interfaces\SimplexEngineInterface;
use pbaczek\simplex\Solver\Interfaces\SimplexSolutionInterface;
use pbaczek\simplex\Solver\Solution;

class SimplexIntegerSolutionEngine implements SimplexEngineInterface
{
    #[Override] public function solve(): SimplexSolutionInterface
    {
        // TODO: Implement solve() method.
        return new Solution();
    }
}