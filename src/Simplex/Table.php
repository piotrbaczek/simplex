<?php

namespace pbaczek\simplex\Simplex;

use Exception;
use pbaczek\simplex\Equation;
use pbaczek\simplex\EquationsCollection;
use pbaczek\simplex\Fraction;
use pbaczek\simplex\FractionsCollection;
use pbaczek\simplex\MFraction;

/**
 * Class Table
 * @package pbaczek\simplex\Simplex
 */
final class Table
{
    /** @var Fraction[][] */
    private $table = [];

    /** @var BaseIndexesCollection|BaseIndex[] $basisVariables */
    private $basisVariables;

    /** @var BaseIndexesCollection|BaseIndex[] $nonBasisVariables */
    private $nonBasisVariables;

    /** @var FractionsCollection|Fraction[] $cCoefficients */
    private $cCoefficients;

    /** @var int $pivotRow */
    private $pivotRow = -1;

    /** @var int $pivotColumn */
    private $pivotColumn = -1;

    /**
     * Table constructor.
     * @param EquationsCollection $equationsCollection
     * @throws Exception
     */
    public function __construct(EquationsCollection $equationsCollection)
    {
        $this->cCoefficients = new FractionsCollection();
        $this->basisVariables = new BaseIndexesCollection();
        $this->nonBasisVariables = new BaseIndexesCollection();

        $equationsCount = $equationsCollection->count();

        /**
         * @var int $key
         * @var Equation $equation
         */
        foreach ($equationsCollection as $equationKey => $equation) {
            $this->fillVariables($equation, $equationKey);
            $this->fillArtificialVariables($equation, $equationsCount, $equationKey);
            $this->fillBoundary($equation, $equationKey);
        }
    }

    /**
     * @return Fraction[]
     */
    public function getTable(): array
    {
        return $this->table;
    }

    /**
     * @param Equation $equation
     * @param $equationKey
     */
    private function fillVariables(Equation $equation, $equationKey): void
    {
        foreach ($equation->getVariables() as $variable) {
            $this->table[$equationKey][] = clone $variable;
        }
    }

    /**
     * @param Equation $equation
     * @param int $equationsCount
     * @param int $equationKey
     * @throws Exception
     */
    private function fillArtificialVariables(Equation $equation, int $equationsCount, int $equationKey): void
    {
        switch ($equation->getSign()->getSignCharacter()) {
            case Equation\Sign\Dictionary\SignCharacter::LESS_OR_EQUAL:
                for ($i = 0; $i < $equationsCount; $i++) {
                    if ($i === $equationKey) {
                        $this->table[$equationKey][] = new MFraction(1);
                    } else {
                        $this->table[$equationKey][] = new MFraction(0);
                    }
                }
                $this->cCoefficients[$equationKey] = new Fraction(0);
                break;
            case Equation\Sign\Dictionary\SignCharacter::EQUAL:
                //@TODO
                throw new Exception('TODO');
            case Equation\Sign\Dictionary\SignCharacter::GREATER_OR_EQUAL:
                //@TODO
                throw new Exception('TODO');
        }
    }

    /**
     * @param Equation $equation
     * @param int $equationKey
     */
    private function fillBoundary(Equation $equation, int $equationKey): void
    {
        $this->table[$equationKey][] = clone $equation->getBoundary();
    }

    /**
     * @return BaseIndex[]|BaseIndexesCollection
     */
    public function getBasisVariables()
    {
        return $this->basisVariables;
    }

    /**
     * @return BaseIndex[]|BaseIndexesCollection
     */
    public function getNonBasisVariables()
    {
        return $this->nonBasisVariables;
    }
}