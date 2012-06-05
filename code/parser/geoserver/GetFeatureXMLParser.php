<?php
/**
 * @package geoviewer
 * @subpackage parser
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */


/**
 * GetFeatureXMLParser parses a GetFeature WMS response and creates 
 * a dataobjectset which will be used to render to map bubble.
 *
 * @package mapping
 * @subpackage parser
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class GetFeatureXMLParser extends GetFeatureParser implements IGetFeatureParser {

	private $namespace_items = null;

	public function canParse($featureName){
		$retValue = true;
		return $retValue;
	}
	
	/**
	 * Note: Filtering of columns is done later.
	 * 
	 * @param String $itemValue
	 * @return 
	 */
	protected function parseFeature($feature) {
		$result = array('properties' => array());
		$namespace_items = $this->namespace_items;
		$featureTypeName = '';
		
		foreach($namespace_items as $namespace) {
			$featureTypeClass = $feature->children($namespace);		
					
			$name = $featureTypeClass->getName();

			if ($name == '') continue;	
					
			$featureTypeName = $name;
			
			if (!$this->canParse($featureTypeName)) continue;

			foreach($featureTypeClass as $items) {
				foreach($items as $item) {
					$column = $item->getName();
					
					// skip geometry columns
					if ($column == 'the_geom') continue;
							 					
					$value = $item->__toString();

					$resultArray['properties'][$column] = $value;					
				}
			}
					
			$resultArray = $this->applyNiceLabels($featureTypeName, $resultArray);
		}
				
		$properties = new DataObjectSet();
		foreach($resultArray['properties'] as $k => $v)  {
			$properties->push(new ArrayData(array(
				'key' => $k,
				'value' => $v
			)));
		}
										
		// Add arraydata object into the dataobject set for the current
		// feature-type.
				
		$item = new ArrayData( array(
			'FeatureTypeName' => $featureTypeName,
			'Properties' => $properties,
			'scope' => 1
		));
				
		return $item;
	}

	public function addFeatureToList(&$features, $feature) {
		$featureTypeName = $feature->getField('FeatureTypeName');
				
		// add this feature to the list of all features of the current feature type.
		if (!isset($features[$featureTypeName])) {
			// Create new dataobjectset object to store features of the 
			// new feature-types.
			$features[$featureTypeName] = new DataObjectSet();									
		}

		$features[$featureTypeName]->push($feature);
		$this->itemCount++;
	}


	/**
	 * Parses the response text (in text/plain format), which is returned by a WMS.
	 *
	 * @param Array response of the GeoNetwork server (as XML).
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
				$items[] = $namespaces[$namespace];
			}
		}
		$this->namespace_items = $items;
		
		$gml = $xml->children($namespace_gml);

		foreach($gml->featureMember as $xml_feature ) {
			
			$feature = $this->parseFeature($xml_feature);
			$this->addFeatureToList($features, $feature);
		}
		return $features;
	}			
}

