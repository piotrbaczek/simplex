<?php

class login {

	const password = '85baf8d8d517ab142292d8814be41a60a5e27bc8ba001ac8';
	const login = 'admin';

	public static function validate($login = '', $password = '') {
		if ((login::login == $login) && (login::password == login::encrypt($password))) {
			return true;
		} else {
			return false;
		}
	}

	public static function encrypt($password) {
		return hash('tiger192,4', $password . 'thisisthesalt');
	}

}

?>
