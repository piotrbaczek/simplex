<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine;

use pbaczek\simplex\Solver\Problem;

class SimplexTable
{
    public static function fromProblem(Problem $problem): static
    {
        return new static();
    }
}