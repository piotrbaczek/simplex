<?php
class Simplex{
	public $index=0;
	public $matrixes;
	public $basecol;
	public $baserow;
	public $basis;
	public $zmiennebazowe;
	public $zmienneniebazowe;
	public $M;
	public $N;
	public $O;
	public $wrongsigns;
	public $d;
	public function __construct(){
		$this->matrixes = Array();
		$this->basecol = Array();
		$this->baserow = Array();
		$this->zmiennebazowe=Array();
		$this->zmienneniebazowe=Array();
	}

	public function Solve(Array $variables, Array $boundaries, Array $signs, Array $targetfunction, $max = true, $gomorry = false){
		if(count($signs)!=count($boundaries)){
			$this->errormessage('Nie zgadza siê liczba znaków równania i liczba zmiennych ograniczaj¹cych');
			break;
		}
		$this->M = count($variables[0]) + 1; //3
		$this->N = count($boundaries) + 1; //4
		$this->O = count($targetfunction);
		$this->d = $max;
		$this->targetfunction=$targetfunction;
		for($i=1;$i<$this->N;$i++){
			$this->zmiennebazowe[$this->index][$i]='S<sub>'.$i.'</sub>';

		}
		for($i=1;$i<$this->N-1;$i++){
			$this->zmienneniebazowe[$this->index][$i]='x<sub>'.$i.'</sub>';
		}

		foreach ($signs as $key => $value){
			if($value!="<="){
				$this->wrongsigns++;
			}
		}

		for ($i = 0; $i < $this->N; $i++) {
			for ($j = 0; $j < $this->N + $this->M - 1 + $this->wrongsigns; $j++) {
				$this->matrixes[$this->index][$i][$j] = 0;
			}
		}
		for ($i = 0; $i < $this->N - 1; $i++) {
			for ($j = 0; $j < $this->M - 1; $j++) {
				$this->matrixes[$this->index][$i][$j] = $variables[$i][$j];
			}
		}

		for ($i = 0; $i < $this->N - 1; $i++) {
			$this->matrixes[$this->index][$i][$this->N + $this->wrongsigns + $this->M - 2] = $boundaries[$i];
		}
		foreach ($signs as $key => $value) {
			switch ($value) {
				case ">=":
					$this->matrixes[$this->index][$key][$this->M-1+$key]=1;
					$this->matrixes[$this->index][$key][$this->O+$this->M]=-1;
					break;

				default:
					for ($j = $this->M - 1; $j < $this->N + $this->M - 2; $j++) {
					if (($j-($this->M-1)) == $key) {
						$this->matrixes[$this->index][$key][$j] = 1;
					} else {
						$this->matrixes[$this->index][$key][$j] = 0;
					}
				}
				break;
			}
		}
		if($max){
			for ($i = 0; $i < $this->O; $i++) {
				$this->matrixes[$this->index][$this->N - 1][$i] = -$targetfunction[$i];
			}
		}else{
			for ($i = 0; $i < $this->O; $i++) {
				$this->matrixes[$this->index][$this->N - 1][$i] = $targetfunction[$i];
			}
		}
		//koniec inicjalizacji
		while(!$this->check()){
			$this->index++;
			$this->matrixes[$this->index]=$this->matrixes[$this->index-1];
			$this->zmiennebazowe[$this->index]=$this->zmiennebazowe[$this->index-1];
			$this->zmienneniebazowe[$this->index]=$this->zmienneniebazowe[$this->index-1];
			$p = $this->findBasecol();
			if ($p == -1) {
				break;
			} else {
				$this->basecol[$this->index] = $p;
			}
			$q = $this->findBaserow($p);
			if ($q == -1) {
				$this->errormessage("Linear problem is unbounded");
				break;
			} else {
				$this->baserow[$this->index] = $q;
			}
			$this->swapBase();
			$this->gaussjordan();
			if(!isset($this->basis[$p])){
				$this->basis[$p] = $q;
			}
		}
		if($gomorry){
			while(true){
				$this->index++;
				$this->matrixes[$this->index]=$this->matrixes[$this->index-1];
				$k=$this->gomorryrow();
				if($k==-1){
					unset($this->matrixes[$this->index]);
					$this->index--;
					break;
				}
				$this->zmiennebazowe[$this->index]=$this->zmiennebazowe[$this->index-1];
				$this->zmienneniebazowe[$this->index]=$this->zmienneniebazowe[$this->index-1];
				$p=$this->gomorryAddRow($k);
				$this->zmiennebazowe[$this->index][count($this->matrixes[0])]='R<sub>1</sub>';
				$q=count($this->matrixes[$this->index])-2;
				//echo 'Przekszta³cenie po elemencie ['.$q.','.$p.']='.$this->matrixes[$this->index][$q][$p];
				$this->basecol[$this->index-1]=$this->basecol[$this->index]=$p;
				$this->baserow[$this->index-1]=$this->baserow[$this->index]=$q;
				//----------------------------------------------------------------
				$this->index++;
				$this->baserow[$this->index]=0;
				$this->basecol[$this->index]=0;
				$this->zmiennebazowe[$this->index]=$this->zmiennebazowe[$this->index-1];
				$this->zmienneniebazowe[$this->index]=$this->zmienneniebazowe[$this->index-1];
				$this->matrixes[$this->index]=$this->matrixes[$this->index-1];
				$this->gaussjordan();
				if(!$this->gomorrycheck()){
					break;
				}
			}
		}
	}

