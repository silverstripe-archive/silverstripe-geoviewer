<?php 
/**
 * @package geoviewer
 * @subpackage code
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */

/**
 * @package geoviewer
 * @subpackage code
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
class MapPageExtension extends DataExtension {

	static $map_presenter_class = 'MapPagePresenter';
	
	static $openlayers_path = '/thirdparty/openlayers_dev_2.12/lib/OpenLayers.js';
	
	static $has_one = array (
		'Map' => 'MapObject'
	);

	static function get_map_presenter_class() {
		return self::$map_presenter_class;
	}
	
	static function set_map_presenter_class($value) {
		self::$map_presenter_class = $value;
	}

	static function get_openlayers_path() {
		return self::$openlayers_path;
	}
	
	static function set_openlayers_path($value) {
		self::$openlayers_path = $value;
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

		$js_files = $presenter->getJavaScriptFiles();
		foreach($js_files as $file) {
			Requirements::javascript($file);
		}

		Requirements::javascript($presenter->getModulePath().$this->get_openlayers_path());

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
			foreach($cssFiles as $file) {
				Requirements::css($file);
			}
			// Requirements::combine_files('mapper.css', $cssFiles );
		}
		
		// we need to add call to js maps somehow, any better way?
		if ($this->owner->MapID) {
			$googleCheck = DataObject::get_one('Layer_GoogleMap',"\"MapID\" = ".$this->owner->MapID." AND \"Enabled\" = 1");
			if($googleCheck){
				$api_key = $presenter->GoogleMapAPIKey();
				Requirements::javascript("http://maps.google.com/maps?file=api&amp;v=2&amp;key={$api_key}&amp;sensor=true");
			}
		}
		Requirements::customScript($presenter->getJavaScript($this->owner->data()));
		
		Requirements::themedCSS('mapstyle');		
	}
	
	
	/**
	 * Overload the map getter from the datamodel
	 * to inject visible states for layers based on GET parameters.
	 */
	function Categories() {
		
		$map = $this->owner->Map();
		$categories = $map->getCategories();
		
		$curr = Controller::curr();
		$request = $curr->getRequest();
	
		// Optionally set layer visible state from GET params
		$selectedLayerIds = explode(',', $request->getVar('layers'));
		if($categories) foreach($categories as $category) {
			$layers = $category->getEnabledLayers($map,'overlay');
			if($layers) foreach($layers as $layer) {
			//	$layer->Visible = true; 
				// (
				// 	in_array($layer->ogc_name, $selectedLayerIds) 
				// 	// Only default to Visible database setting if 'layers' GET param isnt defined.
				// 	// Otherwise we assume the user wants to override these defaults.
				// 	|| ($layer->Visible && !$selectedLayerIds)
				// );
//				echo $layer->Title ." : ". $layer->isVisible();
			}
			// Works by object reference, so is accessible in the template
			$category->OverlayLayersEnabledAndVisible = $layers;
		}

		return $categories;
	}
	
	/**
	 * Partial caching key. This should include any changes that would influence 
	 * the rendering of LayerList.ss
	 * 
	 * @return String
	 */
	function CategoriesCacheKey() {
		$curr = Controller::curr();
		$request = $curr->getRequest();

		return implode('-', array(
			$this->owner->Map()->ID, 
			DataObject::Aggregate("LayerCategory")->Max("LastEdited"), 
			DataObject::Aggregate('Layer')->Max("LastEdited"),
			$request->getVar('layers')
		));
	}
	

}
