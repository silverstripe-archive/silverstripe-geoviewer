var layer = null;
var styles = null;

<% if StyleMap %>
<% control StyleMap %>
$JavaScript
<% end_control %>
<% else %>
// Use default stylemap for this WFS layer
<% end_if %>

var p = new OpenLayers.Protocol.WFS({
	url: '$Storage.URL',
	featurePrefix: null,
	featureType: '$FeatureType',
	<% if Version %>version: "$Version"<% end_if %>
});			

p.format.setNamespace("feature", "http://mapserver.gis.umn.edu/mapserver");

<% if ClusterFeatures %>
var strategyCluster = new OpenLayers.Strategy.Cluster();
strategyCluster.distance = $ClusterDistance;

strategies =  [
	new OpenLayers.Strategy.Fixed(),
	strategyCluster
];
<% else %>
strategies =  [
	new OpenLayers.Strategy.Fixed()
];
<% end_if %>

layer = new OpenLayers.Layer.Vector("$ID", {
	styleMap: styles,
	strategies: strategies,
	protocol: p,
	<% if Projection %>projection: new OpenLayers.Projection("$Projection"),<% end_if %>
	queryable: $isQueryable 
});

this.getOLMap().addLayer(layer);

layer.setVisibility($isVisible);

// add custom flag for vector layers. Used to determine the WFS getFeature handler.
layer.isVector = true;

layer.events.register("loadstart", this.getOLMap(), function(evt) { self.loadStart(evt); } );
layer.events.register("loadend", this.getOLMap(), function(evt) { self.loadEnd(evt); } );
