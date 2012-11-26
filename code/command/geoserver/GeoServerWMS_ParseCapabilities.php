<?php

class GeoServerWMS_ParseCapabilitiesCommand extends ControllerCommand {

	public function execute() {
		$response = array();

		$parameters = $this->getParameters();
		$xml = $parameters['Capabilities'];		
		
		$doc = new DOMDocument();
	  	$doc->loadXML($xml);

		$server = $doc->getElementsByTagName('Layer');
		$layers = $server->item(0)->getElementsByTagName('Layer');

		foreach($layers as $layer) {


			$cascaded = $layer->getAttribute('cascaded');

			$queryable = $layer->getAttribute('queryable');

			// name of the layer
			$items = $layer->getElementsByTagName( "Name" );
		  	$Name = $items->item(0)->nodeValue;

			// title of the layer
			$items = $layer->getElementsByTagName( "Title" );
		  	$Title = $items->item(0)->nodeValue;

			$items = $layer->getElementsByTagName( "Abstract" );
		  	$Abstract = $items->item(0)->nodeValue;

			$items = $layer->getElementsByTagName( "westBoundLongitude" );
		  	$west = $items->item(0)->nodeValue;

			$items = $layer->getElementsByTagName( "eastBoundLongitude" );
		  	$east = $items->item(0)->nodeValue;

			$items = $layer->getElementsByTagName( "southBoundLatitude" );
		  	$south = $items->item(0)->nodeValue;

			$items = $layer->getElementsByTagName( "northBoundLatitude" );
		  	$north = $items->item(0)->nodeValue;

		  	$bbox = array(
		  		"west" => $west,
		  		"east" => $east,
		  		"south" => $south,
		  		"north" => $north
		  	);

		  	$layerObject = array(
		  		"Name" => $Name,
		  		"Title" => $Title,
		  		"Abstract" => $Abstract,
		  		"Queryable" => $queryable,
		  		"Cascaded" => $cascaded,
		  		"bbox" => $bbox
		  	);
		  	$response[] = $layerObject;
		}

		return $response;
	}
}