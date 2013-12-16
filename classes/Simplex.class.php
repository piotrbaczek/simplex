<?php

/**
 * Description of Simplex2
 *
 * @author PETTER
 */
class Simplex {

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
	private $basisVariable;
	private $nonBasisVariable;

	public function __construct(Array $variables, Array $boundaries, Array $signs, Array $targetfunction, $max = true, $gomory = false) {
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

		foreach ($this->signs as $key => $value) {
			if ($value == enumSigns::_LEQ) {
				$this->cCoefficient[$this->index][$key] = new Fraction(0);
			} elseif ($value == enumSigns::_GEQ) {
				$this->cCoefficient[$this->index][$key] = new Fraction(0, 1, -1, 1);
				$this->wrongsigns++;
			} elseif ($value == enumSigns::_EQ) {
				$this->cCoefficient[$this->index][$key] = new Fraction(0, 1, -1, 1);
			}
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

		for ($i = 0; $i < $this->matrixes[$this->index]->getCols() - 1; $i++) {
			$this->matrixes[$this->index]->setValue($this->matrixes[$this->index]->getRows() - 1, $i, clone $boundaries[$i]);
		}

		$ax = 0;
		foreach ($this->signs as $key => $value) {
			switch ($value) {
				case enumSigns::_GEQ:
					$this->matrixes[$this->index]->setValue($this->M - 1 + $key, $key, new Fraction(-1));

					$this->matrixes[$this->index]->setValue($this->M - 1 + $this->N - 1 + $ax, $key, new Fraction(1));
					$this->basisVariable[$this->index][$key + 1] = 'x<sub>' . ($this->M + $key) . '</sub>';

					$this->targetfunction[$this->M - 1 + $this->N - 1 + $ax] = new Fraction(0, 1, -1, 1);
					$ax++;
					break;
				case enumSigns::_EQ:
					$this->matrixes[$this->index]->setValue($this->M - 1 + $key, $key, new Fraction(1));
					$this->basisVariable[$this->index][$key + 1] = 'x<sub>' . ($this->M + $key) . '</sub>';
					$this->targetfunction[$this->M - 1 + $key] = new Fraction(0, 1, -1, 1);
					break;
				default:
					//case LEQ
					$this->matrixes[$this->index]->setValue($this->M - 1 + $key, $key, new Fraction(1));
					$this->basisVariable[$this->index][$key + 1] = 'x<sub>' . ($this->M + $key) . '</sub>';
					break;
			}
		}
		unset($ax);


		for ($i = 0; $i < $this->matrixes[$this->index]->getRows() - 1; $i++) {
			if (!isset($this->targetfunction[$i])) {
				$this->targetfunction[$i] = new Fraction(0);
			} elseif (isset($this->targetfunction[$i]) && !Fraction::hasM($this->targetfunction[$i])) {
				if ($this->extreme) {
					$this->targetfunction[$i]->minusFraction();
				}
			}
			$this->matrixes[$this->index]->setValue($i, $this->N - 1, clone $this->targetfunction[$i]);
		}
		$this->partialAdding();
		//--------------------------------------------
		$this->Solve();
	}

	private function partialAdding() {
		for ($i = 0; $i < $this->matrixes[$this->index]->getRows(); $i++) {
			Fraction::removeM($this->matrixes[$this->index]->getElement($i, $this->matrixes[$this->index]->getCols() - 1));
		}
		$hasM = FALSE;
		for ($i = 0; $i < $this->matrixes[$this->index]->getRows(); $i++) {
			$temp = new Fraction(0);
			for ($j = 0; $j < $this->matrixes[$this->index]->getCols() - 1; $j++) {
				if (isset($this->targetfunction[$i]) && Fraction::hasM($this->targetfunction[$i])) {
					continue;
				}
				if (Fraction::hasM($this->cCoefficient[$this->index][$j])) {
					$hasM = TRUE;
					$temp2 = clone $this->cCoefficient[$this->index][$j];
					$temp2->multiply($this->matrixes[$this->index]->getElement($i, $j));
					$temp->add($temp2);
				}
			}
			if (isset($this->targetfunction[$i]) && Fraction::hasM($this->targetfunction[$i]) && !Fraction::hasM($temp) && !$hasM) {
				$temp->add(new Fraction(0, 1, 1, 1));
			}
			$this->matrixes[$this->index]->getElement($i, $this->matrixes[$this->index]->getCols() - 1)->add($temp);
		}
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
				unset($this->matrixes[$this->index]);
				$this->index--;
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
			if ($this->extreme) {
				$this->cCoefficient[$this->index][$q]->minusFraction();
			}
			$this->swapBase();

			$this->simplexIteration();
			$this->partialAdding($q);
			//-------------------------------
			if ($this->checkTargetFunction()) {
				$this->matrixes[$this->index]->setMainCol(-1);
				$this->matrixes[$this->index]->setMainRow(-1);
				break;
			}
		}

		if ($this->gomory && $this->index != 0) {
			$this->gomorrySolve();
		}
	}

