<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RandomClass
 *
 * @author PETTER
 */
class RandomClass {

    public $array;

    public function __construct() {
        $this->array = Array(1, 5, 2, 4, 3, 6);
    }

    public function getArray() {
        return $this->array;
    }

    public function go() {
        for ($i = $this->getArray()[0]; $i < $this->getArray()[1]; $i++) {
            for ($j = $this->getArray()[2]; $j < $this->getArray()[3]; $j++) {
                for ($k = $this->getArray()[4]; $k < $this->getArray()[5]; $k++) {
                    echo $i . '|' . $j . '|' . $k . PHP_EOL;
                }
            }
        }
    }

}
