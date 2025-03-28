<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Interfaces\IndexCompareInterface;

final class PivotRow implements IndexCompareInterface
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

    public function hasSameIndex(int $index): bool
    {
        return $this->rowIndex === $index;
    }
}