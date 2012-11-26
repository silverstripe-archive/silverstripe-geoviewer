<?php

class GeoServerWMS_GetCapabilitiesCommand extends GeoServerAPICommand {

	/**
	 * Create a WFS-DescribeFeatureType request.
	 *
	 * @param FeatureType $parameter Instance of the feature-type object.
	 *
	 * @return SS_HTTPRequest
	 */ 
	protected function buildRequest($parameter) {

		$geoserver_url = $this->storage->getWMSURL();
		$geoserver_url = str_replace("http://","",$geoserver_url);
		$geoserver_url = str_replace("/wms","/wfs",$geoserver_url);
		$geoserver_url = str_replace("/service/","/",$geoserver_url);
		$geoserver_url = str_replace("/gwc/","/",$geoserver_url);
		$geoserver_url = str_replace("/geowebcache/","/",$geoserver_url);

		if($geoserver_url == ''){
			throw new GeoServerAPICommand_Exception("Undefined GeoServer WMS URL: please check the Geoserver-Storage configuration.");
		}

		$data = new ArrayData(array(
			'FeatureType' => $parameter
		));

		$body = $data->renderWith('GeoserverWFS_DescribeFeatureType');
		
		$getVars = array(
			'request' => 'getCapabilities',
			'service' => 'WMS'
		);

		$request = new SS_HTTPRequest(
			'GET',
			$geoserver_url,
			$getVars
		);
		$request->addHeader('Content-Type', 'application/xml');
		return $request;
	}
	
	public function execute() {
		
		$parameters = $this->getParameters();
		$this->storage = $parameters['Storage'];		

		$response = $this->sendRequest($parameters);
		return $response;
	}	
}
