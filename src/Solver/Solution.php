<?php

namespace pbaczek\simplex\Solver;

use Override;
use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTableCollection;
use pbaczek\simplex\Solver\Interfaces\SimplexSolutionInterface;
use pbaczek\simplex\Solver\Solution\FractionsCollection;

class Solution implements SimplexSolutionInterface
{
    private Fraction $value;

    private FractionsCollection $pointCoordinates;
    private SimplexTableCollection $simplexTables;

    public function __construct(FractionsCollection $pointCoordinates, Fraction $value, SimplexTableCollection $simplexTables)
    {
        $this->pointCoordinates = $pointCoordinates;
        $this->value = $value;
        $this->simplexTables = $simplexTables;
    }

    #[Override] public function getSolutionValue(): Fraction
    {
        return $this->value;
    }

    #[Override] public function getPointCoordinates(): FractionsCollection
    {
        return $this->pointCoordinates;
    }

    #[Override] public function getSimplexTables(): SimplexTableCollection
    {
        return $this->simplexTables;
    }
}