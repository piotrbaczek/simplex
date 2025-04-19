<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine;

use Ramsey\Collection\AbstractCollection;

class SimplexTableCollection extends AbstractCollection
{
    public function getType(): string
    {
        return SimplexTable::class;
    }
}