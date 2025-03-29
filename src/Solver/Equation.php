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
     * Zero all elements
     * @return Equation
     */
    public function zero(): static
    {
        $this->data = $this->map(function (){
            return new Fraction(0);
        })->toArray();

        return $this;
    }
}