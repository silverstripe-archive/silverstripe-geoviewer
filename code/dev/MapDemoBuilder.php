<?php
/**
 * Demo builder
 */ 
class MapDemoBuilder {


	public function createMaps() {
		$storage = null;
		$category = null;
		
		// check if demo-map-page has been created
		$page = DataObject::get_one('MapPage',"\"Title\" = 'New York - Map Demo'");
		if ($page == false) {
			$storage = $this->createStorage();
			$category = $this->createLayerCategory();
			
			$this->createNewYorkMapDemo($storage, $category);
			
			echo "Created: 'New York - Map Demo'.<br/>";
		} else {
			echo "Skipped, 'New York - Map Demo' does exist.<br/>";
		}

		$page = DataObject::get_one('MapPage',"\"Title\" = 'Google Maps - Demo'");
		if ($page == false) {
			if ($storage == null) {
				$storage = $this->createStorage($storage);
			}
			if ($category == null) {
				$category = $this->createLayerCategory();
			}

			$this->createGoogleMapDemo($storage, $category);
			
			echo "Created: 'Google Maps - Demo'.<br/>";
		} else {
			echo "Skipped, 'Google Maps - Demo' does exist.<br/>";
		}
	}

	/**
	 * Core methods to create indevidual map examples
	 */
	public function createNewYorkMapDemo($storage, $category) {
		// create first map example with GeoServer data only.
		$map = $this->createMapObject(
			'Map - New York - Demo',
			40.71, 
			-74, 
			13, 
			"0.703125, 0.3515625, 0.17578125, 0.087890625, 0.0439453125, 0.02197265625, 0.010986328125, 0.0054931640625, 0.00274658203125, 0.001373291015625, 6.866455078125E-4, 3.4332275390625E-4, 1.71661376953125E-4, 8.58306884765625E-5, 4.291534423828125E-5, 2.1457672119140625E-5, 1.0728836059570312E-5, 5.364418029785156E-6, 2.682209014892578E-6, 1.341104507446289E-6, 6.705522537231445E-7, 3.3527612686157227E-7, 1.6763806343078613E-7, 8.381903171539307E-8, 4.190951585769653E-8, 2.0954757928848267E-8, 1.0477378964424133E-8, 5.238689482212067E-9, 2.6193447411060333E-9, 1.3096723705530167E-9, 6.548361852765083E-10", 
			"EPSG:4326"
		);
	
		$style = $this->createStyleMap();

		$layer = $this->createLayer_GeoServerWMS($map, $storage);
	
		$layer_wfs = $this->createLayer_GeoServerWFS($map, $storage, $category, 'New York - Point of Interests - Demo');

		$layers = $style->WFSLayers();
		$layers->add($layer_wfs);

		$featureType =  $this->createFeatureType();
		$featureType->LayerID = $layer_wfs->ID;
		$featureType->write();

		$page = new MapPage();
		$page->Title = 'New York - Map Demo';
		$page->MapObjectID = $map->ID;
		$page->write();
		$page->doPublish();
	}

	public function createGoogleMapDemo($storage, $category) {

		// create google maps demo page
		$map = $this->createMapObject(
			'Google-Maps - Demo', 
			4970052.7560407, 
			-8237950.5056889, 
			14, 
			"156543.03390625, 78271.516953125, 39135.7584765625, 19567.87923828125, 9783.939619140625, 4891.9698095703125, 2445.9849047851562, 1222.9924523925781, 611.4962261962891, 305.74811309814453, 152.87405654907226, 76.43702827453613, 38.218514137268066, 19.109257068634033, 9.554628534317017, 4.777314267158508, 2.388657133579254, 1.194328566789627, 0.5971642833948135, 0.29858214169740677, 0.14929107084870338, 0.07464553542435169, 0.037322767712175846, 0.018661383856087923, 0.009330691928043961, 0.004665345964021981", 
			"EPSG:900913"
		); 

		$layer = $this->createLayer_GoogleMaps($map);

		$style = $this->createStyleMap();

		$layer_wfs = $this->createLayer_GeoServerWFS($map, $storage, $category, 'Google Maps - New York - Point of Interests');

		$layers = $style->WFSLayers();
		$layers->add($layer_wfs);

		$featureType =  $this->createFeatureType();
		$featureType->LayerID = $layer_wfs->ID;
		$featureType->write();

		$page = new MapPage();
		$page->Title = 'Google Maps - Demo';
		$page->MapObjectID = $map->ID;
		$page->write();
		$page->doPublish();
	}
	
