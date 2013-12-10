<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of activity
 *
 * @author PETTER
 */
class activity {
    public static function isactivated($path){
        $doc=new DOMDocument();
        $doc->load($path);
        //'../../activity/active.xml'
        $isactivated=$doc->firstChild;
        $is=$isactivated->nodeValue;
        $json=array('active'=>$is);
        echo json_encode($json);
    }
    
    public static function isactivated2($path){
    	$doc=new DOMDocument();
    	$doc->load($path);
    	//'../../activity/active.xml'
    	$isactivated=$doc->firstChild;
    	$is=$isactivated->nodeValue;
    	if($is=="true"){
    		return true;
    	}else{
    		return false;
    	}
    }
    
    public static function toggleactivity($path){
        $doc=new DOMDocument();
        $doc->load($path);
        //'../../activity/active.xml'
        $isactivated=$doc->firstChild;
        $is=$isactivated->nodeValue;
        if($is=="true"){
            $zmienna='<?xml version="1.0" encoding="utf-8"?>';
            $zmienna.='<activated>false</activated>';
        }else{
            $zmienna='<?xml version="1.0" encoding="utf-8"?>';
            $zmienna.='<activated>true</activated>';
        }
        file_put_contents("../../activity/active.xml", $zmienna);
        $json=array('active'=>$is);
        echo json_encode($json);
    }
    
    public static function errormessage($message){
    	echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>'.$message.'</p></div>';
    }
    
    public static function errormessage2($message){
    	return '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>'.$message.'</p></div>';
    }
}
?>
