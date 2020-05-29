<?php

namespace pbaczek\simplex\Simplex\Solver;

use pbaczek\simplex\Simplex\FractionCollection;
use pbaczek\simplex\Simplex\FractionCollectionsCollection;
use pbaczek\simplex\Simplex\SignCollection;

/**
 * Class SolverAbstract
 * @package pbaczek\simplex\Simplex\Solver
 */
abstract class SolverAbstract
{
    /** @var FractionCollectionsCollection $variables */
    protected $variables;

    /** @var SignCollection */
    protected $signs;

    /** @var FractionCollection $boundaries */
    protected $boundaries;

    /** @var FractionCollection $targetFunction */
    protected $targetFunction;

    /** @var bool $max */
    private $max;

    /**
     * SolverAbstract constructor.
     * @param array $variables
     * @param SignCollection $signs
     * @param FractionCollection $boundaries
     * @param FractionCollection $targetFunction
     * @param bool $isMax
     */
    public function __construct(array $variables, SignCollection $signs, FractionCollection $boundaries, FractionCollection $targetFunction, bool $isMax = true)
    {
        $this->variables = $variables;
        $this->signs = $signs;
        $this->boundaries = $boundaries;
        $this->targetFunction = $targetFunction;
        $this->max = $isMax;
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