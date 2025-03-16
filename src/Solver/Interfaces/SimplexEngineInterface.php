<?php

namespace pbaczek\simplex\Solver\Interfaces;

interface SimplexEngineInterface
{
    public function solve(): SimplexSolutionInterface;
}