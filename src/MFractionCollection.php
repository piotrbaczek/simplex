<?php

namespace pbaczek\simplex;

use Ramsey\Collection\AbstractCollection;

/**
 * Class MFractionCollection
 * @package pbaczek\simplex
 */
class MFractionCollection extends AbstractCollection
{
    /**
     * Returns the type associated with this collection.
     */
    public function getType(): string
    {
        return MFraction::class;
    }
}