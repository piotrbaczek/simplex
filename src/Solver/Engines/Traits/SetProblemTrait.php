<?php

namespace pbaczek\simplex\Solver\Engines\Traits;

use pbaczek\simplex\Solver\Interfaces\SimplexEngineInterface;
use pbaczek\simplex\Solver\Problem;

trait SetProblemTrait
{
    private ?Problem $problem;

    public function setProblem(Problem $problem): SimplexEngineInterface
    {
        $this->problem = $problem;

        return $this;
    }
}