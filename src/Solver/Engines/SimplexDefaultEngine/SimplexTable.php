<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Problem;
use pbaczek\simplex\Solver\Solution\FractionsCollection;

class SimplexTable
{
    public static function fromProblem(Problem $problem): static
    {
        return new static();
    }

    public function getSolutionPoints(): FractionsCollection
    {
        // @TODO implement
        return new FractionsCollection([new Fraction(0), new Fraction(0)]);
    }

    public function getSolutionValue(): Fraction
    {
        // @TODO implement
        return new Fraction(0);
    }
}