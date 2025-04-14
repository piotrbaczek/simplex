<?php

namespace pbaczek\simplex\Solver\Engines\SimplexDefaultEngine\SimplexTable;

use pbaczek\fraction\FractionAbstract;
use pbaczek\fraction\MFraction;

class InternalTable
{
    private array $table;

    public function __construct()
    {
        $this->table = [];
    }

    public function getKey(int $row, int $column): MFraction
    {
        return $this->table[$row][$column];
    }

    public function setKey(int $row, int $column, MFraction $fraction): void
    {
        $this->table[$row][$column] = $fraction;
    }

    public function toArray(): array
    {
        return $this->table;
    }
}