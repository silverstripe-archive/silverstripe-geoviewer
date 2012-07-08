<?php
/**
 * @package geoviewer
 * @subpackage code
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */

/**
 * Example Map Page class. 
 *
 * Can be deleted if example code is not required.
 *
 * @package geoviewer
 * @subpackage code
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class MapPage extends Page {
	
}

/**
 * Controller for example Map Page type. 
 *
 * Can be deleted if example code is not required.
 *
 *
 * @package geoviewer
 * @subpackage code
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class MapPage_Controller extends Page_Controller {

	/**
	 * Initialisation any request.
	 * Is called before any controller action is performed.
	 *
	 * @2do: review if Controller has a hook we can use
	 */
	public function init() {
		parent::init();
	}

	// /**
	//  */
	// function getCategoriesByLayerType($layertype) {
	// 	$map = $this->dataRecord->MapObject();
	// 	$categories = $map->getCategories();
	// 	$retValue = new ArrayList();

	// 	if($categories) {
	// 		foreach($categories as $category) {
 // 				$layers = $category->getEnabledLayers($map, $layertype);
	// 			if ($layers->Count()) {
	// 				$entry = new ArrayData( array(
	// 					'Category' => $category,
	// 					'Layers' => $layers
	// 				));
	// 				$retValue->push($entry);
	// 			}
	// 		}
	// 	}
	// 	return $retValue;
	// }

	// /**
	//  */
	// function getOverlayCategories() {
	// 	return $this->getCategoriesByLayerType('overlay');
	// }

	// /**
	//  */
	// function getBackgroundCategories() {
	// 	return $this->getCategoriesByLayerType('background');
	// }	
	
	// /**
	//  */
	// function getContextualCategories() {
	// 	return $this->getCategoriesByLayerType('contextual');
	// }
	
}

