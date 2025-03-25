<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

final class PivotHistory
{
    private PivotColumnSearchResult $columnSearchResult;
    private PivotRowSearchResult $rowSearchResult;

    public function __construct(PivotRowSearchResult $rowSearchResult, PivotColumnSearchResult $columnSearchResult)
    {
        $this->rowSearchResult = $rowSearchResult;
        $this->columnSearchResult = $columnSearchResult;
    }

    public function getColumnSearchResult(): PivotColumnSearchResult
    {
        return $this->columnSearchResult;
    }

    public function getRowSearchResult(): PivotRowSearchResult
    {
        return $this->rowSearchResult;
    }
}