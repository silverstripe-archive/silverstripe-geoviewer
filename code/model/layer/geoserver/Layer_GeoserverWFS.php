<?php
/**
 * @package geoviewer
 * @subpackage model
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */


/**
 *
 * @package mapping
 * @subpackage geoserver
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class Layer_GeoserverWFS extends Layer_WFS {
	
	static $db = array (
		"OutputFormat" => "Enum(array('json'),'json')",
	);	
	
	static $has_one = array(
		'Storage' => "StorageGeoserver",
		'StyleMap' => 'StyleMap'
	);	

	function getJavaScript() {
		return $this->renderWith('JS_Layer_GeoserverWFS');
	}
	
}