<?php

class login {

    private $password = 'e00fa221684bc62f8de844a062a70a29f13d0e95b6098c26';
    private $login = 'admin';

    public function __construct($login = '', $password = '') {
        if (($this->login == $login) && ($this->password == $this->encrypt($password))) {
            return true;
        } else {
            return false;
        }
    }

    private function encrypt($password) {
        return hash('tiger192,4', $password . 'thisisthesalt');
    }

    public static function getHash($password) {
        return login::encrypt($password);
    }

}

?>
