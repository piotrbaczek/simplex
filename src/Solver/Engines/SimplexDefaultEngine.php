<?php

namespace pbaczek\simplex\Solver\Engines;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTableCollection;
use pbaczek\simplex\Solver\Interfaces\SimplexEngineInterface;
use pbaczek\simplex\Solver\Interfaces\SimplexSolutionInterface;
use pbaczek\simplex\Solver\Problem;
use pbaczek\simplex\Solver\Solution;

class SimplexDefaultEngine implements SimplexEngineInterface
{
    private SimplexTableCollection $simplexTables;
    private ?Problem $problem;

    public function __construct()
    {
        $this->simplexTables = new SimplexTableCollection();
    }

    public function setProblem(Problem $problem): static
    {
        $this->problem = $problem;

        return $this;
    }

    public function solve(): SimplexSolutionInterface
    {
        $initialSimplexTable = new SimplexTable();
        $initialSimplexTable->fromProblem($this->problem);

        $this->simplexTables->add($initialSimplexTable);

        // @TODO remove
        echo $initialSimplexTable;

        do {
            /** @var SimplexTable $iterationSimplexTable */
            $iterationSimplexTable = clone $this->simplexTables->last();

            // @TODO simplex operations
            $pivotColumnSearchResult = $iterationSimplexTable->findPivotColumn();

            if ($pivotColumnSearchResult->getColumnIndex() === self::NO_COLUMN_FOUND) {
                // @TODO write logic
                echo 123;
                die();
            }

            $pivotRowSearchResult = $iterationSimplexTable->findPivotRow($pivotColumnSearchResult);

            if ($pivotRowSearchResult->getRowIndex() === self::NO_COLUMN_FOUND) {
                // @TODO write logic

                echo 234;
                die();
            }


            /** @var SimplexTable $previousStepTable */
            $previousStepTable = $this->simplexTables->last();

//            $currentObjectiveFunction = $iterationSimplexTable->getObjectiveFunction();
//            $currentObjectiveFunction->offsetSet($pivotRowSearchResult->getRowIndex());
//            $iterationSimplexTable->setObjectiveFunction($currentObjectiveFunction);

            $previousMainValue = clone($previousStepTable->getKey($pivotRowSearchResult->getRowIndex(), $pivotColumnSearchResult->getColumnIndex()));


            for ($row = 0; $row < $iterationSimplexTable->getRowsCount(); $row++) {
                for ($column = 0; $column < $iterationSimplexTable->getColumnsCount(); $column++) {
                    if ($row === $pivotRowSearchResult->getRowIndex() && $column === $pivotColumnSearchResult->getColumnIndex()) {
                        $iterationSimplexTable->setKey($row, $column, new Fraction(1));
                    } else if ($row === $pivotRowSearchResult->getRowIndex()) {
                        $currentValue = clone($iterationSimplexTable->getKey($row, $column));
                        $currentValue->divide($previousMainValue);
                        $iterationSimplexTable->setKey($row, $column, $currentValue);
                    } else if ($column === $pivotColumnSearchResult->getColumnIndex()) {
                        $iterationSimplexTable->setKey($row, $column, new Fraction(0));
                    } else {
                        $currentValue = clone($iterationSimplexTable->getKey($row, $column));
                        $previousValueAtRow = clone($previousStepTable->getKey($pivotRowSearchResult->getRowIndex(), $column));
                        $previousValueAtColumn = clone($previousStepTable->getKey($row, $pivotColumnSearchResult->getColumnIndex()));
                        $previousValueAtRow->multiply($previousValueAtColumn);
                        $previousValueAtRow->divide($previousMainValue);
                        $currentValue->subtract($previousValueAtRow);
                        $iterationSimplexTable->setKey($row, $column, $currentValue);
                    }
                }
            }

            $this->simplexTables->add($iterationSimplexTable);

            // @TODO remove
            echo $iterationSimplexTable;

        } while (!$this->isFinishReached($iterationSimplexTable));

        $solution = new Solution(
            $initialSimplexTable->getSolutionPoints(),
            $initialSimplexTable->getSolutionValue(),
            clone $this->simplexTables
        );

        $this->clearAfterSolving();

        return $solution;
    }

    private function isFinishReached(SimplexTable $currentTable): bool
    {
        // @TODO implement
        return true;
    }

    protected function clearAfterSolving(): void
    {
        $this->simplexTables->clear();
        $this->problem = null;
    }
}