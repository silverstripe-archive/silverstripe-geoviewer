<?php
/**
 * @package geoviewer
 * @subpackage code
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */

/**
 * @package geoviewer
 * @subpackage code
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class MapPagePresenter extends ViewableData {

	private $modulePath = 'geoviewer';
	
	private $openlayers_path = '/thirdparty/OpenLayers-2.12/lib/OpenLayers.js';

	function getModulePath() {
		return $this->modulePath;
	}

	function setModulePath($value) {
		$this->modulePath = $value;
	}

	function get_openlayers_path() {
		return $this->openlayers_path;
	}
	
	function set_openlayers_path($value) {
		$this->openlayers_path = $value;
	}
		
	function getCSSFiles() {
		$value = array(
			$this->getModulePath().'/css/mapstyle.css', // CSS for MapStyles
			$this->getModulePath().'/css/layout.css',   // CSS for Map Bubble
			$this->getModulePath().'/css/layerlist.css' // CSS for MapLayer list
		);
		return $value;
	}

	function getJavaScriptFiles() {
		$js_files = array(
			THIRDPARTY_DIR . '/jquery/jquery.js',
			THIRDPARTY_DIR . '/jquery-entwine/dist/jquery.entwine-dist.js',
			THIRDPARTY_DIR . '/jquery.metadata/jquery.metadata.js'
		);
		return $js_files;
	}

	function getJavaScriptMapExtensionsFiles() {
		$js_files = array(
			$this->getModulePath()."/javascript/MapWrapper.js",
			$this->getModulePath().'/javascript/LayerList.js',
			$this->getModulePath()."/javascript/WMSFeatureInfo.js",
			$this->getModulePath()."/javascript/WFSFeatureInfo.js",
			$this->getModulePath()."/javascript/MapPopup.js",
			$this->getModulePath()."/javascript/control/GeoserverGetFeatureInfo.js"
		);
		return $js_files;		
	}

	function getJavaScript($model) {
		$this->failover = $model;		
		return $model->renderWith('JS_MapPage');
	}
	
}