<?php

namespace pbaczek\simplex\Simplex;

use Ramsey\Collection\AbstractCollection;

/**
 * Class BaseIndexCollection
 * @package pbaczek\simplex\Simplex
 */
class BaseIndexCollection extends AbstractCollection
{
    /**
     * Returns the type associated with this collection.
     */
    public function getType(): string
    {
        return BaseIndex::class;
    }
}