	private function findGomorryColumn() {
		$startv = new Fraction(PHP_INT_MAX);
		$starti = -1;
		for ($i = 0; $i < $this->matrixes[$this->index]->getRows() - 1; $i++) {
			$element = clone $this->matrixes[$this->index]->getElement($i, $this->matrixes[$this->index]->getCols() - 2);
			if (Fraction::equalsZero($element) || Fraction::isNegative($element)) {
				continue;
			} elseif ($startv->compare($element)) {
				$starti = $i;
				$startv = clone $element;
			}
		}
		return $starti;
	}

	private function gomorrySolve() {
		//GOMORY'S CUTTING PLANE METHOD
		while (true) {
			$q = $this->gomoryRow() - 1;
			if ($q == -2) {
				break;
			}
			$this->index++;
			$this->matrixes[$this->index] = new SimplexTableu($this->matrixes[$this->index - 1]->getCols() + 1, $this->matrixes[$this->index - 1]->getRows());
			$this->matrixes[$this->index]->swapGomory();
			$this->matrixes[$this->index]->setIndex($this->index);
			$this->basisVariable[$this->index] = $this->basisVariable[$this->index - 1];
			$this->nonBasisVariable[$this->index] = $this->nonBasisVariable[$this->index - 1];
			$this->cCoefficient[$this->index] = $this->cCoefficient[$this->index - 1];
			$this->gomoryNewTableau($q);
			$this->matrixes[$this->index]->setMainRow($this->matrixes[$this->index]->getCols() - 2);
			$this->matrixes[$this->index]->setMainCol($this->findGomorryColumn());
			$this->signs[count($this->signs)] = '<=';
			$this->basisVariable[$this->index][] = 'x<sub>' . (count($this->targetfunction) + 1) . '</sub>';
			$this->cCoefficient[$this->index][count($this->cCoefficient[$this->index])] = 0;
			//-------------------------------------------
			$this->index++;
			$this->matrixes[$this->index] = clone $this->matrixes[$this->index - 1];
			$this->matrixes[$this->index]->swapGomory();
			$this->matrixes[$this->index]->setIndex($this->index);
			$this->basisVariable[$this->index] = $this->basisVariable[$this->index - 1];
			$this->nonBasisVariable[$this->index] = $this->nonBasisVariable[$this->index - 1];
			$this->cCoefficient[$this->index] = $this->cCoefficient[$this->index - 1];
			$this->swapBase();
			$this->simplexIteration();
			//-------------------------------------------
			if ($this->checkTargetIntegerFunction()) {
				$this->matrixes[$this->index]->setMainCol(-1);
				$this->matrixes[$this->index]->setMainRow(-1);
				break;
			}
		}
	}

