<?php

namespace pbaczek\simplex\Simplex;

use Ramsey\Collection\AbstractCollection;

/**
 * Class BaseIndexesCollection
 * @package pbaczek\simplex\Simplex
 */
class BaseIndexesCollection extends AbstractCollection
{
    /**
     * Returns the type associated with this collection.
     */
    public function getType(): string
    {
        return BaseIndex::class;
    }
}