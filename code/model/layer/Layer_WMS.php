<?php
/**
 * @package geoviewer
 * @subpackage layer
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */

/**
 *
 * @package geoviewer
 * @subpackage layer
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class Layer_WMS extends Layer {

	static $db = array(
	);
	
	function getJavaScript() {
		throw new Layer_WMS_Exception('getJavaScript not implemented');
	}
	
}

class Layer_WMS_Exception extends Exception {
	
}