<?php

class Simplex {

	private $index = 0;
	private $matrixes;
	private $basecol;
	private $baserow;
	private $basis;
	private $zmiennebazowe;
	private $zmienneniebazowe;
	private $M;
	private $N;
	private $O;
	private $wrongsigns;
	private $d;
	private $c;
	private $gomorry;
	private $temp;
	private $signs = Array();

	public function __construct() {
		$this->matrixes = Array();
		$this->basecol = Array();
		$this->baserow = Array();
		$this->zmiennebazowe = Array();
		$this->zmienneniebazowe = Array();
		$this->c = Array();
		$this->temp = new Fraction2();
		$this->basis;
		$this->wrongsigns = 0;
	}

	public function Solve(Array $variables, Array $boundaries, Array $signs, Array $targetfunction, $max = true, $gomorry = false) {
		if (count($signs) != count($boundaries)) {
			$this->errormessage('Nie zgadza się liczba znaków równania i liczba zmiennych ograniczających');
			return 0;
		}
		$this->M = count($variables[0]) + 1; //3
		$this->N = count($boundaries) + 1; //4
		$this->O = count($targetfunction);
		$this->d = $max;
		$this->gomorry = $gomorry;
		$this->targetfunction = $targetfunction;
		$this->signs = $signs;
		$this->c[$this->index] = Array();
		$this->basis = new SplFixedArray($this->O);

		if ($this->d) {
			foreach ($this->signs as $key => $value) {
				$this->c[$this->index][$key] = new Fraction2(0);
				if ($value != "<=") {
					$this->c[$this->index][$key] = new Fraction2(0, 1, 1, 1);
					$this->wrongsigns++;
				}
			}
		} else {
			foreach ($this->signs as $key => $value) {
				$this->c[$this->index][$key] = new Fraction2(0, 1, 1, 1);
				if ($value != "<=") {
					$this->wrongsigns++;
				}
			}
		}
		for ($i = 1; $i < $this->N; $i++) {
			$this->zmiennebazowe[$this->index][$i] = 'S<sub>' . $i . '</sub>';
		}
		for ($i = 1; $i < $this->M; $i++) {
			$this->zmienneniebazowe[$this->index][$i] = 'x<sub>' . $i . '</sub>';
		}
		for ($i = $this->M; $i < $this->O + $this->N; $i++) {
			$this->zmienneniebazowe[$this->index][$i] = 'a<sub>' . ($i - $this->N + 2) . '</sub>';
		}

		if ($this->wrongsigns != 0) {
			for ($i = 2 * ($this->N - 1); $i < 2 * ($this->N - 1) + $this->wrongsigns; $i++) {
				$this->zmienneniebazowe[$this->index][$i] = 'R<sub>' . (1 + $i - 2 * ($this->N - 1)) . '</sub>';
			}
		}

		for ($i = 0; $i < $this->N; $i++) {
			for ($j = 0; $j < $this->N + $this->M - 1 + $this->wrongsigns; $j++) {
				$this->matrixes[$this->index][$i][$j] = new Fraction2(0);
			}
		}

		for ($i = 0; $i < $this->N - 1; $i++) {
			for ($j = 0; $j < $this->M - 1; $j++) {
				$this->matrixes[$this->index][$i][$j] = clone $variables[$i][$j];
			}
		}

		for ($i = 0; $i < $this->N - 1; $i++) {
			$this->matrixes[$this->index][$i][$this->N + $this->wrongsigns + $this->M - 2] = clone $boundaries[$i];
		}

		$ax = 0;
		foreach ($this->signs as $key => $value) {
			switch ($value) {
				case ">=":
					$this->matrixes[$this->index][$key][$this->M - 1 + $key] = new Fraction2(-1);
					$this->matrixes[$this->index][$key][$this->M - 1 + $this->N - 1 + $ax] = new Fraction2(1);
					$ax++;
					break;
				default:
					for ($j = $this->M - 1; $j < $this->N + $this->M - 2; $j++) {
						if (($j - ($this->M - 1)) == $key) {
							$this->matrixes[$this->index][$key][$j] = new Fraction2(1);
						}
					}
					break;
			}
		}

		for ($i = 0; $i < $this->O; $i++) {
			$targetfunction[$i]->minusFraction();
			$this->matrixes[$this->index][$this->N - 1][$i] = clone $targetfunction[$i];
		}

		if ($this->d) {
			for ($i = 0; $i < $this->N + $this->M - 2; $i++) {
				$this->temp = new Fraction2();
				for ($j = 0; $j < $this->N - 1; $j++) {
					if ($this->signs[$j] == ">=") {
						$this->temp->add($this->matrixes[$this->index][$j][$i]);
					}
				}
				$this->matrixes[$this->index][$this->N - 1][$i]->substract(new Fraction2(0, 1, $this->temp->getNumerator(), $this->temp->getDenominator()));
			}
			//for boundaries
			$this->temp = new Fraction2();
			for ($j = 0; $j < $this->N - 1; $j++) {
				if ($this->signs[$j] != "<=") {
					$this->temp->add($this->matrixes[$this->index][$j][($this->M - 1) + 2 * $this->wrongsigns + ($this->M - $this->wrongsigns)]);
				}
			}
			$this->matrixes[$this->index][$this->N - 1][($this->M - 1) + 2 * $this->wrongsigns + ($this->M - $this->wrongsigns)]->substract(new Fraction2(0, 1, $this->temp->getNumerator(), $this->temp->getDenominator()));
		} else {
			for ($i = 0; $i < $this->N + $this->M - 2; $i++) {
				$this->temp = new Fraction2();
				for ($j = 0; $j < $this->N - 1; $j++) {
					if ($this->signs[$j] == ">=") {
						$this->temp->add($this->matrixes[$this->index][$j][$i]);
					}
				}
				$this->matrixes[$this->index][$this->N - 1][$i]->substract(new Fraction2(0, 1, $this->temp->getNumerator(), $this->temp->getDenominator()));
			}
			//for boundaries
			$this->temp = new Fraction2();
			for ($j = 0; $j < $this->N - 1; $j++) {
				$this->temp->add($this->matrixes[$this->index][$j][($this->M - 1) + 2 * $this->wrongsigns]);
			}
			$this->matrixes[$this->index][$this->N - 1][($this->M - 1) + 2 * $this->wrongsigns + ($this->M - $this->wrongsigns)]->substract(new Fraction2(0, 1, $this->temp->getNumerator(), $this->temp->getDenominator()));
		}

		while (!$this->check()) {
			$this->index++;
			$this->matrixes[$this->index] = $this->matrixes[$this->index - 1];
			$this->zmiennebazowe[$this->index] = $this->zmiennebazowe[$this->index - 1];
			$this->zmienneniebazowe[$this->index] = $this->zmienneniebazowe[$this->index - 1];
			$this->c[$this->index] = $this->c[$this->index - 1];
			$p = $this->findBasecol();
			if ($p == -1) {
				break;
			} else {
				$this->basecol[$this->index] = $p;
			}
			$q = $this->findBaserow($p);
			if ($q == -1) {
				$this->errormessage("Linear problem is unbounded");
				unset($this->matrixes[$this->index]);
				$this->index--;
				break;
			} else {
				$this->baserow[$this->index] = $q;
			}
			$this->c[$this->index][$q] = clone $this->matrixes[0][$this->M][$p];
			$this->c[$this->index][$q]->minusFraction();
			$this->c[$this->index][$q] = new Fraction2($this->c[$this->index][$q]->getNumerator(), $this->c[$this->index][$q]->getDenominator());
			$this->swapBase();
			$this->gaussjordan();

			if (!isset($this->basis[$q])) {
				$this->basis[$p] = $q;
			}

			if ($this->wrongsigns != 0) {
				for ($i = 0; $i < ($this->M - 1) + 2 * $this->wrongsigns + ($this->M - $this->wrongsigns); $i++) {
					if (!in_array($i, $this->basecol)) {
						$this->temp = new Fraction2();
						for ($j = 0; $j < $this->N - 1; $j++) {
							if ($this->signs[$j] == ">=") {
								$this->temp->add($this->matrixes[$this->index][$j][$i]);
							}
						}
						$this->matrixes[$this->index][$this->N - 1][$i]->substract(new Fraction2(0, 1, $this->temp->getNumerator(), $this->temp->getDenominator()));
					}
				}
				//for last column
				$this->temp = new Fraction2();
				for ($j = 0; $j < $this->N - 1; $j++) {
					if (!in_array($j, $this->baserow) && $this->signs[$j] != "<=") {
						$this->temp->add($this->matrixes[$this->index][$j][($this->M - 1) + 2 * $this->wrongsigns + ($this->M - $this->wrongsigns)]);
					}
				}
				$this->matrixes[$this->index][$this->N - 1][($this->M - 1) + 2 * $this->wrongsigns + ($this->M - $this->wrongsigns)]->substract(new Fraction2(0, 1, $this->temp->getNumerator(), $this->temp->getDenominator()));
			}
			//------------------------
//			if($this->index>0){
//				break;
//			}
			//------------------------
		}
		$this->basecol[$this->index] = -1;
		$this->baserow[$this->index] = -1;

		if ($gomorry && $this->index != 0) {
			while (true) {
				$this->index++;
				$this->matrixes[$this->index] = $this->matrixes[$this->index - 1];
				$k = $this->gomorryrow();
				if ($k == -1) {
					unset($this->matrixes[$this->index]);
					$this->index--;
					break;
				}

				$this->zmiennebazowe[$this->index] = $this->zmiennebazowe[$this->index - 1];
				$this->zmienneniebazowe[$this->index] = $this->zmienneniebazowe[$this->index - 1];
				$this->c[$this->index] = $this->c[$this->index - 1];
				$p = $this->gomorryAddRow($k);
				$this->c[$this->index][2 + count($this->c[$this->index][0])] = new Fraction2(0);
				$this->zmiennebazowe[$this->index][count($this->matrixes[0])] = 'Z<sub>1</sub>';
				$q = count($this->matrixes[$this->index]) - 2;
//echo 'Przekształcenie po elemencie ['.$q.','.$p.']='.$this->matrixes[$this->index][$q][$p]->toString();
				$this->basecol[$this->index] = $p;
				$this->baserow[$this->index] = $q;
//----------------------------------------------------------------
				$this->index++;
				$this->zmiennebazowe[$this->index] = $this->zmiennebazowe[$this->index - 1];
				$this->zmienneniebazowe[$this->index] = $this->zmienneniebazowe[$this->index - 1];
				$this->matrixes[$this->index] = $this->matrixes[$this->index - 1];
				$this->basecol[$this->index] = -1;
				$this->baserow[$this->index] = -1;
				$this->c[$this->index] = $this->c[$this->index - 1];
				$this->gaussjordan();
				if (!$this->gomorrycheck()) {
					break;
				}
//break;
			}
		}
	}

