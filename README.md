# GeoMapping Module ##

## Maintainer Contact ##

 * Rainer Spittel (Nickname: fb3rasp) <rainer (at) silverstripe (dot) com>

## Requirements ##

 * SilverStripe V3.0.x
 * CommandPattern module git@github.com:silverstripe-labs/silverstripe-commandpattern.git)

## Documentation ##


## Installation Instructions ##

Add this module (geoviewer)  and the command pattern module into your project folder and 
run a dev/build to generate the required database schema.

After the installation, you have full GeoViewer support for creating web map applications
supporting Google, OpenStreetMap, GeoServer and KML/GML data sources.

To create a demo dataset (map and page configuration), run the MapDemoBuildTask develoment task:

- http://{your_ip_address}/ss3_viewer/dev/tasks/MapDemoBuildTask

You find two new page types (in draft mode) with two maps demonstrating Google Maps and GeoServer
data integration.

The module supports simple theme out of the box but can be customised if required.

## Usage Overview ##

## Known issues ##

- WMS - get feature info handling

## Third Party components ##

* Map Icons by Momenticons under the Creative Commons Attribution (by) license.
  [ http://findicons.com/icon/261402/flag_blue?id=261402# ]

* Third-party libraries: OpenLayers v2.12

## License ##

	Copyright (c) 2010-2011, SilverStripe Limited - www.silverstripe.com
	All rights reserved.

	Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

	    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
	    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the 
	      documentation and/or other materials provided with the distribution.
	    * Neither the name of SilverStripe nor the names of its contributors may be used to endorse or promote products derived from this software 
	      without specific prior written permission.

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE 
	IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE 
	LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE 
	GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, 
	STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY 
	OF SUCH DAMAGE.