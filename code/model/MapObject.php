<?php
/**
 * @package geoviewer
 * @subpackage model
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */

/**
 * @package geoviewer
 * @subpackage model
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class MapObject extends DataObject {

	static $db = array(
		"Title" => "Varchar(255)",
		"Enabled" => "Boolean",
		"Lat" => "Float",
		"Long" => "Float",
		"ZoomLevel" => "Int",
		'Resolutions' => 'Varchar(1024)',
		'DisplayProjection' => "Enum(array('EPSG:4326','EPSG:900913'),'EPSG:4326')",
		'Projection' => "Enum(array('EPSG:4326','EPSG:900913'),'EPSG:4326')",
		"MinZoomLevel" => "Int",
		"MaxZoomLevel" => "Int"
	);

	static $has_one = array(
		"MapPage" => "Page"
	);	

	static $has_many = array(
		'Layers' => 'Layer'
	);

	static $searchable_fields = array(
	      'Title'  
	);

	static $summary_fields = array(
		'Title',
		'MapPage.Title'
	);

	static $default_sort = "Title ASC";

	function getCMSFields() {
		$fields = parent::getCMSFields();
		
		$fields->addFieldsToTab('Root.Main', array (
	//		new LiteralField('MapPreview',sprintf('<a href=\'$s\' target=\'_mappreview\'>Map preview</a>'))
		));
		
		return $fields;
	}

	function getJavaScript() {
		$js = '';

		// base layers first
		$layers = $this->Layers("\"Enabled\" = 1 AND \"Basemap\" = 1 ");
		
		foreach($layers as $layer) {
			$js .= $layer->getJavaScript();
		}

		// then add all the others layers
		$layers = $this->Layers("\"Enabled\" = 1 AND \"Basemap\" = 0 ");
		
		foreach($layers as $layer) {
			$js .= $layer->getJavaScript();
		}
		return $js;
	}
	
	function getCategories() {
		
		$categories = DataObject::get(
			'LayerCategory', 
			sprintf('"Layer"."ID" IS NOT NULL AND "MapID" = %d', $this->ID), 
			'"Sort" ASC, "Title" ASC', 
			'LEFT JOIN "Layer" ON "LayerCategory"."ID" = "Layer"."LayerCategoryID"'
		);
		// TODO Workaround because the Postgres implementation fails at grouping...
		// See DataObject->buildSQL()
		if($categories) $categories->removeDuplicates('ID');
		
		return $categories;
	}
}