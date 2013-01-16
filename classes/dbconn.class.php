<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dbconn
 *
 * @author PETTER
 */
abstract class Singleton {

    protected static $_instance;

    protected function __construct() {
        
    }

    public static function instance() {
        
    }

}

class dbconn extends Singleton {

    private $_conn;
    private $login = 'simplexuser';
    private $server = 'simplexuser.db.8655588.hostedresource.com';
    private $password = 'BeR5348833';
    private $database = 'simplexuser';
    private $forbidden = array('update', 'insert', '#', '--', '//', 'delete', 'exec', 'bind', 'union', 'drop', 'database',
        ';', '..', '*', '\'', '=', '"', '/', '\\', 'truncate', 'table', 'select', '<', '>', 'parse', 'eval', 'alter', 'dbo.','--','%'
    );

    protected function __construct() {
        $this->_conn = mysql_connect($this->server, $this->login, $this->password) or die(mysql_error());
        mysql_select_db($this->database, $this->_conn) or die(mysql_error());
    }

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new dbconn();
        }
        return self::$_instance;
    }

    public function query($query) {
        return mysql_query($query);
    }

    public function insert($query) {
        if (mysql_query($query)) {
            return mysql_affected_rows();
        } else {
            return 0;
        }
    }

    public function escape($string) {
        //return htmlspecialchars(strip_tags(str_replace($this->forbidden, '', strtolower($string))));
        return htmlspecialchars(strip_tags($string));
    }

}
//
//$baza = dbconn::instance();
//$result = $baza->query("SELECT idfeedback, ocena, version, opinion, date, ip FROM feedback");
//while ($row = mysql_fetch_array($result)) {
//    echo $row['ip'];
//}
?>
