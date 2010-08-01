<?php /* -*- Mode: php; tab-width: 4; indent-tabs-mode: t; c-basic-offset: 4; -*- */
/* vim: :set fdm=marker : */
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

/**
* LibertyGraph class
* A developer service for graphing relations between liberty content. Should not be invoked by just lcconfig, but integrated in any classes wanted to utilize the graphing features.
*
* @version $Revision: $
* @class LibertyEdge
*/

/**
 * Initialize
 */
require_once( KERNEL_PKG_PATH.'BitBase.php' );


class LibertyGraph extends BitBase{

	/**
	 * Primary key for parent object when instantiated
	 */
	var $mContentId;

	public function __construct( $pContentId=NULL ) {
		BitBase::BitBase();
		$this->mContentId = $pContentId;
	}

	public function getGraph( $pParamHash ){ 
		// get all ancestors and childred
	}

	/**
	 * fetches the tail( ancestors ) of a graph starting from the content id vertice 
	 */
	public function getTailGraph( $pHeadContentId ){
		// @TODO query to travel up the tree
	}

	/**
	 * fetches the head( children or subtree ) of a graph starting from the content id vertice 
	 */
	public function getHeadGraph( $pTailContentId ){
		$bindVars = array();
		$selectSql = $joinSql = $whereSql = '';

		if( $this->mDb->isAdvancedPostgresEnabled() ) {

			$query = "SELECT branch AS hash_key, * $selectSql 
					  FROM connectby('`".BIT_DB_PREFIX."liberty_edge`', '`head_content_id`', '`tail_content_id`', ?, 0, '/') AS t(cb_head_content_id int,cb_tail_content_id int, level int, branch text) 
						INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON(lc.`content_id`=fg.`content_id`) 
						$joinSql
					  ORDER BY branch, t.`weight`";

			$bindVars[] = $this->mContentId;

			$rslt = $this->mDb->GetAssoc( $query, $bindVars )
		}
	}
}
