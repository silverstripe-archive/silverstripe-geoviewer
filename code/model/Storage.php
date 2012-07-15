<?php

class Storage extends DataObject {

	static $db = array(
		'Enable' => 'Boolean',
		'Title' => "Varchar(255)",
		'URL' => "Varchar(255)",
		'UseMultiCache' => "Boolean",
		'Cache_URL_01' => "Varchar(255)",
		'Cache_URL_02' => "Varchar(255)",
		'Cache_URL_03' => "Varchar(255)",
		'Cache_URL_04' => "Varchar(255)"
	);	

	static $has_many = array(
		"FeatureTypes" => "FeatureType"
	);

}