	public function printSolution() {
		$string = '';
		foreach ($this->matrixes as $key => $value) {
			if (($key + 1) > $this->index) {
				$divisionArray = Array();
				foreach ($value->getDivisionArray() as $key2 => $darray) {
					$divisionArray[$key2] = new DivisionCoefficient();
				}
			} else {
				$divisionArray = $this->matrixes[$key + 1]->getDivisionArray();
			}
			$string.='<table class="result">';
			$string.='<tbody>';
			$string.='<tr>';
			$string.='<th class="ui-state-default">(' . $value->getIndex() . ')</th>';
			$string.='<th class="ui-state-default"></th>';
			for ($j = 0; $j < $this->N + $this->M - 2 + $this->wrongsigns; $j++) {
				if (isset($this->targetfunction[$j])) {
					$string.='<th class="ui-state-default">' . $this->targetfunction[$j] . '</th>';
				} else {
					$string.='<th class="ui-state-default">0</th>';
				}
			}
			$string.='<th class="ui-state-default" rowspan="2">P<sub>o</sub></th>';
			$string.='<th class="ui-state-default" rowspan="2">P<sub>o</sub>/a<sub>ij</sub></th>';
			$string.='</tr>';
			$string.='<tr><th class="ui-state-default">Baza</th>';
			$string.='<th class="ui-state-default">c</th>';
			for ($j = 0; $j < $this->N + $this->wrongsigns + $this->M - 2; $j++) {
				if (isset($this->nonBasisVariable[$key][$j + 1])) {
					$string.='<th class="ui-state-default">' . $this->nonBasisVariable[$key][$j + 1] . '</th>';
				}
			}
			$string.='</tr>';
			for ($i = 0; $i < $value->getCols() - 1; $i++) {
				$string.='<tr>';
				if (isset($this->basisVariable[$key][($i + 1)])) {
					$string.='<th class="ui-state-default">' . $this->basisVariable[$key][($i + 1)] . '</th>';
					$string.='<td class="center">' . $this->cCoefficient[$key][$i] . '</td>';
				} else {
					$string.='<th class="ui-state-default">z<sub>j</sub>-c<sub>j</sub></th>';
					$string.='<th class="ui-state-default"></th>';
				}
				$string.=$this->printImages($value, $key, $i, $j);
				$string.=$divisionArray[$i];
				$string.='</tr>';
			}

			for ($i = $value->getCols() - 1; $i < $value->getCols(); $i++) {
				$string.='<tr>';
				if (isset($this->basisVariable[$key][($i + 1)])) {
					$string.='<th class="ui-state-default">' . $this->basisVariable[$key][($i + 1)] . '</th>';
					$string.='<td class="center">' . $this->cCoefficient[$key][$i] . '</td>';
				} else {
					$string.='<th class="ui-state-default">z<sub>j</sub>-c<sub>j</sub></th>';
					$string.='<th class="ui-state-default"></th>';
				}
				$string.=$this->printImages($value, $key, $i, $j);
				$string.='<td class="ui-state-default"></td>';
				$string.='</tr>';
			}
			$string.='</tbody>';
			$string.='</table>';
			if (!$value->isGomory()) {
				$string.='A<sub>' . ($key + 1) . '</sub>=' . $this->printCurrentPoint($key);
			}
			$string.='<br/>';
		}
		return $string;
	}

	private function printImages($value, $key, $i, $j) {
		$string = '';
		for ($j = 0; $j < $value->getRows(); $j++) {
			if ($key != 0 && !$value->isGomory()) {
				//ALL PICTURES NEEDED
				if ($j == $this->matrixes[$key]->getMainCol() && $i == $this->matrixes[$key]->getMainRow()) {
					if ($j == $this->matrixes[$key - 1]->getMainCol() && $i == $this->matrixes[$key - 1]->getMainRow()) {
						$string.='<td class="mainelement" data-dane="m,1,' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
					} elseif ($j == $this->matrixes[$key - 1]->getMainCol()) {
						$string.='<td class="mainelement" data-dane="c,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
					} elseif ($i == $this->matrixes[$key - 1]->getMainRow()) {
						$string.='<td class="mainelement" data-dane="r,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
					} else {
						$string.='<td class="mainelement" data-dane="g,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $i) . ',' . $this->matrixes[$key - 1]->getElement($j, $this->matrixes[$key - 1]->getMainRow()) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
					}
				} else {
					if ($j == $this->matrixes[$key - 1]->getMainCol() && $i == $this->matrixes[$key - 1]->getMainRow()) {
						$string.='<td data-dane="m,1,' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
					} elseif ($j == $this->matrixes[$key - 1]->getMainCol()) {
						$string.='<td data-dane="c,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
					} elseif ($i == $this->matrixes[$key - 1]->getMainRow()) {
						$string.='<td data-dane="r,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
					} else {
						$string.='<td data-dane="g,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $i) . ',' . $this->matrixes[$key - 1]->getElement($j, $this->matrixes[$key - 1]->getMainRow()) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
					}
				}
			} else {
				//NO PICTURES
				if ($j == $this->matrixes[$key]->getMainCol() && $i == $this->matrixes[$key]->getMainRow()) {
					$string.='<td class="mainelement">' . $value->getElement($j, $i) . '</td>';
				} elseif ($j == $this->matrixes[$key]->getMainCol()) {
					$string.='<td>' . $value->getElement($j, $i) . '</td>';
				} elseif ($i == $this->matrixes[$key]->getMainRow()) {
					$string.='<td>' . $value->getElement($j, $i) . '</td>';
				} else {
					$string.='<td>' . $value->getElement($j, $i) . '</td>';
				}
			}
		}
		return $string;
	}

