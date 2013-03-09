<?php
class login {
    private $password='e00fa221684bc62f8de844a062a70a29f13d0e95b6098c26';
    private $login='admin';
    public function logIn($login='',$password=''){
        if(($this->login==$login) && ($this->password==$this->encrypt($password))){
            return true;
        }else{
            return false;
        }
    }
    private function encrypt($pass){
        return hash('tiger192,4',$pass.'thisisthesalt');
    }
    public static function getSalt($salt){
        return login::encrypt($salt);
    }
}
?>
