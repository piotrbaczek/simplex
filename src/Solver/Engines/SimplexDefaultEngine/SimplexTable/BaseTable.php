<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

use Ramsey\Collection\AbstractCollection;

class BaseTable extends AbstractCollection
{
    public function getType(): string
    {
        return PivotRowSearchResult::class;
    }
}