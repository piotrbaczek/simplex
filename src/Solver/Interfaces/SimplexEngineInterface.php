<?php

namespace pbaczek\simplex\Solver\Interfaces;

use pbaczek\simplex\Solver\Problem;

interface SimplexEngineInterface
{
    public const int NOT_FOUND = -1;

    public function solve(): SimplexSolutionInterface;

    public function setProblem(Problem $problem): SimplexEngineInterface;
}