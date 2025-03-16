<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Dictionaries\Sign;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\InternalTable;
use pbaczek\simplex\Solver\Problem;
use pbaczek\simplex\Solver\Solution\FractionsCollection;

class SimplexTable
{
    private InternalTable $internalTable;

    public function __construct()
    {
        $this->internalTable = new InternalTable();
    }

    public function fromProblem(Problem $problem): void
    {
        $internalTableHeight = $problem->getProblemEquations()->count();

        /** @var Problem\ProblemEquation $firstEquation */
        $firstEquation = $problem->getProblemEquations()->first();
        $internalTableWidth = $firstEquation->getEquation()->count();

        $this->setEquationVariables($internalTableHeight, $problem, $internalTableWidth);

        echo $this;
    }

    public function getSolutionPoints(): FractionsCollection
    {
        // @TODO implement
        return new FractionsCollection([new Fraction(0), new Fraction(0)]);
    }

    public function getSolutionValue(): Fraction
    {
        // @TODO implement
        return new Fraction(0);
    }

    public function __toString(): string
    {
        $data = $this->internalTable->toArray();

        // Calculate column widths
        $col_widths = array_map(function ($col) {
            return max(array_map('strlen', $col));
        }, array_map(null, ...$data));

        // Create border
        $border = "+-" . implode("-+-", array_map(fn($w) => str_repeat("-", $w), $col_widths)) . "-+";

        $return = $border . PHP_EOL;

        foreach ($data as $i => $row) {
            $return .= "| " . implode(" | ", array_map(function ($item, $w) {
                    return str_pad($item, $w);
                }, $row, $col_widths)) . " |" . PHP_EOL;
        }

        $return .= $border . "\n";

        return $return;
    }

    /**
     * @param int $internalTableHeight
     * @param Problem $problem
     * @param int $internalTableWidth
     * @return void
     */
    public function setEquationVariables(int $internalTableHeight, Problem $problem, int $internalTableWidth): void
    {
        for ($row = 0; $row < $internalTableHeight; $row++) {
            /** @var Problem\ProblemEquation $element */
            $element = $problem->getProblemEquations()->offsetGet($row);

            for ($column = 0; $column < $internalTableWidth; $column++) {
                $this->internalTable->setKey($row, $column, $element->getEquation()->offsetGet($column));
            }
        }
    }
}