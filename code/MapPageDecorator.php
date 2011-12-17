<?php 

class MapPageDecorator extends DataObjectDecorator {

	static $map_presenter_class = 'MapPagePresenter';
	
	static function get_map_presenter_class() {
		return self::$map_presenter_class;
	}
	
	static function set_map_presenter_class($value) {
		self::$map_presenter_class = $value;
	}
		
	
	function extraStatics() {
		return array(
			'has_one' => array (
				'Map' => 'MapObject',
			)
		);
	}

	/**
	 * Update the CMS fields, adding some descriptions and text fields to 
	 * the Browse Page catalogue page.
	 */
	function updateCMSFields(FieldSet &$fields) {

		$items = array();
		$maps  = DataObject::get("MapObject");
		if ($maps) $items = $maps->map('ID','Title');

		$fields->addFieldsToTab("Root.Content.OpenLayers", 
			array(
				new LiteralField("MapLabel","<h2>Map Selection</h2>"),
				// Display parameters
				new CompositeField( 
					new CompositeField( 
						new LiteralField("DefLabel","<h3>Default OpenLayers Map</h3>"),
						new DropdownField("MapID", "Map", $items, $this->owner->MapID, null, true)
					)
				)
			)
		);
	}

	/**
	 *
	 */
 	function contentcontrollerInit($controller) {

		$presenter = singleton(self::get_map_presenter_class());		

		// Check that the class exists before trying to use it
		if (!class_exists('CommandFactory')) {
		    user_error('MapPage_Controller::init() - Please install the command-pattern module from github: git@github.com:silverstripe-labs/silverstripe-commandpattern.git.');
			die();
		}

		$js_files = array(
			'http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.js',
			$presenter->getModulePath().'/thirdparty/jquery-ui-1.7.2.custom.min.js',
			$presenter->getModulePath().'/thirdparty/jquery.entwine/dist/jquery.entwine-dist.js',
			$presenter->getModulePath().'/thirdparty/jquery.metadata/jquery.metadata.js',
		);

		foreach($js_files as $file) {
			Requirements::javascript($file);
		}

		Requirements::javascript($presenter->getModulePath()."/thirdparty/openlayers_dev/OpenLayers.js");

		$js_files = array(
			$presenter->getModulePath()."/javascript/MapWrapper.js",
			$presenter->getModulePath().'/javascript/LayerList.js',
			$presenter->getModulePath()."/javascript/WMSFeatureInfo.js",
			$presenter->getModulePath()."/javascript/WFSFeatureInfo.js",
			$presenter->getModulePath()."/javascript/MapPopup.js",
			$presenter->getModulePath()."/javascript/control/GeoserverGetFeatureInfo.js"
		);
		foreach($js_files as $file) {
			Requirements::javascript($file);
		}
		// Requirements::combine_files('mapper.js', $js_files);

		$cssFiles = $presenter->getCSSFiles();
		if (!empty($cssFiles)) {
			Requirements::combine_files('mapper.css', $cssFiles );
		}
		
		// we need to add call to js maps somehow, any better way?
		if ($this->owner->MapID) {
			$googleCheck = DataObject::get_one('Layer_GoogleMap',"MapID = ".$this->owner->MapID." AND \"Enabled\" = 1");
			if($googleCheck){
				$api_key = $presenter->GoogleMapAPIKey();
				Requirements::javascript("http://maps.google.com/maps?file=api&amp;v=2&amp;key={$api_key}&amp;sensor=true");
			}
		}
		Requirements::customScript($presenter->getJavaScript($this->owner->data()));
		
		Requirements::themedCSS('mapstyle');		
	}

}
