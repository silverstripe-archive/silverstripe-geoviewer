<?php
/**
 * @package geoviewer
 * @subpackage command
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */


/**
 * @package geoviewer
 * @subpackage command
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class GeoserverWFS_DescribeFeatureTypeCommand extends GeoServerAPICommand {

	/**
	 * Create a WFS-DescribeFeatureType request.
	 *
	 * @param FeatureType $parameter Instance of the feature-type object.
	 *
	 * @return SS_HTTPRequest
	 */ 
	protected function buildRequest($parameter) {
		// get geoserver wfs server OGC API
		
		if($parameter == null){
			throw new GeoServerAPICommand_Exception("Internal error: Undefined feature type.");
		} else 
		if($parameter->Storage() == null){
			throw new GeoServerAPICommand_Exception("Feature Type is not assigned to a storage. Please check the layer configuration.");
		}  

		$geoserver_url = $parameter->Storage()->URL;
		$geoserver_url = str_replace("http://","",$geoserver_url);
		$geoserver_url = str_replace("/wms","/wfs",$geoserver_url);
		$geoserver_url = str_replace("/service/","/",$geoserver_url);
		$geoserver_url = str_replace("/gwc/","/",$geoserver_url);
		$geoserver_url = str_replace("/geowebcache/","/",$geoserver_url);

		if($geoserver_url == ''){
			throw new GeoServerAPICommand_Exception("Undefined GeoServer WFS URL: please check the Geoserver-Storage configuration.");
		}

		$data = new ArrayData(array(
			'FeatureType' => $parameter
		));

		$body = $data->renderWith('GeoserverWFS_DescribeFeatureType');
		
		$request = new SS_HTTPRequest(
			'POST',
			$geoserver_url,
			null,
			null,
			$body
		);
		$request->addHeader('Content-Type', 'application/xml');
		return $request;
	}
	
	public function execute() {
		
		$parameters = $this->getParameters();
		$featureType = $parameters['FeatureType'];		
		
		$this->storage = $featureType->Storage();

		$response = $this->sendRequest($featureType);
		return $response;
	}	
}
