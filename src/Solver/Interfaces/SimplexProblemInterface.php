<?php

namespace pbaczek\simplex\Solver\Interfaces;

interface SimplexProblemInterface
{
    public function validate(): bool;

    public function getErrors(): array;
}