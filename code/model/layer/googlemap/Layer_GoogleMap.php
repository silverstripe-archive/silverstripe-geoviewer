<?php
/**
 * @package geoviewer
 * @subpackage googlemap
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */

/**
 *
 * @package geoviewer
 * @subpackage googlemap
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class Layer_GoogleMap extends Layer {
	
	static $gmap_types = array(
		"Satellite" => "google.maps.MapTypeId.SATELLITE",
		"Map" => "google.maps.MapTypeId.ROADMAP",
		"Terrain" => "google.maps.MapTypeId.TERRAIN",
		"Hybrid" => "google.maps.MapTypeId.HYBRID"
	);
	
	static $db = array(
		'GMapTypeName' =>  "Enum(array('Satellite','Map','Terrain','Hybrid'),'Satellite')",
	);

	static $default_sort = "Title ASC, GMapTypeName ASC";
	
	public function getCMSFields($params = null) {
		$fields = parent::getCMSFields($params);
		
		$fields->removeFieldFromTab("Root", "FeatureTypes");
		$fields->removeFieldFromTab("Root.Main", "Queryable");
		$fields->removeFieldFromTab("Root.Main", "Type");
		return $fields;
	}	

	protected function onBeforeWrite() {
		parent::onBeforeWrite();
		
		$this->Type = 'contextual';
		return;
	}
		
	function getGMapType() {
		return self::$gmap_types[$this->GMapTypeName];
	}
	
	function getJavaScript() {
		return $this->renderWith('JS_Layer_GoogleMap');
	}
	
	function isSphericalMercator() {
		$retValue = false;
		
		if ($this->Map()) {
			if ($this->Map()->Projection == 'EPSG:900913') {
				$retValue = true;
			}
		}
		
		return $retValue;
	}
}