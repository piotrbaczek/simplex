<?php

namespace pbaczek\simplex\Simplex\Solver;

use pbaczek\simplex\EquationsCollection;
use pbaczek\simplex\FractionsCollection;
use pbaczek\simplex\Simplex\Table;
use pbaczek\simplex\Simplex\TablesCollection;

/**
 * Class SolverAbstract
 * @package pbaczek\simplex\Simplex\Solver
 */
abstract class SolverAbstract
{
    /** @var TablesCollection $tableCollection */
    private $tableCollection;

    /**
     * SolverAbstract constructor.
     * @param EquationsCollection $equationsCollection
     * @param FractionsCollection $targetFunction
     * @param bool $maximize
     */
    public function __construct(EquationsCollection $equationsCollection, FractionsCollection $targetFunction, bool $maximize = true)
    {
        $this->tableCollection = new TablesCollection();

        $table = new Table($equationsCollection);
        $this->tableCollection->add($table);
    }

    /**
     * Solve the problem
     * @return void
     */
    public function solve(): void
    {
        $this->validate();
        $this->run();
    }

    /**
     * Solve the problem
     * @return void
     */
    protected abstract function run(): void;

    /**
     * Validate parameters
     * @return void
     */
    protected abstract function validate(): void;

    /**
     * @return TablesCollection
     */
    public function getTableCollection(): TablesCollection
    {
        return $this->tableCollection;
    }
}