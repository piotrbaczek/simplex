<?php

namespace pbaczek\simplex\Solver\Interfaces;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTableCollection;
use pbaczek\simplex\Solver\Solution\FractionsCollection;

interface SimplexSolutionInterface
{
    public function getSolutionValue(): Fraction;

    public function getPointCoordinates(): FractionsCollection;

    public function getSimplexTables(): SimplexTableCollection;
}