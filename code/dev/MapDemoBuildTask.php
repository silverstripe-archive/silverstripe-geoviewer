<?php
/**
 * @package cms
 * @subpackage assets
 */
class MapDemoBuildTask extends BuildTask {
	
	protected $title = "Create Map Demo content";
	
	protected $description = "Execute this task to create a new set of examples of map pages, integrating Geoserver and Google maps into the map pages.";
	
	function run($request) {
	
		$builder = new MapDemoBuilder();
		$builder->createMaps();
		
		echo "Done...";
	}
	
}
