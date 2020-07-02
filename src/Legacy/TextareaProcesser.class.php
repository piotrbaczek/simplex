<?php

/**
 * Processes data from textarea into arrays to Simplex class
 * @see Simplex
 * @author Piotr Gołasz <pgolasz@gmail.com>
 * @deprecated
 */
class TextareaProcesser {

	private $signs = Array();
	private $variables = Array();
	private $boundaries = Array();
	private $targetFunction = Array();
	private $max = true;
	private $gomorry = false;
	private $isCorrect = false;

	/**
	 * Create with textarea data:
	 * @see /sources/receiver.php
	 * @param String $param
	 * @param string $param2
	 * @param boolean $param3
	 * @param boolean $param4
	 * $tp=new TextareaProcesser($targetfunction,$textarea,true,false);
	 * if($yp->isCorrect()){
	 * $simplex=new Simplex($tp->getVariables(),.......................);
	 * }
	 * 
	 */
	public function __construct($param = '', $param2 = '', $param3 = true, $param4 = false) {
		if (is_null($param) || is_null($param2) || is_null($param3) || is_null($param4)) {
			$this->errormessage('Błąd przetwarzania - nie przekazano żadnego parametru');
			return 0;
		} else {
			$param = str_replace(' ', '', $param);
			$param = trim($param);
			preg_match_all('/(<=|>=|=)/', $param, $signs);
			preg_match_all('([+|-]?[0-9]*\/[1-9]{1,}[0-9]*[a-z]|[+|-]?[0-9]*[a-z])', $param, $variables);
			preg_match_all('(=[+|-]?[0-9]*\/[1-9]{1,}[0-9]*|=[+|-]?[0-9]*)', $param, $boundaries);
			if (count($signs[0]) != count($boundaries[0]) or (count($variables[0]) % count($boundaries[0]) != 0)) {
				$this->errormessage('Błąd przetwarzania - Wymiary macierzy są nierówne. Sprawdź poprawność danych!');
			} else {
				foreach ($signs[0] as $key => $value) {
					$this->signs[] = $value;
				}


				foreach ($boundaries[0] as $key => $value) {
					if (strpos(substr($value, 1), '/')) {
						$temp = explode('/', substr($value, 1));
						$this->boundaries[] = new Fraction($temp[0], $temp[1]);
					} else {
						$this->boundaries[] = new Fraction(substr($value, 1));
					}
				}

				preg_match_all('([+|-]?[0-9]*\/[1-9]{1,}[0-9]*[a-z]|[+|-]?[0-9]*[a-z])', $param2, $targetFunction);
				foreach ($targetFunction[0] as $key => $value) {
					$value = substr($value, 0, -1);
					if (strpos($value, '/')) {
						$temp = explode('/', $value);
						$this->targetFunction[] = new Fraction($temp[0], $temp[1]);
					} else {
						$this->targetFunction[] = new Fraction($value);
					}
				}

				$index = 0;
				foreach ($variables[0] as $key => $value) {
					if ($key != 0 && $key % count($targetFunction[0]) == 0) {
						$index++;
					}
					if (strpos(substr($value, 0, -1), '/')) {
						$temp = explode('/', substr($value, 0, -1));
						$this->variables[$index][$key % count($targetFunction[0])] = new Fraction($temp[0], $temp[1]);
					} else {
						$this->variables[$index][$key % count($targetFunction[0])] = new Fraction(substr($value, 0, -1));
					}
				}

				$this->gomorry = ($param4 == 'true' ? true : false);

				$this->max = ($param3 == 'true' ? true : false);
			}
		}
		$this->isCorrect = true;
	}

	/**
	 * Return coefficients of variables of LP problem as read from textarea
	 * @return Array
	 */
	public function getVariables() {
		return $this->variables;
	}

	/**
	 * Returns inequalities or equalities signs of LP problem as read from textarea
	 * @return Array
	 */
	public function getSigns() {
		return $this->signs;
	}

	/**
	 * Returns boundaries of LP problem as read from textarea
	 * @return Array
	 */
	public function getBoundaries() {
		return $this->boundaries;
	}

	/**
	 * Returns true if Maxmimize, false if Minimalize
	 * @return boolean
	 */
	public function getMaxMin() {
		return $this->max;
	}

	/**
	 * Returns true if Gomory's Cutting plane method should be used, false otherwise
	 * @return boolean
	 */
	public function getGomorry() {
		return $this->gomorry;
	}

	/**
	 * Returns array of coefficients of target function
	 * @return Array
	 */
	public function getTargetfunction() {
		return $this->targetFunction;
	}

	/**
	 * Returns true if processing was successfull, false otherwise
	 * @return boolean
	 */
	public function isCorrect() {
		return $this->isCorrect ? true : false;
	}

	/**
	 * Returns error message in format of jQuery UI
	 * @param String $message
	 * @return String
	 */
	public static function errormessage($message) {
		return '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
	}

}

?>