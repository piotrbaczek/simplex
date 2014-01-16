<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Point
 *
 * @author PETTER
 */
class Point {

	private $array;

	public function __construct($size = 0) {
		if ($size == 0) {
			throw new Exception('Unspecified size of array in ' . __FUNCTION__);
		} else {
			$this->array = Array();
			for ($i = 0; $i < $size; $i++) {
				$this->array[$i] = 0;
			}
		}
	}

	public function resetPoint() {
		for ($i = 0; $i < count($this->array); $i++) {
			$this->array[$i] = 0;
		}
	}

	public function setPointDimension($key, $value) {
		if ($key >= $this->getPointDimensionAmount()) {
			throw new Exception(__FUNCTION__ . ' array exceeded. (' . $key . ':' . $this->getPointDimensionAmount() . ')');
		} else {
			$this->array[$key] = $value;
		}
	}

	public function toArray() {
		return $this->array;
	}

	public function getPointDimensionAmount() {
		return count($this->array);
	}

	public function multiplyBy(Point $p) {
		if ($this->getPointDimensionAmount() == $p->getPointDimensionAmount()) {
			$sum = 0;
			$remote = (Array) $p->toArray();
			foreach ($this->toArray() as $key => $value) {
				$sum+=($value * $remote[$key]);
			}
			return $sum;
		} else {
			throw new Exception('Size of arrays are not equal ' . __FUNCTION__);
		}
	}

	public function __toString() {
		$x = $this->toArray();
		$string = '[';
		foreach ($x as $value) {
			$string.=round($value, 2) . ';';
		}
		$string = substr($string, 0, -1);
		$string.=']';
		return $string;
	}

}
