<?php

namespace pbaczek\simplex\Solver\Solution;

use pbaczek\fraction\MFraction;
use Ramsey\Collection\AbstractCollection;

class FractionsCollection extends AbstractCollection
{
    public function getType(): string
    {
        return MFraction::class;
    }
}