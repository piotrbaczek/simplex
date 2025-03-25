<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine;

use pbaczek\fraction\Fraction;
use pbaczek\fraction\FractionAbstract;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotHistory;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotHistoryTable;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\InternalTable;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotColumnSearchResult;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotRowSearchResult;
use pbaczek\simplex\Solver\Equation;
use pbaczek\simplex\Solver\Interfaces\SimplexEngineInterface;
use pbaczek\simplex\Solver\Problem;
use pbaczek\simplex\Solver\ProblemProcessor;
use pbaczek\simplex\Solver\Solution\FractionsCollection;
use Ramsey\Collection\Sort;

class SimplexTable
{
    private InternalTable $internalTable;
    private Equation $objectiveFunction;
    private FractionsCollection $limits;
    private PivotHistoryTable $pivotHistory;

    public function __construct()
    {
        $this->internalTable = new InternalTable();
        $this->limits = new FractionsCollection();
        $this->pivotHistory = new PivotHistoryTable();
    }

    public function __clone()
    {
        $this->internalTable = clone $this->internalTable;
        $this->objectiveFunction = clone $this->objectiveFunction;
        $this->limits = clone $this->limits;
        $this->pivotHistory = clone $this->pivotHistory;
    }

    public function fromProblem(Problem $problem): void
    {
        $problemProcessor = new ProblemProcessor();

        list($simplexTable, $internalTable) = $problemProcessor->process($problem);

        $this->internalTable = $internalTable;
        $this->objectiveFunction = $simplexTable->getObjectiveFunction();
        $this->limits = $simplexTable->getLimits();
    }

    public function findPivotColumn(): PivotColumnSearchResult
    {
        $sortedCollection = $this->objectiveFunction
            ->filter(function (Fraction $element) {
                return $element->getValue() < 0;
            })
            ->sort('getValue', Sort::Ascending);

        /** @var Fraction $lowestValue */
        $lowestValue = $sortedCollection->first();

        foreach ($this->objectiveFunction->getIterator() as $index => $objectiveFunctionParameter) {
            if ($lowestValue->equals($objectiveFunctionParameter)) {
                return new PivotColumnSearchResult($lowestValue, $index);
            }
        }

        return new PivotColumnSearchResult(new Fraction(-1), SimplexEngineInterface::NOT_FOUND);
    }

    public function getObjectiveFunction(): Equation
    {
        return $this->objectiveFunction;
    }

    public function setObjectiveFunction(Equation $objectiveFunction): void
    {
        $this->objectiveFunction = $objectiveFunction;
    }

    public function getLimits(): FractionsCollection
    {
        return $this->limits;
    }

    public function setLimits(FractionsCollection $limits): void
    {
        $this->limits = $limits;
    }

    public function findPivotRow(PivotColumnSearchResult $pivotColumnSearchResult): PivotRowSearchResult
    {
        $initialIndex = -1;
        $initialValue = new Fraction(PHP_INT_MAX);

        foreach ($this->internalTable->toArray() as $rowIndex => $row) {
            /** @var Fraction $limitForRow */
            $limitForRow = clone $this->limits[$rowIndex];

            if ($row[$pivotColumnSearchResult->getColumnIndex()]->equals(0)) {
                continue;
            }

            $limitForRow->divide($row[$pivotColumnSearchResult->getColumnIndex()]);

            if ($limitForRow->getValue() < $initialValue->getValue()) {
                $initialValue = $limitForRow;
                $initialIndex = $rowIndex;
            }
        }

        return new PivotRowSearchResult($initialValue, $initialIndex);
    }

    public function addPivotHistory(PivotHistory $pivotHistory): void
    {
        $this->pivotHistory->add($pivotHistory);
    }

    public function getPivotHistory(): PivotHistoryTable
    {
        return $this->pivotHistory;
    }

    public function getRowsCount(): int
    {
        return count($this->internalTable->toArray());
    }

    public function getColumnsCount(): int
    {
        $internalTableArray = $this->internalTable->toArray();
        return count($internalTableArray[0]);
    }

    public function getKey(int $row, int $column): FractionAbstract
    {
        return $this->internalTable->getKey($row, $column);
    }

    public function setKey(int $row, int $column, FractionAbstract $fractionAbstract): void
    {
        $this->internalTable->setKey($row, $column, $fractionAbstract);
    }

    public function __toString(): string
    {
        $data = $this->internalTable->toArray();
        $limits = $this->limits->toArray();
        $objectiveFunction = $this->objectiveFunction->toArray(); // Bottom row

        // Add the $limits column to $data
        foreach ($data as $index => $row) {
            $data[$index][] = $limits[$index] ?? ''; // Append limit column
        }

        // Append the bottom row
        $data[] = $objectiveFunction;

        // Calculate column widths
        $col_widths = array_map(function ($col) {
            return max(array_map('strlen', $col));
        }, array_map(null, ...$data));

        // Create border
        $border = "+-" . implode("-+-", array_map(fn($w) => str_repeat("-", $w), $col_widths)) . "-+";

        $return = $border . PHP_EOL;
        foreach ($data as $row) {
            $return .= "| " . implode(" | ", array_map(function ($item, $w) {
                    return str_pad($item, $w);
                }, $row, $col_widths)) . ' |' . PHP_EOL;
        }
        $return .= $border . PHP_EOL;

        return $return;
    }
}