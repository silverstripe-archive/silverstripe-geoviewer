<?php

class Layer_MapServerWMS extends Layer_WMS {

	static $db = array(
		'LayerName' => 'Varchar(512)',
		'Format' => "Enum(array('image/png','image/jpeg','image/png24','image/gif'),'image/png')",
		'transitionEffect' => "Enum(array('','resize'),'resize')",
		'info_format' => "Enum(array('text/plain','application/vnd.ogc.gml'),'application/vnd.ogc.gml')"
	);
	
	static $has_one = array(
		'Storage' => "StorageMapServer"
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
		return $this->renderWith('JS_Layer_MapServerWMS');
	}
	
	function getNamespace() {
		$namespace = '';
		return $namespace;
	}	
}