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
class OGCModelAdmin extends ModelAdmin {

	static $menu_title = "Map Layers";
	
	static $url_segment = "maplayers";

	static $managed_models = array(
		"Layer_GoogleMap",
		"Layer_OpenStreetMap",
		"Layer_GeoserverWMS",
		"Layer_GeoserverWFS",
		"Layer_MapServerWMS",
		"Layer_MapServerWFS"
	);

	static $allowed_actions = array(
	);
}
