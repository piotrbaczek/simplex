<?php

namespace pbaczek\simplex\Simplex;

use pbaczek\simplex\FractionAbstract;
use Ramsey\Collection\AbstractCollection;

/**
 * Class FractionCollection
 * @package pbaczek\simplex\Simplex
 */
class FractionCollection extends AbstractCollection
{
    /**
     * @inheritDoc
     * @return string
     */
    public function getType(): string
    {
        return FractionAbstract::class;
    }
}