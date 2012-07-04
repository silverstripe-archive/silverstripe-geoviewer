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
class Layer_WFS extends Layer {

	static $db = array(
		'Namespace' => 'Varchar',  			// tiger
		'FeatureType' => 'Varchar',  		// poi
		'Projection' => 'Varchar', 			// EPSG:4326
		'Version' => 'Varchar',    			// 1.1.0
		'ClusterFeatures' => 'Boolean', 	// true/false
		'ClusterDistance' => 'Int', 	// Integer value
	);
	
	function getJavaScript() {
		throw new Layer_WFS_Exception('getJavaScript not implemented');
	}
}

class Layer_WFS_Exception extends Exception {
	
}