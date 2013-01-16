<?php

/**
 * Trida Fraction pro praci se zlomky.
 *
 * Trida Fraction obsahuje vsechny dulezite procedury pro praci se zlomky :
 *  * Kraceni zlomku na zakladni tvar
 *  * Predavedeni realnych cisel na zlomky
 *  * Zakladni matematick� operace
 *    * Scitani
 *    * Odecitani
 *    * Nasobeni
 *    * Deleni
 *
 * @author Tomas Lang <tomas.lang@gmail.com>
 * @copyright Copyright (c) 2008+, Tomas Lang
 * @version 1.2
 * @category Mathematic
 * @package Basic
 */
class Fraction {
	/**
	 * @var int $numerator Citatel zlomku
	 */
	private $numerator;
	/**
	 * @var int $denominator Jmenovatel zlomlu
	 */
	private $denominator;

	/**
	 * Konstruktor
	 *
	 * Vytvori novou instanci tridy zlomek, nacte citatel a jmenovatel a
	 * nakonec zlomek zkrati do zakladniho tvaru.
	 *
	 * @param float $numerator Citatel zlomku
	 * @param float $denominator Jmenovatel zlomlu
	 *
	 * @todo: $number | $denominator = object Fraction
	 */
	public function __construct( $numerator = 1, $denominator = 1 ) {
		$numerator = $this->realToFraction( $numerator );
		$denominator = $this->realToFraction( $denominator );
			
		$this->numerator = (int)( $numerator[ 0 ] * $denominator[ 1 ] );
		$this->denominator = (int)( $denominator[ 0 ] * $numerator[ 1 ] );
			
		if ( $this->denominator == 0 ) {
			throw new Exception( 'Denominator can\'t be 0!' );
		}
			
		$this->reduction();
	}

	/**
	 * Funkce prevede realne cislo do tvaru zlomku
	 *
	 * @param float $number
	 *
	 * @return array Pole o dvou prvcich (citatel, jmenovatel zlomku)
	 */
	public function realToFraction( $number ) {
		$endOfNumber = $number - (int)$number;
		if ( $endOfNumber != 0 ) {
			$mul = bcpow( 10, strlen( $endOfNumber ) - 2 );
			return array( $number * $mul, $mul );
		} else {
			return array( $number, 1 );
		}
	}

	/**
	 * Funkce vraci citatel zlomku
	 *
	 * @return float $this->numerator Citatel zlomku
	 */
	public function getNumerator() {
		return $this->numerator;
	}

	/**
	 * Funkce vraci jmenovatel zlomku
	 *
	 * @return float $this->denominator Jmenovatel zlomku
	 */
	public function getDenominator() {
		return $this->denominator;
	}

	/**
	 * Funkce zkrati zlomek do zakladniho tvaru
	 *
	 * Pokud je citatel i jmenovatel zaporny, pak je zlomek preveden na
	 * kladny, pokud je zaporny pouze jmenovatel, je zapornost prenesena
	 * na citatele a jmenovatel je preveden na kladny, nasledne pak je
	 * zlomek zkracen do zakladniho tvaru na zaklade nalezen�ho nejvysiho
	 * spolecn�ho jmenovatele.
	 */
	public function reduction() {
		if ( ( $this->numerator < 0 && $this->denominator < 0 ) || ( $this->denominator < 0 ) ) {
			$this->expansion( -1 );
		}
		$hcd = $this->highestCommonDivisor( $this->numerator, $this->denominator );
		$this->numerator /= $hcd;
		$this->denominator /= $hcd;
	}

	/**
	 * Funkce vraci hodnotu zlomku v realn�m cisle
	 *
	 * @return float
	 */
	public function getRealValue() {
		return $this->numerator / $this->denominator;
	}

	/**
	 * Funkce vraci nejvysiho spolecn�ho delitele cilel $a a $b
	 *
	 * @param int $a
	 * @param int $b
	 */
	public function highestCommonDivisor( $a, $b ) {
		$a = abs( $a );
		while ( $a != $b ) {
			if ( $a > $b ) {
				$a = $a - $b;
			}else {
				$b = $b - $a;
			}
		}
		return $a;
	}

	/**
	 * Funkce vraci nejmensi spolecny nasobek cisel $a a $b
	 *
	 * @param float $a
	 * @param float $b
	 */
	private function leastCommonMultiple( $a, $b ) {
		return ( $a * $b ) / $this->highestCommonDivisor( $a, $b );
	}

	/**
	 * Funkce prevede tento a zadany zlomek, na zlomly se spolecnym
	 * jmenovatelem
	 *
	 * @param object Fraction
	 */
	public function commonDenominator( &$fraction ) {
		$lcm = $this->leastCommonMultiple( $this->denominator, $fraction->denominator );
		$this->numerator = $this->numerator * ( $lcm / $this->denominator );
		$fraction->numerator = $fraction->numerator * ( $lcm / $fraction->denominator );
		$this->denominator = $fraction->denominator = $lcm;
	}

	/**
	 * Funkce rozsiri zlomek o hodnotu zlomku ci cisla $num
	 *
	 * @param object|float Fraction
	 */
	private function expansion( $num ) {
		$this->numerator *= $num;
		$this->denominator *= $num;
	}

