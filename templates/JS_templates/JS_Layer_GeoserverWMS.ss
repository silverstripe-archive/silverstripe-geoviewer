var options = {layers: '$LayerName' , format: '$Format', transparent: $isTransparent };

var layer = new OpenLayers.Layer.WMS( '$ID', '$Storage.URL', options,  {alpha: true, tileSize: new OpenLayers.Size(256,256)} );
layer.queryable = $isQueryable;
layer.setVisibility($isVisible);	

<% if transitionEffect %>layer.transitionEffect = 'resize';<% end_if %>

this.getOLMap().addLayer(layer);

layer.events.register("loadstart", this.getOLMap(), function(evt) { self.loadStart(evt); } );
layer.events.register("loadend", this.getOLMap(), function(evt) { self.loadEnd(evt); } );

