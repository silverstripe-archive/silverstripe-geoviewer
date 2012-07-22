<?php

/**
 * This model class implements the required API to retrieve all GeoServer
 * configuration elements to manage the map instance in the CMS.
 */
class GeoserverModel {

	protected $storage = null;

	/**
	 * Setter method to set the storage for this GeoServer model.
	 */
	public function setStorage($storage) {
		$this->storage = $storage;
	}

	/**
	 * This method creates am instance of a HTTPRequest object 
	 * which can be used to call the capabilities of a wfs server.
	 *
	 * @param string url to the WFS server
	 *
	 * @return SS_HTTPRequest
	 */
	private function getWFSCapabilitiesRequest($url) {
		$request = new SS_HTTPRequest(
			'GET',
			$url,
			array(
				"service" => "wfs",
				"version" => "1.1.0",
				"request" => "getcapabilities"
			)
		);
		return $request;
	}

	/**
	 * This method is a builder method to create a FeatureType.
	 *
	 * @param string name of the feature type
	 * @param string namespace of the feature type
	 * @param string title of the feature type (human readable)
	 *
	 * @return FeatureType
	 */
	private function createFeatureType($name, $namespace, $title) {
		$FeatureType = new FeatureType();
		$FeatureType->Name = $name;
		$FeatureType->Namespace = $namespace;
		$FeatureType->Title = $title;
		$FeatureType->StorageID = $this->storage->ID;
		$FeatureType->write();		
		return $FeatureType;
	}

	/**
	 * This method parses an array of feature types from the xml document.
	 * It parses the structure and calls the builder method to create the FeatureType 
	 * instance if it does not exist for this storage. 
	 * The key used to identify if a feature type does already exist in the system
	 * is the namespace and the name for this storage.
	 *
	 * @param DOMNodeList $featuretypes segment of the XML document
	 *
	 * @return int number of featuretypes, created in this call.
	 */
	private function parseWFSResponseAndUpdateFeaturesTypes($featuretypes) {
		$count = 0;

		foreach ($featuretypes as $featuretype) {
			$temp = $featuretype->getElementsByTagName('Name');
			$name = $temp->item(0)->nodeValue;

			$temp = $featuretype->getElementsByTagName('Title');
			$title = $temp->item(0)->nodeValue;
			$list = explode(":",$name);

			$namespace = $list[0];
			$name = $list[1];

			$items = DataObject::get('FeatureType');
			$items->where("Name = '$name' AND Namespace = '$namespace' AND StorageID = '".$this->storage->ID."'");

			if ($items->count() == 0) {
				$this->createFeatureType($name, $namespace, $title);
				$count++;
			}
  		}
		return $count;
	}

	/**
	 * This method builds and sends a request to a remote server using the provided request object and assigned storage.
	 * The storage is used to verify authentication parameters if configured. 
	 * But in general the URL passed in can refer to any webservice url.
	 *
	 * @throws GeoserverModel_Excpetion
	 *
	 * @param SS_HTTPRequest $owsRequest
	 *
	 * @return string the response XML document.
	 */
	private function sendRequest($owsRequest) {
	
		//
		// initiate CURL request
		$url = $owsRequest->getURL();
		
		if($owsRequest->getVars()) $url .= '?' . http_build_query($owsRequest->getVars());
		
		$ch  = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if($owsRequest->isPost()) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $owsRequest->getBody());
		}

		$storage = $this->storage;
		if($storage->Username && $storage->Password) {
			curl_setopt($ch, CURLOPT_USERPWD, $storage->Username . ':' . $storage->Password);
		}
		$headers = $owsRequest->getHeaders();
		if($headers) {
			$curlHeaders = array();
			foreach($headers as $header => $value) {
				$curlHeaders[] = "$header: $value";			
			}
			curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders); 
		}
		$xml  = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);
		
		if($info['http_code'] == '404'){
			throw new GeoserverModel_Excpetion("Bad URL? couldn't find GeoServer");
		}
		if(empty($xml)){
			throw new GeoserverModel_Excpetion("Bad request? the response is empty");
		}		
		return $xml;
	}

	/**
	 * This method requests the capability document of a OGC WFS server and creates the FeatureType structure.
	 *
 	 * @return int number of featuretypes, created in this call.
	 */
	public function getWFSCapabilities() {
		$url = $this->storage->URL_WFS;

		$request = $this->getWFSCapabilitiesRequest($url);
		$xml = $this->sendRequest($request);

		$dom = new DOMDocument();
	  	$dom->loadXML($xml);

		$featuretypes = $dom->getElementsByTagName('FeatureType');

		return $this->parseWFSResponseAndUpdateFeaturesTypes($featuretypes);
	}

}


class GeoserverModel_Excpetion extends Exception {
};