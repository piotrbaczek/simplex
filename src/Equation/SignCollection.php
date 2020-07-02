<?php

namespace pbaczek\simplex\Equation;

use pbaczek\simplex\Equation\Sign\SignAbstract;
use Ramsey\Collection\AbstractCollection;

/**
 * Class SignCollection
 * @package pbaczek\simplex\Simplex
 */
final class SignCollection extends AbstractCollection
{
    /**
     * Returns the type associated with this collection.
     */
    public function getType(): string
    {
        return SignAbstract::class;
    }
}