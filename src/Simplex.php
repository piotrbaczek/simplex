<?php

namespace pbaczek\simplex;

use Laminas\Text\Table\Column;
use Laminas\Text\Table\Row;
use pbaczek\simplex\Simplex\Exceptions\Printer\InvalidPrinterException;
use pbaczek\simplex\Simplex\Printer\PrinterAbstract;
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
     * @param string $printerAbstractClassName
     * @return \Laminas\Text\Table\Table
     * @throws InvalidPrinterException
     */
    public function print(string $printerAbstractClassName): string
    {
        /** @var PrinterAbstract $printer */
        $printer = new $printerAbstractClassName($this);
        if ($printer instanceof PrinterAbstract === false) {
            throw new InvalidPrinterException();
        }

        return $printer->print();
    }
}