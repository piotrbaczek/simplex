<?php

namespace pbaczek\simplex\Solver\Interfaces;

use pbaczek\simplex\Solver\Problem;

interface SimplexEngineInterface
{
    public function solve(): SimplexSolutionInterface;

    public function setProblem(Problem $problem): SimplexEngineInterface;
}