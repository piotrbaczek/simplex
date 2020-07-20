<?php

namespace pbaczek\simplex\Simplex;

use Ramsey\Collection\AbstractCollection;

/**
 * Class TablesCollection
 * @package pbaczek\simplex\Simplex
 */
class TablesCollection extends AbstractCollection
{

    /**
     * Returns the type associated with this collection.
     */
    public function getType(): string
    {
        return Table::class;
    }
}