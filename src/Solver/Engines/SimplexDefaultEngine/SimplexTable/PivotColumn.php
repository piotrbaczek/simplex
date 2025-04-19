<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

use pbaczek\fraction\MFraction;
use pbaczek\simplex\Solver\Interfaces\IndexCompareInterface;

final class PivotColumn implements IndexCompareInterface
{
    private MFraction $value;
    private int $columnIndex;

    public function __construct(MFraction $value, int $columnIndex)
    {
        $this->value = $value;
        $this->columnIndex = $columnIndex;
    }

    public function getValue(): MFraction
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