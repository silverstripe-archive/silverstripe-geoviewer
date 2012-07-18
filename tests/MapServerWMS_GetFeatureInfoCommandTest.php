<?php

class MapServerWFS_GetFeatureCommand.phpTest extends SapphireTest {
	
	  public function test() {
		$stub = $this->getMock('MapServerWMS_GetFeatureInfoCommandTest', array('executeOwsRequest','getWmsGetFeatureInfoRequest'));
		$stub->setParameters(array(
			'HTTP_parameters' => array(),
			'URL' => '',
			'Namespace' => array('topp')
		));

		$stub->expects($this->once())
		             ->method('getWmsGetFeatureInfoRequest')
		             ->will($this->returnValue('http://[url]:[port]/[service]'));
	}
}