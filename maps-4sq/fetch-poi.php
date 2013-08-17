<?php
include '../config.php';
include '../classes.php';
include '../display.php';

ob_start("ob_gzhandler");

ini_set('display_errors', 1);
ini_set('error_reporting', -1);

header('Content-type: text/plain');
$m = new MongoClient( 'mongodb://localhost' );
$d = $m->selectDb( DATABASE );
$segments = array_key_exists('segments', $_GET) ? (int) $_GET['segments'] : 1;

$polygon = GeoJSONPolygon::createFromBounds(
	min( 90, (float) $_GET['n'] ),
	min( 180, (float) $_GET['e']) ,
	max( -90, (float) $_GET['s'] ),
	max( -180, (float) $_GET['w'] ),
	$segments
);

$c = $d->selectCollection( 'foursquare' );
$query = array(
	LOC => array(
		'$geoWithin' => array(
			'$geometry' => $polygon->getGeoJSON(),
		),
	),
);
$s = $c->find( $query )->limit( 8000 );

$rets = format_response( $s, false );

echo json_encode( $rets, JSON_PRETTY_PRINT );
?>
