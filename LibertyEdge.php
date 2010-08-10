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

/*
   -==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
   Portions of this file are modifiable

   Anything between the CUSTOM BEGIN: and CUSTOM END:
   comments will be preserved on regeneration of this
   file.
   -==-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
*/

/**
* LibertyEdge class
* A developer service for graphing relations between liberty content. Should not be invoked by just lcconfig, but integrated in any classes wanted to utilize the graphing features.
*
* @version $Revision: $
* @class LibertyEdge
*/

/**
 * Initialize
 */
require_once( KERNEL_PKG_PATH.'BitBase.php' );
require_once( LIBERTY_PKG_PATH . 'LibertyValidator.php' );

/* =-=- CUSTOM BEGIN: require -=-= */

/* =-=- CUSTOM END: require -=-= */


class LibertyEdge extends BitBase {

	/**
	 * Primary key for parent object when instantiated
	 */
	var $mContentId;

	var $mVerification;

	var $mSchema;

	public function __construct( $pContentId=NULL ) {
		BitBase::BitBase();
		$this->mContentId = $pContentId;
	}


	/**
	 * stores a single record in the liberty_edge table
	 */
	function store( &$pParamHash ){
		if( $this->verify( &$pParamHash ) ) {
			if ( !empty( $pParamHash['liberty_edge_store'] ) ){
				$table = 'liberty_edge';
				$this->mDb->StartTrans();
				if( !$this->getOne( $pParamHash['liberty_edge'] ) ){
					$result = $this->mDb->associateInsert( $table, $pParamHash['liberty_edge_store'] );
				// custom code which generator can not anticipate
				}else{
					$locIds = array( 
						'head_content_id' => $pParamHash['liberty_edge_store']['head_content_id'] 
						'tail_content_id' => ( !empty( $pParamHash['liberty_edge_store']['tail_content_id'] ) ? $pParamHash['liberty_edge_store']['tail_content_id'] : NULL ),
					);
					$result = $this->mDb->associateUpdate( $table, $pParamHash['liberty_edge_store'], $locIds );
				}
			}

			/* =-=- CUSTOM BEGIN: store -=-= */

			/* =-=- CUSTOM END: store -=-= */

			$this->mDb->CompleteTrans();
		}
	}


	/** 
	 * verifies a data set for storage in the Liberty_edge table
	 * data is put into $pParamHash['liberty_edge_store'] for storage
	 */
	function verify( &$pParamHash ){
		// Use $pParamHash here since it handles validation right
		$this->validateFields($pParamHash);

		/* =-=- CUSTOM BEGIN: verify -=-= */

		/* =-=- CUSTOM END: verify -=-= */

		return( count( $this->mErrors )== 0 );
	}

	function expunge( &$pParamHash ){
		$ret = FALSE;
		$this->mDb->StartTrans();
		$bindVars = array();
		$whereSql = "";

		// limit results by head_content_id
		if( !empty( $pParamHash['head_content_id'] ) ){
			$bindVars[] = $pParamHash['head_content_id'];
			$whereSql .= " AND `head_content_id` = ?";
		}
		// limit results by tail_content_id
		if( !empty( $pParamHash['tail_content_id'] ) ){
			$bindVars[] = $pParamHash['tail_content_id'];
			$whereSql .= " AND `tail_content_id` = ?";
		}elseif( isset( $pParamHash['tail_content_id'] ) ){
			$whereSql .= " AND `tail_content_id` IS NULL";
		}

		/* =-=- CUSTOM BEGIN: expunge -=-= */

		/* =-=- CUSTOM END: expunge -=-= */

		if( !empty( $whereSql ) ){
			$whereSql = preg_replace( '/^[\s]*AND\b/i', 'WHERE ', $whereSql );
		}

		$query = "DELETE FROM `liberty_edge` ".$whereSql;

		if( $this->mDb->query( $query, $bindVars ) ){
			$ret = TRUE;
		}

		$this->mDb->CompleteTrans();
		return $ret;
	}

	function getList( &$pParamHash = NULL ){
		$ret = $bindVars = array();
		$whereSql = "";

		// limit results by head_content_id
		if( !empty( $pParamHash['head_content_id'] ) ){
			$bindVars[] = $pParamHash['head_content_id'];
			$whereSql .= " AND `head_content_id` = ?";
		}

		// limit results by tail_content_id
		if( !empty( $pParamHash['tail_content_id'] ) ){
			$bindVars[] = $pParamHash['tail_content_id'];
			$whereSql .= " AND `tail_content_id` = ?";
		}

		/* =-=- CUSTOM BEGIN: getList -=-= */

		if( $this->isValid() ) {
			$bindVars[] = $this->mContentId;
			$whereSql .= " AND `tail_content_id` = ?";
		}

		/* =-=- CUSTOM END: getList -=-= */

		if( !empty( $whereSql ) ){
			$whereSql = preg_replace( '/^[\s]*AND\b/i', 'WHERE ', $whereSql );
		}

		$query = "SELECT  `head_content_id`, `tail_content_id`, `weight` FROM `liberty_edge`".$whereSql;
		$ret = $this->mDb->getArray( $query, $bindVars );
		return $ret;
	}

