<?php
class CommonHelper extends AppHelper {
	var $helpers = array('Html', 'Number');

	function customDate($dateString, $format = 'd F Y') {
		return date($format, strtotime($dateString));
	}

	/**
	*
	*	filterisasi content tag
	*
	*	@param string string : string
	*	@return string
	*/
	function safeTagPrint($string){
		return strip_tags($string);
	}
}