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
	
	static $has_one = array (
		'MapObject' => 'MapObject'
	);

	static function get_map_presenter_class() {
		return self::$map_presenter_class;
	}
	
	static function set_map_presenter_class($value) {
		self::$map_presenter_class = $value;
	}

	/**
	 * Update the CMS fields, adding some descriptions and text fields to 
	 * the Browse Page catalogue page.
	 */
	function updateCMSFields(FieldList $fields) {
		$items = array();
		$maps  = DataObject::get("MapObject");
		if ($maps) $items = $maps->map('ID','Title');

		$dropdown = new DropdownField("MapObjectID", "Map Object", $items, $this->owner->MapObjectID);
		$dropdown->setHasEmptyDefault(true);
		$fields->addFieldsToTab("Root.Main",
			array(
				new LiteralField("MapLabel","<h2>Map Selection</h2>"),
				// Display parameters
				new CompositeField(
					new CompositeField(
						new LiteralField("DefLabel","<h3>Default OpenLayers Map</h3>"),
						$dropdown

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

		Requirements::javascript($presenter->getModulePath().$presenter->get_openlayers_path());

		$js_files = $presenter->getJavaScriptMapExtensionsFiles();
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
		if ($this->owner->MapObjectID) {
			$googleCheck = DataObject::get_one('Layer_GoogleMap','"MapID" = '.$this->owner->MapObjectID.' AND "Enabled" = 1');
			if($googleCheck){
				Requirements::javascript("https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false");
//				Requirements::javascript("http://maps.google.com/maps/api/js?v=3&amp;sensor=false");
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
		$map = $this->owner->MapObject();
		$categories = $map->getCategories();

		$result = new ArrayList();
		$curr = Controller::curr();
		$request = $curr->getRequest();
	
		// Optionally set layer visible state from GET params
		// $selectedLayerIds = explode(',', $request->getVar('layers'));
		if($categories) foreach($categories as $category) {
			$layers = $category->getEnabledLayers($map,'overlay');
// 			if($layers) foreach($layers as $layer) {
// 			//	$layer->Visible = true; 
// 				// (
// 				// 	in_array($layer->ogc_name, $selectedLayerIds) 
// 				// 	// Only default to Visible database setting if 'layers' GET param isnt defined.
// 				// 	// Otherwise we assume the user wants to override these defaults.
// 				// 	|| ($layer->Visible && !$selectedLayerIds)
// 				// );
// //				echo $layer->Title ." : ". $layer->isVisible();
// 			}
			// Works by object reference, so is accessible in the template

			$category->OverlayLayersEnabledAndVisible = $layers;
			$result->add($category);
		}
		return $result;
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

		// change orm behaviour which breaks when using PostGreSQL
		// using MAX on a datalist which has a default sort defined in the dataobject.
		$datalist = new DataList("LayerCategory");
		$query = $datalist->dataQuery();
		$query = $query->sort();

		$layer = new DataList("Layer");
		return implode('-', array(
			$this->owner->MapObject()->ID,
			$query->Max("LastEdited"),
			$layer->Max("LastEdited"),
			$request->getVar('layers')
		));
	}
}
