<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

final class PivotHistory
{
    private PivotColumn $columnSearchResult;
    private PivotRow $rowSearchResult;

    public function __construct(PivotRow $rowSearchResult, PivotColumn $columnSearchResult)
    {
        $this->rowSearchResult = $rowSearchResult;
        $this->columnSearchResult = $columnSearchResult;
    }

    public function getColumnSearchResult(): PivotColumn
    {
        return $this->columnSearchResult;
    }

    public function getRowSearchResult(): PivotRow
    {
        return $this->rowSearchResult;
    }
}