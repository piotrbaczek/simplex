<?php

/**
 * Generates array of $size-dimensional point coefficients
 * @example
 * A=[0,1,2,3,4,5]
 * $a=new Point();
 * $a->setPointDimension(0,0);
 * * $a->setPointDimension(1,1);
 * * $a->setPointDimension(2,2);
 * * $a->setPointDimension(3,3);
 * * $a->setPointDimension(4,4);
 * * $a->setPointDimension(5,5);
 * echo $a;
 *
 * @author PETTER
 * @version 1.0
 * @deprecated
 */
class Point {

	private $array;
	private $size;

	/**
	 * Construct array of size $size
	 * @param Integer $size
	 * @throws Exception
	 */
	public function __construct($size = 0) {
		if ($size == 0) {
			throw new Exception('Unspecified size of array in ' . __FUNCTION__);
		} else {
			$this->size = $size;
			$this->array = Array();
			for ($i = 0; $i < $this->size; $i++) {
				$this->array[$i] = 0.0;
			}
		}
	}

	/**
	 * Resets point
	 * (Setting point to [0,0,.......]
	 */
	public function resetPoint() {
		unset($this->array);
		$this->array = Array();
		for ($i = 0; $i < $this->size; $i++) {
			$this->array[$i] = 0.0;
		}
	}

	/**
	 * Setting $key-th dimension to $value
	 * @param Integer $key
	 * @param Float $value
	 * @throws Exception
	 */
	public function setPointDimension($key, $value) {
		if ($key >= $this->getPointDimensionAmount()) {
			throw new Exception(__FUNCTION__ . ' array exceeded. (' . $key . ':' . $this->getPointDimensionAmount() . ')');
		} else {
			$this->array[$key] = (float) round($value, 2);
		}
	}

	/**
	 * Returns point as array
	 * @return Array
	 */
	public function toArray() {
		return $this->array;
	}

	/**
	 * Returns number of point's dimensions (array's count)
	 * @return Integer
	 */
	public function getPointDimensionAmount() {
		return count($this->array);
	}

	/**
	 * Multiply's two points by themselfes
	 * Returns Sum of multiplications
	 * x1*x2+y1*y2+z1*z1+....=$sum
	 * @param Point $p
	 * @return Integer
	 * @throws Exception
	 */
	public function multiplyBy(Point $p) {
		if ($this->getPointDimensionAmount() == $p->getPointDimensionAmount()) {
			$sum = 0.0;
			$remote = (Array) $p->toArray();
			foreach ($this->toArray() as $key => $value) {
				$sum+=($value * $remote[$key]);
			}
			return $sum;
		} else {
			throw new Exception('Size of arrays are not equal ' . __FUNCTION__);
		}
	}

	/**
	 * Outputs string in format [x,y,z,a,b,c,....]
	 * @return string
	 */
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
