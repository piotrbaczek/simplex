<?php

namespace pbaczek\simplex\Simplex;

use Ramsey\Collection\AbstractCollection;

/**
 * Class FractionCollectionsCollection
 * @package pbaczek\simplex\Simplex
 */
class FractionCollectionsCollection extends AbstractCollection
{
    /**
     * Returns the type associated with this collection.
     */
    public function getType(): string
    {
        return FractionCollection::class;
    }
}