<?php
/**
 * @package geoviewer
 * @subpackage controller
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */


/**
 * 
 *
 * @package mapping
 * @subpackage controller
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class Feature_Controller extends Controller {

	public static $url_handlers = array(
		'dogetfeatureinfo/$ID/$OtherID' => 'dogetfeatureinfo',
		'dogetfeature/$ID/$OtherID' => 'dogetfeature'
	);
	

	static $template_name = array(
		'GetFeature' => 'GetFeature',
		'GetFeatureInfo' => 'GetFeatureInfo'
	);

	static $allowed_actions = array(
		'dogetfeatureinfo',
		'dogetfeature'
	);
	
	/**
	 *
	 */
	static function set_template_name($value) {
		self::$template_name = $value;
	}
	
	/**
	 *
	 */
	static function get_template_name($key) {
		return isset(self::$template_name[$key]) ? self::$template_name[$key] : null;
	}
	
	/**
	 * This method determines which Action names need to be executed
	 * to retrieve the requested information. It takes the type of 
	 * connector into consideration.
	 *
	 * NOTE: at this stage, this method does not take the URL of a
	 * connector into consideration. This means following case is not
	 * covered: you want to send a get-feature-info request to two dedicated
	 * geoserver instances.
	 *
	 * @param $layers DataObjectSet of selected layers to run the query on.
	 * @param $action String, name of the action which will be used to determine the command name.
	 *
	 * @return array array structure for each connector type.
	 *
	 * array(
	 *   'GetFeatureInfoCommand' => array(
	 *     'URL' => 'http://localhost:8080/geoserver/service/wms',
	 *     'Action' => 'GetFeatureInfoCommand',
	 *     'Layers' => array(
	 *        Layer-DataObject1,
	 *        Layer-DataObject2,
	 *     )
	 *   )
	 * )
	 */
	private function getActions($layers,$action) {
		$actions = array();
		foreach($layers as $layer) {

			$commandName = $layer->getActionName($action);
			$storage = $layer->Storage();
			
			$id = md5(sprintf('%s-%s',$commandName, $storage->URL));
			
			if (!isset($actions[$id])) {
				$set = new DataObjectSet();
				$set->push($layer);
				$actions[$id] = array(
					"URL" => $storage->URL,
					"Action" => $commandName,
					"Layers" => $set
				);
			} else {
				$actions[$id]['Layers']->push($layer);
			}
		}
		return $actions;
	}
	
	/**
	 * This method creates a Sapphire ORM data model for the returned OGC
	 * features and maps those to the data objects stored in the CMS.
	 *
	 * @param $features array 
	 *
	 * @return DataObjectSet
	 */
	public function mapOGC2ORM($features) {
		$response = new DataObjectSet();
		
		if (!$features) {
			return $response;
		}
		// get featuretypenames to retieve defined featuretypes from the CMS.
		$featureTypeNames = array();
		foreach($features as $key => $value) {
			$featureTypeNames[] = Convert::raw2sql($key);
		}
		
		$sql_segment = implode("','",$featureTypeNames);
		$featureTypes = DataObject::get("FeatureType",sprintf("\"Name\" in ('%s')",$sql_segment));


		// iterate through the feature types and create a dataobject set for the
		// template rendering, bringing together the feature-type dataobject with
		// the OGC response.
		if ($featureTypes) {
			foreach($features as $featureTypeName => $value) {
			
				// Is the returned feature type name stored in the CMS? If so, 
				// then add this feature type to the overall response.
				$DOFeatureType = $featureTypes->find('Name',$featureTypeName);
			
				if ($DOFeatureType) {
					// check if the layer already exists in the response dataobjectset.
					$layerID =  $DOFeatureType->LayerID;
					$layer = $response->find('ID',$layerID);
					if (!$layer) {
					
						// layer not in the response list, create new layer entry
						$layer = new ArrayData(array(
							'ID' => $layerID,
							'Layer' => $DOFeatureType->Layer(),
							'FeatureTypes' => new DataObjectSet(),
							'scope' => 'Layers'
						));
						$response->push($layer);				
					}

					$layer->FeatureTypes->push( new ArrayData(array(
						'FeatureType' => $DOFeatureType,
						'Features' => $value,
						'scope' => 'FeatureTypes'
					)));
				}
			}
		} else {
		}
		return $response;
	}
	
	/**
	 * @return String HTML
	 */
	public function dogetfeatureinfo($request) {
		$action = "GetFeatureInfo";
		$result = null;
		$param = $request->getVars();
		
		$layerID = $param['LAYERS'];
		
		$layers = DataObject::get("Layer",sprintf("\"Layer\".\"ID\" in (%s) AND \"Queryable\" = 1",Convert::raw2sql($layerID)));
		
		$commands = $this->getActions($layers, $action);

		$results = array();
		foreach($commands as $id => $item) {
			$commandName = $item['Action'];
			// initiate command parameters for the WMS-GetFeatureInfo request
			$url = $item['URL'];
			
			$layers = $item['Layers']->map("ID","LayerName");
			$layers = implode(',',$layers);
			$namespaces = $item['Layers']->map("Namespace","Namespace");

			$param['LAYERS'] = $layers;
			
			$data = array(
				'URL' => $url,
				'HTTP_parameters' => $param
			);
			try {
				// get command and execute command
				$cmd = $this->getCommand($commandName, $data);
				$xml = $cmd->execute();
						
				$item = array(
					'Namespace' => $namespaces,
					'ServerResult' => $xml
				);
				$results[] = $item;
			}
			catch(Exception $exception) {
			}
		}
		
		$parser = new GetFeatureXMLParser();
		$parser->setLimit(25);
		$items = new DataObjectSet();
		
		$response_features = array();

		try {
			foreach($results as $result) {
				$response_features[] = $parser->parse($result);
			}
		}
		catch(Exception $exception) {
			//todo		
		}
		
		foreach($response_features as $features) {
			// create Sapphire-Data Structure for template rendering
			$items = $this->mapOGC2ORM($features);
		}

		// Render the template for a single feature type (or multiples of them)
		$vData = new ViewableData();
		$vData->customise( array(
			"Items" => $items
		));
		return $vData->renderWith( self::get_template_name($action) );
	}	
	
	/**
	 * Processes params and finds if request is for a single or multiple stations.
	 * if single station calls renderSingleStation method with station and layers values
	 * if multiple stations create list with stations to render HTML
	 * if not stationID displays message
	 *
	 * @param Request $request
	 *
	 * @throws Feature_Controller_Exception
	 *
	 * @return string HTML segment
	 */
	public function dogetfeature( $request ) {
		$action = "GetFeature";
		
		if( $request->param("ID") == "" || $request->param("OtherID") == "" ) {
			throw new Feature_Controller_Exception('Mandatory request parameters not provided.');
		}
		
		$mapID = (Integer)$request->param("ID");
		$featureIDs = Convert::raw2sql($request->param("OtherID"));
		
		$output = "Sorry we cannot retrieve feature information, please try again.";

		// Determin the layer (The string has a structure like: featureType.featureID)
		// We use the featureType to determin the layer object, used to send
		// the get feature request.
		$featureStructure = explode(".", $featureIDs); 
		
		if(count($featureStructure) <= 1) {
			throw new Feature_Controller_Exception('Invalid feature-ID structure.');
		}
		$featureType = Convert::raw2sql($featureStructure[0]);

		$layer = DataObject::get_one('Layer',sprintf("FeatureType = '%s' AND MapID = '%s'",$featureType,$mapID));
		
		if (!$layer) {
			throw new Feature_Controller_Exception(sprintf("Unknown feature type: '%s'",$featureType));
		}

		$data = array(
			'Layer' => $layer,
			'featureID' => $featureIDs
		);
		
		// get commend, i.e., GeoserverWFS_GetFeature
		$commandName = $layer->getActionName($action);
		$cmd = $this->getCommand($commandName, $data);

		$result = $cmd->execute();

		$viewableData = new ViewableData();
		$obj = new DataObjectSet();
		
		$featureTypeObj = DataObject::get_one("FeatureType",sprintf("\"Name\" = '%s' AND \"LayerID\" = '%s'", Convert::raw2sql($featureType),$layer->ID));			
		
		
		if ($featureTypeObj == null) {
			throw new Feature_Controller_Exception(sprintf("Feature Type '%s' not defined. Can not render result. Please contact system administrator.",$featureType));
		}
		
		$features = array();
		$dataObjectSet = new DataObjectSet();

		if (isset($result['features'])) {
			$features = $result['features'];
		}

		// convert feature properties into a template data structure
		foreach($features as $feature) {
			$dataObjectSet->push(
				new ArrayData($feature['properties'])
			);
		}

		$viewableData->customise( array(
			"Layer" => $layer,
			"FeatureTypes" => $layer->FeatureTypes(),
			"Features" => $dataObjectSet,
			"FeatureIDs" => $featureIDs
		));
		
		$template = $featureTypeObj->FeatureTypeTemplate;
		
		// if a template in the CMS is defined, use the template insteat of the default template.
		if ($template) {
			$viewer = SSViewer::fromString($template);
			return $viewableData->renderWith( $viewer );
		}
		
		return $viewableData->renderWith( self::get_template_name($action) );
	}
}

/**
 *
 */
class Feature_Controller_Exception extends Exception {
}