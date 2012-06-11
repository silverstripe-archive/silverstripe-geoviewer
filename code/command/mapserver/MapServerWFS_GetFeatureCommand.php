<?php
/**
 * @package geoviewer
 * @subpackage command
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */

class MapServerWFS_GetFeatureCommand extends ControllerCommand {

	/**
	 *
	 * @param Layer $layer
	 * @param String $featureID WFS feature-id string (featuretype.id)
	 *
	 * @return SS_HTTPRequest
	 *
	 * @throws MapServerWFS_GetFeatureCommand
	 */
	public function getRequest($layer, $featureID) {
		$url = $layer->Storage()->URL;
		$url = str_replace("https://","",$url);
		$url = str_replace("http://","",$url);

		$array = explode(',',$featureID);
		$featureIDSet = new DataObjectSet();
		foreach($array as $id) {
			$featureIDSet->push(new ArrayData (array('FeatureID' => $id)));
		}

		$data = new ArrayData(array(
			'Layer' => $layer,
			'FeatureIDSet' => $featureIDSet
		));

		$body = $data->renderWith('MapServerWFS_GetFeature');
		$request = new SS_HTTPRequest(
			'POST',
			$url,
			null,
			null,
			$body
		);
		$request->addHeader('Content-Type', 'application/xml');		
		return $request;		
	}
	
	/**
	 *
	 * @param SS_HTTPRequest owsRequest
	 * @param StorageGeoserver storage
	 *
	 * @return json-string $json WFS service response
	 *
	 * @throws GeoserverWFS_GetFeatureCommand_Exception
	 */
	protected function sendRequest($owsRequest, $storage) {
		$url = $owsRequest->getURL();
		
		if($owsRequest->getVars()) {
			 $url .= '?' . http_build_query($owsRequest->getVars());
		}
		
		$ch  = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if($owsRequest->isPost()) {
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $owsRequest->getBody());
		}

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
			throw new MapServerWFS_GetFeatureCommand_Exception("Bad URL? couldn't find GeoServer");
		}
		if(empty($xml)){
			throw new MapServerWFS_GetFeatureCommand_Exception("Bad request? the response is empty");
		}		
		return $xml;
	}
	
	public function execute() {
		$parameters = $this->getParameters();

		$layer = $parameters['Layer'];
		$featureID = $parameters['featureID'];

		$request = $this->getRequest($layer, $featureID);

		$storage = $layer->Storage();
		$result = $this->sendRequest($request,$storage);
		$parser = new GetFeatureXMLParser2();
		$parser->setLimit(25);
		
		$item = array();
		$item['Namespace'] = array( 'ms' => 'ms');
		$item['ServerResult'] = $result;
		
		$response_features = $parser->parse($item);
		return $response_features;
	}
		
}

class MapServerWFS_GetFeatureCommand_Exception extends Exception {}
