<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

use pbaczek\fraction\Fraction;

class InternalTable
{
    private array $table;

    public function __construct()
    {
        $this->table = [];
    }

    public function getKey(int $row, int $column): Fraction
    {
        return $this->table[$row][$column];
    }

    public function setKey(int $row, int $column, Fraction $fraction): void
    {
        $this->table[$row][$column] = $fraction;
    }

    public function toArray(): array
    {
        return $this->table;
    }
}