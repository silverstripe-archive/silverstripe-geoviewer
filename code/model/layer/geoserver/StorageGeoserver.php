<?php
/**
 * @package geoviewer
 * @subpackage geoserver
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */

/**
 *
 * @package geoviewer
 * @subpackage geoserver
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class StorageGeoserver extends Storage {
	
	static $db = array (
		'URL_WFS' => "Varchar(255)",
		'Username' => "Varchar(255)",
		'Password' => "Varchar(255)",
	);
	
	static $has_many = array (
		"Layers" => "Layer_GeoserverWMS"
	);
	
	function getCMSFields() {
		$fields = parent::getCMSFields();
		
	 	$fields->addFieldToTab('Root.Main',
			new LiteralField('Desc1',"<h3>Cache settings</h3><p>Use CACHE if you provide more than one WMS-T server from the same source.</p>"), 'UseMultiCache'
		);
	 	$fields->addFieldToTab('Root.Main',
			new LiteralField('Desc2',"<h3>OGC Web Feature Service (WFS)</h3><p>Use OGC WFS, if vector data has been made available.</p>"), 'URL_WFS'
		);
	 	$fields->addFieldToTab('Root.Main',
			new LiteralField('Desc3',"<h3>Authentication</h3><p>Use Authentication if data is protected (this feature is in beta).</p>"), 'Username'
		);

		$controller = Controller::curr();

		$fields->addFieldsToTab('Root.Main', array(
			new LiteralField(
				'importFeatureTypes',
				sprintf(
					'<a class="ss-ui-button ss-ui-action ui-button-text-icon-primary ss-ui-button-ajax" data-icon="arrow-circle-double" title="%s" href="%s">%s</a>',
					'Refresh the list of available featuretypes for this WFS Service.',
					$controller->Link("StorageGeoserver/doImportFeatureTypes?ID=".$this->ID),
					'Import Feature Types'
				)
			)
		));


		return $fields;
	}

	// http://www.geoviewer.umwelt.bremen.de:8080/geoserver/wms?request=getCapabilities

	// http://www.geoviewer.umwelt.bremen.de:8080/geoserver/wfs?service=wfs&version=1.1.0&request=getcapabilities
}
?>