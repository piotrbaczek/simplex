<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SimplexSolver
 *
 * @author pgolasz@gmail.com &copy; 2012 www.pgolasz.me/simplex
 */
class SimplexSolver {

    public $M;
    public $N;
    public $O;
    public $matrixes;
    public $index = 0;
    public $baserow;
    public $basecol;
    public $basis;
    public $d;
    public $loop = 0;
    public $gomorryloop = -1;
    public $previousresult = 0;
    public $totaltime = 0;
    public $test = 'Simplex';

    public function Solve(Array $A, Array $B, Array $C, $D=true, $E=false) {
//$D - czy max, czy min; $E - czy Gomorryc
        $mtime = microtime();
        $mtime = explode(" ", $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;

        $this->matrixes = Array();
        $this->basecol = Array();
        $this->baserow = Array();

        $this->M = count($A[0]) + 1; //3
        $this->N = count($B) + 1; //5
        $this->O = count($C);
        $this->d = $D;
        for ($i = 0; $i < $this->N; $i++) {
            for ($j = 0; $j < $this->M; $j++) {
                $this->matrixes[$this->index][$i][$j] = 0;
            }
        }
        for ($i = 0; $i < $this->N - 1; $i++) {
            for ($j = 0; $j < $this->M - 1; $j++) {
                $this->matrixes[$this->index][$i][$j] = $A[$i][$j];
            }
        }

        for ($i = 0; $i < $this->N - 1; $i++) {
            $this->matrixes[$this->index][$i][$this->M - 1] = $B[$i];
        }
        //$this->test=$this->N;

        if (!$D) {
            for ($i = 0; $i < $this->O; $i++) {
                $this->matrixes[$this->index][$this->N - 1][$i] = $C[$i];
            }
        } else {
            for ($i = 0; $i < $this->O; $i++) {
                $this->matrixes[$this->index][$this->N - 1][$i] = -$C[$i];
            }
        }
        if (!$E) {
            //Solve Regual linear problem
            while (true) {
                $this->index++;
                $this->matrixes[$this->index] = $this->matrixes[$this->index - 1];
                $p = $this->findBasecol();
                if ($p == -1) {
                    break;
                } else {
                    $this->basecol[$this->index] = $p;
                }
                $q = $this->findBaserow();
                $this->basis[$p] = $q;
                if ($q == -1) {
                    throw new Exception("Linear problem is unbounded");
                } else {
                    $this->baserow[$this->index] = $q;
                }
                $this->gaussjordan();
                if ($this->check()) {
                    break;
                }
            }
        } else {
            //Gomorry - integer numbers
            while (true) {
                $this->index++;
                $this->matrixes[$this->index] = $this->matrixes[$this->index - 1];
                if ($this->gomorryloop == -1) {
                    $p = $this->findBasecol();
                    if ($p == -1) {
                        break;
                    } else {
                        $this->basecol[$this->index] = $p;
                    }
                    $q = $this->findBaserow();
                    $this->basis[$p] = $q;
                    if ($q == -1) {
                        throw new Exception("Linear problem is unbounded");
                    } else {
                        $this->baserow[$this->index] = $q;
                    }
                    $this->gaussjordan();
                } else {
                    $p = $this->findBasecolGomorry();
                    $this->basecol[$this->index] = $p;
                    $q = $this->M;
                    //echo 'q=' . $q;
                    $this->baserow[$this->index] = $q;
                    //$this->gaussjordan();
//                    $this->gomorryloop = -1;
                    break;
                }
                //$this->gaussjordan();
                if ($this->check()) {
                    //echo 'is='.$this->isBaseFeasibleGomorry().'<br/>';
                    if ($this->isBaseFeasibleGomorry()) {
                        break;
                    } else {
                        $this->index++;
                        $this->matrixes[$this->index] = $this->matrixes[$this->index - 1];
                        $z = $this->returnUnfeasible();
                        for ($i = 0; $i < $this->N - 1; $i++) {
                            $this->matrixes[$this->index][$this->M + 1][$i] = $this->matrixes[$this->index][$this->M][$i];
                            $this->matrixes[$this->index][$this->M][$i] = $this->getInteger($this->matrixes[$this->index][$z][$i]);
                        }
                        $this->gomorryloop = $z;
                        //echo 'nie<br/>';
                    }
                    //break;
                }
            }
        }
        $mtime = microtime();
        $mtime = explode(" ", $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $endtime = $mtime;
        $this->totaltime = ($endtime - $starttime);
    }

    private function isNonBaseFeasible() {
        for ($i = 0; $i < $this->M - 1; $i++) {
            if ($this->matrixes[$this->index][$this->N - 1][$i] < 0) {
                return false;
                break;
            }
        }
        return true;
    }

    private function isBaseFeasible() {
        foreach ($this->basis as $value) {
            if ($this->matrixes[$this->index][$value][$this->M - 1] < 0) {
                return false;
                break;
            }
        }
        return true;
    }

    public function isBaseFeasibleGomorry() {
        foreach ($this->basis as $value) {
            if (!is_int($this->matrixes[$this->index][$value][$this->M - 1])) {
                return false;
                break;
            }
        }
        return true;
    }

    public function returnUnfeasible() {
        foreach ($this->basis as $value) {
            if ($this->matrixes[$this->index][$value][$this->M - 1] != round($this->matrixes[$this->index][$value][$this->M - 1], 0)) {
                return $value;
            }
        }
    }

    private function isResultFeasible() {
        return $this->getResult() > 0 ? true : false;
    }

    private function isLooping() {
        if ($this->getResult() == $this->previousresult) {
            $this->loop++;
        }
        if ($this->loop < 5) {
            $this->previousresult = $this->getResult();
            return true;
        } else {
            return false;
        }
    }

    private function check() {
        return $this->isBaseFeasible() && $this->isNonBaseFeasible() && $this->isResultFeasible() && $this->isLooping();
    }

    private function gaussjordan() {
        //element główny
        for ($i = 0; $i < $this->N; $i++) {
            for ($j = 0; $j < $this->M; $j++) {
                if ($i == $this->baserow[$this->index - 1] && $j == $this->basecol[$this->index - 1]) {
                    //element główny
                    $this->matrixes[$this->index][$i][$j] = round(1 / $this->matrixes[$this->index][$i][$j], 2);
                } else if ($i == $this->baserow[$this->index - 1]) {
                    //wiersz główny
                    $this->matrixes[$this->index][$i][$j] = round($this->matrixes[$this->index][$i][$j] / $this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]], 2);
                } else if ($j == $this->basecol[$this->index - 1]) {
                    //kolumna główna
                    $this->matrixes[$this->index][$i][$j] = -round($this->matrixes[$this->index][$i][$j] / $this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]], 2);
                } else {
                    //normalny element
                    $this->matrixes[$this->index][$i][$j] -= round((($this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$j] * $this->matrixes[$this->index - 1][$i][$this->basecol[$this->index - 1]]) / $this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]]), 2);
                }
            }
        }
    }

    private function findBaserow() {
        $indeks = -1;
        $value = 1000000;
        //$this->test=$this->matrixes[$this->index][0][$this->M-1];
        //$this->test=$this->matrixes[$this->index][0][$this->basecol[$this->index]];
        for ($i = 0; $i < $this->M; $i++) {
            if ($this->matrixes[$this->index][$i][$this->basecol[$this->index]] == 0) {
                continue;
            }
            if ($this->matrixes[$this->index][$i][$this->M - 1] / $this->matrixes[$this->index][$i][$this->basecol[$this->index]] > 0 && $this->matrixes[$this->index][$i][$this->M - 1] / $this->matrixes[$this->index][$i][$this->basecol[$this->index]] < $value) {
                $value = $this->matrixes[$this->index][$i][$this->M - 1] / $this->matrixes[$this->index][$i][$this->basecol[$this->index]];
                $indeks = $i;
            }
        }
        //$this->test = $this->matrixes[$this->index][2][$this->basecol[$this->index]] . ' ' . $this->index;
        $this->baserow[$this->index - 1] = $indeks;
        return $indeks;
    }

    private function findBasecol() {
        $col = 100000000;
        $indeks = -1;
        //$this->test=$this->matrixes[$this->index][$this->N - 1][0];
        for ($i = 0; $i < $this->O; $i++) {
            if ($this->matrixes[$this->index][$this->N - 1][$i] < $col && $this->matrixes[$this->index][$this->N - 1][$i] < 0) {
                $col = $this->matrixes[$this->index][$this->N - 1][$i];
                $indeks = $i;
            }
        }
        //$this->test = $col;
        $this->basecol[$this->index - 1] = $indeks;
        return $indeks;
    }

    public function findBasecolGomorry() {
        $col = 100000000;
        $indeks = -1;
        //$this->test=$this->matrixes[$this->index][$this->M-1][1];
        for ($i = 0; $i < $this->O; $i++) {
            if ($this->matrixes[$this->index][$this->M - 1][$i] < $col) {
                $col = $this->matrixes[$this->index][$this->M - 1][$i];
                $indeks = $i;
            }
        }
        //$this->test = $indeks;
        $this->basecol[$this->index - 1] = $indeks;
        return $indeks;
    }

    public function printMatrix($index) {
        $a = count($this->matrixes[$i]);
        $b = count($this->matrixes[$i][0]);
        for ($i = 0; $i < $a; $i++) {
            for ($j = 0; $j < $b; $j++) {
                echo $this->matrixes[$index][$i][$j] . ' ';
            }
            echo '<br/>';
        }
        //print_r($this->matrixes[$index]);
    }

    public function printAllMatrix() {
        for ($i = 0; $i < $this->index + 1; $i++) {
            $a = count($this->matrixes[$i]);
            $b = count($this->matrixes[$i][0]);
            for ($j = 0; $j < $a; $j++) {
                for ($k = 0; $k < $b; $k++) {
                    echo $this->matrixes[$i][$j][$k] . ' ';
                }
                echo '<br/>';
            }
            echo '<br/><br/>';
            //print_r($this->matrixes);
        }
    }

    public function getMatrix($index) {
        return $this->matrixes[$index];
    }

    public function getAllMatrix() {
        return $this->matrixes;
    }

    public function getIndex() {
        return $this->index;
    }

    private function getInteger($int) {
        if (($int - floor($int)) < 0) {
            return $int - floor($int) + 1;
        } else {
            return $int - floor($int);
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

    public function printResult() {
        echo $this->d ? 'Result=' . $this->matrixes[$this->index][$this->N - 1][$this->M - 1] . '<br/>' : 'Result=-' . $this->matrixes[$this->index][$this->N - 1][$this->M - 1] . '<br/>';
    }

    public function getResult() {
        return $this->d ? $this->matrixes[$this->index][$this->N - 1][$this->M - 1] : -$this->matrixes[$this->index][$this->N - 1][$this->M - 1];
    }

    public function printValuePair() {
        $print = '';
        for ($i = 0; $i < count($this->basis); $i++) {
            $print.= $this->matrixes[$this->index][$this->basis[$i]][$this->M - 1] . ' ';
        }
        echo $print;
        unset($print);
    }

    public function getValuePair() {
        $tab = Array();
        for ($i = 0; $i < count($this->basis); $i++) {
            $tab[] = $this->matrixes[$this->index][$this->basis[$i]][$this->M - 1];
        }
        return $tab;
        unset($tab);
    }

    public function getjsonData(Array $A, Array $B, Array $C, $D) {
        if (count($A[0]) == 2) {
            //dwuwymiarowa
            $json = Array();
            if ($D) {
                //maksymalizacja f. celu
                $maxx = 0;
                $maxy = 0;
                for ($i = 0; $i < count($A); $i++) {
                    if ($A[$i][1] == 0) {
                        continue;
                    }
                    if (($B[$i] / $A[$i][1]) > $maxy) {
                        $maxy = $B[$i] / $A[$i][1];
                    }
                    if ($A[$i][0] == 0) {
                        continue;
                    }
                    if (($B[$i] / $A[$i][0]) > $maxx) {
                        $maxx = $B[$i] / $A[$i][0];
                    }
                }
                for ($i = 0; $i < count($A); $i++) {
                    $json[$i] = Array('label' => 'S' . ($i + 1), 'data' => '');
                    if (($A[$i][1]) == 0) {
                        $json[$i]['data'][] = Array($B[$i] / $A[$i][0], $maxy);
                    } else {
                        $json[$i]['data'][] = Array(0, $B[$i] / $A[$i][1]);
                    }
                    if (($A[$i][0]) == 0) {
                        $json[$i]['data'][] = Array($maxx, $B[$i] / $A[$i][1]);
                    } else {
                        $json[$i]['data'][] = Array($B[$i] / $A[$i][0], 0);
                    }
                }
                $json[] = Array('label' => 'gradient', 'data' => Array(Array(0, 0), Array($maxx, $C[0] * $maxx / $C[1])));
            } else {
                //minimalizacja f. celu
            }
            echo json_encode($json);
        } else {
            //wielowymiarowa
        }
    }

}

/* $a = Array(Array(2, 5), Array(2, 3), Array(0, 3));
$b = Array(30, 26, 15);
$c = Array(2, 6); */
//---------------------------------------
/* $a = Array(Array(1, 0, 0), Array(0, 1, 0), Array(0, 0, 1), Array(3, 6, 2));
$b = Array(1000, 500, 1500, 6750);
$c = Array(4, 12, 3); */
//----------------------------------------
//$a=Array(Array(1,0),Array(0,1),Array(1,1));
//$b=Array(1,2,2);
//$c=Array(2,1);
/* $s = new SimplexSolver();
try {
    $s->Solve($a, $b, $c, true, false);
    $s->printAllMatrix();
    $s->printCol();
    $s->printRow();
    echo 'Index:' . $s->index . '<br/>';
    $s->printResult();
    $s->printValuePair();
        echo 'test=' . $s->test . '</br>';
    echo 'gomorry=' . $s->gomorryloop . '</br>';
} catch (Exception $error_string) {
    echo $error_string;
} */
?>
