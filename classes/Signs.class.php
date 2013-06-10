<?php

/**
 * Description of Signs
 *
 * @author PETTER
 */
class enumSigns {

	const _LEQ = "<=";
	const _GEQ = ">=";
	const _EQ = "=";

}

class Signs {

	private $sign;

	public function Signs($param) {
		if (($param instanceof enumSigns)) {
			$this->sign = $param;
		} else {
			switch ($param) {
				case "<=":
					$this->sign = enumSigns::_LEQ;
					break;
				case ">=":
					$this->sign = enumSigns::_GEQ;
					break;
				default:
					$this->sign = enumSigns::_EQ;
					break;
			}
		}
	}

	public function toString() {
		return $this->sign;
	}

}

$znak = new Signs("<=");
var_dump($znak);
?>