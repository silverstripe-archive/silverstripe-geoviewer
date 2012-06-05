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

this.getOLMap().addLayer(layer);

layer.events.register("loadstart", this.getOLMap(), function(evt) { self.loadStart(evt); } );
layer.events.register("loadend", this.getOLMap(), function(evt) { self.loadEnd(evt); } );

/*

	var minZoom = this.getMinZoomLevel();
	var maxZoom = this.getMaxZoomLevel();

    var matrixIds = new Array(maxZoom);
	var index = 0;
    for (var i=minZoom; i<maxZoom; ++i) {
        matrixIds[index++] = "EPSG:900913:" + i;
    }  

	var layer = new OpenLayers.Layer.WMTS({
	    name: '$ID',
	    url: urlArray,
	    layer: '$LayerName',
	        matrixSet: "EPSG:900913",
	        matrixIds: matrixIds,
	        format: "image/png",
	    style: "simple_roads",
	    opacity: 0.7,
		transparent: $isTransparent,
	    isBaseLayer: false,
		tileFullExtent : new OpenLayers.Bounds(943655.3233232222,6984847.863014574,1000873.541583,7096825.882063465)
	});  

*/