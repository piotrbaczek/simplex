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
    public $array2;

    public function __construct() {
        $this->array = Array(0, 4, 2, 4, 3, 6);
        $this->array2 = $this->array;
    }

    public function getArray() {
        return $this->array;
    }

    public function go() {
        $this->array[1] = count($this->array2);
        for ($i = $this->getArray()[0]; $i < $this->getArray()[1]; $i++) {
            $this->array2[$i] = 0;
        }
        if (max($this->array2) == 0) {
            echo 'Here you can cast functions as arrays<br/>';
        } else {
            echo 'Here you <b>can\'t</b> cast functions as arrays<br/>';
        }
    }

}
