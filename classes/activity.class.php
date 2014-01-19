<?php

/**
 * Changes value of XML file that can be
 * then read as turning app on/off
 *
 * @author Piotr Gołasz <pgolasz@gmail.com>
 */
class activity {

	/**
	 * Prints json array with 'active' value of XML file in $path path
	 * @param String $path
	 */
	public static function isactivated($path) {
		$doc = new DOMDocument();
		$doc->load($path);
		$isactivated = $doc->firstChild;
		$is = $isactivated->nodeValue;
		$json = array('active' => $is);
		echo json_encode($json);
	}

	/**
	 * Tests if XML File has value true (hence app is On)
	 * @param String $path
	 * @return boolean
	 */
	public static function isactivated2($path) {
		$doc = new DOMDocument();
		$doc->load($path);
		$isactivated = $doc->firstChild;
		$is = $isactivated->nodeValue;
		if ($is == "true") {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Toggles activity - swithces app On/Off
	 * @param Path $path
	 */
	public static function toggleactivity($path) {
		$doc = new DOMDocument();
		$doc->load($path);
		$isactivated = $doc->firstChild;
		$is = $isactivated->nodeValue;
		if ($is == "true") {
			$zmienna = '<?xml version="1.0" encoding="utf-8"?>';
			$zmienna.='<activated>false</activated>';
		} else {
			$zmienna = '<?xml version="1.0" encoding="utf-8"?>';
			$zmienna.='<activated>true</activated>';
		}
		file_put_contents("../../activity/active.xml", $zmienna);
		$json = array('active' => $is);
		echo json_encode($json);
	}

	/**
	 * Returns Error message in way formed by jQuery UI
	 * @param String $message
	 * @return String
	 */
	public static function errormessage($message) {
		return '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span><strong>Alert:</strong>' . $message . '</p></div>';
	}

}

?>
