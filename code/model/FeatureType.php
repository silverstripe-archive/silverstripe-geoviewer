<?php
/**
 * @package geoviewer
 * @subpackage model
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */


/**
 * 
 *
 * @package mapping
 * @subpackage model
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class FeatureType extends DataObject {
	
	static $db = array(
		"Namespace" => "Varchar(128)",
		"Name" => "Varchar(256)",
		"FeatureTypeTemplate" => "Text"
	);
	
	static $has_one = array(
		"Layer" => "Layer"
	);

	static $has_many = array(
		"Labels" => "FeatureTypeLabel"
	);
	
	static $summary_fields = array(
		'Name',
		'Layer.Title',
		'Layer.Map.Title'		
	);

	static $default_sort = "\"Name\" ASC";
	
	/**
	 * Returns the feature type name, incl the namespace as a prefix if the
	 * feature type has an namespace.
	 *
	 * @return string namespace and featuretype-name
	 */
	function getFeatureTypeName() {
		$result = $this->Name;
		
		if ($this->Namespace) {
			$result = $this->Namespace.":".$result;
		}
		return $result;
	}

	function getLabelTableField() {
		$tableField = new TableField(
		  'Labels', // fieldName
		  'FeatureTypeLabel', // sourceType
		  array(
			'Visible' => 'Visible Property',
			'Label'=>'Label',
		    'RemoteColumnName'=>'Remote Column Name',
			'Sort'       =>  'Front-end sorting'
		  ), // fieldList
		  array(
		    'Visible'=>'CheckboxField',
			'Label'=>'TextField',
		    'RemoteColumnName'=>'TextField',
			'Sort'=>'TextField'
		  ), // fieldTypes
		  "FeatureTypeID",
		  $this->ID,
		  true,
		  "Sort,Label"
		);
		// add some HiddenFields thats saved with each new row
		$tableField->setExtraData(array(
		  'FeatureTypeID' => $this->ID ? $this->ID : '$RecordID'
		));	
		return $tableField;	
	}
	
	function getCMSFields() {
		$fields = parent::getCMSFields();

		$controller = Controller::curr();
		$link = Controller::join_links($controller->Link());

		$presenter = singleton(MapPageDecorator::get_map_presenter_class());		
		Requirements::javascript($presenter->getModulePath().'javascript/FeatureType.js');	

		$tableField = $this->getLabelTableField();
		$fields->addFieldsToTab("Root.Labels", 
			array(
				$tableField, 
				new LiteralField("label",'<div id=\'info\'><i>Please use pseudo template language to create composite or richer styling<br/>I.e. add \'&lt;a href="$LINK"&gt;$INFO&lt;/a&gt;\' to the \'Retrieve Property\' to have a clickable link in the information bubble.</i></br></div>')
				)
			);
		
		return $fields;
	}
	
	function getLayerLegendURL() {
		$geoserverUrl = $this->Layer()->Storage()->URL;
		$geoserverUrl = str_replace('/gwc/service','',$geoserverUrl);
		
		$name = sprintf("%s:%s",$this->Namespace,$this->Name);
		$url = sprintf("%s?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=50&HEIGHT=20&LAYER=%s&LEGEND_OPTIONS=dx:0.2;dy:0.2;mx:0.2;my:0.2;fontStyle:normal;fontSize:10",$geoserverUrl,$name);
				
		$value = json_encode($url);
		return $value;
	}	
	
	/**
	 */
	public function getCMSActions() {
		
		$actions = parent::getCMSActions();
		$actions->push(new FormAction("doImportLabels", "Import Labels"));		
		return $actions;
	}

	/**
	 * todo: check
	 */
	protected function onBeforeDelete() { 
		parent::onBeforeDelete();
		
		$labels = $this->Labels();
		foreach($labels as $label) {
			$label->delete();
		}
	}


}