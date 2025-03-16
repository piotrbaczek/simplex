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
    private Problem $problem;

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
        $table = SimplexTable::fromProblem($this->problem);

        $this->simplexTables->add($table);

        $solution = new Solution($table->getSolutionPoints(), $table->getSolutionValue(), clone $this->simplexTables);

        $this->clearAfterSolving();

        return $solution;
    }

    protected function clearAfterSolving(): void
    {
        $this->simplexTables->clear();
    }
}