<?php

namespace pbaczek\simplex\Solver\Engines;

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