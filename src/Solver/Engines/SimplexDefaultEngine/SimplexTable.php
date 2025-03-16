<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Dictionaries\Sign;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\InternalTable;
use pbaczek\simplex\Solver\Equation;
use pbaczek\simplex\Solver\Problem;
use pbaczek\simplex\Solver\Solution\FractionsCollection;

class SimplexTable
{
    private InternalTable $internalTable;
    private Equation $objectiveFunction;
    private FractionsCollection $limits;

    public function __construct()
    {
        $this->internalTable = new InternalTable();
        $this->limits = new FractionsCollection();
    }

    public function fromProblem(Problem $problem): void
    {
        $internalTableHeight = $problem->getProblemEquations()->count();

        $internalTableWidth = $problem->getProblemEquations()->first()->getEquation()->count();

        $this->setBasicVariables($internalTableHeight, $problem, $internalTableWidth);

        $this->setNonBasicVariables($internalTableHeight, $problem, $internalTableWidth);

        $this->setObjectiveFunction($problem);

        $this->setLimits($problem);

        // @TODO remove
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
        $columnWidths = array_map(function ($col) {
            return max(array_map('strlen', $col));
        }, array_map(null, ...$data));

        // Create border
        $border = "+-" . implode("-+-", array_map(fn($w) => str_repeat("-", $w), $columnWidths)) . "-+";

        $return = $border . PHP_EOL;

        foreach ($data as $row) {
            $return .= "| " . implode(" | ", array_map(function ($item, $w) {
                    return str_pad($item, $w);
                }, $row, $columnWidths)) . " |" . PHP_EOL;
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
    public function setBasicVariables(int $internalTableHeight, Problem $problem, int $internalTableWidth): void
    {
        for ($row = 0; $row < $internalTableHeight; $row++) {
            /** @var Problem\ProblemEquation $element */
            $element = $problem->getProblemEquations()->offsetGet($row);

            for ($column = 0; $column < $internalTableWidth; $column++) {
                $this->internalTable->setKey($row, $column, $element->getEquation()->offsetGet($column));
            }
        }
    }

    /**
     * @param int $internalTableHeight
     * @param Problem $problem
     * @param $internalTableWidth
     * @return void
     */
    public function setNonBasicVariables(int $internalTableHeight, Problem $problem, $internalTableWidth): void
    {
        for ($row = 0; $row < $internalTableHeight; $row++) {
            /** @var Problem\ProblemEquation $element */
            $element = $problem->getProblemEquations()->offsetGet($row);

            switch ($element->getSign()) {
                case Sign::LEQ:
                    for ($column = 0; $column < $internalTableHeight; $column++) {
                        if ($column === $row) {
                            $this->internalTable->setKey($row, $internalTableWidth + $column, new Fraction(1));
                        } else {
                            $this->internalTable->setKey($row, $internalTableWidth + $column, new Fraction(0));
                        }
                    }
                    break;
                case Sign::GEQ:
                case Sign::EQ:
                    break;
            }
        }
    }

    /**
     * @param Problem $problem
     * @return void
     */
    public function setLimits(Problem $problem): void
    {
        /** @var Problem\ProblemEquation $problemEquation */
        foreach ($problem->getProblemEquations() as $problemEquation) {
            $this->limits->add($problemEquation->getLimit());
        }
    }

    /**
     * @param Problem $problem
     * @return void
     */
    public function setObjectiveFunction(Problem $problem): void
    {
        $this->objectiveFunction = $problem->getObjectiveFunction();
    }
}