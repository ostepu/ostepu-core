<?php


/**
 * @file index.php executes the DBControl component on calling via rest api
 *
 * @author Till Uhlig
 * @date 2014
 */
 
require_once ( dirname( __FILE__ ) . '/DBControl.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBControl::getPrefix( ) );

// runs the DBControl
if ( !$com->used( ) )
    new DBControl( $com->loadConfig( ) );
?>