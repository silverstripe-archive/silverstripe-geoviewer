<?php
/**
 * @package geoviewer
 * @subpackage parser
 * @author Rainer Spittel (rainer at silverstripe dot com)
 *
 */

/**
 * 
 *
 * @package mapping
 * @subpackage parser
 * @author Rainer Spittel (rainer at silverstripe dot com)
 */
interface IGetFeatureParser {
	
	/**
	 * @return DataObjectSet
	 */
	public function parse($value);
}