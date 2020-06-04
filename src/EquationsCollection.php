<?php

namespace pbaczek\simplex;

use Ramsey\Collection\AbstractCollection;

/**
 * Class EquationsCollection
 * @package pbaczek\simplex
 */
final class EquationsCollection extends AbstractCollection
{
    /**
     * Returns the type associated with this collection.
     * @return string
     */
    public function getType(): string
    {
        return Equation::class;
    }
}