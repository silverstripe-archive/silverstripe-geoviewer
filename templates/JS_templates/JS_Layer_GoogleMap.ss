//
// Initialise Google Maps - $GMapType
//
var layer = null;
var options = [];
layer = new OpenLayers.Layer.Google(
	"$ID",
	$.extend({
		type: $GMapType, 
		numZoomLevels: 22,
     	maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
		<% if isSphericalMercator %>sphericalMercator: true<% end_if %> 
	}, options, {})
);

layer.queryable = false;
layer.ssid = $ID;

this.getOLMap().addLayer(layer);

layer.setVisibility($isVisible);	
<% if Visible %>this.getOLMap().setBaseLayer(layer, false);<% end_if %>

this.getOLMap().setLayerZIndex(layer,1);
