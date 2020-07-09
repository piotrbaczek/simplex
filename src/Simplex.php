<?php

namespace pbaczek\simplex;

use Laminas\Text\Table\Column;
use Laminas\Text\Table\Row;
use pbaczek\simplex\Simplex\Solver\SolverAbstract;
use pbaczek\simplex\Simplex\Table;
use pbaczek\simplex\Simplex\TableCollection;

/**
 * Class Simplex
 * @package pbaczek\simplex
 */
class Simplex
{
    /** @var SolverAbstract $solver */
    private $solver;

    /**
     * Simplex constructor.
     * @param SolverAbstract $solverAbstract
     */
    public function __construct(SolverAbstract $solverAbstract)
    {
        $this->solver = $solverAbstract;
    }

    /**
     * Solve equation
     * @return void
     */
    public function solve(): void
    {
        $this->solver->solve();
    }

    /**
     * Get Tables
     * @return TableCollection|Table[]
     */
    public function getTableCollection(): TableCollection
    {
        return $this->solver->getTableCollection();
    }

    /**
     * @return \Laminas\Text\Table\Table
     */
    public function getConsolePrintableTablesCollection(): \Laminas\Text\Table\Table
    {
        $table = new \Laminas\Text\Table\Table([
            'columnWidths' => array_fill(0, count(current($this->getTableCollection()->first()->getTable())), 10)
        ]);

        foreach ($this->getTableCollection() as $simplexTable) {

            foreach ($simplexTable->getTable() as $rowIndex => $rowValues) {
                $row = new Row();

                /** @var FractionAbstract $rowValue */
                foreach ($rowValues as $rowValue) {
                    $row->appendColumn(new Column((string)$rowValue));
                }

                $table->appendRow($row);
            }
        }

        return $table;
    }
}