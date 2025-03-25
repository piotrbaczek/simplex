<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

use Ramsey\Collection\AbstractCollection;

class PivotHistoryTable extends AbstractCollection
{
    public function getType(): string
    {
        return PivotHistory::class;
    }
}