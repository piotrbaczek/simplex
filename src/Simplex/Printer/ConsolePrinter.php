<?php

namespace pbaczek\simplex\Simplex\Printer;

use Laminas\Text\Table\Column;
use Laminas\Text\Table\Row;
use Laminas\Text\Table\Table;
use pbaczek\simplex\FractionAbstract;
use pbaczek\simplex\Simplex;

/**
 * Class ConsolePrinter
 * @package pbaczek\simplex\Simplex\Printer
 */
class ConsolePrinter extends PrinterAbstract
{
    private const LAMINAS_TABLE_COLUMNWIDTHS = 'columnWidths';

    /** @var Simplex $simplex */
    private $simplex;

    /**
     * ConsolePrinter constructor.
     * @param Simplex $simplex
     */
    public function __construct(Simplex $simplex)
    {
        $this->simplex = $simplex;
    }

    /**
     * Print solution
     * @return string
     */
    public function print(): string
    {
        $table = new Table([
            self::LAMINAS_TABLE_COLUMNWIDTHS => array_fill(0, count(current($this->simplex->getTableCollection()->first()->getTable())), 10)
        ]);

        foreach ($this->simplex->getTableCollection() as $simplexTable) {

            foreach ($simplexTable->getTable() as $rowIndex => $rowValues) {
                $row = new Row();

                /** @var FractionAbstract $rowValue */
                foreach ($rowValues as $rowValue) {
                    $row->appendColumn(new Column((string)$rowValue));
                }

                $table->appendRow($row);
            }
        }

        return $table;
    }
}