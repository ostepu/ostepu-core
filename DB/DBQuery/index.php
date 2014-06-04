<?php
require_once ( dirname( __FILE__ ) . '/DBQuery.php' );
include_once ( '../../Assistants/CConfig.php' );

// runs the CConfig
$com = new CConfig( DBQuery::getPrefix( ) );

// runs the DBQuery
if ( !$com->used( ) )
    new DBQuery( $com->loadConfig( ) );
?>