	public function testPrint() {
		foreach ($this->matrixes as $value) {
			echo 'Index:' . $value->getIndex() . '<br/>';
			echo 'Col: ' . $value->getMainCol() . '<br/>';
			echo 'Row: ' . $value->getMainRow() . '<br/>';
			echo 'Gomory: ' . $value->isGomory() . '<br/>';
			echo '<table border="1">';
			for ($i = 0; $i < $value->getCols(); $i++) {
				echo '<tr>';
				for ($j = 0; $j < $value->getRows(); $j++) {
					echo '<td>' . $value->getElement($j, $i) . '</td>';
				}
				echo '</tr>';
			}
			echo '</table><br/>';
		}
	}

	private function gomoryRow() {
		foreach ($this->getValuePair() as $key => $value) {
			if (!$value->isInteger()) {
				return $key;
			}
		}
		return -1;
	}

	private function gomoryNewTableau($k) {
		for ($i = 0; $i < $this->matrixes[$this->index - 1]->getCols() - 1; $i++) {
			for ($j = 0; $j < $this->matrixes[$this->index - 1]->getRows(); $j++) {
				$this->matrixes[$this->index]->setValue($j, $i, $this->matrixes[$this->index - 1]->getElement($j, $i));
			}
		}
		for ($j = 0; $j < $this->matrixes[$this->index - 1]->getRows(); $j++) {
			$this->matrixes[$this->index]->setValue($j, $this->matrixes[$this->index]->getCols() - 1, $this->matrixes[$this->index - 1]->getElement($j, $this->matrixes[$this->index - 1]->getCols() - 1));
			$s = clone $this->matrixes[$this->index - 1]->getElement($j, $k);
			$s->getImproperPart();
			$this->matrixes[$this->index]->setValue($j, $this->matrixes[$this->index]->getCols() - 2, $s);
		}
	}

	private function checkTargetFunction() {
		for ($i = 0; $i < $this->matrixes[$this->index]->getRows() - 1; $i++) {
			if (Fraction::isNegative($this->matrixes[$this->index]->getElement($i, $this->matrixes[$this->index]->getCols() - 1))) {
				return false;
			}
		}
		return true;
	}

	private function checkTargetIntegerFunction() {
		foreach ($this->getValuePair() as $value) {
			if ($value->isInteger()) {
				continue;
			} else {
				return false;
			}
		}
		return true;
	}

	public function getResult() {
		return $this->matrixes[$this->index]->getElement($this->matrixes[$this->index]->getRows() - 1, $this->matrixes[$this->index]->getCols() - 1);
	}

