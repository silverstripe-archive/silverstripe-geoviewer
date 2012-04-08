<?php
/**
 * @package geoviewer
 * @subpackage backend
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */


/**
 * Map - Model-Admin class.
 *
 * @package mapping
 * @subpackage backend
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class MapModelAdmin extends ModelAdmin {

	static $menu_title = "Map";
	
	static $url_segment = "maps";

	static $managed_models = array(
		"MapObject",
		"LayerCategory"
	);

	static $allowed_actions = array(
	);
}
