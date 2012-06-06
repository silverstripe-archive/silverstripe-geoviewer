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
class Layer_MapServerWFS extends Layer_WFS {

	static $db = array (
	);	
	
	static $has_one = array(
		'Storage' => "StorageMapServer",
		'StyleMap' => 'StyleMap'
	);	

	function getCMSFields() {
		$fields = parent::getCMSFields();
	
		$fields->removeByName('Namespace');
		return $fields;
	}

	function getJavaScript() {
		return $this->renderWith('JS_Layer_MapServerWFS');
	}
}
