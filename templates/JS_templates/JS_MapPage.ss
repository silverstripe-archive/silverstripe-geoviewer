(function($) { 
	$.entwine('ol', function($) {	
		$('.olMap').entwine({ 

			ControllerName: 'Feature', 

			<% if MapObjectID %>
			<% with MapObject %>
			MapID: '$ID', 

			DisplayProjection : new OpenLayers.Projection('$DisplayProjection'),
			
			Projection : new OpenLayers.Projection('$Projection'),
			
			initLayers: function() {
				var self = this;
				$JavaScript
			},

			loadConfiguration: function() {

				var map = this.getOLMap();

				var center = new OpenLayers.LonLat($Long, $Lat);
				var center = center.transform(map.displayProjection, map.projection);
		 		map.setCenter(center, $ZoomLevel );

				OpenLayers.ProxyHost="Proxy/dorequest?u=";
			},

			getResolutions: function() {
				return [ $Resolutions ];
			},

			getMinZoomLevel: function() {
				return $MinZoomLevel;
			},

			getMaxZoomLevel: function() {
				return $MaxZoomLevel;
			}
			<% end_with %>
			<% else %>
			MapID: '', 

			DisplayProjection : new OpenLayers.Projection(''),
		
			Projection : new OpenLayers.Projection(''),

			initLayers: function() {
				$('.initmap')[0].innerHTML = "<div class='initmap_error'><h2>Map is not initialised.</h2><p>Unfortunately this page has not been setup completely.</p><p>If you are a website editor, assign a map to this page via the SilverStripe CMS.<br/>Otherwise contact the website editor please.</p></div>";
				$('.initmap').removeClass('initmap');
			},

			loadConfiguration: function() {
			},

			getResolutions: function() {
				return null;
			},

			getMinZoomLevel: function() {
				return null;
			},

			getMaxZoomLevel: function() {
				return null;
			}
				
			<% end_if%>
		});
	});

}(jQuery));



