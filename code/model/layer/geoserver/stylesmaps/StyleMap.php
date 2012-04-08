<?php
/**
 * @package geoviewer
 * @subpackage model
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */


class StyleMap extends DataObject {
	
	
	static $db = array(
		"Name" => "Varchar(100)",
		"default" => "Text",
		"select" => "Text",
		"temporary" => "Text"
	);
	
	static $has_many = array(
		"WFSLayers" => "Layer_GeoserverWFS"
	);
	
	function getJavaScript() {
		return $this->renderWith('JS_StyleMap');
	}
	
}