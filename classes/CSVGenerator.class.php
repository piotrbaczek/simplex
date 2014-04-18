<?php

/**
 * @example ../sources/generateCSV.php generating csv from input data
 * @author PETTER
 */
class CSVGenerator {

    /**
     * True/False corresponding to maximization function
     * @var String 
     */
    private $function;

    /**
     * 'true'/'false' corresponding to use of Gomory algorithm
     * @var String
     */
    private $gomorryFunction;

    /**
     * Target function on Linear problem URI encoded
     * @var String
     */
    private $targetFunction;

    /**
     * Multidimensional array with coefficients if Linear problem
     * @var String
     */
    private $textarea;

    public function __construct($function, $gomorry, $targetFunction, $textarea) {
        $this->function = $function;
        $this->gomorryFunction = $gomorry;
        $this->targetFunction = $targetFunction;
        $this->textarea = $textarea;
        echo urldecode($this->textarea);
    }

    /**
     * Creates Array to be outputed as CSV from input data
     * @return Array
     */
    public function toArray() {
        $array = Array();
        if ($this->function == 'true') {
            $array[0][0] = 'max';
        } else {
            $array[0][0] = 'min';
        }
        if ($this->gomorryFunction == 'true') {
            $array[0][1] = 'true';
        } else {
            $array[0][1] = 'false';
        }
        $tf = preg_split('/x|\+|\-/', urldecode($this->targetFunction));
        $ArrayIndex = 2;
        for ($i = 0; $i < count($tf); $i+=2) {
            $array[0][$ArrayIndex] = $tf[$i];
            $ArrayIndex++;
        }
        $rows = explode("%0D%0A", $this->textarea);
        foreach ($rows as $key => $value) {
            $rows[$key] = urldecode($value);
        }
        foreach ($rows as $key => $value) {
            echo $value . PHP_EOL;
            $row = preg_split('/=|<=|>=/', $value);
            $left = preg_split('/x1|x2|x3|x4|x5|x6|x7|x8|x9/', $row[0]);
            for ($i = 0; $i < count($left); $i++) {
                if (empty($left[$i])) {
                    continue;
                } else {
                    $array[$key + 1][] = (float) $left[$i];
                }
            }
            if (strpos($value, '<=') !== false) {
                $array[$key + 1][] = '<=';
            } elseif (strpos($value, '>=') !== false) {
                $array[$key + 1][] = '>=';
            } else {
                $array[$key + 1][] = '=';
            }
            $array[$key + 1][] = $row[1];
        }
        return $array;
    }

    /**
     * Puts data into PHP's fputcsv() outputing as CSV
     * @param array $data
     * @static
     * @description 
     */
    public static function outputCSV(Array $data) {
        $output = fopen("php://output", "w");
        foreach ($data as $row) {
            fputcsv($output, $row, ';');
        }
        fclose($output);
    }

}
