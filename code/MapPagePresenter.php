<?php
/**
 * @package geoviewer
 * @subpackage code
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */

/**
 * Model
 */
class MapPagePresenter extends ViewableData {

	private $modulePath = 'geoviewer';

	function getModulePath() {
		return $this->modulePath;
	}

	function setModulePath($value) {
		$this->modulePath = $value;
	}
		
	function getCSSFiles() {
		$value = array(
			$this->getModulePath().'/css/MapStyle.css', // CSS for MapStyles
			$this->getModulePath().'/css/MapPage.css', // CSS for MapPage
			$this->getModulePath().'/css/layout.css', // CSS for Map Bubble
			$this->getModulePath().'/css/LayerList.css' // CSS for MapLayer list
		);
		return $value;
	}

	function getJavaScriptFiles() {
		$js_files = array(
			'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js',
			// $this->getModulePath().'/thirdparty/jquery-ui-1.7.2.custom.min.js',
			$this->getModulePath().'/thirdparty/jquery.entwine/dist/jquery.entwine-dist.js',
			$this->getModulePath().'/thirdparty/jquery.metadata/jquery.metadata.js',
		);
		return $js_files;
	}

	function getJavaScript($model) {
		$this->failover = $model;		
		return $model->renderWith('JS_MapPage');
	}
	
	function GoogleMapAPIKey() {
		global $googlemap_api_keys;
		$environment = Director::get_environment_type();

		$api_key = null;
		$host = $_SERVER['HTTP_HOST'];
		if (isset($googlemap_api_keys["$environment"])) {
			$api_key = $googlemap_api_keys["$environment"];
		} elseif (isset($googlemap_api_keys[$host])) {
			$api_key = $googlemap_api_keys[$host];
		}
		return $api_key;
	}	
}