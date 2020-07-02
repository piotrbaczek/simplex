<?php

/**
 * Dantzig's Simplex Method and Gomory's Cutting Plane Method
 * Master thesis for Gdańsk University of Technology
 *
 * GNU Licence 2014 For public use
 * @author Piotr Gołasz <pgolasz@gmail.com>
 * @see sources/receiver.php
 * @deprecated
 */
class Simplex {

    private $index = 0;
    private $matrixes = array();
    private $gomory;
    private $extreme;
    private $variables;
    private $boundaries;
    private $signs;
    private $targetfunction = [];
    private $wrongsigns = 0;
    private $M, $N, $O;
    private $cCoefficient;
    private $basisVariable;
    private $nonBasisVariable;
    private $eMessage = '';

    /**
     * Construct with PL problem data
     * @param Array $variables
     * @param Array $boundaries
     * @param Array $signs
     * @param Array $targetfunction
     * @param boolean $max
     * @param boolean $gomory
     * @throws Exception
     */
    public function __construct(array $variables, array $boundaries, array $signs, array $targetfunction, $max = true, $gomory = false) {
	$this->gomory = (boolean) $gomory;
	$this->extreme = (boolean) $max;
	$this->variables = $variables;
	$this->targetfunction[$this->index] = $targetfunction;
	$this->boundaries = $boundaries;
	$this->signs = $signs;
	$this->M = count($variables[0]) + 1; //3
	$this->N = count($boundaries) + 1; //4
	$this->O = count($targetfunction[$this->index]);
	$this->cCoefficient[$this->index] = array();
	$this->basisVariable = array();
	$this->nonBasisVariable = array();

	if (empty($variables) || empty($boundaries) || empty($signs) || empty($targetfunction[$this->index])) {
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

	$this->matrixes[$this->index] = new SimplexTableau($this->N, $this->N + $this->M - 1 + $this->wrongsigns);

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
		    $this->basisVariable[$this->index][$key + 1] = 'x<sub>' . ($this->M + $this->N - 1 + $ax) . '</sub>';
		    $this->targetfunction[$this->index][$this->M - 1 + $this->N - 1 + $ax] = new Fraction(0, 1, -1, 1);
		    $ax++;
		    break;
		case enumSigns::_EQ:
		    $this->matrixes[$this->index]->setValue($this->M - 1 + $key, $key, new Fraction(1));
		    $this->basisVariable[$this->index][$key + 1] = 'x<sub>' . ($this->M + $key) . '</sub>';
		    $this->targetfunction[$this->index][$this->M - 1 + $key] = new Fraction(0, 1, -1, 1);
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
	    if (!isset($this->targetfunction[$this->index][$i])) {
		$this->targetfunction[$this->index][$i] = new Fraction(0);
	    } elseif (isset($this->targetfunction[$this->index][$i]) && !Fraction::hasM($this->targetfunction[$this->index][$i])) {
		if ($this->extreme) {
		    $this->targetfunction[$this->index][$i]->minusFraction();
		}
	    }
	    $this->matrixes[$this->index]->setValue($i, $this->N - 1, clone $this->targetfunction[$this->index][$i]);
	}

	for ($i = 0; $i < count($this->targetfunction[$this->index]) + 1; $i++) {
	    $this->nonBasisVariable[$this->index][$i] = 'x<sub>' . $i . '</sub>';
	}

	$this->partialAdding();
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
		if (isset($this->targetfunction[$this->index][$i]) && Fraction::hasM($this->targetfunction[$this->index][$i])) {
		    continue;
		}
		if (Fraction::hasM($this->cCoefficient[$this->index][$j])) {
		    $hasM = TRUE;
		    $temp2 = clone $this->cCoefficient[$this->index][$j];
		    $temp2->multiply($this->matrixes[$this->index]->getElement($i, $j));
		    $temp->add($temp2);
		}
	    }
	    if (isset($this->targetfunction[$this->index][$i]) && Fraction::hasM($this->targetfunction[$this->index][$i]) && !Fraction::hasM($temp) && !$hasM) {
		$temp->add(new Fraction(0, 1, 1, 1));
	    }
	    $this->matrixes[$this->index]->getElement($i, $this->matrixes[$this->index]->getCols() - 1)->add($temp);
	}
    }

    private function Solve($recurring = false) {
	while (true) {
	    $this->index++;
	    $this->matrixes[$this->index] = clone $this->matrixes[$this->index - 1];
	    $this->matrixes[$this->index]->setIndex($this->index + 1);
	    if ($recurring) {
		$this->matrixes[$this->index]->swapGomory();
	    }
	    $this->basisVariable[$this->index] = $this->basisVariable[$this->index - 1];
	    $this->nonBasisVariable[$this->index] = $this->nonBasisVariable[$this->index - 1];
	    $this->cCoefficient[$this->index] = $this->cCoefficient[$this->index - 1];
	    $this->targetfunction[$this->index] = $this->targetfunction[$this->index - 1];
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
		$this->eMessage.='Problem PL jest nieograniczony w ekstremum.';
		unset($this->matrixes[$this->index]);
		$this->index--;
		break;
	    } else {
		$this->matrixes[$this->index - 1]->setMainRow($q);
		$this->matrixes[$this->index]->setMainRow($q);
	    }

	    if (isset($this->targetfunction[$this->index][$p])) {
		$this->cCoefficient[$this->index][$q] = clone $this->targetfunction[$this->index][$p];
	    } else {
		$this->cCoefficient[$this->index][$q] = new Fraction(0);
	    }
	    if ($this->extreme) {
		$this->cCoefficient[$this->index][$q]->minusFraction();
	    }
	    $this->swapBase();

	    $this->simplexIteration();
	    $this->partialAdding();

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

    private function gomorrySolve() {
	//GOMORY'S CUTTING PLANE METHOD
	while (true) {
	    $q = $this->gomoryRow() - 1;
	    if ($q == -2) {
		break;
	    }
	    $this->index++;
	    $this->matrixes[$this->index] = new SimplexTableau($this->matrixes[$this->index - 1]->getCols() + 1, $this->matrixes[$this->index - 1]->getRows() + 2);
	    $this->matrixes[$this->index]->swapGomory();
	    $this->matrixes[$this->index]->setIndex($this->index + 1);
	    $this->basisVariable[$this->index] = $this->basisVariable[$this->index - 1];
	    $this->nonBasisVariable[$this->index] = $this->nonBasisVariable[$this->index - 1];
	    $this->cCoefficient[$this->index] = $this->cCoefficient[$this->index - 1];
	    $this->targetfunction[$this->index] = $this->targetfunction[$this->index - 1];
	    $this->gomoryNewTableau($q);
	    if ($this->extreme) {
		$this->cCoefficient[$this->index][count($this->cCoefficient[$this->index])] = new Fraction(0, 1, -1, 1);
	    } else {
		$this->cCoefficient[$this->index][count($this->cCoefficient[$this->index])] = new Fraction(0, 1, 1, 1);
	    }
	    $this->signs[] = enumSigns::_GEQ;
	    $this->targetfunction[$this->index][] = new Fraction(0);
	    $this->targetfunction[$this->index][] = new Fraction(0, 1, -1, 1);
	    $this->partialAdding();
	    $this->matrixes[$this->index]->setMainRow($this->matrixes[$this->index]->getCols() - 2);
	    $this->matrixes[$this->index]->setMainCol($this->matrixes[$this->index]->findBaseCol());
	    $this->basisVariable[$this->index][] = 'x<sub>' . count($this->targetfunction[$this->index]) . '</sub>';
	    $this->nonBasisVariable[$this->index][] = 'x<sub>' . (count($this->targetfunction[$this->index]) - 1) . '</sub>';
	    $this->nonBasisVariable[$this->index][] = 'x<sub>' . count($this->targetfunction[$this->index]) . '</sub>';
	    $this->Solve(true);
	    //-------------------------------------------
	    if ($this->checkTargetIntegerFunction() && $this->checkTargetFunction()) {
		$this->matrixes[$this->index]->setMainCol(-1);
		$this->matrixes[$this->index]->setMainRow(-1);
		break;
	    }
	}
    }

    /**
     * Prints solution (All Tableau's) of LP problem solved
     * @return string
     */
    public function printSolution() {
	foreach ($this->cCoefficient[$this->index] as $value) {
	    if (Fraction::hasM($value)) {
		$this->eMessage.='Zbiór rozwiązań dopuszczalnych jest pusty.';
		break;
	    }
	}
	$string = '';
	foreach ($this->matrixes as $key => $value) {
	    if (($key + 1) > $this->index) {
		$divisionArray = array();
		foreach ($value->getDivisionarray() as $key2 => $darray) {
		    $divisionArray[$key2] = new DivisionCoefficient();
		}
	    } else {
		$divisionArray = $this->matrixes[$key + 1]->getDivisionarray();
	    }
	    $string.='<table class="result">';
	    $string.='<tbody>';
	    $string.='<tr>';
	    $string.='<th class="ui-state-default">(' . $value->getIndex() . ')</th>';
	    $string.='<th class="ui-state-default"></th>';
	    for ($j = 0; $j < count($this->targetfunction[$key]); $j++) {
		if (isset($this->targetfunction[$key][$j])) {
		    $string.='<th class="ui-state-default">' . $this->targetfunction[$key][$j] . '</th>';
		} else {
		    $string.='<th class="ui-state-default">0</th>';
		}
	    }
	    $string.='<th class="ui-state-default" rowspan="2">P<sub>o</sub></th>';
	    $string.='<th class="ui-state-default" rowspan="2">P<sub>o</sub>/a<sub>ij</sub></th>';
	    $string.='</tr>';
	    $string.='<tr><th class="ui-state-default">Baza</th>';
	    $string.='<th class="ui-state-default">c</th>';
	    for ($j = 0; $j < count($this->targetfunction[$key]); $j++) {
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
	if (strlen($this->eMessage) != 0) {
	    $string.=Simplex::errorMessage($this->eMessage);
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
			if (Fraction::hasM($this->matrixes[$key - 1]->getElement($j, $i)) || Fraction::hasM($this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $i))) {
			    $removedM = clone $this->matrixes[$key - 1]->getElement($j, $i);
			    Fraction::removeM($removedM);
			    $removedM2 = clone $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $i);
			    Fraction::removeM($removedM2);
			    $string.='<td data-dane="g,' . $removedM . ',' . $removedM2 . ',' . $this->matrixes[$key - 1]->getElement($j, $this->matrixes[$key - 1]->getMainRow()) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
			    unset($removedM);
			    unset($removedM2);
			} else {
			    $string.='<td data-dane="g,' . $this->matrixes[$key - 1]->getElement($j, $i) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $i) . ',' . $this->matrixes[$key - 1]->getElement($j, $this->matrixes[$key - 1]->getMainRow()) . ',' . $this->matrixes[$key - 1]->getElement($this->matrixes[$key - 1]->getMainCol(), $this->matrixes[$key - 1]->getMainRow()) . '">' . $value->getElement($j, $i) . '</td>';
			}
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

    private function gomoryRow() {
	foreach ($this->getValuePair() as $key => $value) {
	    if (!$value->isInteger()) {
		return $key;
	    }
	}
	return -1;
    }

    private function gomoryNewTableau($k) {
	for ($i = 0; $i < $this->matrixes[$this->index - 1]->getCols(); $i++) {
	    for ($j = 0; $j < $this->matrixes[$this->index - 1]->getRows(); $j++) {
		if ($i == $this->matrixes[$this->index - 1]->getCols() - 1 || $j == $this->matrixes[$this->index - 1]->getRows() - 1) {
		    if ($i == $this->matrixes[$this->index - 1]->getCols() - 1 && $j == $this->matrixes[$this->index - 1]->getRows() - 1) {
			$this->matrixes[$this->index]->setValue($this->matrixes[$this->index]->getRows() - 1, $this->matrixes[$this->index]->getCols() - 1, clone $this->matrixes[$this->index - 1]->getElement($j, $i));
		    } elseif ($i == $this->matrixes[$this->index - 1]->getCols() - 1) {
			$this->matrixes[$this->index]->setValue($j, $i + 1, clone $this->matrixes[$this->index - 1]->getElement($j, $i));
		    } elseif ($j == $this->matrixes[$this->index - 1]->getRows() - 1) {
			$this->matrixes[$this->index]->setValue($j + 2, $i, clone $this->matrixes[$this->index - 1]->getElement($j, $i));
		    }
		} else {
		    $this->matrixes[$this->index]->setValue($j, $i, clone $this->matrixes[$this->index - 1]->getElement($j, $i));
		}
	    }
	}
	for ($i = 0; $i < $this->matrixes[$this->index]->getRows(); $i++) {
	    $temp = clone $this->matrixes[$this->index]->getElement($i, $k);
	    $temp->getImproperPart();
	    $this->matrixes[$this->index]->setValue($i, $this->matrixes[$this->index]->getCols() - 2, $temp);
	}
	$this->matrixes[$this->index]->setValue($this->matrixes[$this->index]->getRows() - 2, $this->matrixes[$this->index]->getCols() - 2, new Fraction(1));
	$this->matrixes[$this->index]->setValue($this->matrixes[$this->index]->getRows() - 3, $this->matrixes[$this->index]->getCols() - 2, new Fraction(-1));
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

    /*
     * Returns value of Function on Maximized / Minimized point of PL cube
     */

    public function getResult() {
	$value = clone $this->matrixes[$this->index]->getElement($this->matrixes[$this->index]->getRows() - 1, $this->matrixes[$this->index]->getCols() - 1);
	if (Fraction::isNegative($value)) {
	    $value->minusFraction();
	}
	return $value;
    }

    /**
     * Prints result as 'W=35'
     * @return String
     */
    public function printResult() {
	if ($this->eMessage == '') {
	    if (Fraction::isFraction($this->getResult())) {
		return 'W=' . $this->getResult() . '(' . $this->getResult()->getRealValue() . ')';
	    } else {
		return 'W=' . $this->getResult();
	    }
	} else {
	    return '';
	}
    }

    /**
     * Prints Error Message in jQuery UI format
     * @static
     * @param String $message
     */
    public static function errorMessage($message) {
	return '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
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

    /**
     * Prints problem read by class from input data
     * @return string
     */
    public function printProblem() {
	$string = '';
	$string.=$this->extreme ? 'max ' : 'min ';
	ksort($this->targetfunction[0]);
	foreach ($this->targetfunction[0] as $key => $value) {
	    $temp = clone $value;
	    if (Fraction::equalsZero($temp) && ($key >= $this->M - 1)) {
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
		    $string.=($j != 0) ? '+' : '';
		}
		$string.=$element . 'x<sub>' . ($j + 1) . '</sub>';
	    }
	    $string.=enumSigns::_EQ;
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
	if ($this->eMessage != '') {
	    return '';
	} else {
	    $string = '';
	    foreach ($this->getValuePair() as $key => $value) {
		$string.='x<sub>' . $key . '</sub>=' . $value . (Fraction::isFraction($value) ? ' (' . $value->getRealValue() . ')' : '') . '<br/>';
	    }
	    return $string;
	}
    }

    /**
     * Returns Array of current Point
     * @param Integer $indexarray
     * @return array
     */
    public function getValuePair($indexarray = -1) {
	if ($indexarray == -1) {
	    $indexarray = $this->index;
	}
	$x = array();
	for ($i = 1; $i < 2 + max(array_keys($this->targetfunction[$indexarray])); $i++) {
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

    /**
     * Returns data for jQuery.flot graph
     * @return Array
     */
    public function getPrimaryGraphJson() {
	$a = 0;
	$json = array();
	foreach ($this->targetfunction[$this->index] as $key => $value) {
	    if (!Fraction::equalsZero($value) && !Fraction::hasM($value) || ($key < $this->M - 1)) {
		$a++;
	    }
	}
	if ($a == 2 || $a == 1) {
	    $b = count($this->boundaries);
	    $mr = $this->getMaxRangearray();
	    $maxx = new Fraction($mr[0]);
	    $maxy = new Fraction($mr[1]);
	    for ($i = 0; $i < $b; $i++) {
		$json[$i] = array('label' => 'S' . ($i + 1), 'data' => []);
		if (Fraction::equalsZero($this->variables[$i][1])) {
		    $s = clone $this->boundaries[$i];
		    $s->divide($this->variables[$i][0]);
		    $json[$i]['data'][] = array($s->getValue(), $maxy->getValue());
		} elseif (Fraction::isNegative($this->variables[$i][1])) {
		    $left = clone $this->variables[$i][0];
		    $left->multiply($maxx);
		    $boundaries = clone $this->boundaries[$i];
		    $boundaries->substract($left);
		    $boundaries->divide($this->variables[$i][1]);
		    $json[$i]['data'][] = array($maxx->getValue(), $boundaries->getValue());
		} else {
		    $j = clone $this->boundaries[$i];
		    $j->divide($this->variables[$i][1]);
		    $json[$i]['data'][] = array(0, $j->getValue());
		}
		if (Fraction::equalsZero($this->variables[$i][0])) {
		    $s = clone $this->boundaries[$i];
		    $s->divide($this->variables[$i][1]);
		    $json[$i]['data'][] = array($maxx->getValue(), $s->getValue());
		} elseif (Fraction::isNegative($this->variables[$i][0])) {
		    $left = clone $this->variables[$i][0];
		    $left->multiply($maxx);
		    $boundaries = clone $this->boundaries[$i];
		    $boundaries->substract($left);
		    $boundaries->divide($this->variables[$i][1]);
		    $json[$i]['data'][] = array($maxx->getValue(), $boundaries->getValue());
		} else {
		    $j = clone $this->boundaries[$i];
		    $j->divide($this->variables[$i][0]);
		    $json[$i]['data'][] = array($j->getValue(), 0);
		}
	    }
	    if (!Fraction::equalsZero($this->targetfunction[0][0])) {
		if (!Fraction::equalsZero($this->targetfunction[0][1])) {
		    $t = clone $this->targetfunction[$this->index][1];
		    $t->multiply($maxx);
		    $t->divide($this->targetfunction[$this->index][0]);
		    $json[] = array('label' => 'gradient', 'data' => array(array(0, 0), array($maxx->getValue() / 4, $t->getValue() / 4)));
		} else {
		    $json[] = array('label' => 'gradient', 'data' => array(array(0, 0), array($maxx->getValue(), 0)));
		}
	    } else {
		$json[] = array('label' => 'gradient', 'data' => array(array(0, 0), array(0, $maxy->getRealValue())));
	    }
	    foreach ($this->matrixes as $key => $value) {
		if (!$value->isGomory()) {
		    $key1 = $this->getValuePair($key);
		    $json[] = array('label' => 'A' . ($key + 1), 'data' => array(array($key1[1]->getRealValue(), $key1[2]->getRealValue())), 'points' => array('show' => true));
		}
	    }
	}
	return $json;
    }

    /**
     * Returns Target Function Coefficients as Array
     * Only non-M non-zero elements included
     * @return Array
     */
    public function getTargetFunction() {
	$x = array();
	foreach ($this->targetfunction[$this->index] as $key => $value) {
	    if (Fraction::equalsZero($value) || Fraction::hasM($value)) {
		continue;
	    } else {
		$x[$key] = abs($value->getRealValue());
	    }
	}
	return $x;
    }

    /**
     * Returns number to increase in for loops, to prevent some points not being displayed
     * due to the too large separation
     * @param Integer $number
     * @return real|int
     */
    public static function getIterationSeparation($number = -1) {
	if ($number < 0) {
	    throw new Exception('$number in ' . __FUNCTION__ . ' can\'t be negative');
	} else {
	    return floatval($number / 50);
	}
    }

    /**
     * Returns data for XpressCanvas graph
     * @return Array Array of points for 3d graph
     */
    public function getSecondaryGraphJson() {
	$point = new Point(count($this->getMaxRangearray()));
	$maxRange = $this->getMaxRangearray();
	$minRange = $this->getMinRangearray();
	$json = array();
	if (count($this->getTargetFunction()) == 2 || count($this->getTargetFunction()) == 1) {
	    if ($this->extreme) {
		for ($i = $minRange[0]; $i < $maxRange[0] + Simplex::getIterationSeparation($maxRange[0]); $i += Simplex::getIterationSeparation($maxRange[0])) {
		    for ($j = $minRange[1]; $j < $maxRange[1] + Simplex::getIterationSeparation($maxRange[1]); $j += Simplex::getIterationSeparation($maxRange[1])) {
			$point->resetPoint();
			$point->setPointDimension(0, $i);
			$point->setPointDimension(1, $j);
			if ($this->isValidPoint($point)) {
			    $json[] = array($i, $j, -round($this->targetfunction[0][0]->getRealValue() * $i + $this->targetfunction[0][1]->getRealValue() * $j, 2));
			}
		    }
		}
		//Matrix points hidden
//				foreach ($this->matrixes as $key => $value) {
//					$key1 = $this->getValuePair($key);
//					$point->resetPoint();
//					foreach ($key1 as $key2 => $value2) {
//						$point->setPointDimension($key2 - 1, $value2->getRealValue());
//					}
//					if ($this->isValidPoint($point)) {
//						$json[] = array(round($key1[1]->getRealValue(), 2), round($key1[2]->getRealValue(), 2), -round($this->targetfunction[0][0]->getRealValue() * $key1[1]->getRealValue() + $this->targetfunction[0][1]->getRealValue() * $key1[2]->getRealValue(), 2));
//					}
//				}
	    } else {
		for ($i = $minRange[0]; $i < $maxRange[0] + Simplex::getIterationSeparation($maxRange[0]); $i += Simplex::getIterationSeparation($maxRange[0])) {
		    for ($j = $minRange[1]; $j < $maxRange[1] + Simplex::getIterationSeparation($maxRange[1]); $j += Simplex::getIterationSeparation($maxRange[1])) {
			$point->resetPoint();
			$point->setPointDimension(0, $i);
			$point->setPointDimension(1, $j);
			if ($this->isValidPoint($point)) {
			    $json[] = array($i, $j, round($this->targetfunction[0][0]->getRealValue() * $i + $this->targetfunction[0][1]->getRealValue() * $j, 2));
			}
		    }
		}
		//Matrix points hidden
//				foreach ($this->matrixes as $key => $value) {
//					$key1 = $this->getValuePair($key);
//					$point->resetPoint();
//					foreach ($key1 as $key2 => $value2) {
//						$point->setPointDimension($key2 - 1, $value2->getRealValue());
//					}
//					if ($this->isValidPoint($point)) {
//						$json[] = array(round($key1[1]->getRealValue(), 2), round($key1[2]->getRealValue(), 2), round($this->targetfunction[0][0]->getRealValue() * $key1[1]->getRealValue() + $this->targetfunction[0][1]->getRealValue() * $key1[2]->getRealValue(), 2));
//					}
//				}
	    }
	} else {
	    for ($i = $minRange[0]; $i < $maxRange[0] + Simplex::getIterationSeparation($maxRange[0]); $i += Simplex::getIterationSeparation($maxRange[0])) {
		for ($j = $minRange[1]; $j < $maxRange[1] + Simplex::getIterationSeparation($maxRange[1]); $j += Simplex::getIterationSeparation($maxRange[1])) {
		    for ($k = $minRange[2]; $k < $maxRange[2] + Simplex::getIterationSeparation($maxRange[2]); $k+=Simplex::getIterationSeparation($maxRange[2])) {
			$point->resetPoint();
			$point->setPointDimension(0, $i);
			$point->setPointDimension(1, $j);
			$point->setPointDimension(2, $k);
			if ($this->isValidPoint($point)) {
			    $json[] = array($i, $j, $k);
			}
		    }
		}
	    }
	}
	return $json;
    }

    /**
     * Checks if Point $p is part of Simplex feasible solution
     * @param Point $p
     * @param Integer $decreaser Decrease value of boundary by $decreaser
     * @return boolean
     */
    public function isValidPoint(Point $p, $decreaser = 0) {
	$currentRow = new Point($p->getPointDimensionAmount());
	for ($i = 0; $i < $this->matrixes[0]->getCols() - 1; $i++) {
	    $currentRow->resetPoint();
	    for ($j = 0; $j < $this->matrixes[0]->getRows() - 1; $j++) {
		$currentRow->setPointDimension($j, $this->matrixes[0]->getElement($j, $i)->getRealValue());
	    }
	    $left = $currentRow->multiplyBy($p);
	    $right = $this->boundaries[$i]->getRealValue();
	    if ($decreaser != 0 && $decreaser > 0) {
		$right-=$decreaser;
	    }
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
	return TRUE;
    }

    /**
     * Prints current point of multidimensional cube
     * @param Integer $indexarray
     * @return string
     */
    private function printCurrentPoint($indexarray = -1) {
	if ($indexarray == -1) {
	    $indexarray = $this->index;
	}
	$x = $this->getValuePair($indexarray);
	$string = '[';
	foreach ($x as $value) {
	    $string.=round($value->getRealValue(), 2) . ';';
	}
	$string = substr($string, 0, -1);
	$string.=']';
	return $string;
    }

    /**
     * Returns array of Maximal value of each dimension's range
     * @return Array
     */
    public function getMaxRangearray() {
	$array = array();
	for ($i = 0; $i < $this->matrixes[0]->getCols() - 1; $i++) {
	    for ($j = 0; $j < $this->matrixes[0]->getRows() - 1; $j++) {
		$temp = clone $this->matrixes[0]->getElement($j, $i);
		$variable = clone $this->matrixes[0]->getElement($this->matrixes[0]->getRows() - 1, $i);
		if (Fraction::equalsZero($temp)) {
		    $array[$j][$i] = 0;
		} else {
		    $variable->divide($temp);
		    $array[$j][$i] = $variable->getValue();
		}
	    }
	}
	$array2 = array();
	foreach ($array as $key => $value) {
	    $array2[$key] = round(max($value), 2);
	}
	return $array2;
    }

    /**
     * Returns Array of Zero's
     * @return Array
     */
    public function getMinRangearray() {
	$x = array();
	for ($i = 0; $i < count($this->targetfunction[0]); $i++) {
	    $x[] = 0;
	}
	return $x;
    }

    /**
     * Returns array of points where slider parameters present
     * @param Array $dimensions
     * @param Array $values
     * @return Array
     */
    public function getRedrawData(Array $dimensions, Array $values) {
	$json = array();
	$maxRange = $this->getMaxRangearray();
	$minRange = $this->getMinRangearray();
	$point = new Point(count($maxRange));
	$decreaser = 0;

	foreach ($values as $key => $value) {
	    if ($value == 'undefined') {
		unset($values[$key]);
	    } else {
		$decreaser+=$value;
	    }
	}
	$ak = array_keys($dimensions);
	for ($i = $minRange[$dimensions[$ak[0]]]; $i <= $maxRange[$dimensions[$ak[0]]]; $i += Simplex::getIterationSeparation($maxRange[$dimensions[$ak[0]]])) {
	    for ($j = $minRange[$dimensions[$ak[1]]]; $j <= $maxRange[$dimensions[$ak[1]]]; $j += Simplex::getIterationSeparation($maxRange[$dimensions[$ak[1]]])) {
		for ($k = $minRange[$dimensions[$ak[2]]]; $k <= $maxRange[$dimensions[$ak[2]]]; $k+=Simplex::getIterationSeparation($maxRange[$dimensions[$ak[2]]])) {
		    $point->resetPoint();
		    $point->setPointDimension($dimensions[$ak[0]], $i);
		    $point->setPointDimension($dimensions[$ak[1]], $j);
		    $point->setPointDimension($dimensions[$ak[2]], $k);
		    if ($this->isValidPoint($point, $decreaser)) {
			$json[] = array(round($i, 2), round($j, 2), round($k, 2));
		    }
		}
	    }
	}
	return $json;
    }

}

?>
