<?php /* -*- Mode: php; tab-width: 4; indent-tabs-mode: t; c-basic-offset: 4; -*- */
/**
 * $Header: $
 *
 * Copyright (c) 2010 Tekimaki LLC http://tekimaki.com
 * Copyright (c) 2010 Will James will@tekimaki.com
 *
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details *
 * $Id: $
 * @package libertygraph
 * @subpackage class
 */

/*
   -==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
   Portions of this file are modifiable

   Anything between the CUSTOM BEGIN: and CUSTOM END:
   comments will be preserved on regeneration of this
   file.
   -==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
*/


$tables = array(
    'liberty_edge' => "
        head_content_id I4 NOTNULL,
        tail_content_id I4,
        weight I4
        CONSTRAINT '
		, CONSTRAINT `liberty_edge_head_content_id_content_id_ref` FOREIGN KEY (`head_content_id`) REFERENCES `liberty_content` (`content_id`)
		, CONSTRAINT `liberty_edge_tail_content_id_content_id_ref` FOREIGN KEY (`tail_content_id`) REFERENCES `liberty_content` (`content_id`)
        '
    ",
);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( LIBERTYGRAPH_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( LIBERTYGRAPH_PKG_NAME, array(
	'description' => "A developer package for creating graph relations between content objects",
	'license' => '<a href="http://www.gnu.org/copyleft/lesser.html">LGPL</a>',));

// $indices = array();
// $gBitInstaller->registerSchemaIndexes( LIBERTYGRAPH_PKG_NAME, $indices );

// Sequences
$gBitInstaller->registerSchemaSequences( LIBERTYGRAPH_PKG_NAME, array (
));

// Schema defaults
$defaults = array(
);
if (count($defaults) > 0) {
	$gBitInstaller->registerSchemaDefault( LIBERTYGRAPH_PKG_NAME, $defaults);
}


// User Permissions
$gBitInstaller->registerUserPermissions( LIBERTYGRAPH_PKG_NAME, array(
	array ( 'p_libertygraph_admin'  , 'Can admin the libertygraph package', 'admin'      , LIBERTYGRAPH_PKG_NAME ),
	array ( 'p_libertygraph_view'  , 'Can view the libertygraph package', 'admin'      , LIBERTYGRAPH_PKG_NAME ),
));

// Default Preferences
$gBitInstaller->registerPreferences( LIBERTYGRAPH_PKG_NAME, array(
));

// Requirements
$gBitInstaller->registerRequirements( LIBERTYGRAPH_PKG_NAME, array(
	'liberty' => array( 'min' => '2.1.5', ),

));
