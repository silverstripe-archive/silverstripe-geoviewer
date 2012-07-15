<?php

class GeoserverModel {

	protected $storage = null;

	public function setStorage($storage) {
		$this->storage = $storage;
	}

	public function getWFSCapabilities() {
		// http://www.geoviewer.umwelt.bremen.de:8080/geoserver/wfs?service=wfs&version=1.1.0&request=getcapabilities

		$url = $this->storage->URL_WFS;

		$request = new SS_HTTPRequest(
			'GET',
			$url,
			array(
				"service" => "wfs",
				"version" => "1.1.0",
				"request" => "getcapabilities"
			)
		);
		$count = 0;
		$xml = $this->sendRequest($request);

		$dom = new DOMDocument();
	  	$dom->loadXML($xml);

		$featuretypes = $dom->getElementsByTagName('FeatureType');

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
				$FeatureType = new FeatureType();
				$FeatureType->Name = $name;
				$FeatureType->Namespace = $namespace;
				$FeatureType->Title = $title;
				$FeatureType->StorageID = $this->storage->ID;
				$FeatureType->write();
				$count++;
			}
  		}
  		return $count;
	}

	protected function sendRequest($owsRequest) {
	
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
}


class GeoserverModel_Excpetion extends Exception {

};