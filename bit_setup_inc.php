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

define( 'LIBERTY_SERVICE_LIBERTY_EDGE', 'graph_relationships' );

global $gBitSystem;

$registerHash = array(
	'package_name' => 'libertygraph',
	'package_path' => dirname( __FILE__ ).'/',
	'homeable' => TRUE,
);
$gBitSystem->registerPackage( $registerHash );

// If package is active and the user has view auth then register the package menu
if( $gBitSystem->isPackageActive( 'libertygraph' ) && $gBitUser->hasPermission( 'p_libertygraph_view' ) ) {
	$menuHash = array(
		'package_name'  => LIBERTYGRAPH_PKG_NAME,
		'index_url'     => LIBERTYGRAPH_PKG_URL.'index.php',
		'menu_template' => 'bitpackage:libertygraph/menu_libertygraph.tpl',
	);
	$gBitSystem->registerAppMenu( $menuHash );

    $gLibertySystem->registerService(
		LIBERTY_SERVICE_LIBERTY_EDGE,
		LIBERTYGRAPH_PKG_NAME,
        array(
			'content_preview_function' => 'liberty_edge_content_preview',
			'content_edit_function' => 'liberty_edge_content_edit',
			'content_store_function' => 'liberty_edge_content_store',
			'content_expunge_function' => 'liberty_edge_content_expunge',
        ),
        array(
			'description' => 'A developer service for graphing relations between liberty content. Should not be invoked by just lcconfig, but integrated in any classes wanted to utilize the graphing features.'
        )
    );

}
