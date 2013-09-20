<?php

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
	private $wrongsigns = 0;
	private $M, $N, $O;
	private $cCoefficient;
	private $basis;
	private $basisVariable;
	private $nonBasisVariable;

	public function __construct(Array $variables = [], Array $boundaries = [], Array $signs = [], Array $targetfunction = [], $max = true, $gomory = false) {
		$this->gomory = (boolean) $gomory;
		$this->extreme = (boolean) $max;
		$this->variables = $variables;
		$this->targetfunction = $targetfunction;
		$this->boundaries = $boundaries;
		$this->signs = $signs;
		$this->M = count($variables[0]) + 1; //3
		$this->N = count($boundaries) + 1; //4
		$this->O = count($targetfunction);
		$this->cCoefficient[$this->index] = Array();
		$this->basisVariable = Array();
		$this->nonBasisVariable = Array();

		if (empty($variables) || empty($boundaries) || empty($signs) || empty($targetfunction)) {
			throw new Exception('Input array is empty!.');
		}

		if (count($boundaries) != count($signs) || count($signs) == 0) {
			throw new Exception('Sizes of arrays Boundaries and Signs have to be the same.');
		}
		if ($this->extreme) {
			foreach ($this->signs as $key => $value) {
				if ($value != enumSigns::_LEQ) {
					$this->cCoefficient[$this->index][$key] = new Fraction(0, 1, -1, 1);
					$this->wrongsigns++;
				} else {
					$this->cCoefficient[$this->index][$key] = new Fraction(0);
				}
			}
		} else {
			foreach ($this->signs as $key => $value) {
				$this->cCoefficient[$this->index][$key] = new Fraction(0, 1, 1, 1);
				if ($value != enumSigns::_LEQ) {
					$this->wrongsigns++;
				}
			}
		}

		$this->basis = new SplFixedArray($this->O + $this->N + $this->wrongsigns - 1);

		for ($i = 1; $i < $this->N; $i++) {
			$this->basisVariable[$this->index][$i] = 'S<sub>' . $i . '</sub>';
		}
		for ($i = 1; $i < $this->O + $this->N + $this->wrongsigns; $i++) {
			$this->nonBasisVariable[$this->index][$i] = 'x<sub>' . $i . '</sub>';
		}

		$this->matrixes[$this->index] = new SimplexTableu($this->N, $this->N + $this->M - 1 + $this->wrongsigns);

		for ($i = 0; $i < $this->N - 1; $i++) {
			for ($j = 0; $j < $this->M - 1; $j++) {
				$this->matrixes[$this->index]->setValue($j, $i, clone $this->variables[$i][$j]);
			}
		}

		for ($i = 0; $i < $this->N - 1; $i++) {
			$this->matrixes[$this->index]->setValue($this->N + $this->wrongsigns + $this->M - 2, $i, clone $boundaries[$i]);
		}

		$ax = 0;
		foreach ($this->signs as $key => $value) {
			switch ($value) {
				case enumSigns::_GEQ:
					$this->matrixes[$this->index]->setValue($this->M - 1 + $key, $key, new Fraction(-1));
					$this->matrixes[$this->index]->setValue($this->M - 1 + $this->N - 1 + $ax, $key, new Fraction(1));
					$ax++;
					break;
				default:
					for ($j = $this->M - 1; $j < $this->N + $this->M - 2; $j++) {
						if (($j - ($this->M - 1)) == $key) {
							$this->matrixes[$this->index]->setValue($j, $key, new Fraction(1));
						}
					}
					break;
			}
		}
		unset($ax);

		for ($i = 0; $i < $this->O; $i++) {
			$targetfunction[$i]->minusFraction();
			$this->matrixes[$this->index]->setValue($i, $this->N - 1, clone $targetfunction[$i]);
		}

		if ($this->extreme) {
			for ($i = 0; $i < $this->N + $this->M - 2; $i++) {
				$temp = new Fraction();
				for ($j = 0; $j < $this->N - 1; $j++) {
					if ($this->signs[$j] == enumSigns::_GEQ) {
						$temp->add($this->matrixes[$this->index]->getElement($i, $j));
					}
				}
				$this->matrixes[$this->index]->getElement($i, $this->N - 1)->substract(new Fraction(0, 1, $temp->getNumerator(), $temp->getDenominator()));
			}
			//for boundaries
			$temp = new Fraction();
			$b = count($this->matrixes[$this->index]->getRows());
			for ($j = 0; $j < $this->N - 1; $j++) {
				if ($this->signs[$j] != enumSigns::_LEQ) {
					$temp->add($this->matrixes[$this->index]->getElement($b - 1, $j));
				}
			}
			$this->matrixes[$this->index]->getElement($b - 1, $this->N - 1)->substract(new Fraction(0, 1, $temp->getNumerator(), $temp->getDenominator()));
		} else {
			for ($i = 0; $i < $this->N + $this->M - 2; $i++) {
				$temp = new Fraction();
				for ($j = 0; $j < $this->N - 1; $j++) {
					if ($this->signs[$j] == enumSigns::_GEQ) {
						$temp->add($this->matrixes[$this->index]->getElement($i, $j));
					}
				}
				$this->matrixes[$this->index]->getElement($i, $this->N - 1)->substract(new Fraction(0, 1, $temp->getNumerator(), $temp->getDenominator()));
			}
			//for boundaries
			$b = count($this->matrixes[$this->index]->getRows());
			$temp = new Fraction();
			for ($j = 0; $j < $this->N - 1; $j++) {
				$temp->add($this->matrixes[$this->index]->getElement($b - 1, $j));
			}
			$this->matrixes[$this->index]->getElement($b - 1, $this->N - 1)->substract(new Fraction(0, 1, $temp->getNumerator(), $temp->getDenominator()));
		}

		//--------------------------------------------
		$this->Solve();
	}

	public function Solve() {
		while (true) {
			$this->index++;
			$this->matrixes[$this->index] = clone $this->matrixes[$this->index - 1];
			$this->matrixes[$this->index]->setIndex($this->index);
			$this->basisVariable[$this->index] = $this->basisVariable[$this->index - 1];
			$this->nonBasisVariable[$this->index] = $this->nonBasisVariable[$this->index - 1];
			$this->cCoefficient[$this->index] = $this->cCoefficient[$this->index - 1];
			$p = $this->matrixes[$this->index]->findBaseCol();
			if ($p == -1) {
				break;
			} else {
				$this->matrixes[$this->index - 1]->setMainCol($p);
				$this->matrixes[$this->index]->setMainCol($p);
			}

			$q = $this->matrixes[$this->index]->findBaseRow($p);
			if ($q == -1) {
				$this->errorMessage("Linear problem is unbounded");
				unset($this->matrixes[$this->index]);
				$this->index--;
				break;
			} else {
				$this->matrixes[$this->index - 1]->setMainRow($q);
				$this->matrixes[$this->index]->setMainRow($q);
			}

			if (isset($this->targetfunction[$p])) {
				$this->cCoefficient[$this->index][$q] = clone $this->targetfunction[$p];
			} else {
				$this->cCoefficient[$this->index][$q] = new Fraction(0);
			}
			$this->cCoefficient[$this->index][$q]->minusFraction();
			$this->cCoefficient[$this->index][$q] = clone $this->cCoefficient[$this->index][$q];
			$this->swapBase();
			$this->simplexIteration();
//			echo 'p=' . $this->matrixes[$this->index - 1]->getMainCol() . '<br/>';
//			echo 'q=' . $this->matrixes[$this->index - 1]->getMainRow() . '<br/>';
//			echo 'z=' . $this->matrixes[$this->index - 1]->getElement($this->matrixes[$this->index - 1]->getMainCol(), $this->matrixes[$this->index - 1]->getMainRow()) . '<br/>';
			//$this->matrixes[$this->index]->simplexIteration($this->matrixes[$this->index - 1]->getMainRow(), $this->matrixes[$this->index - 1]->getMainCol(), $this->matrixes[$this->index - 1]->getElement($this->matrixes[$this->index - 1]->getMainCol(), $this->matrixes[$this->index - 1]->getMainRow()));
			//-------------------------------
			break;
//			if (!$this->matrixes[$this->index]->checkTargetFunction()) {
//				break;
//			}
		}
	}

	public function printSolution() {
		foreach ($this->matrixes as $value) {
			$a = $value->getRows();
			$b = $value->getCols();
			for ($i = 0; $i < $value->getCols(); $i++) {
				for ($j = 0; $j < $value->getRows(); $j++) {
					echo $value->getElement($j, $i);
				}
				echo '<br/>';
			}
			echo '<br/>';
		}
	}

	public static function errorMessage($message) {
		echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
	}

	public function swapBase() {
		$buffer = $this->basisVariable[$this->index][$this->matrixes[$this->index - 1]->getMainRow() + 1];
		$this->basisVariable[$this->index][$this->matrixes[$this->index - 1]->getMainRow() + 1] = $this->nonBasisVariable[$this->index][$this->matrixes[$this->index - 1]->getMainCol() + 1];
		$this->nonBasisVariable[$this->index][$this->matrixes[$this->index - 1]->getMainCol() + 1] = $buffer;
		unset($buffer);
	}

	public function simplexIteration() {
		$previousBaseRow = $this->matrixes[$this->index - 1]->getMainRow();
		$previousBaseCol = $this->matrixes[$this->index - 1]->getMainCol();
		$previousMainElement = $this->matrixes[$this->index - 1]->getElement($this->matrixes[$this->index - 1]->getMainCol(), $this->matrixes[$this->index - 1]->getMainRow());
		for ($i = 0; $i < $this->matrixes[$this->index]->getCols(); $i++) {
			for ($j = 0; $j < $this->matrixes[$this->index]->getRows(); $j++) {
				if ($i == $previousBaseRow && $j == $previousBaseCol) {
					//Main element
					$this->matrixes[$this->index]->setValue($i, $j, new Fraction(1));
				} elseif ($i == $previousBaseRow) {
					//Main row
					$s = clone $this->matrixes[$this->index]->getElement($j, $i);
					$n = clone $previousMainElement;
					$s->divide($n);
					$this->matrixes[$this->index]->setValue($j, $i, clone $s);
				} elseif ($j == $previousBaseCol) {
					//Main column
					$this->matrixes[$this->index]->setValue($j, $i, new Fraction(0));
				} else {
					//Other elements
					$s = clone $this->matrixes[$this->index - 1]->getElement($j, $this->matrixes[$this->index - 1]->getMainRow());
					$m = clone $this->matrixes[$this->index - 1]->getElement($this->matrixes[$this->index - 1]->getMainCol(), $i);
					$n = clone $this->matrixes[$this->index - 1]->getElement($this->matrixes[$this->index - 1]->getMainCol(), $this->matrixes[$this->index - 1]->getMainRow());
					$l = clone $this->matrixes[$this->index]->getElement($j, $i);
					$s->multiply($m);
					$s->divide($n);
					$l->substract($s);
					$this->matrixes[$this->index]->setValue($j, $i, $l);
				}
			}
		}
	}

}

?>
