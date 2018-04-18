<?php

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/db.php';

$connection = DB\getConnection( [
	'host' => 'localhost',
	'username' => 'root',
	'password' => $productionEnv ? '95a9e9d5deeb8046fc4c530080afcdbe5a855d5c5de09056' : ''
] );
// "Use" the database
$connection->exec( file_get_contents( __DIR__ . '/Database setup.sql' ) );

function getProjects () {

	global $connection;

	return DB\getEntries( $connection, 'projects' );

}

function getProjectBySlug ( $slug ) {

	global $connection;

	return DB\getEntriesWhere( $connection, 'projects', [ 'slug' => $slug ] );

}

function getProjectsByType () {

	global $connection;

	$query = 'SELECT name, slug, type, `type description`, `featured images` FROM projects ORDER BY type';
	$projects = DB\raw( $connection, $query );

	// Parse the values that are JSON-encoded
	foreach ( $projects as &$project ) {
		foreach ( $project as $key => $value ) {
			if ( $value[ 0 ] == '[' )
				$project[ $key ] = json_decode( $value, true );
		}
	}
	unset( $project );

	$projectsByType = [ ];
	foreach ( $projects as $project ) {
		$projectsByType[ $project[ 'type' ] ][ ] = $project;
	}

	return $projectsByType;

}