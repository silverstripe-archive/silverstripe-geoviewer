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
		"Layer_GeoserverWMS",
		"Layer_GeoserverWFS",
		"Layer_MapServerWMS",
		"Layer_MapServerWFS"
	);

	static $allowed_actions = array(
	);
}
