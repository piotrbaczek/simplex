<?php

namespace pbaczek\simplex\Solver;

use pbaczek\fraction\Fraction;
use Ramsey\Collection\AbstractCollection;

class Equation extends AbstractCollection
{
    public function getType(): string
    {
        return Fraction::class;
    }
}