	/**
	 * preview prepares the fields in this type for preview
	 */
	 function previewFields( &$pParamHash ) {
		$this->prepVerify();
		if (!empty($pParamHash['liberty_edge'])) {
			LibertyValidator::preview(
				$this->mVerification['liberty_edge'],
				$pParamHash['liberty_edge'],
				$this, $pParamHash['liberty_edge_store']);
		}
	}

	/**
	 * validateFields validates the fields in this type
	 */
	function validateField( &$pParamHash ) {
		$this->prepVerify();
		if (!empty($pParamHash['liberty_edge'])) {
			LibertyValidator::validate(
				$this->mVerification['liberty_edge'],
				$pParamHash['liberty_edge'],
				$this, $pParamHash['liberty_edge_store']);
		}
	}

	/**
	 * prepVerify prepares the object for input verification
	 */
	function prepVerify() {
		if (empty($this->mVerification['liberty_edge'])) {

	 		/* Validation for head_content_id */
			$this->mVerification['liberty_edge']['reference']['head_content_id'] = array(
				'name' => 'head_content_id',
				'table' => 'liberty_content',
				'column' => 'content_id',
				'required' => '1'
			);
	 		/* Validation for tail_content_id */
			$this->mVerification['liberty_edge']['reference']['tail_content_id'] = array(
				'name' => 'tail_content_id',
				'table' => 'liberty_content',
				'column' => 'content_id'
			);
	 		/* Validation for weight */
			$this->mVerification['liberty_edge']['int']['weight'] = array(
				'name' => 'weight',
			);

		}
	}

	/**
	 * returns the data schema by database table
	 */
	public function getSchema() {
		if (empty($this->mSchema['liberty_edge'])) {

	 		/* Schema for head_content_id */
			$this->mSchema['liberty_edge']['head_content_id'] = array(
				'name' => 'head_content_id',
				'type' => 'reference',
				'label' => 'Head Content Reference',
				'help' => '',
				'table' => 'liberty_content',
				'column' => 'content_id',
				'required' => '1'
			);
	 		/* Schema for tail_content_id */
			$this->mSchema['liberty_edge']['tail_content_id'] = array(
				'name' => 'tail_content_id',
				'type' => 'reference',
				'label' => 'Tail Content Reference',
				'help' => '',
				'table' => 'liberty_content',
				'column' => 'content_id'
			);
	 		/* Schema for weight */
			$this->mSchema['liberty_edge']['weight'] = array(
				'name' => 'weight',
				'type' => 'int',
				'label' => 'Edge Weight',
				'help' => '',
			);
		}


		return $this->mSchema;
	}


	// {{{ =================== Custom Helper Mthods  ====================


	/* This section is for any helper methods you wish to create */
	/* =-=- CUSTOM BEGIN: methods -=-= */

	function getOne( &$pParamHash ){
		$ret = FALSE;

		if( !empty( $pParamHash['head_content_id'] ) && 
			isset( $pParamHash['tail_content_id'] ) 
		){
			$bindVars = array();
			$whereSql = "";

			// limit results by head_content_id
			$bindVars[] = $pParamHash['head_content_id'];
			$whereSql .= " AND `{$fieldName}}` = ?";
			// limit results by tail_content_id
			if( !empty( $pParamHash['tail_content_id'] ) ){
				$bindVars[] = $pParamHash['tail_content_id'];
				$whereSql .= " AND `{$fieldName}}` = ?";
			}elseif( isset( $pParamHash['tail_content_id'] ) ){
				$whereSql .= " AND `tail_content_id` IS NULL";
			}

			$whereSql = preg_replace( '/^[\s]*AND\b/i', 'WHERE ', $whereSql );

			$query = "SELECT * FROM `liberty_edge` ".$whereSql;

			if( $this->mDb->getOne( $query, $bindVars ) ){
				$ret = TRUE;
			}
		}

		return $ret;
	}

	/* =-=- CUSTOM END: methods -=-= */


	// }}} -- end of Custom Helper Methods


}

function liberty_edge_content_preview( $pObject, $pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_LIBERTY_EDGE ) ){
		$liberty_edge = new LibertyEdge(); 
		$pObject->mInfo['liberty_edge'] = $liberty_edge->previewFields( $pParamHash );
	}
}
function liberty_edge_content_edit( $pObject, $pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_LIBERTY_EDGE ) ){
		// pass through to display to load up content data
		liberty_edge_content_display( $pObject, $pParamHash );
	}
}
function liberty_edge_content_store( $pObject, $pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_LIBERTY_EDGE ) ){
		$liberty_edge = new LibertyEdge( $pObject->mContentId ); 
		if( !$liberty_edge->store( $pParamHash ) ){
			$pObject->setError( 'liberty_edge', $liberty_edge->mErrors );
		}
	}
}
function liberty_edge_content_expunge( $pObject, $pParamHash ){
	if( $pObject->hasService( LIBERTY_SERVICE_LIBERTY_EDGE ) ){
		$liberty_edge = new LibertyEdge( $pObject->mContentId ); 
		if( !$liberty_edge->expunge() ){
			$pObject->setError( 'liberty_edge', $liberty_edge->mErrors );
		}
	}
}