	public function printAllMatrix() {
		for ($i = 0; $i < $this->index + 1; $i++) {
			$a = count($this->matrixes[$i]);
			$b = count($this->matrixes[$i][0]);
			echo '<table class="result"><tbody>';
			for ($j = 0; $j < $a; $j++) {
				echo '<tr>';
				for ($k = 0; $k < $b; $k++) {
					echo '<td>' . $this->matrixes[$i][$j][$k] . '</td>';
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

	public function testprint(){
		for ($i = 0; $i < $this->index + 1; $i++) {
			$a = count($this->matrixes[$i]);
			$b = count($this->matrixes[$i][0]);
			echo '<table class="result"><tbody>';
			echo '<tr><td style="width:30px;text-align:center;" class="ui-state-default">('.$i.')</td>';
			for ($j = 0; $j < $this->N + $this->M - 2 + $this->wrongsigns; $j++) {
				if(isset($this->targetfunction[$j])){
					echo '<td class="ui-state-default">'.$this->targetfunction[$j].'</td>';
				}else{
					echo '<td class="ui-state-default">0</td>';
				}
			}
			echo '<td class="ui-state-default" rowspan="2">Warto&#347;&#263;</td></tr>';
			echo '<tr><td class="ui-state-default">Baza</td>';
			for ($j = 0; $j < $this->N+$this->wrongsigns+$this->M-2; $j++) {
				if(isset($this->zmienneniebazowe[$i][$j+1])){
					echo '<td class="ui-state-default">'.$this->zmienneniebazowe[$i][$j+1].'</td>';
				}else if($j+2-$this->M<=$this->M){
					echo '<td class="ui-state-default">a<sub>'.($j+2-$this->M).'</sub></td>';
				}else{
					echo '<td class="ui-state-default">R<sub>'.($j+2-(2*$this->M)).'</sub></td>';
				}
			}
			echo '</tr>';
			for ($j = 0; $j < $a; $j++) {
				if(isset($this->zmiennebazowe[$i][$j+1])){
					echo '<tr><td class="ui-state-default">'.$this->zmiennebazowe[$i][$j+1].'</td>';
				}else{
					echo '<tr><td class="ui-state-default">z<sub>j</sub>-c<sub>j</sub></td>';
				}

				for ($k = 0; $k < $b; $k++) {
					if ($k == $this->basecol[$i] && $j == $this->baserow[$i] && $i!=$this->index) {
						echo '<td style="color:white;background-color:red;text-align:center;width:45px;">' . $this->matrixes[$i][$j][$k] . '</td>';
					} else {
						echo '<td style="text-align:center;width:45px;" title="costam">' . $this->matrixes[$i][$j][$k] . '</td>';
					}
				}
				echo '</tr>';
			}
			echo '</tbody></table>';
			echo '<br/><br/>';
		}
	}

	private function findBaseCol(){
		$a = count($this->matrixes[$this->index]);
		$startv=10000000000;
		$starti=-1;

		for($i=0;$i<$a-1;$i++){
			if($startv>$this->matrixes[$this->index][$a-1][$i]){
				$starti=$i;
				$startv=$this->matrixes[$this->index][$a-1][$i];
			}
		}
		$this->basecol[$this->index - 1] = $starti;
		//echo $startv.' '.$starti;
		return $starti;

	}

	private function findBaseRow($p){
		$a = count($this->matrixes[$this->index]);
		$b = count($this->matrixes[$this->index][0]);
		$startv = 100000000;
		$starti = -1;
		//echo $p;
		for($i=0;$i<$a-1;$i++){
			if($this->matrixes[$this->index][$i][$p]==0){
				continue;
			}
			if(($this->matrixes[$this->index][$i][$b-1]/$this->matrixes[$this->index][$i][$p])<$startv && ($this->matrixes[$this->index][$i][$b-1]/$this->matrixes[$this->index][$i][$p])>0){
				$startv=($this->matrixes[$this->index][$i][$b-1]/$this->matrixes[$this->index][$i][$p]);
				$starti=$i;
			}
			//echo $this->matrixes[$this->index][$i][$b-1].' / '.$this->matrixes[$this->index][$i][$p].'<br/>';
		}
		$this->baserow[$this->index - 1] = $starti;
		return $starti;
		//echo 'row='.$startv.' '.$starti;
	}

	private function gaussjordan(){
		$a = count($this->matrixes[$this->index]);
		$b = count($this->matrixes[$this->index][0]);
		for($i=0;$i<$a;$i++){
			for($j=0;$j<$b;$j++){
				if($i == $this->baserow[$this->index - 1] && $j == $this->basecol[$this->index - 1]){
					//element g³ówny
					$this->matrixes[$this->index][$i][$j] = round(1 / $this->matrixes[$this->index][$i][$j], 2);
				}elseif ($i == $this->baserow[$this->index - 1]){
					//wiersz g³ówny
					$this->matrixes[$this->index][$i][$j] = round($this->matrixes[$this->index][$i][$j] / $this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]], 2);
				}elseif($j == $this->basecol[$this->index - 1]){
					//kolumna g³ówna
					$this->matrixes[$this->index][$i][$j] = -round($this->matrixes[$this->index][$i][$j] / $this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]], 2);
				}else{
					//normalny element
					$this->matrixes[$this->index][$i][$j] -= round((($this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$j] * $this->matrixes[$this->index - 1][$i][$this->basecol[$this->index - 1]]) / $this->matrixes[$this->index - 1][$this->baserow[$this->index - 1]][$this->basecol[$this->index - 1]]), 2);
				}
			}
		}
	}

	private function check(){
		return $this->checktargetfunction();
	}

	private function checktargetfunction(){
		$a = count($this->matrixes[$this->index]);
		$b = count($this->targetfunction);
		for($i=0;$i<$b;$i++){
			if($this->matrixes[$this->index][$a-1][$i]<0){
				return false;
			}
		}
		return true;
	}

	private function swapBase(){
		$buffer=$this->zmiennebazowe[$this->index][($this->baserow[$this->index-1]+1)];
		$this->zmiennebazowe[$this->index][($this->baserow[$this->index-1]+1)]=$this->zmienneniebazowe[$this->index][($this->basecol[$this->index-1]+1)];
		$this->zmienneniebazowe[$this->index][($this->basecol[$this->index-1]+1)]=$buffer;
		unset($buffer);
	}

	public function printValuePair() {
		$a=count($this->matrixes[$this->index][0]);
		sort($this->basis);
		foreach($this->basis as $key => $value){
			echo 'x<sub>'.($key+1).'</sub>='.$this->matrixes[$this->index][$value][$a-1].'<br/>';
		}
	}
	public function returnValuePair(){
		$x=Array();
		$a=count($this->matrixes[$this->index][0]);
		sort($this->basis);
		foreach($this->basis as $key => $value){
			$x[]=$this->matrixes[$this->index][$value][$a-1];
		}
		return $x;
	}

	private function gomorrycheck(){
		$x=$this->returnValuePair();
		foreach ($x as $key => $value){
			if(!is_integer($value)){
				return false;
			}
		}
		return true;
	}

	private function gomorryrow(){
		foreach($this->returnValuePair() as $key => $value){
			if(round($value,0)!=$value){
				return $key;
			}
		}
		return -1;
	}


	private function gomorryAddRow($q){
		$a=count($this->matrixes[$this->index][0]);
		$b=count($this->matrixes[$this->index]);
		$startv=1;
		$starti=-1;
		for($i=0;$i<$a;$i++){
			$this->matrixes[$this->index][$b][$i]=$this->matrixes[$this->index][$b-1][$i];
			$this->matrixes[$this->index][$b-1][$i]=$this->getInt($this->matrixes[$this->index][$q][$i]);
			if($this->getInt($this->matrixes[$this->index][$q][$i])<$startv){
				$startv=$this->getInt($this->matrixes[$this->index][$q][$i]);
				$starti=$i;
			}
		}
		return $starti;

	}

	public static function getInt($e){
		$e=$e-round($e,0);
		return $e<0 ? -($e+1) : -$e;
	}
	public function getResult(){
		$a=count($this->matrixes[$this->index]);
		$b=count($this->matrixes[$this->index][0]);
		return $this->matrixes[$this->index][$a-1][$b-1];
	}

	public function printResult(){
		$a=count($this->matrixes[$this->index]);
		$b=count($this->matrixes[$this->index][0]);
		echo 'W='.$this->matrixes[$this->index][$a-1][$b-1];
	}
	
	public static function errormessage($message){
		echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>'.$message.'</p></div>';
	}
}


/* $a = Array(Array(2, 5), Array(2, 3), Array(0, 3));
 $b = Array(30, 26, 15);
$c = Array("<=", "<=", "<=");
$d = Array(2, 6); */
//---------------------------------------
/* $a = Array(Array(1, 0, 0), Array(0, 1, 0), Array(0, 0, 1), Array(3, 6, 2));
 $b = Array(1000, 500, 1500, 6750);
$c = Array("<=", "<=", "<=","<=");
$d = Array(4, 12, 3); */
//----------------------------------------
//$a=Array(Array(1,0),Array(0,1),Array(1,1));
//$b=Array(1,2,2);
//$c=Array(2,1);
/* try{
 $s=new Simplex();
$s->Solve($a,$b,$c,$d,true,false);
$s->testprint();
//$s->printCol();
//$s->printRow();
$s->printValuePair();
$s->printResult();
}catch(Exception $e){

} */