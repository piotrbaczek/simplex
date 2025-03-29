<?php

namespace pbaczek\simplex\Solver\Engines;

use pbaczek\fraction\Fraction;
use pbaczek\fraction\FractionAbstract;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotColumn;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotHistory;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotRow;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTableCollection;
use pbaczek\simplex\Solver\Engines\Traits\SetProblemTrait;
use pbaczek\simplex\Solver\Equation;
use pbaczek\simplex\Solver\Exceptions\OutOfBoundsException;
use pbaczek\simplex\Solver\Interfaces\SimplexEngineInterface;
use pbaczek\simplex\Solver\Interfaces\SimplexSolutionInterface;
use pbaczek\simplex\Solver\Solution;
use pbaczek\simplex\Solver\Solution\FractionsCollection;
use Ramsey\Collection\Sort;

class SimplexDefaultEngine implements SimplexEngineInterface
{
    use SetProblemTrait;

    private SimplexTableCollection $simplexTables;

    public function __construct()
    {
        $this->simplexTables = new SimplexTableCollection();
    }

    public function __clone()
    {
        $this->simplexTables = clone $this->simplexTables;
    }

    /**
     * @throws OutOfBoundsException
     */
    public function solve(): SimplexSolutionInterface
    {
        $initialSimplexTable = new SimplexTable();
        $initialSimplexTable->fromProblem($this->problem);

        $this->simplexTables->add($initialSimplexTable);

        do {
            /** @var SimplexTable $iterationSimplexTable */
            $iterationSimplexTable = clone $this->simplexTables->last();

            $pivotColumnSearchResult = $iterationSimplexTable->findPivotColumn();

            if ($pivotColumnSearchResult->getColumnIndex() === self::NOT_FOUND) {
                throw new OutOfBoundsException(
                    'Pivot column not found',
                    $pivotColumnSearchResult->getColumnIndex()
                );
            }

            $pivotRowSearchResult = $iterationSimplexTable->findPivotRow($pivotColumnSearchResult);

            if ($pivotRowSearchResult->getRowIndex() === self::NOT_FOUND) {
                throw new OutOfBoundsException('Pivot row not found', $pivotRowSearchResult->getRowIndex());
            }

            /** @var SimplexTable $previousStepTable */
            $previousStepTable = clone $this->simplexTables->last();

            $previousPivotValue = clone $previousStepTable->getKey(
                $pivotRowSearchResult->getRowIndex(),
                $pivotColumnSearchResult->getColumnIndex()
            );

            $this->pivotLimits(
                $iterationSimplexTable,
                $pivotRowSearchResult,
                $previousPivotValue,
                $previousStepTable,
                $pivotColumnSearchResult
            );

            $this->pivotSimplexTable(
                $iterationSimplexTable,
                $pivotRowSearchResult,
                $pivotColumnSearchResult,
                $previousPivotValue,
                $previousStepTable
            );

            $iterationSimplexTable->addPivotHistory(new PivotHistory($pivotRowSearchResult, $pivotColumnSearchResult));

            $iterationSimplexTable->setResourcesAtPoint($this->calculateResourcesAtPoint($iterationSimplexTable));

            $iterationSimplexTable->setObjectiveFunctionAtPoint($this->calculateObjectiveFunctionAtPoint($iterationSimplexTable));

            $iterationSimplexTable->setValue($this->calculateValue($iterationSimplexTable));

            $this->simplexTables->add($iterationSimplexTable);

        } while (!$this->isFinishReached($iterationSimplexTable));

        return new Solution($this->getSolutionPoints(), $this->getSolutionValue(), $this->simplexTables);
    }

    public function getSolutionPoints(): FractionsCollection
    {
        $points = new FractionsCollection();

        /** @var SimplexTable $lastSimplexTable */
        $lastSimplexTable = $this->simplexTables->last();
        $history = $lastSimplexTable->getPivotHistory()->sort(null, Sort::Descending);

        /** @var PivotHistory $pivotHistory */
        foreach ($history as $pivotHistory) {
            $points->offsetSet(
                $pivotHistory->getColumnSearchResult()->getColumnIndex(),
                $lastSimplexTable->getLimits()->offsetGet($pivotHistory->getRowSearchResult()->getRowIndex())
            );
//            $points->add(
//                $lastSimplexTable->getLimits()->offsetGet($pivotHistory->getRowSearchResult()->getRowIndex())
//            );
        }

        return $points;
    }

    public function getSolutionValue(): Fraction
    {
        /** @var SimplexTable $lastTable */
        $lastTable = $this->simplexTables->last();

        return $lastTable->getValue();
    }

    private function isFinishReached(SimplexTable $currentTable): bool
    {
        $objectiveFunctionAtPoints = $currentTable
            ->getObjectiveFunctionAtPoint()
            ->filter(function (Fraction $item) {
                return $item->getValue() < 0;
            });

        return $objectiveFunctionAtPoints->count() === 0;
    }

