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
class FeatureTypeAdmin extends ModelAdmin {

	static $menu_title = "Feature Types";
	
	static $url_segment = "featuretypes";

	static $managed_models = array(
		"FeatureType",
	);
	
	static $allowed_actions = array(
		"doImportLabels",
		"doCreateTemplate",
		"doDeleteLabels"
	);
	
	/**
	 */
	public function init() {
		parent::init();
	}	

	/**
	 * This method is a controller action, called by the CMS backend controller.
	 * The FeatureType dataobject creates a 'Import Labels' butten which triggers
	 * this action method. The method sends of a request to retrieve all available
	 * labels of the selected feature class.
	 * This action requires an ID get parameter.
	 */
	public function doImportLabels($request) {
		$params = $request->getVars();

		$ID = $params['ID'];
		$featureType = DataObject::get_by_id('FeatureType',$ID);
		if ($featureType == false) {
			$this->response->addHeader('X-Status', "FeatureType not known to the system.");			
			return;
		}

		$data = array(
			'FeatureType' => $featureType
		);
		
		// get command and execute command
		$message = "";
		try {
			$cmd = $this->getCommand('ImportFeatureTypeLabels', $data);

			$result = $cmd->execute();
			$message = sprintf(
				"%s labels for '%s' has been imported sucessfully.",
				$result, $featureType->getFeatureTypeName());
		} 
		catch(Exception $e) {
			$message = sprintf("FeatureType import failed. Please try again. <br/>Error Message: '%s'", $e->getMessage());
		}
		$this->response->addHeader('X-Status', $message);
	}	

	public function doDeleteLabels($request) {
		$params = $request->getVars();

		$ID = $params['ID'];
		$featureType = DataObject::get_by_id('FeatureType',$ID);
		if ($featureType == false) {
			$this->response->addHeader('X-Status', "FeatureType not known to the system.");			
			return;
		}

		$labels = $featureType->Labels();

		$count = $labels->count();
		foreach($labels as $label) {
			$label->delete();
		}
		$this->response->addHeader('X-Status', sprintf("All %d labels has been deleted.",$count));			
	}

	public function doCreateTemplate($request) {
		$params = $request->getVars();

		$ID = $params['ID'];
		$featureType = DataObject::get_by_id('FeatureType',$ID);
		if ($featureType == false) {
			$this->response->addHeader('X-Status', "FeatureType not known to the system.");			
			return;
		}

		$viewableData = new ViewableData();


		$viewableData->customise( array(
			"FeatureType" => $featureType
		));
		

		$featureType->FeatureTypeTemplate = $viewableData->renderWith( "FeatureTypeTemplate" );
		$featureType->write();
		$this->response->addHeader('X-Status', "Template created");
	}
}
