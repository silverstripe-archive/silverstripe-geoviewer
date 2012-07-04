<?php
/**
 * @package geoviewer
 * @subpackage parser
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */

/**
 * GetFeatureXMLParser parses a GetFeature WMS response and creates 
 * a dataobjectset which will be used to render to map bubble.
 *
 * @package geoviewer
 * @subpackage parser
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class GetFeatureXMLParser2 extends GetFeatureParser implements IGetFeatureParser {

	private $namespace_items = null;

	public function canParse($featureName){
		$retValue = true;
		return $retValue;
	}
	
	/**
	 * Note: Filtering of columns is done later.
	 * 
	 * @param SimpleXMLElement $itemValue
	 * @return 
	 */
	protected function parseFeature($feature) {
		
		$featureResult = array();
		
		$namespace_items = $this->namespace_items;
		$featureTypeName = '';
		
		foreach($namespace_items as $namespace) {
			$featureTypeClass = $feature->children($namespace['Reference']);							
			$name = $featureTypeClass->getName();

			if ($name == '') continue;	
					
			$featureTypeName = $name;
			$featureResult['Namespace'] = $namespace['Namespace'];
			$featureResult['FeatureType'] = $featureTypeName;
			
			if (!$this->canParse($featureTypeName)) continue;

			foreach($featureTypeClass as $items) {
				foreach($items as $item) {
					$column = $item->getName();
					
					// skip geometry columns
					if ($column == 'the_geom') continue;
					if ($column == 'msGeometry') continue;
					
					$value = $item->__toString();

					$featureResult['properties'][$column] = $value;					
				}
			}
		}
		return $featureResult;
	}

	/**
	 * Parses the response text (in text/plain format), which is returned by a WMS.
	 *
	 * @param Array response of the GeoNetwork server (as XML).
	 *				$item['Namespace'] : String Name of Feature Namespace 
	 *				$item['ServerResult'] : String of the XML response 
	 *
	 * @return Array
	 */
	public function parse($item) {
		$features = array();
		
		$used_namespace = array_values($item['Namespace']);
		$result = $item['ServerResult'];
		$xml = new SimpleXmlElement($result);
		
		$namespaces = $xml->getNameSpaces(true);		
		$namespace_gml = $namespaces['gml'];

		$items = array();
		foreach($used_namespace as $namespace) {
			if (isset($namespaces[$namespace])) {
				$items[] = array(
					"Namespace" => $namespace,
					"Reference" => $namespaces[$namespace]
				);
			}
		}
		$this->namespace_items = $items;
		
		$gml = $xml->children($namespace_gml);

		foreach($gml->featureMember as $xml_feature ) {
			$feature = $this->parseFeature($xml_feature);
			$features[] = $feature;
		}
		
		$func = function($value) {
		    return $value['Namespace'].":".$value['FeatureType'];
		};
		
		$featureTypesNames = array_unique(array_map($func, $features));
		return array('features' => $features,
		             'featureTypesNames' => $featureTypesNames);
	}			
}

