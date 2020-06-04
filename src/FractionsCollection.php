<?php

namespace pbaczek\simplex;

use Ramsey\Collection\AbstractCollection;

/**
 * Class FractionsCollection
 * @package pbaczek\simplex
 */
final class FractionsCollection extends AbstractCollection
{
    /**
     * Returns the type associated with this collection.
     */
    public function getType(): string
    {
        return Fraction::class;
    }
}