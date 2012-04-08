<?php
/**
 * @package geoviewer
 * @subpackage model
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */


/**
 *
 * @package geoviewer
 * @subpackage fileformat
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class Layer_KML extends Layer {
	
	static $db = array (
		'URL' => 'Varchar(1024)',
		'EPSGCode' => 'Varchar(128)'
	);	
	
	static $has_one = array(
		'kmlFile' => "File",
	);	

	function getJavaScript() {
		return $this->renderWith('JS_Layer_KML');
	}
	
	static function getFeatureInfoParserName() {
		return "GetFeatureTextPlainParser";
	}

	public function getCMSFields($params = null) {
		$fields = parent::getCMSFields($params);
				
		$fields->addFieldToTab('Root.KML', new FileIFrameField('kmlFile','KML File'));
		$fields->addFieldToTab('Root.KML', new TextField('EPSGCode','Projection (in EPSG) for local dataset (i.e. EPSG:4326)'));
		$fields->removeFieldFromTab("Root.FeatureTypes", "FeatureTypes");
		$fields->removeFieldFromTab("Root", "FeatureTypes");
		return $fields;
	}	

	function getFileName() {
		$value = $this->URL;
		
		if (!$value) {
			if ($this->kmlFile()) {
				$value = $this->kmlFile()->getAbsoluteURL();
			}
		}
		return $value;
	}

}