<?php

namespace pbaczek\simplex\Simplex;

/**
 * Class BaseIndex
 * @package pbaczek\simplex\Simplex
 */
class BaseIndex
{
    /** @var int $index */
    private $index;

    /** @var string $variable */
    private $variable;

    /**
     * BaseIndex constructor.
     * @param int $index
     * @param string $variable
     */
    public function __construct(int $index, string $variable = 'x')
    {
        $this->index = abs($index);
        $this->variable = $variable;
    }

    /**
     * @inheritDoc
     * @return string
     */
    public function __toString()
    {
        return $this->variable . $this->index;
    }
}