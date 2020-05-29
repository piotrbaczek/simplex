<?php

namespace pbaczek\simplex\Simplex;

use pbaczek\simplex\Simplex\Sign\SignAbstract;
use Ramsey\Collection\AbstractCollection;

/**
 * Class SignCollection
 * @package pbaczek\simplex\Simplex
 */
class SignCollection extends AbstractCollection
{
    /**
     * Returns the type associated with this collection.
     */
    public function getType(): string
    {
        return SignAbstract::class;
    }
}