<?php
/**
 * @package geoviewer
 * @subpackage model
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */

/**
 * 
 *
 * @package mapping
 * @subpackage geoserver
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class Layer_GeoserverWMS extends Layer_WMS {
	
	static $db = array(
		'LayerName' => 'Varchar(512)',
		'Format' => "Enum(array('image/png','image/jpeg','image/png24','image/gif'),'image/png')",
		'transitionEffect' => "Enum(array('','resize'),'resize')",
		'info_format' => "Enum(array('text/plain','application/vnd.ogc.gml'),'application/vnd.ogc.gml')"
	);
	
	static $has_one = array(
		'Storage' => "StorageGeoserver"
	);

	static $summary_fields = array(
		'Title',
		'LayerName',
		'Map.Title'
	);
	
	static $searchable_fields = array(
	      'Title',
	      'LayerName'
	);
	
	static $default_sort = "Title ASC, LayerName ASC";
	
	function getJavaScript() {
		return $this->renderWith('JS_Layer_GeoserverWMS');
	}
	
	function getNamespace() {
		$namespace = '';
		foreach($this->FeatureTypes() as $featuretype) {
			$namespace = $featuretype->Namespace;
		}

		if ($namespace == '') {
			$pairs = preg_split("/:/", $this->LayerName, -1, PREG_SPLIT_NO_EMPTY);
			$namespace = $pairs[0];
		}
		return $namespace;
	}
}