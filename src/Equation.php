<?php

namespace pbaczek\simplex;

use pbaczek\simplex\Equation\Sign\SignAbstract;

/**
 * Class Equation
 * @package pbaczek\simplex
 */
final class Equation
{
    /** @var FractionsCollection $variables */
    private $variables;

    /** @var SignAbstract $sign */
    private $sign;

    /** @var Fraction $boundary */
    private $boundary;

    /**
     * Equation constructor.
     * @param FractionsCollection $variables
     * @param SignAbstract $sign
     * @param Fraction $boundary
     */
    public function __construct(FractionsCollection $variables, SignAbstract $sign, Fraction $boundary)
    {
        $this->variables = $variables;
        $this->sign = $sign;
        $this->boundary = $boundary;
    }

    /**
     * @return FractionsCollection
     */
    public function getVariables(): FractionsCollection
    {
        return $this->variables;
    }

    /**
     * @return SignAbstract
     */
    public function getSign(): SignAbstract
    {
        return $this->sign;
    }

    /**
     * @return Fraction
     */
    public function getBoundary(): Fraction
    {
        return $this->boundary;
    }
}