    /**
     * @param SimplexTable $iterationSimplexTable
     * @param PivotRow $pivotRowSearchResult
     * @param FractionAbstract $previousPivotValue
     * @param SimplexTable $previousStepTable
     * @param PivotColumn $pivotColumnSearchResult
     * @return void
     */
    public function pivotLimits(
        SimplexTable     $iterationSimplexTable,
        PivotRow         $pivotRowSearchResult,
        FractionAbstract $previousPivotValue,
        SimplexTable     $previousStepTable,
        PivotColumn      $pivotColumnSearchResult
    ): void
    {
        $newLimits = clone $iterationSimplexTable->getLimits();

        foreach ($iterationSimplexTable->getLimits() as $limitKey => $limitValue) {

            $limitAtRow = Fraction::from($limitValue);

            if ($limitKey === $pivotRowSearchResult->getRowIndex()) {
                $limitAtRow->divide($previousPivotValue);
            } else {
                $rowElement = Fraction::from($previousStepTable->getKey($limitKey, $pivotColumnSearchResult->getColumnIndex()));
                $columnElement = Fraction::from($iterationSimplexTable->getLimits()->offsetGet($pivotRowSearchResult->getRowIndex()));
                $rowElement->multiply($columnElement);
                $rowElement->divide($previousPivotValue);
                $limitAtRow->subtract($rowElement);
            }

            $newLimits->offsetSet($limitKey, clone($limitAtRow));
        }

        $iterationSimplexTable->setLimits($newLimits);
    }

    /**
     * @param SimplexTable $iterationSimplexTable
     * @param PivotRow $pivotRowSearchResult
     * @param PivotColumn $pivotColumnSearchResult
     * @param FractionAbstract $previousPivotValue
     * @param SimplexTable $previousStepTable
     * @return void
     */
    public function pivotSimplexTable(
        SimplexTable     $iterationSimplexTable,
        PivotRow         $pivotRowSearchResult,
        PivotColumn      $pivotColumnSearchResult,
        FractionAbstract $previousPivotValue,
        SimplexTable     $previousStepTable
    ): void
    {
        for ($row = 0; $row < $iterationSimplexTable->getRowsCount(); $row++) {
            for ($column = 0; $column < $iterationSimplexTable->getColumnsCount(); $column++) {
                if ($pivotRowSearchResult->hasSameIndex($row)
                    && $pivotColumnSearchResult->hasSameIndex($column)) {
                    $iterationSimplexTable->setKey($row, $column, new Fraction(1));
                } else if ($pivotRowSearchResult->hasSameIndex($row)) {
                    $currentValue = Fraction::from($iterationSimplexTable->getKey($row, $column));
                    $currentValue->divide($previousPivotValue);
                    $iterationSimplexTable->setKey($row, $column, $currentValue);
                } else if ($pivotColumnSearchResult->hasSameIndex($column)) {
                    $iterationSimplexTable->setKey($row, $column, new Fraction(0));
                } else {
                    $currentValue = Fraction::from($iterationSimplexTable->getKey($row, $column));
                    $valueAtPivotRow = Fraction::from($previousStepTable->getKey($pivotRowSearchResult->getRowIndex(), $column));
                    $valueAtPivotColumn = Fraction::from($previousStepTable->getKey($row, $pivotColumnSearchResult->getColumnIndex()));

                    $valueAtPivotRow->multiply($valueAtPivotColumn);
                    $valueAtPivotRow->divide($previousPivotValue);
                    $currentValue->subtract($valueAtPivotRow);

                    $iterationSimplexTable->setKey($row, $column, $currentValue);
                }
            }
        }
    }

    private function calculateValue(SimplexTable $currentTable): Fraction
    {
        $sum = new Fraction(0);

        /** @var PivotHistory $pivotHistory */
        foreach ($currentTable->getPivotHistory() as $pivotHistory) {
            $lowestPivotColumnValue = Fraction::from($pivotHistory->getColumnSearchResult()->getValue());
            $lowestPivotColumnValue->changeSign();
            $lowestPivotColumnValue->multiply($currentTable->getLimits()->offsetGet($pivotHistory->getRowSearchResult()->getRowIndex()));
            $sum->add($lowestPivotColumnValue);
        }

        return $sum;
    }

    private function calculateResourcesAtPoint(SimplexTable $iterationSimplexTable): Equation
    {
        $resourcesAtPoint = $iterationSimplexTable->getResourcesAtPoint()->zero();

        /** @var PivotHistory $pivotHistory */
        foreach ($iterationSimplexTable->getPivotHistory() as $pivotHistory) {
            for ($columnIndex = 0; $columnIndex < $iterationSimplexTable->getColumnsCount(); $columnIndex++) {
                $multiplier = Fraction::from($pivotHistory->getColumnSearchResult()->getValue());
                $multiplier->changeSign();

                $element = Fraction::from($iterationSimplexTable->getKey($pivotHistory->getRowSearchResult()->getRowIndex(), $columnIndex));
                $element->multiply($multiplier);

                $previousResourceAtPoint = $resourcesAtPoint->offsetGet($columnIndex);
                $previousResourceAtPoint->add($element);
                $resourcesAtPoint->offsetSet($columnIndex, $previousResourceAtPoint);
            }
        }

        return $resourcesAtPoint;
    }

    private function calculateObjectiveFunctionAtPoint(SimplexTable $iterationSimplexTable): Equation
    {
        $objectiveFunctionAtPoint = $iterationSimplexTable->getObjectiveFunctionAtPoint()->zero();

        /** @var Fraction $resourceAtPointParam */
        foreach ($iterationSimplexTable->getResourcesAtPoint() as $index => $resourceAtPointParam) {
            $item = Fraction::from($resourceAtPointParam);
            $item->subtract($iterationSimplexTable->getObjectiveFunction()->offsetGet($index));
            $objectiveFunctionAtPoint->offsetSet($index, $item);
        }

        return $objectiveFunctionAtPoint;
    }
}