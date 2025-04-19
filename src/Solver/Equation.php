<?php

namespace pbaczek\simplex\Solver;

use pbaczek\fraction\MFraction;
use Ramsey\Collection\AbstractCollection;

class Equation extends AbstractCollection
{
    public function getType(): string
    {
        return MFraction::class;
    }

    /**
     * Make all elements of array equal to Zero
     * @return Equation
     */
    public function fillWithZeros(): static
    {
        $this->data = array_map(function () {
            return new MFraction(0);
        }, $this->data);

        return $this;
    }
}