<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of verDetect
 *  This class displays version of Android system used (displays 'PC' otherwise)
 * @author PETTER
 */
class verDetect {

    //put your code here
    public function returnDetect() {
        $ua = $_SERVER['HTTP_USER_AGENT'];
        if (stripos($ua, 'Android') !== false) { // && stripos($ua,'mobile') !== false) {
            $exp=explode(";", $ua);
            return $exp['2'];
        }else if(stripos($ua, 'Chrome')!==false){
            $exp=explode(" ", $ua);
            return $exp['9'];
        }else if(stripos($ua, 'Firefox')){
            $exp=explode(" ", $ua);
            return $exp['7'];
        }else if(stripos($ua, 'MSIE')){
            $exp=explode(";", $ua);
            return $exp['1'];
        }else if(stripos($ua, 'Presto')){
            $exp=explode(" ", $ua);
            return $exp['0'];
        }else{
            $exp=explode(" ", $ua);
            return $exp['10'];
        }
    }

}

?>