<?php

/**
 * Forms CSV input into arrays
 * that can be imported into Simplex class
 * @see Simplex
 *
 * @author PETTER
 */
class Processer extends Csv_Reader {

    private $table;
    private $targetfunction = array();
    private $variables = array();
    private $signs = array();
    private $boundaries = array();
    private $function;
    private $gomorry;
    private $znaki = array("<=", ">=", "=");

    /**
     * Construct with *.csv file
     * @param File $plik
     */
    public function __construct($plik) {
        if(!file_exists($plik)){
            throw new Exception('Processer Class : File Not Found Error. Please notify the administrator of the website.');
        }
        parent::__construct($plik);
        $this->table = parent::get();
        if ($this->table[0][0] == 'max' || $this->table[0][0] == 'min') {
            if ($this->table[0][0] == 'max') {
                $this->function = true;
            } else {
                $this->function = false;
            }
        } else {
            throw new Exception('Nierozpoznane ekstremum funkcji. Pierwsze pole powinno zawierać \'min\' lub \'max\'.');
        }
        if ($this->table[0][1] != 'false' && $this->table[0][1] != 'true') {
            throw new Exception('Nierozpoznane zastosowanie algorytmu DantzigGomorry\'ego. Drugie pole powinno zawierać \'true\' lub \'false\'.' . $this->table[0][1]);
        } else {
            $this->gomorry = ($this->table[0][1] == 'true' ? true : false);
        }
        $bb = count($this->table['1']);
//		for ($i = 1; $i < $bb; $i++) {
//			if ($bb != count($this->table[$i])) {
//				$this->errormessage('Równania są różnej długości. Ilość zmiennych równania powinna wynosić ' . $bb . ', a w równaniu ' . $i . ' jest ich ' . count($this->table[$i]));
//				break;
//			}
//		}

        for ($i = 2; $i < count($this->table[0]); $i++) {
            if (is_numeric(trim($this->table[0][$i]))) {
                $this->targetfunction[] = new Fraction(trim($this->table[0][$i]));
            } elseif (strpos($this->table[0][$i], '/')) {
                $temp = explode('/', $this->table[0][$i]);
                $this->targetfunction[] = new Fraction($temp[0], $temp[1]);
            }
        }

        $aa = count($this->table);
        $k = 0;
        for ($i = 1; $i < $aa; $i++) {
            for ($j = 0; $j < $bb; $j++) {
                if (in_array($this->table[$i][$j], $this->znaki)) {
                    $this->signs[] = $this->table[$i][$j];
                    $a = trim($this->table[$i][++$j]);
                    if (strpos($a, '/')) {
                        $temp = explode('/', $a);
                        $this->boundaries[] = new Fraction($temp[0], $temp[1]);
                    } else {
                        $this->boundaries[] = new Fraction($a);
                    }
                } else {
                    $value = trim($this->table[$i][$j]);
                    if (strpos($value, '/')) {
                        $temp = explode('/', $value);
                        $this->variables[$k][$j] = new Fraction($temp[0], $temp[1]);
                    } else {
                        $this->variables[$k][$j] = new Fraction($value);
                    }
                }
            }
            $k++;
        }
    }

    /**
     * Returns array of inputs
     * @return Array
     */
    public function getTextareaData() {
        $array = Array();
        $array[0] = $this->getMinMax();
        $array[1] = $this->gomorry ? 'true' : 'false';
        $string = '';
        foreach ($this->getTargetFunction() as $key => $value) {
            $a = clone $value;
            if (Fraction::isNegative($a)) {
                $a->minusFraction();
            }
            if ($key == 0) {
                $string.=$a . 'x' . ($key + 1);
            } else {
                if (Fraction::isPositive($a) || Fraction::equalsZero($a)) {
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
                    if (Fraction::isPositive($value2) || Fraction::equalsZero($value2)) {
                        $string.='+' . $value2->getRealValue() . 'x' . ($key2 + 1);
                    } else {
                        $string.='-' . $value2->getRealValue() . 'x' . ($key2 + 1);
                    }
                }
            }
            $string.=$this->getSigns()[$key] . $this->getBoundaries()[$key] . '&#13;&#10;';
        }
        $array[3] = substr($string, 0, -10);
        unset($string);
        return $array;
    }

    /**
     * Output Error Message in jQuery UI format
     * @param string $message
     * @static
     */
    public static function errormessage($message) {
        echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
    }

    /**
     * Return array of elements from target function
     * @return array
     */
    public function getTargetFunction() {
        return $this->targetfunction;
    }

    /**
     * Returns array of LP coefficients
     * @return array
     */
    public function getVariables() {
        return $this->variables;
    }

    /**
     * Returns array of enumSign objects - signs from [<=,>=,=]
     * @return array
     */
    public function getSigns() {
        return $this->signs;
    }

    /**
     * Returns boundaries of each LP equation
     * @return array
     */
    public function getBoundaries() {
        return $this->boundaries;
    }

    /**
     * Returns true/false if Gomory's Cutting Plane Algorithm should be used
     * @return boolean
     */
    public function getGomorry() {
        return $this->gomorry;
    }

    /**
     * Returns true if max, false if min
     * @return boolean
     */
    public function getMinMax() {
        return $this->function;
    }

}
?>