	public function printResult() {
		return 'W=' . $this->getResult();
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
					$this->matrixes[$this->index]->setValue($j, $i, new Fraction(1));
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
					$s = clone $this->matrixes[$this->index - 1]->getElement($j, $previousBaseRow);
					$m = clone $this->matrixes[$this->index - 1]->getElement($previousBaseCol, $i);
					$n = clone $this->matrixes[$this->index - 1]->getElement($previousBaseCol, $previousBaseRow);
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
		$string = '';
		$string.=$this->extreme ? 'max ' : 'min ';
		ksort($this->targetfunction);
		foreach ($this->targetfunction as $key => $value) {
			$temp = clone $value;
			if (Fraction::equalsZero($temp)) {
				continue;
			}
			if ($this->extreme) {
				if (Fraction::hasM($temp)) {
					
				} else {
					$temp->minusFraction();
				}
			} else {
				if (Fraction::hasM($temp)) {
					$temp->minusFraction();
				}
			}
//			if (!Fraction::hasM($value)) {
//				$temp->minusFraction();
//			} else {
//				if ($this->extreme) {
//					$temp->minusFraction();
//				}
//			}
			if ($key != 0) {
				if (Fraction::isPositive($temp) || Fraction::equalsZero($temp)) {
					$string.='+';
				}
			}
			$string.=$temp . 'x<sub>' . ($key + 1) . '</sub>';
		}
		$string.='<br/>';
		for ($i = 0; $i < $this->matrixes[0]->getCols() - 1; $i++) {
			for ($j = 0; $j < $this->matrixes[0]->getRows() - 1; $j++) {
				$element = clone $this->matrixes[0]->getElement($j, $i);
				if (Fraction::isPositive($element) || Fraction::equalsZero($element)) {
					$string.=$j != 0 ? '+' : '';
				}
				$string.=$element . 'x<sub>' . ($j + 1) . '</sub>';
			}
			$string.=$this->signs[$i];
			$string.=$this->boundaries[$i];
			$string.='<br/>';
		}
		for ($i = 0; $i < $this->matrixes[0]->getRows() - 1; $i++) {
			$string.='x<sub>' . ($i + 1) . '</sub>&ge;0<br/>';
		}
		if ($this->gomory) {
			$string.='<u>i całkowitoliczbowe</u>';
		}
		$string.='<br/>';
		return $string;
	}

	public function printValuePair() {
		$string = '';
		foreach ($this->getValuePair() as $key => $value) {
			$string.='x<sub>' . $key . '</sub>=' . $value . (Fraction::isFraction($value) ? ' (' . $value->getRealValue() . ')' : '') . '<br/>';
		}
		return $string;
	}

	public function getValuePair($indexarray = -1) {
		if ($indexarray == -1) {
			$indexarray = $this->index;
		}
		$x = Array();
		for ($i = 1; $i < 2 + max(array_keys($this->targetfunction)); $i++) {
			$x[$i] = new Fraction();
		}
		$index = 0;
		foreach ($this->basisVariable[$indexarray] as $value) {
			$temp = explode('<sub>', $value);
			$x[(int) $temp['1']] = clone $this->matrixes[$indexarray]->getElement($this->matrixes[$indexarray]->getRows() - 1, $index);
			$index++;
		}
		unset($index);
		return $x;
	}

