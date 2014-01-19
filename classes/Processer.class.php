<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Processer
 *
 * @author PETTER
 */
class Processer extends Csv_Reader {

	public $tabela;
	private $a = array();
	private $b = array();
	private $c = array();
	private $d = array();
	private $funkcja;
	private $gomorry;
	private $znaki = array("<=", ">=", "=");

	public function __construct($plik) {
		parent::__construct($plik);
		$this->tabela = parent::get();
		if ($this->tabela['0']['0'] != "max" && $this->tabela['0']['0'] != "min") {
			$this->errormessage('Nierozpoznane ekstremum funkcji. Pierwsze pole powinno zawierać \'min\' lub \'max\'.');
		} else {
			$this->funkcja = $this->tabela['0']['0'];
		}
		if ($this->tabela['0']['1'] != 'false' && $this->tabela['0']['1'] != 'true') {
			$this->errormessage('Nierozpoznane zastosowanie algorytmu Gomorry\'ego. Drugie pole powinno zawierać \'true\' lub \'false\'.');
		} else {
			$this->gomorry = ($this->tabela['0']['1'] == 'true' ? true : false);
		}
		$bb = count($this->tabela['1']);
		for ($i = 1; $i < $bb; $i++) {
			if ($bb != count($this->tabela[$i])) {
				$this->errormessage('Równania są różnej długości. Ilość zmiennych równania powinna wynosić ' . $bb . ', a w równaniu ' . $i . ' jest ich ' . count($this->tabela[$i]));
				break;
			}
		}
		foreach ($this->tabela[0] as $key => $value) {
			if (is_numeric(trim($value))) {
				if (strpos($value, '/')) {
					$temp = explode('/', value);
					$this->a[] = new Fraction($temp[0], $temp[1]);
				} else {
					$this->a[] = new Fraction(trim($value));
				}
			}
		}

		$aa = count($this->tabela);
		$k = 0;
		for ($i = 1; $i < $aa; $i++) {
			for ($j = 0; $j < $bb; $j++) {
				if (in_array($this->tabela[$i][$j], $this->znaki)) {
					$this->c[] = $this->tabela[$i][$j];
					$a = trim($this->tabela[$i][++$j]);
					if (strpos($a, '/')) {
						$temp = explode('/', $a);
						$this->d[] = new Fraction($temp[0], $temp[1]);
					} else {
						$this->d[] = new Fraction($a);
					}
				} else {
					$value = trim($this->tabela[$i][$j]);
					if (strpos($value, '/')) {
						$temp = explode('/', $value);
						$this->b[$k][$j] = new Fraction($temp[0], $temp[1]);
					} else {
						$this->b[$k][$j] = new Fraction($value);
					}
				}
			}
			$k++;
		}
	}

	public function getTextareaData() {
		$array = Array();
		$array[0] = $this->getMinMax();
		$array[1] = $this->gomorry ? 'true' : 'false';
		$string = '';
		foreach ($this->getTargetFunction() as $key => $value) {
			$a = clone $value;
			$a->minusFraction();
			if ($key == 0) {
				$string.=$a . 'x' . ($key + 1);
			} else {
				if (Fraction::isPositive($a)) {
					$string.='+' . $a . 'x' . ($key + 1);
				} else {
					$string.='-' . $a . 'x' . ($key + 1);
				}
			}
		}
		$array[2] = $string;
		unset($string);
		$string = '';

		foreach ($this->getVariables() as $key => $value) {
			foreach ($value as $key2 => $value2) {
				if ($key2 == 0) {
					$string.=$value2->getRealValue() . 'x' . ($key2 + 1);
				} else {
					if (Fraction::isPositive($value2)) {
						$string.='+' . $value2->getRealValue() . 'x' . ($key2 + 1);
					} else {
						$string.='-' . $value2->getRealValue() . 'x' . ($key2 + 1);
					}
				}
			}
			$string.=$this->getSigns()[$key] . $this->getBoundaries()[$key] . ';';
		}
		$array[3] = substr($string, 0, -1);
		unset($string);
		return $array;
	}

	public static function errormessage($message) {
		echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
	}

	public function getTargetFunction() {
		return $this->a;
	}

	public function getVariables() {
		return $this->b;
	}

	public function getSigns() {
		return $this->c;
	}

	public function getBoundaries() {
		return $this->d;
	}

	public function getGomorry() {
		return $this->gomorry;
	}

	public function getMinMax() {
		return $this->funkcja;
	}

}
?>
