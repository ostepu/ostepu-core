<?php


/**
 * @file index.php executes the CFilter component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once ( dirname( __FILE__ ) . '/CFilter.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( CFilter::getPrefix( ) );

// runs the CFilter
if ( !$com->used( ) )
    new CFilter( $com->loadConfig( ) );