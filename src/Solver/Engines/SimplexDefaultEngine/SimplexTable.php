<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine;

use BadFunctionCallException;
use pbaczek\fraction\FractionAbstract;
use pbaczek\fraction\MFraction;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\InternalTable;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotColumn;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotHistory;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotHistoryTable;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotRow;
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
    private Equation $resourcesAtPoint;
    private Equation $objectiveFunctionAtPoint;
    private FractionsCollection $limits;
    private PivotHistoryTable $pivotHistory;
    private MFraction $value;

    public function __construct()
    {
        $this->internalTable = new InternalTable();
        $this->limits = new FractionsCollection();
        $this->pivotHistory = new PivotHistoryTable();
        $this->value = new MFraction(0);
        $this->objectiveFunction = new Equation();
        $this->resourcesAtPoint = new Equation();
        $this->objectiveFunctionAtPoint = new Equation();
    }

    public function __clone()
    {
        $this->internalTable = clone $this->internalTable;
        $this->objectiveFunction = clone $this->objectiveFunction;
        $this->limits = clone $this->limits;
        $this->pivotHistory = clone $this->pivotHistory;
        $this->resourcesAtPoint = clone $this->resourcesAtPoint;
        $this->objectiveFunctionAtPoint = clone $this->objectiveFunctionAtPoint;
    }

    public function fromProblem(Problem $problem): void
    {
        $problemProcessor = new ProblemProcessor();

        list($simplexTable, $internalTable) = $problemProcessor->process($problem);

        $this->internalTable = $internalTable;
        $this->objectiveFunction = $simplexTable->getObjectiveFunction();
        $this->limits = $simplexTable->getLimits();
        $this->resourcesAtPoint = $simplexTable->getResourcesAtPoint();
        $this->objectiveFunctionAtPoint = $simplexTable->getObjectiveFunctionAtPoint();
    }

    public function findPivotRow(PivotColumn $pivotColumnSearchResult): PivotRow
    {
        $pivotIndex = SimplexEngineInterface::NOT_FOUND;
        $pivotRatio = new MFraction(PHP_INT_MAX);

        foreach ($this->internalTable->toArray() as $rowIndex => $row) {

            $limitForRow = MFraction::from($this->limits[$rowIndex]);

            if ($row[$pivotColumnSearchResult->getColumnIndex()]->equals(0)) {
                continue;
            }

            $limitForRow->divide($row[$pivotColumnSearchResult->getColumnIndex()]);

            if ($limitForRow->getValue() < $pivotRatio->getValue()) {
                $pivotRatio = $limitForRow;
                $pivotIndex = $rowIndex;
            }
        }

        return new PivotRow($pivotRatio, $pivotIndex);
    }

    /**
     * @return PivotColumn
     */
    public function findPivotColumn(): PivotColumn
    {
        $sortedCollection = $this->objectiveFunctionAtPoint
            ->filter(function (MFraction $element) {
                return $element->getValue() < 0;
            })
            ->sort('getValue', Sort::Ascending);

        if ($sortedCollection->count() === 0) {
            return new PivotColumn(new MFraction(-1), SimplexEngineInterface::NOT_FOUND);
        }

        /** @var MFraction $lowestValue */
        $lowestValue = $sortedCollection->first();

        foreach ($this->objectiveFunctionAtPoint->getIterator() as $objectiveFunctionIndex => $objectiveFunctionParameter) {
            if ($lowestValue->equals($objectiveFunctionParameter)) {
                return new PivotColumn($lowestValue, $objectiveFunctionIndex);
            }
        }

        throw new BadFunctionCallException('Lowest element not found in collection');
    }

    public function getObjectiveFunction(): Equation
    {
        return $this->objectiveFunction;
    }

    public function setObjectiveFunction(Equation $objectiveFunction): void
    {
        $this->objectiveFunction = $objectiveFunction;
    }

    public function getResourcesAtPoint(): Equation
    {
        return $this->resourcesAtPoint;
    }

    public function setResourcesAtPoint(Equation $resourcesAtPoint): void
    {
        $this->resourcesAtPoint = $resourcesAtPoint;
    }

    public function getObjectiveFunctionAtPoint(): Equation
    {
        return $this->objectiveFunctionAtPoint;
    }

    public function setObjectiveFunctionAtPoint(Equation $objectiveFunctionAtPoint): void
    {
        $this->objectiveFunctionAtPoint = $objectiveFunctionAtPoint;
    }

    public function getLimits(): FractionsCollection
    {
        return $this->limits;
    }

    public function setLimits(FractionsCollection $limits): void
    {
        $this->limits = $limits;
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

    public function getKey(int $row, int $column): MFraction
    {
        return $this->internalTable->getKey($row, $column);
    }

    public function setKey(int $row, int $column, MFraction $fractionAbstract): void
    {
        $this->internalTable->setKey($row, $column, $fractionAbstract);
    }

    public function setValue(MFraction $value): void
    {
        $this->value = $value;
    }

    public function getValue(): MFraction
    {
        return $this->value;
    }

    public function __toString(): string
    {
        $table = $this->internalTable->toArray();
        $limits = $this->limits->toArray();
        //$limits = array_merge($limits, [$this->value]);
        array_unshift($limits, '');
        $objectiveFunction = $this->objectiveFunction->toArray();
        $objectiveFunctionAtPoint = $this->objectiveFunctionAtPoint->toArray();
        $resourcesAtPoint = $this->resourcesAtPoint->toArray();

        // Prepend $objectiveFunction as the first row
        array_unshift($table, $objectiveFunction);

        // Append $limits column to each row
        foreach ($table as $index => $row) {
            $table[$index][] = $limits[$index]; // Add corresponding limit value
        }

        // Append $resourcesAtPoint as the last row
        $table[] = array_merge($resourcesAtPoint, [$this->value]);

        // Append $objectiveFunctionAtPoint as the final row
        $table[] = $objectiveFunctionAtPoint;

        // Determine column widths dynamically
        $colWidths = array_map(null, ...$table);
        $colWidths = array_map(fn($col) => max(array_map(fn($num) => strlen($num), $col)), $colWidths);

        // Generate table line
        $line = "+-" . implode("-+-", array_map(fn($w) => str_repeat("-", $w), $colWidths)) . "-+\n";

        // Print table
        $return = $line;
        foreach ($table as $row) {
            $return .= "| " . implode(" | ", array_map(fn($i, $w) => str_pad($i, $w), $row, $colWidths)) . " |\n";
            $return .= $line;
        }

        return $return;
    }
}