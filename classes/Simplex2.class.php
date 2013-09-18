<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Simplex2
 *
 * @author PETTER
 */
class Simplex2 {

	private $index = 0;
	private $matrixes = Array();
	private $gomory;
	private $extreme;
	private $variables;
	private $boundaries;
	private $signs;
	private $targetfunction;

	public function __construct(Array $variables, Array $boundaries, Array $signs, Array $targetfunction, $max = true, $gomory = false) {
		$this->gomory = (boolean) $gomory;
		$this->extreme = (boolean) $max;
		$this->variables = $variables;
		$this->targetfunction = $targetfunction;
		$this->boundaries = $boundaries;

		foreach ($signs as $key => $value) {
			$this->signs[$key] = Signs::Signs($value);
		}

		if (count($boundaries) != count($signs)) {
			throw new Exception('Sizes of arrays Boundaries and Signs have to be the same.');
		}
		$this->Solve();
	}

	public function Solve() {
		
	}

}

?>
