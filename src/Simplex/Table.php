<?php

namespace pbaczek\simplex\Simplex;

use pbaczek\simplex\Equation;
use pbaczek\simplex\EquationsCollection;
use pbaczek\simplex\Fraction;
use pbaczek\simplex\MFraction;
use pbaczek\simplex\MFractionCollection;

/**
 * Class Table
 * @package pbaczek\simplex\Simplex
 */
final class Table
{
    /** @var Fraction[][] */
    private $table = [];

    /** @var BaseIndexCollection|BaseIndex[] $basisVariables */
    private $basisVariables;

    /** @var BaseIndexCollection|BaseIndex[] $nonBasisVariables */
    private $nonBasisVariables;

    /** @var MFractionCollection|MFraction[] $cCoefficients */
    private $cCoefficients;

    /**
     * Table constructor.
     * @param EquationsCollection $equationsCollection
     */
    public function __construct(EquationsCollection $equationsCollection)
    {
        $this->cCoefficients = new MFractionCollection();
        $this->basisVariables = new BaseIndexCollection();
        $this->nonBasisVariables = new BaseIndexCollection();

        $equationsCount = $equationsCollection->count();

        /**
         * @var int $key
         * @var Equation $equation
         */
        foreach ($equationsCollection as $equationKey => $equation) {
            $this->fillVariables($equation, $equationKey);
            $this->fillSigns($equation, $equationsCount, $equationKey);
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
     * @param $equationKey
     */
    private function fillSigns(Equation $equation, int $equationsCount, $equationKey): void
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
                $this->cCoefficients[$equationKey] = new MFraction(0);
                break;
            case Equation\Sign\Dictionary\SignCharacter::EQUAL:
                //@TODO
                break;
            case Equation\Sign\Dictionary\SignCharacter::GREATER_OR_EQUAL:
                //@TODO
                break;
        }
    }

    /**
     * @param Equation $equation
     * @param $equationKey
     */
    private function fillBoundary(Equation $equation, $equationKey): void
    {
        $this->table[$equationKey][] = clone $equation->getBoundary();
    }

    /**
     * @return BaseIndex[]|BaseIndexCollection
     */
    public function getBasisVariables()
    {
        return $this->basisVariables;
    }

    /**
     * @return BaseIndex[]|BaseIndexCollection
     */
    public function getNonBasisVariables()
    {
        return $this->nonBasisVariables;
    }
}