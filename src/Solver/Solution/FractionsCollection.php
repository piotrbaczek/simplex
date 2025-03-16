<?php

namespace pbaczek\simplex\Solver\Solution;

use pbaczek\fraction\Fraction;
use Ramsey\Collection\AbstractCollection;

class FractionsCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Fraction::class;
    }
}