<?php
class login {
    private $password='5a29d2127a35ed8a1bfc558a8c3724b6';
    private $login='admin';
    public function logIn($login='',$password=''){
        if(($this->login==$login) && ($this->password==md5($password))){
            return true;
        }else{
            return false;
        }
        
    }
}
?>
