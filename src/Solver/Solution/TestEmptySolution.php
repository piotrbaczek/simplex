<?php

namespace pbaczek\simplex\Solver\Solution;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Solution;

class TestEmptySolution extends Solution
{
    public function __construct()
    {
        parent::__construct(
            new Solution\FractionsCollection(
                [
                    new Fraction(0),
                    new Fraction(0)
                ]
            ),
            new Fraction(0));
    }
}