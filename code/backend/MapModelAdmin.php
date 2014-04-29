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
class MapModelAdmin extends ModelAdmin {

	private static $menu_title = "Maps and Styles";
	
	private static $menu_icon = 'geoviewer/images/16x16/mapicon.png';

	private static $url_segment = "maps";

	public $showImportForm = false;

	private static $managed_models = array(
		"MapObject",
		"StyleMap",
		"LayerCategory"
	);

	static $allowed_actions = array(
	);
}
