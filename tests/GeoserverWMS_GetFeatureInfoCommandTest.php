<?php

class GeoserverWMS_GetFeatureInfoCommandTest extends SapphireTest {
	
	public function testgetWmsGetFeatureInfoRequest() {
		$stub = $this->getMock('GeoserverWMS_GetFeatureInfoCommand', array('executeOwsRequest','getWmsGetFeatureInfoRequest'));
		$stub->setParameters(array(
			'HTTP_parameters' => array(),
			'URL' => '',
			'Namespace' => array('topp')
		));

		$stub->expects($this->once())
		             ->method('getWmsGetFeatureInfoRequest')
		             ->will($this->returnValue('http://[url]:[port]/[service]'));
		// took example request from GeoServer test client.
		$xml =<<<XML
<?xml version="1.0" encoding="UTF-8"?><wfs:FeatureCollection xmlns="http://www.opengis.net/wfs" xmlns:wfs="http://www.opengis.net/wfs" xmlns:topp="http://www.openplans.org/topp" xmlns:gml="http://www.opengis.net/gml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.openplans.org/topp http://localhost:8080/geoserver/wfs?service=WFS&amp;version=1.0.0&amp;request=DescribeFeatureType&amp;typeName=topp%3Astates http://www.opengis.net/wfs http://localhost:8080/geoserver/schemas/wfs/1.0.0/WFS-basic.xsd"><gml:boundedBy><gml:null>unknown</gml:null></gml:boundedBy><gml:featureMember><topp:states fid="states.11"><topp:the_geom><gml:MultiPolygon srsName="http://www.opengis.net/gml/srs/epsg.xml#4326"><gml:polygonMember><gml:Polygon><gml:outerBoundaryIs><gml:LinearRing><gml:coordinates xmlns:gml="http://www.opengis.net/gml" decimal="." cs="," ts=" ">-114.519844,33.027668 -114.519844,33.027668</gml:coordinates></gml:LinearRing></gml:outerBoundaryIs></gml:Polygon></gml:polygonMember></gml:MultiPolygon></topp:the_geom><topp:STATE_NAME>Arizona</topp:STATE_NAME><topp:STATE_FIPS>04</topp:STATE_FIPS><topp:SUB_REGION>Mtn</topp:SUB_REGION><topp:STATE_ABBR>AZ</topp:STATE_ABBR><topp:LAND_KM>294333.462</topp:LAND_KM><topp:WATER_KM>942.772</topp:WATER_KM><topp:PERSONS>3665228.0</topp:PERSONS><topp:FAMILIES>940106.0</topp:FAMILIES><topp:HOUSHOLD>1368843.0</topp:HOUSHOLD><topp:MALE>1810691.0</topp:MALE><topp:FEMALE>1854537.0</topp:FEMALE><topp:WORKERS>1358263.0</topp:WORKERS><topp:DRVALONE>1178320.0</topp:DRVALONE><topp:CARPOOL>239083.0</topp:CARPOOL><topp:PUBTRANS>32856.0</topp:PUBTRANS><topp:EMPLOYED>1603896.0</topp:EMPLOYED><topp:UNEMPLOY>123902.0</topp:UNEMPLOY><topp:SERVICE>455896.0</topp:SERVICE><topp:MANUAL>185109.0</topp:MANUAL><topp:P_MALE>0.494</topp:P_MALE><topp:P_FEMALE>0.506</topp:P_FEMALE><topp:SAMP_POP>468178.0</topp:SAMP_POP></topp:states></gml:featureMember></wfs:FeatureCollection>				
XML;

		$stub->expects($this->once())
		             ->method('executeOwsRequest')
		             ->will($this->returnValue($xml));

		$result = $stub->execute();
		
		$features = $result['features'];
		
		$this->assertEquals(1, count($features));
		$feature = $features[0];

		$this->assertEquals("topp", $feature["Namespace"]);
		$this->assertEquals("states", $feature["FeatureType"]);
		
		$properties = $feature["properties"];
		$this->assertEquals(22, count($properties));

		// test first two properties
		$this->assertEquals("Arizona", $properties["STATE_NAME"]);
		$this->assertEquals("04", $properties["STATE_FIPS"]);

		// test last two properties
		$this->assertEquals("0.506", $properties["P_FEMALE"]);
		$this->assertEquals("468178.0", $properties["SAMP_POP"]);
		
		$featureTypesNames = $result['featureTypesNames'];
		$this->assertEquals(1, count($featureTypesNames));
		$this->assertEquals("topp:states", $featureTypesNames[0]);
	}
	
}