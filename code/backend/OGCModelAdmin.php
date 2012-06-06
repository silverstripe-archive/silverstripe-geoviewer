<?php
/**
 * @package geoviewer
 * @subpackage backend
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */


/**
 * OGC WebService - Model-Admin class.
 *
 * @package mapping
 * @subpackage backend
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class OGCModelAdmin extends ModelAdmin {

	static $menu_title = "Map Sources";
	
	static $url_segment = "mapsources";

	static $managed_models = array(
		"Layer_GoogleMap",
		"Layer_OpenStreetMap",
		"StorageGeoserver",
		"Layer_GeoserverWMS",
		"Layer_GeoserverWFS",
		"StorageMapServer",
		"Layer_MapServerWMS",
		"Layer_MapServerWFS",
		"Layer_KML",
		"Layer_GML",
		"StyleMap",
	);

	static $allowed_actions = array(
	);
}
