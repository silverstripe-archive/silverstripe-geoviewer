<wfs:GetFeature service="WFS" version="1.1.0"
  xmlns:topp="http://www.openplans.org/topp"
  xmlns:wfs="http://www.opengis.net/wfs"
  xmlns:ogc="http://www.opengis.net/ogc"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.opengis.net/wfs
                      http://schemas.opengis.net/wfs/$Layer.Version/wfs.xsd">
  <wfs:Query typeName="$Layer.FeatureType">
    <ogc:Filter>
	<% control FeatureIDSet %>
       <ogc:FeatureId fid="$FeatureID"/>
	<% end_control %>
    </ogc:Filter>
  </wfs:Query>
</wfs:GetFeature>