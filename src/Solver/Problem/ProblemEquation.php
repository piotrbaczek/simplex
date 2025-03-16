<?php

namespace pbaczek\simplex\Solver\Problem;

use pbaczek\fraction\Fraction;
use pbaczek\simplex\Solver\Dictionaries\Sign;
use pbaczek\simplex\Solver\Equation;

class ProblemEquation
{
    private Equation $equation;
    private Sign $sign;
    private Fraction $limit;

    public function __construct(Equation $equation, Sign $sign, Fraction $limit)
    {
        $this->equation = $equation;
        $this->sign = $sign;
        $this->limit = $limit;
    }

    public function getEquation(): Equation
    {
        return $this->equation;
    }

    public function getSign(): Sign
    {
        return $this->sign;
    }

    public function getLimit(): Fraction
    {
        return $this->limit;
    }
}