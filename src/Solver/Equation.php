<?php

namespace pbaczek\simplex\Solver;

use pbaczek\fraction\Fraction;
use Ramsey\Collection\AbstractCollection;

class Equation extends AbstractCollection
{
    public function getType(): string
    {
        return Fraction::class;
    }

    /**
     * Make all elements of array equal to Zero
     * @return Equation
     */
    public function fillWithZeros(): static
    {
        $this->data = array_map(function () {
            return new Fraction(0);
        }, $this->data);

        return $this;
    }
}