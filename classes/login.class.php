<?php

/**
 * Loging into administrator's panel requires login
 * Validating login and password
 * 
 * @author Piotr Gołasz <pgolasz@gmail.com>
 */
class login {

	const password = '85baf8d8d517ab142292d8814be41a60a5e27bc8ba001ac8';
	const login = 'admin';

	/**
	 * Returns true if login and hash are correct, false otherwise
	 * @param String $login
	 * @param String $password
	 * @return boolean
	 */
	public static function validate($login = '', $password = '') {
		if ((login::login == $login) && (login::password == login::encrypt($password))) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Hashes the string with some hash and salt
	 * @param String $password
	 * @return String
	 */
	public static function encrypt($password) {
		return hash('tiger192,4', $password . 'thisisthesalt');
	}

}

?>
