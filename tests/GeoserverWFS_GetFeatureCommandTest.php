<?php

class GeoserverWFS_GetFeatureCommandTest extends SapphireTest {
	
	private function getSimpleStub() {
		$stub = $this->getMock('GeoserverWFS_GetFeatureCommand',array('getServiceURL'));
		$stub->expects($this->once())
		             ->method('getServiceURL')
		             ->will($this->returnValue("http://localhost:8080/geoserver/wfs"));
		return $stub;
	}
	
	/**
	 *
	 */
	public function test_GetRequestCreatesHTTPPostRequestObject() {
		$stub = $this->getSimpleStub();
		
		$layer = new Layer_GeoserverWFS();

		$result = $stub->getRequest($layer, "");

		$this->assertInstanceOf('SS_HTTPRequest', $result);
		$this->assertTrue($result->isPOST());
	}	
	
	/**
	 *
	 */	
	public function test_GetRequestParsesTheWFSURLCorrectly() {
		$stub = $this->getSimpleStub();
		
		$layer = new Layer_GeoserverWFS();

		$result = $stub->getRequest($layer, "");

		$this->assertEquals("localhost:8080/geoserver/wfs", $result->getURL());
	}	
	
	/**
	 *
	 */
	public function test_GetSimpleRequest() {
		$stub = $this->getSimpleStub();

		$layer = new Layer_GeoserverWFS();
		$layer->Namespace = "MyNamespace";
		$layer->FeatureType = "MyFeatureType";
		$layer->OutputFormat = "json";

		$result = $stub->getRequest($layer, "Feature.1");

		$xml = <<<XML
<wfs:GetFeature service="WFS" version="1.1.0"
  xmlns:topp="http://www.openplans.org/topp"
  xmlns:wfs="http://www.opengis.net/wfs"
  xmlns:ogc="http://www.opengis.net/ogc"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.opengis.net/wfs
                      http://schemas.opengis.net/wfs//wfs.xsd">
  <wfs:Query typeName="MyNamespace:MyFeatureType">
    <ogc:Filter>
       <ogc:FeatureId fid="Feature.1"/>
    </ogc:Filter>
  </wfs:Query>
</wfs:GetFeature>
XML;
		$xml_expected = new DOMDocument;
		$xml_expected->loadXML($xml);

		$xml_actual = new DOMDocument;
		$xml_actual->loadXML($result->getBody());

		$this->assertXmlStringEqualsXmlString($xml, $result->getBody());
	}

	/**
	 *
	 */
	public function test_GetRequestWithMultipleFeatures() {
		$stub = $this->getSimpleStub();
		
		$layer = new Layer_GeoserverWFS();
		$layer->Namespace = "MyNamespace";
		$layer->FeatureType = "MyFeatureType";
		$layer->OutputFormat = "json";

		$result = $stub->getRequest($layer, "Feature.11,Feature.13");

		$xml = <<<XML
<wfs:GetFeature service="WFS" version="1.1.0"
  xmlns:topp="http://www.openplans.org/topp"
  xmlns:wfs="http://www.opengis.net/wfs"
  xmlns:ogc="http://www.opengis.net/ogc"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.opengis.net/wfs
                      http://schemas.opengis.net/wfs//wfs.xsd">
  <wfs:Query typeName="MyNamespace:MyFeatureType">
    <ogc:Filter>
       <ogc:FeatureId fid="Feature.11,Feature.13"/>
    </ogc:Filter>
  </wfs:Query>
</wfs:GetFeature>
XML;

		$xml_expected = new DOMDocument;
		$xml_expected->loadXML($xml);

		$xml_actual = new DOMDocument;
		$xml_actual->loadXML($result->getBody());

		$this->assertXmlStringEqualsXmlString($xml, $result->getBody());
	}
	
	/**
	 *
	 */
	public function test_ExecuteCommandWithSimpleValidResponse() {
		// create layer stub
		$stubLayer = $this->getMock('Layer_GeoserverWFS',array('Storage'));
		$stubLayer->expects($this->once())
		             ->method('Storage')
		             ->will($this->returnValue(new StorageGeoserver()));

		// create command stub
		$stub = $this->getMock('GeoserverWFS_GetFeatureCommand', array('getRequest','sendRequest'));
		$stub->setParameters(array(
			"Layer" => $stubLayer,
			"featureID" => "Any featureID"
		));

		// took example request from GeoServer test client.
		$json =<<<JSON
{"type":"FeatureCollection","features":[{"type":"Feature","id":"states.3","geometry":{"type":"MultiPolygon","coordinates":[[[[38.55747600000001,-75.70742],[38.649551,-75.71106],[38.83017000000001,-75.724937],[39.141548,-75.752922],[39.24775299999999,-75.761658],[39.295849000000004,-75.764664],[39.38300699999999,-75.772697],[39.72375500000001,-75.791435],[39.72444200000001,-75.775269],[39.77481800000001,-75.745934],[39.820347,-75.695114],[39.83819600000001,-75.644341],[39.84000800000001,-75.583794],[39.826435000000004,-75.470345],[39.798869999999994,-75.42083],[39.789658,-75.412117],[39.778130000000004,-75.428009],[39.763248000000004,-75.460754],[39.74171799999999,-75.475128],[39.71997099999999,-75.476334],[39.71474499999999,-75.489639],[39.61279300000001,-75.610725],[39.566722999999996,-75.562996],[39.46376799999999,-75.590187],[39.36694,-75.515572],[39.25763699999999,-75.402481],[39.073036,-75.397728],[39.01238599999999,-75.324852],[38.945910999999995,-75.307899],[38.808670000000006,-75.190941],[38.799812,-75.083138],[38.44949,-75.045998],[38.449963,-75.068298],[38.45045099999999,-75.093094],[38.455208,-75.350204],[38.463066,-75.69915],[38.55747600000001,-75.70742]]]]},"geometry_name":"the_geom","properties":{"STATE_NAME":"Delaware","STATE_FIPS":"10","SUB_REGION":"S Atl","STATE_ABBR":"DE","LAND_KM":5062.456,"WATER_KM":1385.022,"PERSONS":666168,"FAMILIES":175867,"HOUSHOLD":247497,"MALE":322968,"FEMALE":343200,"WORKERS":247566,"DRVALONE":258087,"CARPOOL":42968,"PUBTRANS":8069,"EMPLOYED":335147,"UNEMPLOY":13945,"SERVICE":87973,"MANUAL":44140,"P_MALE":0.485,"P_FEMALE":0.515,"SAMP_POP":102776}}],"crs":{"type":"EPSG","properties":{"code":"4326"}},"bbox":[38.44949,-75.791435,39.84000800000001,-75.045998]}
JSON;

		$stub->expects($this->once())
		             ->method('sendRequest')
		             ->will($this->returnValue($json));

		$result = $stub->execute();

		$features = $result['features'];
		$this->assertEquals(1, count($features));
		
		$feature = $features[0];
		$this->assertEquals("states.3", $feature["id"]);

		$properties = $feature["properties"];
		$this->assertEquals(22, count($properties));

		// test first two properties
		$this->assertEquals("Delaware", $properties["STATE_NAME"]);
		$this->assertEquals("10", $properties["STATE_FIPS"]);
		
		// test last two properties
		$this->assertEquals(0.515, $properties["P_FEMALE"]);
		$this->assertEquals(102776, $properties["SAMP_POP"]);
	}
	
}