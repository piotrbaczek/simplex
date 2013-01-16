<?php
class TextareaProcesser{
	private $a=Array();
	private $b=Array();
	private $c=Array();
	private $d=Array();
	private $max=true;
	private $gomorry=false;
	public function __construct($param='',$param2='', $param3=true, $param4=false){
		if(!isset($param) or !isset($param2)){
			$this->errormessage('B��d przetwarzania - nie przekazano �adnego parametru');
			return 0;
		}else{
			//przetwarzanie textarea
			$param=str_replace(' ', '', $param);
			$param=trim($param);
			preg_match_all('/(<=|>=|=)/', $param,$a);
			preg_match_all('([+|-]?[0-9]*\/[1-9]{1,}[0-9]*[a-z]|[+|-]?[0-9]*[a-z])', $param,$b);
			preg_match_all('(=[+|-]?[0-9]*\/[1-9]{1,}[0-9]*|=[+|-]?[0-9]*)', $param, $c);
			if(count($a[0])!=count($c[0]) or (count($b[0])%count($c[0])!=0)){
				$this->errormessage('B��d przetwarzania - Kom�rki macierzy s� nier�wne. Sprawd� poprawno�� danych!');
			}else{
				foreach ($a[0] as $key => $value) {
					$this->a[]=$value;
				}
				

				foreach ($c[0] as $key => $value) {
					if(strpos(substr($value, 1), '/')){
						$temp=explode('/', substr($value, 1));
						$this->c[]=new Fraction2($temp[0],$temp[1]);
					}else{
						$this->c[]=new Fraction2(substr($value, 1));
					}
				}
				//przetwarzanie funkcji celu
				preg_match_all('([+|-]?[0-9]*\/[1-9]{1,}[0-9]*[a-z]|[+|-]?[0-9]*[a-z])', $param2, $d);
				foreach ($d[0] as $key => $value) {
					$value=substr($value, 0,-1);
					if(strpos($value, '/')){
						$temp=explode('/', $value);
						$this->d[]=new Fraction2($temp[0],$temp[1]);
					}else{
						$this->d[]=new Fraction2($value);
					}
				}
				
				$index=0;
				foreach ($b[0] as $key=>$value){
					if($key!=0 && $key%count($d[0])==0){
						$index++;
					}
					if(strpos(substr($value,0,-1), '/')){
						$temp=explode('/', substr($value,0,-1));
						$this->b[$index][$key%count($d[0])]=new Fraction2($temp[0],$temp[1]);
					}else{
						$this->b[$index][$key%count($d[0])]=new Fraction2(substr($value,0,-1));
					}
				
				}

				//przetwarzanie f. gomorry'ego
				$this->gomorry=($param4=='true' ? true : false);

				//przetworzenie max/min
				$this->max=($param3=='true' ? true : false);
			}
				
		}
	}
	public function getVariables(){
		return $this->b;
	}

	public function getSigns(){
		return $this->a;
	}

	public function getBoundaries(){
		return $this->c;
	}

	public function getMaxMin(){
		return $this->max;
	}

	public function getGomorry(){
		return $this->gomorry;
	}

	public function getTargetfunction(){
		return $this->d;
	}

	public static function errormessage($message){
		echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>'.$message.'</p></div>';
	}
}

?>