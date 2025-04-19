<?php

namespace pbaczek\simplex\Solver\Interfaces;

interface IndexCompareInterface
{
    public function hasSameIndex(int $index): bool;
}