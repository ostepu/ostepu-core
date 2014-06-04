<?php
require_once ( dirname( __FILE__ ) . '/DBQuery2.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBQuery2::getPrefix( ) );

// runs the DBQuery
if ( !$com->used( ) )
    new DBQuery2( $com->loadConfig( ) );
?>