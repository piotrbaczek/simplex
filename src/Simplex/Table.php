<?php

namespace pbaczek\simplex\Simplex;

use pbaczek\simplex\Equation;
use pbaczek\simplex\EquationsCollection;
use pbaczek\simplex\Fraction;
use pbaczek\simplex\FractionsCollection;

/**
 * Class Table
 * @package pbaczek\simplex\Simplex
 */
final class Table
{
    /** @var Fraction[][] */
    private $table;

    /** @var FractionsCollection $targetFunction */
    private $targetFunction;

    /**
     * Table constructor.
     * @param EquationsCollection $equationsCollection
     * @param FractionsCollection $targetFunction
     */
    public function __construct(EquationsCollection $equationsCollection, FractionsCollection $targetFunction)
    {
        /**
         * @var int $key
         * @var Equation $equation
         */
        foreach ($equationsCollection as $key => $equation) {

            var_dump($equation);
            die();
        }
        $this->targetFunction = $targetFunction;
    }

    /**
     * @return Fraction[]
     */
    public function getTable(): array
    {
        return $this->table;
    }
}