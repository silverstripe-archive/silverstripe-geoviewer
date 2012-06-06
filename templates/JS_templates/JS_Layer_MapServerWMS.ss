
var options = {layers: '$LayerName' , format: '$Format', transparent: $isTransparent };
<% if Storage.UseMultiCache %>
var urlArray = ["$Storage.Cache_URL_01",
                "$Storage.Cache_URL_02",
                "$Storage.Cache_URL_03",
                "$Storage.Cache_URL_04"];
<% else %>
var urlArray = ["$Storage.URL"];
<% end_if %>

var layer = new OpenLayers.Layer.WMS( '$ID', urlArray, options,  {alpha: true, tileSize: new OpenLayers.Size(256,256)} );
layer.queryable = $isQueryable;
layer.setVisibility($isVisible);	

<% if transitionEffect %>layer.transitionEffect = 'resize';<% end_if %>
<% if Basemap %>layer.isBaseLayer = $Basemap;<% end_if %>

this.getOLMap().addLayer(layer);

layer.events.register("loadstart", this.getOLMap(), function(evt) { self.loadStart(evt); } );
layer.events.register("loadend", this.getOLMap(), function(evt) { self.loadEnd(evt); } );
