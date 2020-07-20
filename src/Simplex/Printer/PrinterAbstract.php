<?php

namespace pbaczek\simplex\Simplex\Printer;

/**
 * Class PrinterAbstract
 * @package pbaczek\simplex\Simplex\Printer
 */
abstract class PrinterAbstract
{
    /**
     * Print solution
     * @return string
     */
    public abstract function print(): string;
}