<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Interfaces\IndexCompareInterface;

final class PivotColumn implements IndexCompareInterface
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

    public function hasSameIndex(int $index): bool
    {
        return $this->columnIndex === $index;
    }
}