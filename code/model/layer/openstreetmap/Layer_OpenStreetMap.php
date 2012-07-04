<?php
/**
 * @package geoviewer
 * @subpackage openstreetmap
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */

/**
 *
 * @package geoviewer
 * @subpackage openstreetmap
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class Layer_OpenStreetMap extends Layer {

	function getJavaScript() {
		return $this->renderWith('JS_Layer_OpenStreetMap');
	}

}