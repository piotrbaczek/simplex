<?php

namespace pbaczek\simplex\Simplex\Solver;

use pbaczek\simplex\EquationsCollection;
use pbaczek\simplex\FractionsCollection;

/**
 * Class SolverAbstract
 * @package pbaczek\simplex\Simplex\Solver
 */
abstract class SolverAbstract
{
    /** @var EquationsCollection $equationsCollection */
    protected $equationsCollection;

    /** @var bool $maximize */
    protected $maximize;

    /** @var FractionsCollection $targetFunction */
    protected $targetFunction;

    /**
     * SolverAbstract constructor.
     * @param EquationsCollection $equationsCollection
     * @param FractionsCollection $targetFunction
     * @param bool $maximize
     */
    public function __construct(EquationsCollection $equationsCollection, FractionsCollection $targetFunction, bool $maximize = true)
    {
        $this->equationsCollection = $equationsCollection;
        $this->targetFunction = $targetFunction;
        $this->maximize = $maximize;
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