	public function getPrimaryGraphJson() {
		$a = 0;
		$json = Array();
		foreach ($this->targetfunction as $value) {
			if (!Fraction::equalsZero($value) && !Fraction::hasM($value)) {
				$a++;
			}
		}
		if ($a == 2) {
			$b = count($this->boundaries);
			$maxx = new Fraction(0);
			$maxy = new Fraction(0);
			for ($i = 0; $i < $b; $i++) {
				if (Fraction::equalsZero($this->variables[$i][1])) {
					continue;
				}
				$s = clone $this->boundaries[$i];
				$s->divide($this->variables[$i][1]);
				if ($s->compare($maxy)) {
					$maxy = $s;
				}
				if (Fraction::equalsZero($this->variables[$i][0])) {
					continue;
				}
				$s = clone $this->boundaries[$i];
				$s->divide($this->variables[$i][0]);
				if ($s->compare($maxx)) {
					$maxx = $s;
				}
			}
			for ($i = 0; $i < $b; $i++) {
				$json[$i] = Array('label' => 'S' . ($i + 1), 'data' => '');
				if (Fraction::equalsZero($this->variables[$i][1])) {
					$s = clone $this->boundaries[$i];
					$s->divide($this->variables[$i][0]);
					$json[$i]['data'][] = Array($s->getValue(), $maxy->getValue());
				} else {
					$j = clone $this->boundaries[$i];
					$j->divide($this->variables[$i][1]);
					$json[$i]['data'][] = Array(0, $j->getValue());
				}
				if (Fraction::equalsZero($this->variables[$i][0])) {
					$s = clone $this->boundaries[$i];
					$s->divide($this->variables[$i][1]);
					$json[$i]['data'][] = Array($maxx->getValue(), $s->getValue());
				} else {
					$j = clone $this->boundaries[$i];
					$j->divide($this->variables[$i][0]);
					$json[$i]['data'][] = Array($j->getValue(), 0);
				}
			}
			if (!Fraction::equalsZero($this->targetfunction[0])) {
				$t = clone $this->targetfunction[1];
				$t->multiply($maxx);
				$t->divide($this->targetfunction[0]);
				$json[] = Array('label' => 'gradient', 'data' => Array(Array(0, 0), Array($maxx->getValue() / 4, $t->getValue() / 4)));
			}
			foreach ($this->matrixes as $key => $value) {
				if (!$value->isGomory()) {
					$key1 = $this->getValuePair($key);
					$json[] = Array('label' => 'A' . ($key + 1), 'data' => Array(Array($key1[1]->getRealValue(), $key1[2]->getRealValue())), 'points' => Array('show' => true));
				}
			}
		}
		return $json;
	}

	public function getTargetFunction() {
		$x = Array();
		foreach ($this->targetfunction as $key => $value) {
			if (Fraction::equalsZero($value) || Fraction::hasM($value)) {
				continue;
			} else {
				$x[$key] = abs($value->getRealValue());
			}
		}
		return $x;
	}

	public function getSecondaryGraphJson() {
		$a = 0;
		$json = Array();
		$nonZeroTargetFunction = Array();
		foreach ($this->targetfunction as $key => $value) {
			if (!Fraction::equalsZero($value) && !Fraction::hasM($value)) {
				$a++;
				$nonZeroTargetFunction[] = $key;
			}
		}
		if ($a == 2) {
			$b = count($this->boundaries);
			$maxx = new Fraction(0);
			$maxy = new Fraction(0);
			for ($i = 0; $i < $b; $i++) {
				if (Fraction::equalsZero($this->variables[$i][1])) {
					continue;
				}
				$s = clone $this->boundaries[$i];
				$s->divide($this->variables[$i][1]);
				if ($s->compare($maxy)) {
					$maxy = $s;
				}

				if (Fraction::equalsZero($this->variables[$i][0])) {
					continue;
				}
				$s = clone $this->boundaries[$i];
				$s->divide($this->variables[$i][0]);
				if ($s->compare($maxx)) {
					$maxx = $s;
				}
			}
			if ($this->extreme) {
				for ($i = 0; $i < $maxx->getRealValue(); $i += ($maxx->getRealValue() / 20)) {
					for ($j = 0; $j < $maxy->getRealValue(); $j += ($maxy->getRealValue() / 20)) {
						if ($this->isValidPoint2D($i, $j)) {
							$json[] = Array(round($i, 2), round($j, 2), -round($this->targetfunction[$nonZeroTargetFunction[0]]->getRealValue() * $i + $this->targetfunction[$nonZeroTargetFunction[1]]->getRealValue() * $j, 2));
						}
					}
				}
				foreach ($this->matrixes as $key => $value) {
					$key1 = $this->getValuePair($key);
					$json[] = Array(round($key1[1]->getRealValue(), 2), round($key1[2]->getRealValue(), 2), -round($this->targetfunction[$nonZeroTargetFunction[0]]->getRealValue() * $key1[1]->getRealValue() + $this->targetfunction[$nonZeroTargetFunction[1]]->getRealValue() * $key1[2]->getRealValue(), 2));
				}
			} else {
				for ($i = 0; $i < $maxx->getRealValue(); $i += ($maxx->getRealValue() / 20)) {
					for ($j = 0; $j < $maxy->getRealValue(); $j += ($maxy->getRealValue() / 20)) {
						if ($this->isValidPoint2D($i, $j)) {
							$json[] = Array(round($i, 2), round($j, 2), round($this->targetfunction[$nonZeroTargetFunction[0]]->getRealValue() * $i + $this->targetfunction[$nonZeroTargetFunction[1]]->getRealValue() * $j, 2));
						}
					}
				}
				foreach ($this->matrixes as $key => $value) {
					$key1 = $this->getValuePair($key);
					$json[] = Array(round($key1[1]->getRealValue(), 2), round($key1[2]->getRealValue(), 2), round($this->targetfunction[$nonZeroTargetFunction[0]]->getRealValue() * $key1[1]->getRealValue() + $this->targetfunction[$nonZeroTargetFunction[1]]->getRealValue() * $key1[2]->getRealValue(), 2));
				}
			}
		} else {
			$b = count($this->boundaries);
			$maxx = new Fraction(0);
			$maxy = new Fraction(0);
			$maxz = new Fraction(0);
			for ($i = 0; $i < $b; $i++) {
				if (Fraction::equalsZero($this->variables[$i][2])) {
					continue;
				}
				$s = clone $this->boundaries[$i];
				$s->divide($this->variables[$i][2]);
				if ($s->compare($maxz)) {
					$maxz = $s;
				}
				if (Fraction::equalsZero($this->variables[$i][1])) {
					continue;
				}
				$s = clone $this->boundaries[$i];
				$s->divide($this->variables[$i][1]);
				if ($s->compare($maxy)) {
					$maxy = $s;
				}

				if (Fraction::equalsZero($this->variables[$i][0])) {
					continue;
				}
				$s = clone $this->boundaries[$i];
				$s->divide($this->variables[$i][0]);
				if ($s->compare($maxx)) {
					$maxx = $s;
				}
			}
			for ($i = 0; $i < $maxx->getRealValue(); $i += ($maxx->getRealValue() / 20)) {
				for ($j = 0; $j < $maxy->getRealValue(); $j += ($maxy->getRealValue() / 20)) {
					for ($k = 0; $k < $maxz->getRealValue(); $k+=($maxz->getRealValue() / 20)) {
						if ($this->isValidPoint3D($i, $j, $k)) {
							$json[] = Array($i, $j, $k);
						}
					}
				}
			}
		}
		return $json;
	}

