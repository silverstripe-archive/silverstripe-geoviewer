<?php

abstract class GeoServerAPICommand extends ControllerCommand {

	protected $storage = null;


	/**
	 * @param string $featureType Name of the feature-type (incl. namespace, i.e. tiger:poi).
	 *
	 * @return SS_HTTPRequest
	 */ 
	protected function buildRequest($parameter) {
	}

	/**
	 * @param FeatureType $featureType FeatureType Object
	 *
	 * @return string $xml XML-string (WFS service response)
	 *
	 * @throws GeoServerAPICommand_Exception
	 */
	protected function sendRequest($parameter) {
		//
		// get request object
		$owsRequest = $this->buildRequest($parameter);
	
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
			throw new GeoServerAPICommand_Exception("Bad URL? couldn't find GeoServer");
		}
		if(empty($xml)){
			throw new GeoServerAPICommand_Exception("Bad request? the response is empty");
		}		
		return $xml;
	}

}

class GeoServerAPICommand_Exception extends Exception {}