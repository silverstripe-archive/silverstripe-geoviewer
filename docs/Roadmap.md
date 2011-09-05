## Roadmap

### Alpha release

#### DONE

* Full GeoServer support (WMS and WFS) for mapping applications. 
* Enable backend users to administer the map layers and create new map instances.
* Ability to add GoogleMaps and OpenStreetMap layers
* Ability to add KML and GML files
* Ability to define map popup template and behavior via SS template engine
* Ability to define vector layer styling
* Ability to create layer-list controls
* Ability to group WMS layers to create basemaps (non-queryable)
* Ability to group WMS layers and to enable query ability to those layers.

#### OUTSTANDING

* Using OGC APIs to connect to backend systems to retrieve data from remote servers.
* Support EPSG:4326 and EPSG:900913 projections out of the box.
* Ability to load GWC tiles, GeoServer WFS and GeoServer WMS services

### Beta release

* WMS and WFS query integration
* KML and GML query integration
* Use jQuery templating to move popup-render into client
* Integrated support of GWC support (incl. getFeatureInfo) 
* Ability to create legend symbols ( available as proof of concept )

### RC 1

* UnitTest coverage

### RC 2

### Release Version 1.0

## Later releases 

### Release Version 1.1

* Enable UMN Mapserver support (WMS and WFS)
* Ability to add CQL WFS queries
* Ability to add SLD support for WMS services