	private function isValidPoint3D($x, $y, $z) {
		$b = $this->N - 1;
		for ($i = 0; $i < $b; $i++) {
			$left = ($this->variables[$i][0]->getRealValue() * $x) + ($this->variables[$i][1]->getRealValue() * $y) + ($this->variables[$i][2]->getRealValue() * $z);
			$right = $this->boundaries[$i]->getRealValue();
			switch ($this->signs[$i]) {
				case enumSigns::_GEQ:
					if ($left < $right) {
						return FALSE;
					}
					break;
				case enumSigns::_LEQ:
					if ($left > $right) {
						return FALSE;
					}
					break;
				case enumSigns::_EQ:
					if ($left != $right) {
						return FALSE;
					}
					break;
				default :
					return FALSE;
			}
		}
		return true;
	}

	private function isValidPoint2D($x, $y) {
		$b = $this->N - 1;
		for ($i = 0; $i < $b; $i++) {
			$left = ($this->variables[$i][0]->getRealValue() * $x) + ($this->variables[$i][1]->getRealValue() * $y);
			$right = $this->boundaries[$i]->getRealValue();
			switch ($this->signs[$i]) {
				case enumSigns::_GEQ:
					if ($left < $right) {
						return FALSE;
					}
					break;
				case enumSigns::_LEQ:
					if ($left > $right) {
						return FALSE;
					}
					break;
				case enumSigns::_EQ:
					if ($left != $right) {
						return FALSE;
					}
					break;
				default :
					return FALSE;
			}
		}
		return true;
	}

	private function printCurrentPoint($indexarray = -1) {
		if ($indexarray == -1) {
			$indexarray = $this->index;
		}
		$x = $this->getValuePair($indexarray);
		$string = '[';
		foreach ($x as $value) {
			$string.=$value->getRealValue() . ',';
		}
		$string = substr($string, 0, -1);
		$string.=']';
		return $string;
	}

}

?>
