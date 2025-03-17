<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine;

use pbaczek\fraction\Fraction;
use pbaczek\fraction\MFraction;
use pbaczek\simplex\Solver\Dictionaries\Sign;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable\InternalTable;
use pbaczek\simplex\Solver\Equation;
use pbaczek\simplex\Solver\Problem;
use pbaczek\simplex\Solver\Solution\FractionsCollection;
use Ramsey\Collection\Sort;

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

        $this->setBasicVariables($internalTableHeight, $internalTableWidth, $problem);

        $this->setNonBasicVariables($internalTableHeight, $internalTableWidth, $problem);

        $this->setObjectiveFunction($problem);

        $this->setLimits($problem);
    }

    public function findPivotColumn(): int
    {
        $sortedCollection = $this->objectiveFunction
            ->filter(function (Fraction $element) {
                return $element->getRealValue() < 0;
            })
            ->sort('getRealValue', Sort::Ascending);

        /** @var Fraction $lowestValue */
        $lowestValue = $sortedCollection->first();

        foreach ($this->objectiveFunction->getIterator() as $index => $parameter) {
            if ($lowestValue->equals($parameter)) {
                return $index;
            }
        }

        return -1;
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
        $limits = $this->limits->toArray();
        $objectiveFunction = $this->objectiveFunction->toArray(); // Bottom row

        // Add the $limits column to $data
        foreach ($data as $index => &$row) {
            $row[] = $limits[$index] ?? ''; // Append limit column
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

    /**
     * @param int $internalTableHeight
     * @param Problem $problem
     * @param int $internalTableWidth
     * @return void
     */
    private function setBasicVariables(int $internalTableHeight, int $internalTableWidth, Problem $problem): void
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
     * @param int $internalTableWidth
     * @param Problem $problem
     * @return void
     */
    private function setNonBasicVariables(int $internalTableHeight, int $internalTableWidth, Problem $problem): void
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
                    for ($column = 0; $column < $internalTableHeight; $column++) {
                        if ($column === $row) {
                            $this->internalTable->setKey($row, $internalTableWidth + $column, new MFraction(0, 1, -1, 1));
                        } else {
                            $this->internalTable->setKey($row, $internalTableWidth + $column, new Fraction(0));
                        }
                    }
                    break;
            }
        }
    }

    /**
     * @param Problem $problem
     * @return void
     */
    private function setLimits(Problem $problem): void
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
    private function setObjectiveFunction(Problem $problem): void
    {
        $this->objectiveFunction = $problem->getObjectiveFunction();

        /** @var Fraction $objectiveFunctionParameter */
        foreach ($this->objectiveFunction as $objectiveFunctionParameter) {
            $objectiveFunctionParameter->changeSign();
        }

        // Fill with zeros
        foreach ($problem->getProblemEquations() as $ignored) {
            $this->objectiveFunction->add(new Fraction(0));
        }
    }
}