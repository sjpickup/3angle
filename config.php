<?php
define( 'DATABASE', 'demo' );
define( 'COLLECTION', 'poiConcat' );

define( 'TYPE', 'ty' );
define( 'LOC', 'l' );
define( 'TAGS', 'ts' );
define( 'META', 'm' );

$layers = [
	'3angle' => [
		'directory' => 'maps-3angle',
		'layerName' => 'threeangle',
	],
	'5 Pubs' => [
		'directory' => 'maps-5pubs',
		'layerName' => 'fivepubs',
	],
	'Events' => [
		'directory' => 'maps-events',
		'layerName' => 'events',
	],
	'Flickr' => [
		'directory' => 'maps-flickr',
		'layerName' => 'flickr',
	],
	'Foursquare' => [
		'directory' => 'maps-4sq',
		'layerName' => 'foursquare',
	],
	'Great Circle' => [
		'directory' => 'maps-great-circle',
		'layerName' => 'gc',
	],
	'Postbox' => [
		'directory' => 'maps-postbox',
		'layerName' => 'postbox',
	],
	'Real Cider/Real Ale' => [
		'directory' => 'maps-pubs-aggregation',
		'layerName' => 'pubsaggr',
	],
	'Timezone' => [
		'directory' => 'maps-timezone',
		'layerName' => 'timezone',
	],
	'Timezone (All)' => [
		'directory' => 'maps-all-zones',
		'layerName' => 'timezones',
	],
];
?>
