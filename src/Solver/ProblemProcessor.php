<?php

namespace pbaczek\simplex\Solver;

use pbaczek\fraction\MFraction;
use pbaczek\simplex\Solver\Dictionaries\Sign;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

final class ProblemProcessor
{
    private SimplexTable $simplexTable;
    private SimplexTable\InternalTable $internalTable;

    public function __construct()
    {
        $this->simplexTable = new SimplexTable();
        $this->internalTable = new SimplexTable\InternalTable();
    }

    public function process(Problem $problem): array
    {
        $internalTableHeight = $problem->getProblemEquations()->count();

        $internalTableWidth = $problem->getProblemEquations()->first()->getEquation()->count();

        $this->setBasicVariables($internalTableHeight, $internalTableWidth, $problem);

        $this->setNonBasicVariables($internalTableHeight, $internalTableWidth, $problem);

        $this->setObjectiveFunctionFromProblem($problem);

        $this->setLimitsFromProblem($problem);

        $this->setResourcesAtPointFromProblem($problem);

        $this->setObjectiveFunctionAtPoint();

        return [$this->simplexTable, $this->internalTable];
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
                $this->internalTable->setKey($row, $column, MFraction::from($element->getEquation()->offsetGet($column)));
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
                            $this->internalTable->setKey($row, $internalTableWidth + $column, new MFraction(1));
                        } else {
                            $this->internalTable->setKey($row, $internalTableWidth + $column, new MFraction(0));
                        }
                    }
                    break;
                case Sign::GEQ:
                case Sign::EQ:
                    for ($column = 0; $column < $internalTableHeight; $column++) {
                        if ($column === $row) {
                            $this->internalTable->setKey($row, $internalTableWidth + $column, new MFraction(0, 1, -1, 1));
                        } else {
                            $this->internalTable->setKey($row, $internalTableWidth + $column, new MFraction(0));
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
    private function setLimitsFromProblem(Problem $problem): void
    {
        /** @var Problem\ProblemEquation $problemEquation */
        foreach ($problem->getProblemEquations() as $problemEquation) {
            $this->simplexTable->getLimits()->add(MFraction::from($problemEquation->getLimit()));
        }
    }

    /**
     * @param Problem $problem
     * @return void
     */
    private function setObjectiveFunctionFromProblem(Problem $problem): void
    {
        $objectiveFunction = clone $problem->getObjectiveFunction();

        // Fill with zeros
        foreach ($problem->getProblemEquations() as $ignored) {
            $objectiveFunction->add(new MFraction(0));
        }

        $this->simplexTable->setObjectiveFunction($objectiveFunction);
    }

    private function setResourcesAtPointFromProblem(Problem $problem): void
    {
        /** @var MFraction $ignored */
        foreach ($problem->getObjectiveFunction() as $ignored) {
            $this->simplexTable->getResourcesAtPoint()->add(new MFraction(0));
        }

        /** @var Equation $ignored */
        foreach ($problem->getProblemEquations() as $ignored) {
            $this->simplexTable->getResourcesAtPoint()->add(new MFraction(0));
        }
    }

    private function setObjectiveFunctionAtPoint(): void
    {
        /**
         * @var int $index
         * @var MFraction $objectiveFunctionParameter
         */
        foreach ($this->simplexTable->getObjectiveFunction() as $index => $objectiveFunctionParameter) {
            $remainingResourceAtPoint = MFraction::from($this->simplexTable->getResourcesAtPoint()->offsetGet($index));
            $remainingResourceAtPoint->subtract($objectiveFunctionParameter);
            $this->simplexTable->getObjectiveFunctionAtPoint()->offsetSet($index, $remainingResourceAtPoint);
        }
    }
}