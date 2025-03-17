<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

use pbaczek\fraction\Fraction;

final class PivotColumnSearchResult
{
    private Fraction $value;
    private int $columnIndex;

    public function __construct(Fraction $value, int $columnIndex)
    {
        $this->value = $value;
        $this->columnIndex = $columnIndex;
    }

    public function getValue(): Fraction
    {
        return $this->value;
    }

    public function getColumnIndex(): int
    {
        return $this->columnIndex;
    }
}