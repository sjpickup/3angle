<?php
function createDescription( MongoCollection $c, &$r )
{
	$tags = Functions::split_tags( $r[TAGS] );

	/* Find closest street */
	$query = [ LOC => [ '$near' => $r[LOC] ], TAGS => new MongoRegex('/^highway=(trunk|pedestrian|service|primary|secondary|tertiary|residential|unclassified)/' ) ];
	$road = $c->findOne( $query );
	$roadTags = Functions::split_tags( $road[TAGS] );
	$roadName = array_key_exists( 'name', $roadTags ) ? $roadTags['name'] : "Unknown " . $roadTags['highway'];
	$s[] = $road;

	/* Find all roads that intersect with the $road */
	$q = $c->find( [
		LOC => [ '$geoIntersects' => [ '$geometry' => $road[LOC] ] ],
		TAGS => new MongoRegex('/^highway=(trunk|pedestrian|service|primary|secondary|tertiary|residential|unclassified)/' ),
		'_id' => [ '$ne' => $road['_id'] ],
	] );

	$intersectingWays = array();
	foreach ( $q as $crossRoad )
	{
		$crossTags = Functions::split_tags( $crossRoad[TAGS] );
		if ( !in_array( "name={$roadName}", $crossRoad ) && array_key_exists( 'name', $crossTags ) )
		{
			$intersectingWays[] = $crossRoad['_id'];
		}
	}

	/* Find closest road to the point, only using $intersectingWay roads */
	$res = $c->aggregate( array(
		'$geoNear' => array(
			'near' => $r[LOC],
			'distanceField' => 'distance',
			'distanceMultiplier' => 1,
			'maxDistance' => 5000,
			'spherical' => true,
			'query' => array( '_id' => [ '$in' => $intersectingWays ], TAGS => [ '$ne' => "name={$roadName}" ] ),
			'limit' => 1,
		)
	) );

	$intersectingRoad = false;

	if ( array_key_exists( 'result', $res ) && ( count( $res['result'] ) > 0 ) )
	{
		$intersectingRoad = $res['result'][0];

		$roadTags = Functions::split_tags( $intersectingRoad[TAGS] );
		if ( array_key_exists( 'name', $roadTags ) )
		{
			$intersectRoadName = $roadTags['name'];
		}
		else if ( array_key_exists( 'ref', $roadTags ) )
		{
			$intersectRoadName = $roadTags['ref'];
		}
		else
		{
			$intersectRoadName = "???";
		}
		$s[] = $intersectingRoad;
	}

	/* If there is a ref, use it, otherwise set ??? */
	if ( array_key_exists( 'ref', $tags ) )
	{
		$pbref = $tags['ref'];
	}
	else
	{
		$pbref = '???';
	}

	/* Add name tag */
	if ( ! $intersectingRoad )
	{
		$desc = "On $roadName";
	}
	else
	{
		if ( $intersectingRoad['distance'] < 20 )
		{
			$desc = "On $roadName, on the corner with $intersectRoadName";
		}
		else
		{
			$desc = "On $roadName, near $intersectRoadName";
		}
	}

	$r['desc'] = $desc;
	$r['ref'] = $pbref;

	$r[TAGS][] = "name={$pbref}<br/>{$desc}";

	return $desc;
}
