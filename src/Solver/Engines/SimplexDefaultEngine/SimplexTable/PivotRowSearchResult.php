<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

use pbaczek\fraction\Fraction;

final class PivotRowSearchResult
{
    private int $rowIndex;
    private Fraction $value;

    public function __construct(Fraction $value, int $rowIndex)
    {
        $this->value = $value;
        $this->rowIndex = $rowIndex;
    }

    public function getRowIndex(): int
    {
        return $this->rowIndex;
    }

    public function getValue(): Fraction
    {
        return $this->value;
    }
}