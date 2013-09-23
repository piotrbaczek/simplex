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

	private function Solve() {
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
			$this->swapBase();
			$this->simplexIteration();
			if (!isset($this->basis[$q])) {
				$this->basis[$p] = $q;
			}
			//-------------------------------
			//break;
			if ($this->matrixes[$this->index]->checkTargetFunction()) {
				$this->matrixes[$this->index]->setMainCol(-1);
				$this->matrixes[$this->index]->setMainRow(-1);
				break;
			}
		}
	}

	public function printSolution() {
		foreach ($this->matrixes as $key => $value) {
			echo '<table class="result">';
			echo '<tbody>';
			echo '<tr>';
			echo '<th class="ui-state-default">(' . $value->getIndex() . ')</th>';
			echo '<th class="ui-state-default"></th>';
			for ($j = 0; $j < $this->N + $this->M - 2 + $this->wrongsigns; $j++) {
				if (isset($this->targetfunction[$j])) {
					echo '<th class="ui-state-default">' . $this->targetfunction[$j] . '</th>';
				} else {
					echo '<th class="ui-state-default">0</th>';
				}
			}
			echo '<th class="ui-state-default" rowspan="2">Warto&#347;&#263;</th>';
			echo '</tr>';
			echo '<tr><th class="ui-state-default">Baza</th>';
			echo '<th class="ui-state-default">c</th>';
			for ($j = 0; $j < $this->N + $this->wrongsigns + $this->M - 2; $j++) {
				if (isset($this->nonBasisVariable[$key][$j + 1])) {
					echo '<th class="ui-state-default">' . $this->nonBasisVariable[$key][$j + 1] . '</th>';
				}
			}
			echo '</tr>';
			for ($i = 0; $i < $value->getCols(); $i++) {
				echo '<tr>';
				if (isset($this->basisVariable[$key][($i + 1)])) {
					echo '<th class="ui-state-default">' . $this->basisVariable[$key][($i + 1)] . '</th>';
					echo '<td class="center">' . $this->cCoefficient[$key][$i] . '</td>';
				} else {
					echo '<th class="ui-state-default">z<sub>j</sub>-c<sub>j</sub></th>';
					echo '<th></th>';
				}
				for ($j = 0; $j < $value->getRows(); $j++) {
					if ($key != 0) {
						//ALL PICTURES NEEDED
						if ($j == $this->matrixes[$key]->getMainCol() && $i == $this->matrixes[$key]->getMainRow()) {
							if ($j == $this->matrixes[$key - 1]->getMainCol() && $i == $this->matrixes[$key - 1]->getMainRow()) {
								echo '<td class="mainelement">' . $value->getElement($j, $i) . '(m)</td>';
							} elseif ($j == $this->matrixes[$key - 1]->getMainCol()) {
								echo '<td class="mainelement">' . $value->getElement($j, $i) . '(c)</td>';
							} elseif ($i == $this->matrixes[$key - 1]->getMainRow()) {
								echo '<td class="mainelement">' . $value->getElement($j, $i) . '(r)</td>';
							} else {
								echo '<td class="mainelement">' . $value->getElement($j, $i) . '(g)</td>';
							}
						} else {
							if ($j == $this->matrixes[$key - 1]->getMainCol() && $i == $this->matrixes[$key - 1]->getMainRow()) {
								echo '<td>' . $value->getElement($j, $i) . '(m)</td>';
							} elseif ($j == $this->matrixes[$key - 1]->getMainCol()) {
								echo '<td>' . $value->getElement($j, $i) . '(c)</td>';
							} elseif ($i == $this->matrixes[$key - 1]->getMainRow()) {
								echo '<td>' . $value->getElement($j, $i) . '(r)</td>';
							} else {
								echo '<td>' . $value->getElement($j, $i) . '(g)</td>';
							}
						}
					} else {
						//NO PICTURES
						if ($j == $this->matrixes[$key]->getMainCol() && $i == $this->matrixes[$key]->getMainRow()) {
							echo '<td class="mainelement">' . $value->getElement($j, $i) . '</td>';
						} elseif ($j == $this->matrixes[$key]->getMainCol()) {
							echo '<td>' . $value->getElement($j, $i) . '</td>';
						} elseif ($i == $this->matrixes[$key]->getMainRow()) {
							echo '<td>' . $value->getElement($j, $i) . '</td>';
						} else {
							echo '<td>' . $value->getElement($j, $i) . '</td>';
						}
					}
				}
				echo '</tr>';
			}
			echo '</tbody>';
			echo '</table>';
			echo '<br/>';
		}
	}

	public function getResult() {
		return $this->matrixes[$this->index]->getElement($this->matrixes[$this->index]->getRows() - 1, $this->matrixes[$this->index]->getCols() - 1);
	}

	public function printResult() {
		echo 'W=' . $this->getResult();
	}

	public static function errorMessage($message) {
		echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
	}

	private function swapBase() {
		$buffer = $this->basisVariable[$this->index][$this->matrixes[$this->index - 1]->getMainRow() + 1];
		$this->basisVariable[$this->index][$this->matrixes[$this->index - 1]->getMainRow() + 1] = $this->nonBasisVariable[$this->index][$this->matrixes[$this->index - 1]->getMainCol() + 1];
		$this->nonBasisVariable[$this->index][$this->matrixes[$this->index - 1]->getMainCol() + 1] = $buffer;
		unset($buffer);
	}

	private function simplexIteration() {
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

	public function printProblem() {
		$index = 1;
		echo $this->extreme == true ? 'max ' : 'min ';
		foreach ($this->targetfunction as $key => $value) {
			$a = clone $value;
			$value->minusFraction();
			if ($key == 0 && (Fraction::isPositive($value) || Fraction::equalsZero($value))) {
				echo $value . 'x<sub>' . $index . '</sub>';
			} else {
				echo '+' . $value . 'x<sub>' . $index . '</sub>';
			}
			$index++;
		}
		echo '<br/>';
		$index = 1;
		for ($i = 0; $i < $this->matrixes[0]->getCols() - 1; $i++) {
			for ($j = 0; $j < $this->matrixes[0]->getRows() - 1; $j++) {
				if (Fraction::isPositive($this->matrixes[0]->getElement($j, $i)) || Fraction::equalsZero($this->matrixes[0]->getElement($j, $i))) {
					echo $j != 0 ? '+' : '';
					echo $this->matrixes[0]->getElement($j, $i) . 'x<sub>' . $index . '</sub>';
				} else {
					echo $this->matrixes[0]->getElement($j, $i) . 'x<sub>' . $index . '</sub>';
				}
				$index++;
			}
			echo $this->signs[$i];
			echo $this->boundaries[$i];
			echo '<br/>';
			$index = 1;
		}
		$index = 1;
		for ($i = 0; $i < $this->matrixes[0]->getRows() - 1; $i++) {
			echo 'x<sub>' . $index . '</sub>' . enumSigns::_GEQ . '0<br/>';
			$index++;
		}
		echo '<br/>';
	}

	public function printValuePair() {
		foreach ($this->getValuePair() as $key => $value) {
			echo 'x<sub>' . ($key + 1) . '</sub>=' . $value . '<br/>';
		}
	}

	public function getValuePair() {
		if ($this->index == 0) {
			return Array("NaN");
		} else {
			$x = Array();
			foreach ($this->basis as $key => $value) {
				if (!isset($value)) {
					$x[$key] = new Fraction(0, 1);
				} else {
					$x[$key] = $this->matrixes[$this->index]->getElement($this->matrixes[$this->index]->getRows() - 1, $value);
				}
			}
			return $x;
		}
	}

}

?>
