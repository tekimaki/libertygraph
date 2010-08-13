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

	public function isValid(){
		return( $this->verifyId( $this->mContentId ) && is_numeric( $this->mContentId ) && $this->mContentId > 0 );
	}

	public function getGraph( &$pParamHash = array() ){ 
		// get all ancestors and children
		$tails = $this->getTailGraph( $pParamHash );
		$heads = $this->getHeadGraph( $pParamHash );
		// not sure if this merge is how we want the data to look - might want to return a mixed array
		return array_merge( $heads, $tails );
	}

	public function getGraphHash( &$pParamHash = array() ){
		// get all ancestors and children
		$tails = $this->getTailGraphHash( $pParamHash );
		$heads = $this->getHeadGraphHash( $pParamHash );
		// @TODO merge this somehow
		return NULL;
	}

	/**
	 * fetches the tail( ancestors ) of a graph starting from the content id vertice 
	 */
	public function getTailGraph( &$pParamHash = array() ){
		if( $this->mDb->isAdvancedPostgresEnabled() ) {
			$bindVars = array();
			$selectSql = $joinSql = $whereSql = '';

			// set head content id
			$headContentId = !empty($pParamHash['head_content_id'])?$pParamHash['head_content_id']:( $this->isValid()?$this->mContentId:NULL );
			$bindVars[] = $headContentId;

			// limit by content type 
			if( !empty( $pParamHash['content_type_guid'] ) ){
				$whereSql .= " WHERE lc.`content_type_guid` = ?";
				$bindVars[] = $pParamHash['content_type_guid'];
			}

			$query = "SELECT branch AS hash_key, * $selectSql 
					  FROM connectby('`".BIT_DB_PREFIX."liberty_edge`', '`tail_content_id`', '`head_content_id`', ?, 0, '/') AS t(cb_tail_content_id int,cb_head_content_id int, level int, branch text) 
						INNER JOIN `".BIT_DB_PREFIX."liberty_edge` le ON(le.`tail_content_id`=`cb_tail_content_id`) 
						INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON(lc.`content_id`=`cb_tail_content_id`) 
						$joinSql $whereSql 
					  ORDER BY branch, le.`weight` ASC";

			$rslt = $this->mDb->GetAssoc( $query, $bindVars );

			return $rslt;
		}
	}

	/**
	 * fetches the head( children or subtree ) of a graph starting from the content id vertice 
	 */
	public function getHeadGraph( &$pParamHash = array() ){
		if( $this->mDb->isAdvancedPostgresEnabled() ) {
			$bindVars = array();
			$selectSql = $joinSql = $whereSql = '';

			// set tail content id
			$tailContentId = !empty($pParamHash['tail_content_id'])?$pParamHash['tail_content_id']:( $this->isValid()?$this->mContentId:NULL );
			$bindVars[] = $tailContentId;

			// limit by content type 
			if( !empty( $pParamHash['content_type_guid'] ) ){
				$whereSql .= " WHERE lc.`content_type_guid` = ?";
				$bindVars[] = $pParamHash['content_type_guid'];
			}

			$query = "SELECT branch AS hash_key, * $selectSql 
					  FROM connectby('`".BIT_DB_PREFIX."liberty_edge`', '`head_content_id`', '`tail_content_id`', ?, 0, '/') AS t(cb_head_content_id int,cb_tail_content_id int, level int, branch text) 
						INNER JOIN `".BIT_DB_PREFIX."liberty_edge` le ON(le.`head_content_id`=`cb_head_content_id`) 
						INNER JOIN `".BIT_DB_PREFIX."liberty_content` lc ON(lc.`content_id`=`cb_head_content_id`) 
						$joinSql $whereSql 
					  ORDER BY branch, le.`weight` ASC";

			$rslt = $this->mDb->GetAssoc( $query, $bindVars );

			return $rslt;
		}
	}

	/**
	 * returns a graph as a nested hash instead of a list
	 */
	public function getHeadGraphHash( &$pParamHash = array() ){
		return $this->listToHash( $this->getHeadGraph( $pParamHash ) );
	}

	public function getTailGraphHash( &$pParamHash = array() ){
		return $this->listToHash( $this->getTailGraph( $pParamHash ) );
	}

	public function listToHash( $pGraphArray ){ 
		$ret = array();
		LibertyGraph::splitConnectByGraph( $ret, $pGraphArray );
		return $ret; 
	}

	public static function splitConnectByGraph( &$pRet, $pGraphArray ) {
		if( $pGraphArray ) {
			foreach( array_keys( $pGraphArray ) as $conId ) {
				$path = explode( '/', $conId );
				LibertyGraph::recurseConnectByPath( $pRet, $pGraphArray[$conId], $path );
			}
		}
	}

	public static function recurseConnectByPath( &$pRet, $pGraphArray, $pPath ) {
		$popId = array_shift( $pPath );
		if( count( $pPath ) > 0 ) {
			if( empty( $pRet[$popId]['nodes'] ) ) {
				$pRet[$popId]['nodes'] = array();
			}
			LibertyGraph::recurseConnectByPath( $pRet[$popId]['nodes'], $pGraphArray, $pPath );
		} else {
			$pRet[$popId]['content'] = $pGraphArray;
		}
	}

	public function setContentId( $pContentId ){
		if( $this->verifyId( $pContentId ) ){
			$this->mContentId = $pContentId;
		}
	}
}