	public function printRawMatrix() {
		for ($i = 0; $i < $this->index + 1; $i++) {
			$a = count($this->matrixes[$i]);
			$b = count($this->matrixes[$i][0]);
			echo '<table class="result" border="1"><tbody>';
			for ($j = 0; $j < $a; $j++) {
				echo '<tr>';
				for ($k = 0; $k < $b; $k++) {
					echo '<td>' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
				}
				echo '</tr>';
			}
			echo '</tbody></table>';
			echo '<br/><br/>';
		}
	}

	public function printCol() {
		foreach ($this->basecol as $key => $value) {
			echo $key . ':' . $value . ' ';
		}
		echo '<br/>';
	}

	public function printRow() {
		foreach ($this->baserow as $key => $value) {
			echo $key . ':' . $value . ' ';
		}
		echo '<br/>';
	}

	public function printSolution() {
		//PRINT FOR REGULAR MATRIX
		//echo of the first matrix
		$a = count($this->matrixes[0]);
		$b = count($this->matrixes[0][0]);
		echo '<table class="result"><tbody>';
		echo '<tr><th style="width:30px;text-align:center;" class="ui-state-default">(0)</th>';
		echo '<th style="width:30px;text-align:center;" class="ui-state-default"></th>';
		for ($j = 0; $j < $this->N + $this->M - 2 + $this->wrongsigns; $j++) {
			if (isset($this->targetfunction[$j])) {
				echo '<th class="ui-state-default">' . $this->targetfunction[$j]->toString() . '</th>';
			} else {
				echo '<th class="ui-state-default">0</th>';
			}
		}
		echo '<th class="ui-state-default" rowspan="2">Warto&#347;&#263;</th></tr>';
		echo '<tr><th class="ui-state-default">Baza</th>';
		echo '<th class="ui-state-default">c</th>';
		for ($j = 0; $j < $this->N + $this->wrongsigns + $this->M - 2; $j++) {
			if (isset($this->zmienneniebazowe[0][$j + 1])) {
				echo '<th class="ui-state-default">' . $this->zmienneniebazowe[0][$j + 1] . '</th>';
			}
		}
		echo '</tr>';
		for ($j = 0; $j < $a; $j++) {
			if (isset($this->zmiennebazowe[0][$j + 1])) {
				echo '<tr><th class="ui-state-default">' . $this->zmiennebazowe[0][$j + 1] . '</th><td class="center">' . $this->c[0][$j]->toString() . '</td>';
			} else {
				echo '<tr><th class="ui-state-default">z<sub>j</sub>-c<sub>j</sub></th><th></th>';
			}

			for ($k = 0; $k < $b; $k++) {
				if ($k == $this->basecol[0] && $j == $this->baserow[0] && 0 != $this->index) {
					echo '<td style="color:white;background-color:red;text-align:center;width:45px;">' . $this->matrixes[0][$j][$k]->toString() . '</td>';
				} else {
					echo '<td style="text-align:center;width:45px;">' . $this->matrixes[0][$j][$k]->toString() . '</td>';
				}
			}
			echo '</tr>';
		}
		echo '</tbody></table>';
		echo '<br/><br/>';
//rest of matrixes
		if (!$this->gomorry) {
			for ($i = 1; $i < $this->index + 1; $i++) {
				$a = count($this->matrixes[$i]);
				$b = count($this->matrixes[$i][0]);
				echo '<table class="result"><tbody>';
				echo '<tr><th style="width:30px;text-align:center;" class="ui-state-default">(' . $i . ')</th><th style="width:30px;text-align:center;" class="ui-state-default"></th>';
				for ($j = 0; $j < $this->N + $this->M - 2 + $this->wrongsigns; $j++) {
					if (isset($this->targetfunction[$j])) {
						echo '<th class="ui-state-default">' . $this->targetfunction[$j]->toString() . '</th>';
					} else {
						echo '<th class="ui-state-default">0</th>';
					}
				}
				echo '<th class="ui-state-default" rowspan="2">Warto&#347;&#263;</th></tr>';
				echo '<tr><th class="ui-state-default">Baza</th>';
				echo '<th class="ui-state-default">c</th>';
				for ($j = 0; $j < $this->N + $this->wrongsigns + $this->M - 2; $j++) {
					if (isset($this->zmienneniebazowe[$i][$j + 1])) {
						echo '<th class="ui-state-default">' . $this->zmienneniebazowe[$i][$j + 1] . '</th>';
					}
				}
				echo '</tr>';
				for ($j = 0; $j < $a; $j++) {
					if (isset($this->zmiennebazowe[$i][($j + 1)])) {
						echo '<tr><th class="ui-state-default">' . $this->zmiennebazowe[$i][($j + 1)] . '</th><td class="center">' . $this->c[$i][$j]->toString() . '</td>';
					} else {
						echo '<tr><th class="ui-state-default">z<sub>j</sub>-c<sub>j</sub></th><th></th>';
					}
					for ($k = 0; $k < $b; $k++) {
						if ($k == $this->basecol[$i] && $j == $this->baserow[$i] && $i != $this->index) {
							if ($k == $this->basecol[$i - 1] && $j == $this->baserow[$i - 1]) {
								echo '<td style="color:white;background-color:red;text-align:center;width:45px;" data-dane="m,1,' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							} elseif ($k == $this->basecol[$i - 1]) {
								echo '<td style="color:white;background-color:red;text-align:center;width:45px;" data-dane="c,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							} elseif ($j == $this->baserow[$i - 1]) {
								echo '<td style="color:white;background-color:red;text-align:center;width:45px;" data-dane="r,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							} else {
								echo '<td style="color:white;background-color:red;text-align:center;width:45px;" data-dane="g,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$k]->toString() . ',' . $this->matrixes[$i - 1][$j][$this->basecol[$i - 1]]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							}
						} else {
							if ($k == $this->basecol[$i - 1] && $j == $this->baserow[$i - 1]) {
								echo '<td style="text-align:center;width:45px;" data-dane="m,1,' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							} elseif ($k == $this->basecol[$i - 1]) {
								echo '<td style="text-align:center;width:45px;" data-dane="c,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							} elseif ($j == $this->baserow[$i - 1]) {
								echo '<td style="text-align:center;width:45px;" data-dane="r,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							} else {
								echo '<td style="text-align:center;width:45px;" data-dane="g,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$k]->toString() . ',' . $this->matrixes[$i - 1][$j][$this->basecol[$i - 1]]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							}
						}
					}
					echo '</tr>';
				}
				echo '</tbody></table>';
				echo '<br/><br/>';
			}
		} else {
// GOMORRY PRINT
//-------------------------------------------------
			for ($i = 1; $i < $this->index + 1; $i++) {
				$a = count($this->matrixes[$i]);
				$b = count($this->matrixes[$i][0]);
//echo 'macierz['.$i.']='.$a.'x'.$b;
				if ($this->basecol[$i - 1] == -1 && $this->baserow[$i - 1] == -1) {
					echo '<table class="result2"><tbody>';
				} else {
					echo '<table class="result"><tbody>';
				}
				echo '<tr><th style="width:30px;text-align:center;" class="ui-state-default">(' . $i . ')</th>';
				echo '<th style="width:30px;text-align:center;" class="ui-state-default"></th>';
				for ($j = 0; $j < $this->N + $this->M - 2 + $this->wrongsigns; $j++) {
					if (isset($this->targetfunction[$j])) {
						echo '<th class="ui-state-default">' . $this->targetfunction[$j]->toString() . '</th>';
					} else {
						echo '<th class="ui-state-default">0</th>';
					}
				}
				echo '<th class="ui-state-default" rowspan="2">Warto&#347;&#263;</th></tr>';
				echo '<tr><th class="ui-state-default">Baza</th>';
				echo '<th class="ui-state-default">c</th>';
				for ($j = 0; $j < $this->N + $this->wrongsigns + $this->M - 2; $j++) {
					if (isset($this->zmienneniebazowe[$i][$j + 1])) {
						echo '<th class="ui-state-default">' . $this->zmienneniebazowe[$i][$j + 1] . '</th>';
					} else if ($j + 2 - $this->M <= $this->M) {
						echo '<th class="ui-state-default">a<sub>' . ($j + 2 - $this->M) . '</sub></th>';
					} else {
						echo '<th class="ui-state-default">R<sub>' . ($j + 2 - (2 * $this->M)) . '</sub></th>';
					}
				}
				echo '</tr>';
				for ($j = 0; $j < $a; $j++) {
					if (isset($this->zmiennebazowe[$i][1 + $j])) {
						echo '<tr><th class="ui-state-default">' . $this->zmiennebazowe[$i][1 + $j] . '</th><td class="center">' . $this->c[$this->index][$j]->toString() . '</td>';
					} else {
						echo '<tr><th class="ui-state-default">z<sub>j</sub>-c<sub>j</sub></th>';
						echo '<td></td>';
					}


					if ($this->baserow[$i] == -1 && $this->basecol[$i] == -1) {
//brak wierszy głównych
						for ($k = 0; $k < $b; $k++) {
							if ($k == $this->basecol[$i - 1] && $j == $this->baserow[$i - 1]) {
								echo '<td style="text-align:center;width:45px;" data-dane="m,1,' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							} elseif ($k == $this->basecol[$i - 1]) {
								echo '<td style="text-align:center;width:45px;" data-dane="c,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							} elseif ($j == $this->baserow[$i - 1]) {
								echo '<td style="text-align:center;width:45px;" data-dane="r,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							} else {
								echo '<td style="text-align:center;width:45px;" data-dane="g,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$k]->toString() . ',' . $this->matrixes[$i - 1][$j][$this->basecol[$i - 1]]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							}
						}
					} elseif ($this->baserow[$i - 1] == -1 && $this->basecol[$i - 1] == -1) {
//wiersze główne
						for ($k = 0; $k < $b; $k++) {
							if ($k == $this->basecol[$i] && $j == $this->baserow[$i]) {
								echo '<td style="color:white;background-color:red;text-align:center;width:45px;">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							} else {
								echo '<td style="text-align:center;width:45px;">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
							}
						}
					} else {
						for ($k = 0; $k < $b; $k++) {
							if ($k == $this->basecol[$i] && $j == $this->baserow[$i] && $i != $this->index) {
								if ($k == $this->basecol[$i - 1] && $j == $this->baserow[$i - 1]) {
									echo '<td style="color:white;background-color:red;text-align:center;width:45px;" data-dane="m,1,' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
								} elseif ($k == $this->basecol[$i - 1]) {
									echo '<td style="color:white;background-color:red;text-align:center;width:45px;" data-dane="c,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
								} elseif ($j == $this->baserow[$i - 1]) {
									echo '<td style="color:white;background-color:red;text-align:center;width:45px;" data-dane="r,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
								} else {
									echo '<td style="color:white;background-color:red;text-align:center;width:45px;" data-dane="g,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$k]->toString() . ',' . $this->matrixes[$i - 1][$j][$this->basecol[$i - 1]]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
								}
							} else {
								if ($k == $this->basecol[$i - 1] && $j == $this->baserow[$i - 1]) {
									echo '<td style="text-align:center;width:45px;" data-dane="m,1,' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
								} elseif ($k == $this->basecol[$i - 1]) {
									echo '<td style="text-align:center;width:45px;" data-dane="c,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
								} elseif ($j == $this->baserow[$i - 1]) {
									echo '<td style="text-align:center;width:45px;" data-dane="r,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
								} else {
									echo '<td style="text-align:center;width:45px;" data-dane="g,' . $this->matrixes[$i - 1][$j][$k]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$k]->toString() . ',' . $this->matrixes[$i - 1][$j][$this->basecol[$i - 1]]->toString() . ',' . $this->matrixes[$i - 1][$this->baserow[$i - 1]][$this->basecol[$i - 1]]->toString() . '">' . $this->matrixes[$i][$j][$k]->toString() . '</td>';
								}
							}
						}
					}
					echo '</tr>';
				}
				echo '</tbody></table>';
				echo '<br/><br/>';
			}
		}
	}

	private function findBaseCol() {
		$a = count($this->matrixes[$this->index]);
		$b = count($this->matrixes[$this->index][0]);
		$startv = new Fraction2(100000);
		$starti = -1;
		for ($i = 0; $i < $this->M + $this->N - 2; $i++) {
			if ($this->matrixes[$this->index][$a - 1][$i]->getNumerator() == 0) {
				continue;
			} elseif ($startv->compare($this->matrixes[$this->index][$a - 1][$i]) && Fraction2::isNegative($this->matrixes[$this->index][$a - 1][$i])) {
				//echo 'this=' . $startv->toString() . ' candidate=' . $this->matrixes[$this->index][$a - 1][$i]->toString() . '<br/>';
				$starti = $i;
				$startv = $this->matrixes[$this->index][$a - 1][$i];
			}
//echo $this->matrixes[$this->index][$a-1][$i]->toString();
		}
		$this->basecol[$this->index - 1] = $starti;
		//echo 's='.$startv->toString() . ' ' . $starti.'<br/>';
		return $starti;
	}

	private function findBaseRow($p) {
		$a = count($this->matrixes[$this->index]);
		$b = count($this->matrixes[$this->index][0]);
		$startv = new Fraction2(100000);
		$starti = -1;
		for ($i = 0; $i < $a - 1; $i++) {
//echo $this->matrixes[$this->index][$i][$b-1]->toString();
			$s = new Fraction2($this->matrixes[$this->index][$i][$b - 1]->getNumerator(), $this->matrixes[$this->index][$i][$b - 1]->getDenominator());
			$n = new Fraction2($this->matrixes[$this->index][$i][$p]->getNumerator(), $this->matrixes[$this->index][$i][$p]->getDenominator());
			if ($n->getNumerator() == 0) {
				continue;
			} else {
//echo $s->toString().'/'.$n->toString().'<br/>';
				$s->divide($n);
//echo $s->toString().'<br/>';
				if (!$s->compare($startv) && Fraction2::isPositive($s)) {
					$starti = $i;
					$startv = $s;
				}
			}
		}
//echo $startv->toString().' '.$starti.'<br/>';
		$this->baserow[$this->index - 1] = $starti;
		return $starti;
	}

	private function gaussjordan() {
		$a = count($this->matrixes[$this->index]);
		$b = count($this->matrixes[$this->index][0]);
		for ($i = 0; $i < $a; $i++) {
			for ($j = 0; $j < $b; $j++) {
				if ($i == $this->baserow[$this->index - 1] && $j == $this->basecol[$this->index - 1]) {
//element główny
//                    $s = new Fraction2($this->matrixes[$this->index][$i][$j]->getNumerator(), $this->matrixes[$this->index][$i][$j]->getDenominator());
//                    $s->reverse();
//                    $this->matrixes[$this->index][$i][$j] = $s;
					$this->matrixes[$this->index][$i][$j] = new Fraction2(1);
				} elseif ($i == $this->baserow[$this->index - 1]) {
//wiersz główny
					$s = new Fraction2($this->matrixes[$this->index][$i][$j]->getNumerator(), $this->matrixes[$this->index][$i][$j]->getDenominator());
					$n = new Fraction2($this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]]->getNumerator(), $this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]]->getDenominator());
					$s->divide($n);
//echo $s->toString();
					$this->matrixes[$this->index][$i][$j] = new Fraction2($s->getNumerator(), $s->getDenominator());
				} elseif ($j == $this->basecol[$this->index - 1]) {
//kolumna główna
//                    $s = new Fraction2($this->matrixes[$this->index][$i][$j]->getNumerator(), $this->matrixes[$this->index][$i][$j]->getDenominator());
//                    $n = new Fraction2($this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]]->getNumerator(), $this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]]->getDenominator());
//                    $s->divide($n);
//                    $s->multiply(-1);
//                    $this->matrixes[$this->index][$i][$j] = $s;
					$this->matrixes[$this->index][$i][$j] = new Fraction2(0);
//$this->matrixes[$this->index][$i][$j] = -round($this->matrixes[$this->index][$i][$j] / $this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]], 2);
				} else {
//normalny element
					$s = new Fraction2($this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$j]->getNumerator(), $this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$j]->getDenominator());
					$m = new Fraction2($this->matrixes[$this->index - 1][$i][$this->basecol[$this->index - 1]]->getNumerator(), $this->matrixes[$this->index - 1][$i][$this->basecol[$this->index - 1]]->getDenominator());
					$n = new Fraction2($this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]]->getNumerator(), $this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]]->getDenominator());
					$l = new Fraction2($this->matrixes[$this->index][$i][$j]->getNumerator(), $this->matrixes[$this->index][$i][$j]->getDenominator());
