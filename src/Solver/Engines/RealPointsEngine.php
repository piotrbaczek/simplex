<?php

namespace pbaczek\simplex\Solver\Engines;

use Override;
use pbaczek\simplex\Solver\Engines\Traits\SetProblemTrait;
use pbaczek\simplex\Solver\Interfaces\SimplexEngineInterface;
use pbaczek\simplex\Solver\Interfaces\SimplexSolutionInterface;

class RealPointsEngine implements SimplexEngineInterface
{
    use SetProblemTrait;
    #[Override] public function solve(): SimplexSolutionInterface
    {
        // TODO: Implement solve() method.
    }
}