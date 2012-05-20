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
class StorageGeoserver extends DataObject {
	
	static $db = array (
		'Enable' => 'Boolean',
		'Title' => "Varchar(255)",
		'URL' => "Varchar(255)",
		'UseMultiCache' => "Boolean",
		'Cache_URL_01' => "Varchar(255)",
		'Cache_URL_02' => "Varchar(255)",
		'Cache_URL_03' => "Varchar(255)",
		'Cache_URL_04' => "Varchar(255)",
		'URL_WFS' => "Varchar(255)",
		'Username' => "Varchar(255)",
		'Password' => "Varchar(255)",
	);
	
	static $has_many = array (
		"Layers" => "Layer_GeoserverWMS"
	);
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		
	 	$fields->addFieldToTab('Root.Main',
			new LiteralField('Desc1',"<h3>Cache settings</h3><p>Use CACHE if you provide more than one WMS-T server from the same source.</p>"), 'UseMultiCache'
		);
	 	$fields->addFieldToTab('Root.Main',
			new LiteralField('Desc2',"<h3>OGC Web Feature Service (WFS)</h3><p>Use OGC WFS, if vector data has been made available.</p>"), 'URL_WFS'
		);
	 	$fields->addFieldToTab('Root.Main',
			new LiteralField('Desc3',"<h3>Authentication</h3><p>Use Authentication if data is protected (this feature is in beta).</p>"), 'Username'
		);

		return $fields;
	}
}
?>