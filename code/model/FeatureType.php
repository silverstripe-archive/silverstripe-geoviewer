<?php
/**
 * @package geoviewer
 * @subpackage model
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */

/**
 * @package geoviewer
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
	public function getFeatureTypeName() {
		$result = $this->Name;
		
		if ($this->Namespace) {
			$result = $this->Namespace.":".$result;
		}
		return $result;
	}
	
	/**
	 * Return the datalist of visible and retrievable labels only,
	 * sorted by the 'sort' number.
	 * Used the by FeatureTypeTemplate creation bua the 
	 * FeatureTypeAdmin::doCreateTemplate action.
	 */
	public function getLabelsForTheTemplate() {
		$list = $this->Labels();
		$list->where('Visible = \'1\' AND Retrieve = \'1\'');
		$list->sort('Sort ASC, Label ASC');
		return $list;
	}


	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$controller = Controller::curr();
		
		$ID = $this->ID;
		if ($ID) {
			$fields->addFieldsToTab('Root.Main', array(
				new LiteralField(
					'importLabels',
					sprintf(
						'<a class="ss-ui-button ss-ui-action ui-button-text-icon-primary ss-ui-button-ajax" data-icon="arrow-circle-double" title="%s" href="%s">%s</a>',
						'Refresh the list of available labels for this featuretype ('.$this->getFeatureTypeName().').',
						$controller->Link("FeatureType/doImportLabels?ID=$ID"),
						'Import Labels'
					)
				),
				new LiteralField(
					'deleteLabels',
					sprintf(
						'<a class="ss-ui-button ss-ui-action ui-button-text-icon-primary ss-ui-button-ajax ss-ui-action-destructive" data-icon="arrow-circle-double" title="%s" href="%s">%s</a>',
						'Delete all labels for this featuretype ('.$this->getFeatureTypeName().').',
						$controller->Link("FeatureType/doDeleteLabels?ID=$ID"),
						'Delete Labels'
					)
				))		
			);

			if ($this->Labels()->count() > 0) {

					$createTemplateButton = new LiteralField('TemplateButton',
						sprintf(
							'<div class="field"><div class="middleColumn"><a class="ss-ui-button ss-ui-action ui-button-text-icon-primary ss-ui-button-ajax" data-icon="arrow-circle-double" title="%s" href="%s">%s</a></div></div>',
							sprintf('Create template for %s based on label configuration.',$this->getFeatureTypeName()),
							$controller->Link("FeatureType/doCreateTemplate?ID=$ID"),
							'Create Template'
						));

				$fields->insertBefore($createTemplateButton,'FeatureTypeTemplate');
			}
		}
		return $fields;
	}
	
	public function getLayerLegendURL() {
		$geoserverUrl = $this->Layer()->Storage()->URL;
		$geoserverUrl = str_replace('/gwc/service','',$geoserverUrl);
		
		$name = sprintf("%s:%s",$this->Namespace,$this->Name);
		$url = sprintf("%s?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&WIDTH=50&HEIGHT=20&LAYER=%s&LEGEND_OPTIONS=dx:0.2;dy:0.2;mx:0.2;my:0.2;fontStyle:normal;fontSize:10",$geoserverUrl,$name);
				
		$value = json_encode($url);
		return $value;
	}	
	
	/**
	 * Delete all labels, associated to this feature type as well.
	 */
	protected function onBeforeDelete() { 
		parent::onBeforeDelete();
		
		$labels = $this->Labels();
		foreach($labels as $label) {
			$label->delete();
		}
	}

}