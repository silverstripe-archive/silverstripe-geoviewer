<?php
/**
 * @package geoviewer
 * @subpackage mapserver
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */

/**
 *
 * @package geoviewer
 * @subpackage mapserver
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class StorageMapServer extends DataObject {

	
	static $db = array (
		'Enable' => 'Boolean',
		'Title' => "Varchar(255)",
		'URL' => "Varchar(255)",
		'UseMultiCache' => "Boolean",
		'Cache_URL_01' => "Varchar(255)",
		'Cache_URL_02' => "Varchar(255)",
		'Cache_URL_03' => "Varchar(255)",
		'Cache_URL_04' => "Varchar(255)"
	);
	
	static $has_many = array (
		"Layers" => "Layer_MapServerWMS"
	);
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		
	 	$fields->addFieldToTab('Root.Main',
			new LiteralField('Desc1',"<h3>Cache settings</h3><p>Use CACHE if you provide more than one WMS-T server from the same source.</p>"), 'UseMultiCache'
		);
		return $fields;
	}
}