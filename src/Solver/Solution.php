<?php

namespace pbaczek\simplex\Solver;

use Override;
use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Interfaces\SimplexSolutionInterface;
use pbaczek\simplex\Solver\Solution\FractionsCollection;

class Solution implements SimplexSolutionInterface
{
    private Fraction $value;

    private FractionsCollection $pointCoordinates;

    public function __construct(FractionsCollection $pointCoordinates, Fraction $value)
    {
        $this->pointCoordinates = $pointCoordinates;
        $this->value = $value;
    }

    #[Override] public function getSolutionValue(): Fraction
    {
        return $this->value;
    }

    #[Override] public function getPointCoordinates(): FractionsCollection
    {
        return $this->pointCoordinates;
    }
}