	/**
	 * Funkce zkrati zlomek o hodnotu zlomku ci cisla $parametr
	 *
	 * @param object|float
	 */
	private function contraction( $parametr ) {
		$this->numerator /= $num;
		$this->denominator /= $num;
	}

	/**
	 * Pricte zadany zlomek ci cislo k tomuto zlomku a zkrati jej na
	 * zakladni tvar
	 *
	 * @param object|float Fraction
	 */
	public function add( $parametr ) {
		if ( !( $parametr instanceOf Fraction ) && is_numeric( $parametr ) ) {
			$parametr = new Fraction( $parametr );
			$this->commonDenominator( $parametr );
			$this->numerator = $this->numerator + $parametr->numerator;
		} elseif($parametr instanceof Fraction) {
			$newdenominator=$this->denominator*$parametr->getDenominator();
			$newnumerator=($this->numerator*$parametr->getDenominator())+($parametr->getNumerator()*$this->denominator);
			$this->numerator=$newnumerator;
			$this->denominator=$newdenominator;
		}else{
			throw new Exception( 'Parametr must be fraction or number!' );
		}
		$this->reduction();
	}

	/**
	 * Odecte zadany zlomek ci cislo od tohoto zlomku a zkrati jej na
	 * zakladni tvar
	 *
	 * @param object|float Fraction
	 */
	public function sub( $parametr ) {
		if ( !( $parametr instanceOf Fraction ) && is_numeric( $parametr ) ) {
			$parametr = new Fraction( $parametr );
			$this->commonDenominator( $parametr );
			$this->numerator = $this->numerator - $parametr->numerator;
		} elseif($parametr instanceof Fraction){
			$newdenominator=$this->denominator*$parametr->getDenominator();
			$newnumerator=($this->numerator*$parametr->getDenominator())-($parametr->getNumerator()*$this->denominator);
			$this->numerator=$newnumerator;
			$this->denominator=$newdenominator;
		}else{
			throw new Exception( 'Parametr must be fraction or number!' );
		}
		$this->reduction();
	}

	/**
	 * Vynasobi tento zlomek zadanym zlomkem ci cislem a zkrati jej na
	 * zakladni tvar
	 *
	 * @param object|float Fraction
	 */
	public function multiplication( $parametr ) {
		if ( $parametr instanceOf Fraction ) {
			$numerator = $parametr->numerator;
			$denominator = $parametr->denominator;
		} elseif ( is_numeric( $parametr ) ) {
			$parametr = $this->realToFraction( $parametr );
			$numerator = $parametr[ 0 ];
			$denominator = $parametr[ 1 ];
		} else {
			throw new Exception( 'Parametr must be fraction or number!' );
		}
			
		$this->numerator *= (int)$numerator;
		$this->denominator *= (int)$denominator;
			
		$this->reduction();
	}

	/**
	 * Vydeli tento zlomek zadanym zlomkem ci cislem a zkrati jej na
	 * zakladni tvar
	 *
	 * @param object|float Fraction
	 */
	public function division( $parametr ) {
		if ( $parametr instanceOf Fraction ) {
			$numerator = $parametr->numerator;
			$denominator = $parametr->denominator;
		} elseif ( is_numeric( $parametr ) ) {
			$parametr = $this->realToFraction( $parametr );
			$numerator = $parametr[ 0 ];
			$denominator = $parametr[ 1 ];
		} else {
			throw new Exception( 'Parametr must be fraction or number!' );
		}

		$this->numerator *= (int)$denominator;
		$this->denominator *= (int)$numerator;

		$this->reduction();
	}

	public function toString(){
		if($this->denominator==1){
			return $this->numerator;
		}else{
			return $this->numerator.'/'.$this->denominator;
		}

	}

	public function minusFraction(){
		$this->numerator=-$this->numerator;
	}

	public function compare($param){
		if($param instanceof Fraction){
			$a=$this->numerator*$param->getDenominator();
			$b=$this->denominator*$param->getNumerator();
			if($a>$b){
				return true;
				//this fraction is bigger
			}else{
				return false;
				//param fraction is bigger
			}
		}elseif (is_numeric($param)){
			$p=$this->realToFraction($param);
			$a=$this->numerator*$p[1];
			$b=$this->denominator*$p[0];
			if($a>$b){
				return true;
			}else{
				return false;
			}
		}
	}

	public static function isPositive($param){
		if($param instanceof Fraction){
			return $param->numerator0 ? true : false;
		}elseif(is_numeric($param)){
			return $param>0 ? true : false;
		}
	}

	public static function isNegative($param){
		if($param instanceof Fraction){
			return $param->numerator<0 ? true : false;
		}elseif(is_numeric($param)){
			return $param<0 ? true : false;
		}
	}

	public function reverse(){
		$znak=1;
		$numerator=$this->numerator;
		$denominator=$this->denominator;
		if($this->numerator<0){
			$znak=-1;
			$this->numerator*=$znak;
		}
		$this->numerator=$denominator;
		$this->denominator=$numerator;
		if($znak==-1){
			$this->numerator*=$znak;
		}
		$this->reduction();
	}

}

$s1=new Fraction(30);
$s2=new Fraction(5);
$s1->division($s2);
echo $s1->toString().'<br/>';
?>