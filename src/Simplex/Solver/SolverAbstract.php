<?php

namespace pbaczek\simplex\Simplex\Solver;

use pbaczek\simplex\EquationsCollection;
use pbaczek\simplex\FractionsCollection;
use pbaczek\simplex\Simplex\Table;
use pbaczek\simplex\Simplex\TableCollection;

/**
 * Class SolverAbstract
 * @package pbaczek\simplex\Simplex\Solver
 */
abstract class SolverAbstract
{
    /** @var TableCollection $tableCollection */
    private $tableCollection;

    /**
     * SolverAbstract constructor.
     * @param EquationsCollection $equationsCollection
     * @param FractionsCollection $targetFunction
     * @param bool $maximize
     */
    public function __construct(EquationsCollection $equationsCollection, FractionsCollection $targetFunction, bool $maximize = true)
    {
        $this->tableCollection = new TableCollection();

        $table = new Table($equationsCollection, $targetFunction);
        $this->tableCollection->add($table);
    }

    /**
     * Solve the problem
     * @return void
     */
    public function run(): void
    {
        $this->validate();
        $this->solve();
    }

    /**
     * Solve the problem
     * @return void
     */
    protected abstract function solve(): void;

    /**
     * Validate parameters
     * @return void
     */
    protected abstract function validate(): void;
}