	/**
	 * Builder methods
	 */		
	public function createMapObject($title, $lat, $long, $zoomlevel, $resolution, $projection) {
		$map = new MapObject();
		$map->Title = $title;
		$map->Enabled = true;
		$map->Lat = $lat;
		$map->Long = $long;
		$map->ZoomLevel = $zoomlevel;
		$map->Resolutions = $resolution;
		$map->DisplayProjection = $map->Projection = $projection;
		$map->write();
		return $map;
	}

	public function createStorage() {
		$storage = new StorageGeoserver();
		$storage->Title = 'GeoServer - New York - Demo'; 
		$storage->URL = 'http://localhost:8080/geoserver/wms'; 
		$storage->URL_WFS = 'http://localhost:8080/geoserver/wfs'; 
		$storage->Enable = true;
		$storage->write();
		return $storage;
	}

	public function createLayerCategory() {
		$category = new LayerCategory();
		$category->Title = 'Points of Interests';
		$category->Sort = 1;
		$category->write();
		return $category;
	}
	
	public function createFeatureType() {
		$featureType = new FeatureType();
		$featureType->Namespace = 'tiger';
		$featureType->Name = 'poi';
		$featureType->FeatureTypeTemplate = <<<HTML
<% if Message %>
<div class='message'>\$Message</div>
<% else %>
<div class='featureInfoContent'>
	<h2>\$Layer.Title</h2>	
	<h4>Features: \$FeatureIDs</h4>
	<ul>
		<li>Name : <strong>\$Feature.NAME</strong></li>
		<li>Thumb : \$Feature.THUMBNAIL</li>
		<li>Website : <a href="\$Feature.MAINPAGE" _target="test">Website</a></li>
	</ul>
</div>
<% end_if %>
HTML;
		$featureType->write();
		return $featureType;
	}
	
	public function createLayer_GeoServerWMS($map, $storage) {
		$layer = new Layer_GeoserverWMS();
		$layer->Title = 'New York - Demo';
		$layer->Enabled = true;
		$layer->Type = 'contextual';
		$layer->Basemap = true;
		$layer->Visible = true;
		$layer->Queryable = false;
		$layer->Sort = 1;
		$layer->LayerName = 'tiger:giant_polygon,tiger:poly_landmarks,tiger:tiger_roads';
		$layer->Format = 'image/png';
		$layer->StorageID = $storage->ID;
		$layer->MapID = $map->ID;
		$layer->write();
		return $layer;
	}

	public function createLayer_GeoServerWFS($map, $storage, $category, $title) {
		$layer = new Layer_GeoserverWFS();
		$layer->Title = $title;
		$layer->Enabled = true;
		$layer->Visible = true;
		$layer->Queryable = true;
		$layer->Sort = 500;
		$layer->Namespace = 'tiger';
		$layer->FeatureType = 'poi';
		$layer->Projection = 'EPSG:4326';
		$layer->Version = '1.1.0';
		$layer->StorageID = $storage->ID;
		$layer->MapID = $map->ID;
		$layer->LayerCategoryID = $category->ID;
		$layer->write();
		return $layer;
	}
	
	public function createLayer_GoogleMaps($map) {
		$layer = new Layer_GoogleMap();
		$layer->Title = 'Google Maps - Street Map';
		$layer->Enabled = true;
		$layer->Type = 'contextual';
		$layer->Basemap = true;
		$layer->Visible = true;
		$layer->Queryable = false;
		$layer->Sort = 1;
		$layer->MapID = $map->ID;
		$layer->GMapTypeName = 'Map';
		$layer->write();
		return $layer;
	}
	
	public function createStyleMap() {
		$style = new StyleMap();	
		$style->Name = 'Point of Interests - Demo';
		$style->default = 'new OpenLayers.Style({ pointRadius: 16, externalGraphic: "geoviewer/images/icons/flag_blue.png" })';
		$style->select = 'new OpenLayers.Style({ pointRadius: 16, externalGraphic: "geoviewer/images/icons/flag_red.png" })';
		$style->temporary = 'new OpenLayers.Style({ pointRadius: 16, externalGraphic: "geoviewer/images/icons/flag_red.png" })';
		$style->write();
		return $style;		
	}
}