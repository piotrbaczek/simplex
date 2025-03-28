<?php

namespace pbaczek\simplex\Solver\Engines;

use pbaczek\fraction\Fraction;
use pbaczek\fraction\FractionAbstract;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotColumnSearchResult;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotHistory;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\PivotRowSearchResult;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTableCollection;
use pbaczek\simplex\Solver\Engines\Traits\SetProblemTrait;
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

            $this->pivotObjectiveFunction($iterationSimplexTable, $pivotColumnSearchResult);

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
            $points->add(
                $lastSimplexTable->getLimits()->offsetGet($pivotHistory->getRowSearchResult()->getRowIndex())
            );
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
        $objectiveFunctionParametersSum = clone ($currentTable
            ->getObjectiveFunction())
            ->reduce(function (Fraction $carry, Fraction $objectiveFunctionParam) {
                $carry->add($objectiveFunctionParam);
                return $carry;
            }, new Fraction(0));

        return $objectiveFunctionParametersSum->equals(0);
    }

    /**
     * @param SimplexTable $iterationSimplexTable
     * @param PivotRowSearchResult $pivotRowSearchResult
     * @param FractionAbstract $previousPivotValue
     * @param SimplexTable $previousStepTable
     * @param PivotColumnSearchResult $pivotColumnSearchResult
     * @return void
     */
    public function pivotLimits(
        SimplexTable            $iterationSimplexTable,
        PivotRowSearchResult    $pivotRowSearchResult,
        FractionAbstract        $previousPivotValue,
        SimplexTable            $previousStepTable,
        PivotColumnSearchResult $pivotColumnSearchResult
    ): void
    {
        $newLimits = clone($iterationSimplexTable->getLimits());

        foreach ($iterationSimplexTable->getLimits() as $limitKey => $limitValue) {
            /** @var Fraction $limitAtRow */
            $limitAtRow = clone($limitValue);

            if ($limitKey === $pivotRowSearchResult->getRowIndex()) {
                $limitAtRow->divide($previousPivotValue);
            } else {
                $rowElement = clone($previousStepTable->getKey($limitKey, $pivotColumnSearchResult->getColumnIndex()));
                $columnElement = clone($iterationSimplexTable->getLimits()->offsetGet($pivotRowSearchResult->getRowIndex()));
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
     * @param PivotColumnSearchResult $pivotColumnSearchResult
     * @return void
     */
    public function pivotObjectiveFunction(
        SimplexTable            $iterationSimplexTable,
        PivotColumnSearchResult $pivotColumnSearchResult
    ): void
    {
        $iterationSimplexTable
            ->getObjectiveFunction()
            ->offsetSet(
                $pivotColumnSearchResult->getColumnIndex(),
                new Fraction(0)
            );
    }

    /**
     * @param SimplexTable $iterationSimplexTable
     * @param PivotRowSearchResult $pivotRowSearchResult
     * @param PivotColumnSearchResult $pivotColumnSearchResult
     * @param FractionAbstract $previousPivotValue
     * @param SimplexTable $previousStepTable
     * @return void
     */
    public function pivotSimplexTable(
        SimplexTable            $iterationSimplexTable,
        PivotRowSearchResult    $pivotRowSearchResult,
        PivotColumnSearchResult $pivotColumnSearchResult,
        FractionAbstract        $previousPivotValue,
        SimplexTable            $previousStepTable
    ): void
    {
        for ($row = 0; $row < $iterationSimplexTable->getRowsCount(); $row++) {
            for ($column = 0; $column < $iterationSimplexTable->getColumnsCount(); $column++) {
                if ($row === $pivotRowSearchResult->getRowIndex()
                    && $column === $pivotColumnSearchResult->getColumnIndex()) {
                    $iterationSimplexTable->setKey($row, $column, new Fraction(1));
                } else if ($row === $pivotRowSearchResult->getRowIndex()) {
                    $currentValue = clone($iterationSimplexTable->getKey($row, $column));
                    $currentValue->divide($previousPivotValue);
                    $iterationSimplexTable->setKey($row, $column, clone($currentValue));
                } else if ($column === $pivotColumnSearchResult->getColumnIndex()) {
                    $iterationSimplexTable->setKey($row, $column, new Fraction(0));
                } else {
                    $currentValue = clone($iterationSimplexTable->getKey($row, $column));
                    $previousValueAtRow = clone(
                    $previousStepTable->getKey($pivotRowSearchResult->getRowIndex(), $column)
                    );
                    $previousValueAtColumn = clone(
                    $previousStepTable->getKey($row, $pivotColumnSearchResult->getColumnIndex())
                    );
                    $previousValueAtRow->multiply($previousValueAtColumn);
                    $previousValueAtRow->divide($previousPivotValue);
                    $currentValue->subtract($previousValueAtRow);
                    $iterationSimplexTable->setKey($row, $column, clone($currentValue));
                }
            }
        }
    }

    private function calculateValue(SimplexTable $currentTable): Fraction
    {
        $sum = new Fraction(0);

        /** @var PivotHistory $pivotHistory */
        foreach ($currentTable->getPivotHistory() as $pivotHistory) {
            $item = clone $pivotHistory->getColumnSearchResult()->getValue();
            $item->multiply($currentTable->getLimits()->offsetGet($pivotHistory->getRowSearchResult()->getRowIndex()));
            $sum->add($item);
        }

        $sum->changeSign();

        return $sum;
    }
}