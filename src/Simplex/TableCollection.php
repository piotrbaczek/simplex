<?php

namespace pbaczek\simplex\Simplex;

use Ramsey\Collection\AbstractCollection;

/**
 * Class TableCollection
 * @package pbaczek\simplex\Simplex
 */
class TableCollection extends AbstractCollection
{

    /**
     * Returns the type associated with this collection.
     */
    public function getType(): string
    {
        return Table::class;
    }
}