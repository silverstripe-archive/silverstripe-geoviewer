<?php
/**
 * @package geoviewer
 * @subpackage backend
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */

/**
 * @package geoviewer
 * @subpackage backend
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class StorageModelAdmin extends ModelAdmin {

	static $menu_title = "Storages and Files";
	
	static $url_segment = "storages";

	static $managed_models = array(
		"StorageGeoserver",
		"StorageMapServer",
		"Layer_KML",
		"Layer_GML"
	);

	static $allowed_actions = array(
	);
}
