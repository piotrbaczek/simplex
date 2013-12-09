<?php
/**
 * Description of RandomColor
 *
 * @author PETTER
 */
class RandomColor {

	public static function getRandomColor() {
		return '#' . strtoupper(dechex(rand(0, 10000000)));
	}

}
