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
		'doImportFeatureTypes'
	);


/*
http://www.geoviewer.umwelt.bremen.de:8080/geoserver/wfs?service=wfs&version=1.1.0&request=getcapabilities
*/
	public function doImportFeatureTypes($request) {
		$params = $request->getVars();

		$ID = $params['ID'];
		$storage = DataObject::get_by_id('StorageGeoserver',$ID);
		if ($storage == false) {
			$this->response->addHeader('X-Status', "Storage not known to the system.");			
			return;
		}

		$data = array(
			'Storage' => $storage
		);
		
		// request all WFS FeatureTypes
		$model = new GeoserverModel();
		$model->setStorage($storage);

		$result = $model->getWFSCapabilities();
		$message = sprintf("In total %d feature types have been created.",$result);
		$this->response->addHeader('X-Status', $message);
	}
}
