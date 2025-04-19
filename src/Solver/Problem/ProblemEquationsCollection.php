<?php

namespace pbaczek\simplex\Solver\Problem;

use Ramsey\Collection\AbstractCollection;

class ProblemEquationsCollection extends AbstractCollection
{
    public function getType(): string
    {
        return ProblemEquation::class;
    }
}