//echo $l->toString().'='.$l->toString().'-('.$s->toString().'*'.$m->toString().')/'.$n->toString().'<br/>';
					$s->multiply($m);
					$s->divide($n);
//echo '('.$s->toString().'*'.$m->toString().')/'.$n->toString();
					$l->substract($s);
//echo '='.$l->toString().'<br/>';
					$this->matrixes[$this->index][$i][$j] = $l;
//$this->matrixes[$this->index][$i][$j] = round((($this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$j] * $this->matrixes[$this->index - 1][$i][$this->basecol[$this->index - 1]]) / $this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]]), 2);
				}
			}
		}
	}

	private function check() {
		return $this->checktargetfunction();
	}

	private function checktargetfunction() {
		$a = count($this->matrixes[$this->index]);
		$b = count($this->matrixes[$this->index][0]);
		for ($i = 0; $i < $this->M + $this->N - 2; $i++) {
			if (Fraction2::isNegative($this->matrixes[$this->index][$a - 1][$i])) {
				return false;
			}
		}
		return true;
	}

	private function swapBase() {
//echo 'zmieniam '.$this->zmiennebazowe[$this->index][($this->baserow[$this->index-1]+1)].' na'.$this->zmienneniebazowe[$this->index][($this->basecol[$this->index-1]+1)].'<br/>';
		$buffer = $this->zmiennebazowe[$this->index][($this->baserow[$this->index - 1] + 1)];
		$this->zmiennebazowe[$this->index][($this->baserow[$this->index - 1] + 1)] = $this->zmienneniebazowe[$this->index][($this->basecol[$this->index - 1] + 1)];
		$this->zmienneniebazowe[$this->index][($this->basecol[$this->index - 1] + 1)] = $buffer;
		unset($buffer);
	}

	public function printValuePair() {
		$a = count($this->matrixes[$this->index][0]);
		$x = $this->getValuePair();
		foreach ($x as $key => $value) {
			if ($value != 'NaN') {
				echo 'x<sub>' . ($key + 1) . '</sub>=' . $value->toString() . '<br/>';
				continue;
			}
		}
	}

	public function getValuePair() {
		if ($this->index == 0) {
			return Array("NaN");
		} else {
			$x = Array();
			$a = count($this->matrixes[$this->index][0]);
			foreach ($this->basis as $key => $value) {
				$x[$key] = $this->matrixes[$this->index][$value][$a - 1];
			}
			return $x;
		}
	}

	private function gomorrycheck() {
		$x = $this->getValuePair();
		foreach ($x as $key => $value) {
			if (!is_integer($value)) {
				return false;
			}
		}
		return true;
	}

	private function gomorryrow() {
		foreach ($this->returnValuePair() as $key => $value) {
			if ($value->getNumerator() != 1) {
				return $key;
			}
		}
		return -1;
	}

	private function gomorryAddRow($q) {
		$a = count($this->matrixes[$this->index][0]);
		$b = count($this->matrixes[$this->index]);
		$startv = new Fraction2(1);
		$starti = -1;
		for ($i = 0; $i < $a; $i++) {
			$this->matrixes[$this->index][$b][$i] = $this->matrixes[$this->index][$b - 1][$i];
			$s = new Fraction2($this->matrixes[$this->index][$q][$i]->getNumerator(), $this->matrixes[$this->index][$q][$i]->getDenominator());
			$s->getImproperPart();
//echo $s->toString();
			$this->matrixes[$this->index][$b - 1][$i] = $s;
			if (!$this->matrixes[$this->index][$q][$i]->compare($startv)) {
				$startv = $this->matrixes[$this->index][$q][$i];
				$starti = $i;
			}
		}
//echo $starti;
		return $starti;
	}

	public function getResult() {
		$a = count($this->matrixes[$this->index]);
		$b = count($this->matrixes[$this->index][0]);
		return $this->matrixes[$this->index][$a - 1][$b - 1];
	}

	public function printResult() {
		echo 'W=' . $this->getResult()->toString();
	}

	public static function errormessage($message) {
		echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
	}

	public static function getjsonData(Array $variables, Array $boundaries, Array $targetfunction, $signs) {
		$a = count($variables[0]);
		$b = count($variables);
		$json = Array();
		switch ($a) {
			case 2:
				$maxx = new Fraction2(0);
				$maxy = new Fraction2(0);
				for ($i = 0; $i < $b; $i++) {
					if ($variables[$i][1]->getNumerator() == 0) {
						continue;
					}
					$s = new Fraction2($boundaries[$i]->getNumerator(), $boundaries[$i]->getDenominator());
					$s->divide($variables[$i][1]);
					if ($s->compare($maxy)) {
						$maxy = $s;
					}
					if ($variables[$i][0]->getNumerator() == 0) {
						continue;
					}
					$s = new Fraction2($boundaries[$i]->getNumerator(), $boundaries[$i]->getDenominator());
					$s->divide($variables[$i][0]);
					if ($s->compare($maxx)) {
						$maxx = $s;
					}
				}
				for ($i = 0; $i < $b; $i++) {
					$json[$i] = Array('label' => 'S' . ($i + 1), 'data' => '');
					if ($variables[$i][1]->getNumerator() == 0) {
						$s = new Fraction2($boundaries[$i]->getNumerator(), $boundaries[$i]->getDenominator());
						$s->divide($variables[$i][0]);
						$json[$i]['data'][] = Array($s->getRealValue(), $maxy->getRealValue());
					} else {
						$j = new Fraction2($boundaries[$i]->getNumerator(), $boundaries[$i]->getDenominator());
						$j->divide($variables[$i][1]);
						$json[$i]['data'][] = Array(0, $j->getRealValue());
					}
					if ($variables[$i][0]->getNumerator() == 0) {
						$s = new Fraction2($boundaries[$i]->getNumerator(), $boundaries[$i]->getDenominator());
						$s->divide($variables[$i][1]);
						$json[$i]['data'][] = Array($maxx->getRealValue(), $s->getRealValue());
					} else {
						$j = new Fraction2($boundaries[$i]->getNumerator(), $boundaries[$i]->getDenominator());
						$j->divide($variables[$i][0]);
						$json[$i]['data'][] = Array($j->getRealValue(), 0);
					}
				}
				if ($targetfunction[0]->getNumerator() != 0 && $targetfunction[1]->getNumerator() != 0) {
					$t = new Fraction2($targetfunction[0]->getNumerator(), $targetfunction[0]->getDenominator());
					$t->multiply($maxx);
					$t->divide($targetfunction[1]);
					$json[] = Array('label' => 'gradient', 'data' => Array(Array(0, 0), Array($maxx->getRealValue(), $t->getRealValue())));
				}
				echo '<script>';
				echo '$(document).ready(function(){';
				echo '$.plot($("#placeholder1"),' . json_encode($json) . ');';
				echo '});';
				echo '</script>';
				echo '<div style="width:480px;float:right;">';
				echo '<div id="placeholder1" style="width: 480px; height: 360px;"></div>';
				echo '</div>';
				break;
			default:
				$maxx = new Fraction2(0);
				$maxy = new Fraction2(0);
				$maxz = new Fraction2(0);
				for ($i = 0; $i < $b; $i++) {
					if ($variables[$i][1]->getNumerator() == 0) {
						continue;
					}
					$s = new Fraction2($boundaries[$i]->getNumerator(), $boundaries[$i]->getDenominator());
					$s->divide($variables[$i][1]);
					if ($s->compare($maxy)) {
						$maxy = $s;
					}

					if ($variables[$i][0]->getNumerator() == 0) {
						continue;
					}
					$s = new Fraction2($boundaries[$i]->getNumerator(), $boundaries[$i]->getDenominator());
					$s->divide($variables[$i][0]);
					if ($s->compare($maxx)) {
						$maxx = $s;
					}
					if ($variables[$i][2]->getNumerator() == 0) {
						continue;
					}

					$s = new Fraction2($boundaries[$i]->getNumerator(), $boundaries[$i]->getDenominator());
					$s->divide($variables[$i][2]);
					if ($s->compare($maxz)) {
						$maxz = $s;
					}
				}
				for ($i = 0; $i < $maxx->getRealValue(); $i = $i + ($maxx->getRealValue() / 25)) {
					for ($j = 0; $j < $maxy->getRealValue(); $j = $j + ($maxy->getRealValue() / 25)) {
						for ($k = 0; $k < $maxz->getRealValue(); $k = $k + ($maxz->getRealValue() / 25)) {
							if (Simplex::isValidPoint($i, $j, $k, $variables, $boundaries, $signs)) {
								$json[] = Array($i, $j, $k);
							}
						}
					}
				}
				echo '<canvas id="canvas1" width="613" height="500"></canvas>';
				echo '<script>';
				echo '$(document).ready(function() {';
				echo 'var vars = [];';
				echo 'a =' . json_encode($json) . ';';
				echo 'for (var i = 0; i < a.length; i++) {';
				echo 'vars.push("Punkt" + (i + 1));';
				echo '}';
				echo 'var x = {
                            "y": {
                                "vars": vars,
                                "smps": [
                                    "X",
                                    "Y",
                                    "Z"
                                ],
                                "desc": [
                                    "Simplex method"
                                ],
                                "data": a
                            }
                        };';
				echo 'new CanvasXpress("canvas1", x, {';
				echo '"graphType": "Scatter3D",';
				echo '"useFlashIE": true,';
				echo '"xAxis": [';
				echo '"X"';
				echo '],';
				echo '"yAxis": [';
				echo '"Y"';
				echo '],';
				echo '"zAxis": [';
				echo '"Z"';
				echo '],';
				echo '"scatterType": false,';
				echo '"setMinX": 0,';
				echo '"setMinY": 0';
				echo '});';
				echo '});';
				echo '</script>';
				break;
		}
	}

	public static function isValidPoint($x, $y, $z, $variables, $boundaries, $signs) {
		$b = count($boundaries);
		$str = false;
		for ($i = 0; $i < $b; $i++) {
			eval("\$str = ((\$variables[$i][0]->getRealValue()*$x+\$variables[$i][1]->getRealValue()*$y+\$variables[$i][2]->getRealValue()*$z)$signs[$i](\$boundaries[$i]->getRealValue())) ? true : false;");
			if (!$str) {
				return false;
			}
		}
		return true;
	}

}
?>