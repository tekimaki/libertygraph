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

	public function getGraph( $pParamHash = array() ){ 
		// get all ancestors and children
		$tails = $this->getTailGraph();
		$heads = $this->getHeadGraph();
		// not sure if this merge is how we want the data to look - might want to return a mixed array
		return array_merge( $heads, $tails );
	}

	/**
	 * fetches the tail( ancestors ) of a graph starting from the content id vertice 
	 */
	public function getTailGraph( $pHeadContentId = NULL ){
		if( $this->mDb->isAdvancedPostgresEnabled() ) {
			$bindVars = array();
			$selectSql = $joinSql = $whereSql = '';

			if( empty( $pHeadContentId ) && $this->isValid() ){
				$pHeadContentId = $this->mContentId;
			}

			$query = "SELECT branch AS hash_key, * $selectSql 
					  FROM connectby('`".BIT_DB_PREFIX."liberty_edge`', '`tail_content_id`', '`head_content_id`', ?, 0, '/') AS t(cb_tail_content_id int,cb_head_content_id int, level int, branch text) 
						INNER JOIN `".BIT_DB_PREFIX."liberty_edge` le ON(le.`tail_content_id`=`cb_tail_content_id`) 
						INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON(lc.`content_id`=`cb_tail_content_id`) 
						$joinSql $whereSql 
					  ORDER BY branch, le.`weight`";

			$bindVars[] = $pHeadContentId;

			$rslt = $this->mDb->GetAssoc( $query, $bindVars );

			return $rslt;
		}
	}

	/**
	 * fetches the head( children or subtree ) of a graph starting from the content id vertice 
	 */
	public function getHeadGraph( $pTailContentId = NULL ){
		if( $this->mDb->isAdvancedPostgresEnabled() ) {
			$bindVars = array();
			$selectSql = $joinSql = $whereSql = '';

			if( empty( $pTailContentId ) && $this->isValid() ){
				$pTailContentId = $this->mContentId;
			}

			$query = "SELECT branch AS hash_key, * $selectSql 
					  FROM connectby('`".BIT_DB_PREFIX."liberty_edge`', '`head_content_id`', '`tail_content_id`', ?, 0, '/') AS t(cb_head_content_id int,cb_tail_content_id int, level int, branch text) 
						INNER JOIN `".BIT_DB_PREFIX."liberty_edge` le ON(le.`head_content_id`=`cb_head_content_id`) 
						INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON(lc.`content_id`=`cb_head_content_id`) 
						$joinSql $whereSql 
					  ORDER BY branch, le.`weight`";

			$bindVars[] = $pTailContentId;

			$rslt = $this->mDb->GetAssoc( $query, $bindVars );

			return $rslt;
		}
	}

	public function isValid(){
		return( $this->verifyId( $this->mContentId ) && is_numeric( $this->mContentId ) && $this->mContentId > 0 );
	}
}
