<?php

namespace pbaczek\simplex\Solver\Engines;

use Override;
use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Interfaces\SimplexEngineInterface;
use pbaczek\simplex\Solver\Interfaces\SimplexSolutionInterface;
use pbaczek\simplex\Solver\Solution;

class SimplexIntegerSolutionEngine implements SimplexEngineInterface
{
    #[Override] public function solve(): SimplexSolutionInterface
    {
        return new Solution(
            new Solution\FractionsCollection(
                [
                    new Fraction(0),
                    new Fraction(0)
                ]
            ),
            new Fraction(0)
        );
    }
}