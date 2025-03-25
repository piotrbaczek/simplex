<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

use pbaczek\fraction\Fraction;

final class PivotRowSearchResult
{
    private int $rowIndex;
    private Fraction $ratio;

    public function __construct(Fraction $ratio, int $rowIndex)
    {
        $this->ratio = $ratio;
        $this->rowIndex = $rowIndex;
    }

    public function getRowIndex(): int
    {
        return $this->rowIndex;
    }

    public function getRatio(): Fraction
    {
        return $this->ratio;
    }
}